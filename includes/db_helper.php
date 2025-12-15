<?php
/**
 * Database Helper Functions
 * XML database operations
 */

/**
 * Load XML file
 */
function loadXML($filepath) {
    if (!file_exists($filepath)) {
        return false;
    }
    
    $xml = simplexml_load_file($filepath);
    return $xml !== false ? $xml : false;
}

/**
 * Save XML file
 */
function saveXML($xml, $filepath) {
    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($xml->asXML());
    
    $fp = fopen($filepath, 'w');
    if (flock($fp, LOCK_EX)) {
        fwrite($fp, $dom->saveXML());
        flock($fp, LOCK_UN);
        fclose($fp);
        return true;
    }
    fclose($fp);
    return false;
}

/**
 * Get all users
 */
function getAllUsers() {
    $xml = loadXML(DB_USERS);
    if (!$xml) return [];
    
    $users = [];
    foreach ($xml->user as $user) {
        $users[] = [
            'id' => (string)$user->id,
            'username' => (string)($user->username ?? ''),
            'email' => (string)$user->email,
            'password' => (string)$user->password,
            'first_name' => (string)($user->first_name ?? ''),
            'last_name' => (string)($user->last_name ?? ''),
            'name' => (string)$user->name,
            'role' => (string)$user->role,
            'status' => (string)$user->status,
            'google_id' => (string)($user->google_id ?? ''),
            'profile_photo' => (string)($user->profile_photo ?? ''),
            'created_at' => (string)$user->created_at
        ];
    }
    return $users;
}

/**
 * Get user by ID
 */
function getUserById($id) {
    $users = getAllUsers();
    foreach ($users as $user) {
        if ($user['id'] === $id) {
            return $user;
        }
    }
    return null;
}

/**
 * Get user by email
 */
function getUserByEmail($email) {
    $users = getAllUsers();
    foreach ($users as $user) {
        if (strtolower($user['email']) === strtolower($email)) {
            return $user;
        }
    }
    return null;
}

/**
 * Get user by username
 */
function getUserByUsername($username) {
    $users = getAllUsers();
    foreach ($users as $user) {
        if (strtolower($user['username'] ?? '') === strtolower($username)) {
            return $user;
        }
    }
    return null;
}

/**
 * Get user by Google ID
 */
function getUserByGoogleId($googleId) {
    $users = getAllUsers();
    foreach ($users as $user) {
        if (($user['google_id'] ?? '') === $googleId) {
            return $user;
        }
    }
    return null;
}

/**
 * Update user's Google ID
 */
function updateUserGoogleId($userId, $googleId) {
    $xml = loadXML(DB_USERS);
    if (!$xml) return false;
    
    foreach ($xml->user as $user) {
        if ((string)$user->id === $userId) {
            if (isset($user->google_id)) {
                $user->google_id = $googleId;
            } else {
                $user->addChild('google_id', $googleId);
            }
            return saveXML($xml, DB_USERS);
        }
    }
    return false;
}

/**
 * Create new user (extended for SSO)
 */
function createUserSSO($data) {
    $xml = loadXML(DB_USERS);
    if (!$xml) return false;
    
    // Check if user exists
    if (getUserByEmail($data['email'])) {
        return false;
    }
    
    $user = $xml->addChild('user');
    $userId = generateUniqueId('user_');
    $user->addChild('id', $userId);
    $user->addChild('username', $data['username'] ?? '');
    $user->addChild('email', $data['email']);
    $user->addChild('password', $data['password'] ?? hashPassword(bin2hex(random_bytes(16))));
    $user->addChild('first_name', $data['first_name'] ?? '');
    $user->addChild('last_name', $data['last_name'] ?? '');
    $user->addChild('name', ($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
    $user->addChild('role', $data['role'] ?? ROLE_MEMBER);
    $user->addChild('status', $data['status'] ?? 'active');
    $user->addChild('google_id', $data['google_id'] ?? '');
    $user->addChild('profile_photo', $data['profile_photo'] ?? '');
    $user->addChild('created_at', date('Y-m-d H:i:s'));
    
    return saveXML($xml, DB_USERS) ? $userId : false;
}

/**
 * Create new user
function createUser($email, $password, $name, $role = ROLE_MEMBER) {
    $xml = loadXML(DB_USERS);
    if (!$xml) return false;
    
    // Check if user exists
    if (getUserByEmail($email)) {
        return false;
    }
    
    $user = $xml->addChild('user');
    $user->addChild('id', generateUniqueId('user_'));
    $user->addChild('email', $email);
    $user->addChild('password', hashPassword($password));
    $user->addChild('name', $name);
    $user->addChild('role', $role);
    $user->addChild('status', 'active');
    $user->addChild('created_at', date('Y-m-d H:i:s'));
    
    return saveXML($xml, DB_USERS) ? (string)$user->id : false;
}

/**
 * Update user
 */
function updateUser($id, $data) {
    $xml = loadXML(DB_USERS);
    if (!$xml) return false;
    
    foreach ($xml->user as $user) {
        if ((string)$user->id === $id) {
            foreach ($data as $key => $value) {
                if ($key === 'password') {
                    $value = hashPassword($value);
                }
                $user->$key = $value;
            }
            return saveXML($xml, DB_USERS);
        }
    }
    return false;
}

/**
 * Delete user
 */
function deleteUser($id) {
    $xml = loadXML(DB_USERS);
    if (!$xml) return false;
    
    $index = 0;
    foreach ($xml->user as $user) {
        if ((string)$user->id === $id) {
            unset($xml->user[$index]);
            return saveXML($xml, DB_USERS);
        }
        $index++;
    }
    return false;
}

/**
 * Get all members
 */
function getAllMembers() {
    $xml = loadXML(DB_MEMBERS);
    if (!$xml) return [];
    
    $members = [];
    foreach ($xml->member as $member) {
        $memberData = [
            'id' => (string)$member->id,
            'user_id' => (string)$member->user_id,
            'member_id' => (string)$member->member_id,
            'first_name' => (string)$member->first_name,
            'middle_name' => (string)$member->middle_name,
            'last_name' => (string)$member->last_name,
            'birthdate' => (string)$member->birthdate,
            'gender' => (string)$member->gender,
            'email' => (string)$member->email,
            'phone' => (string)$member->phone,
            'alt_phone' => (string)$member->alt_phone,
            'address' => (string)$member->address,
            'city' => (string)($member->city ?? ''),
            'province' => (string)($member->province ?? ''),
            'zip_code' => (string)($member->zip_code ?? ''),
            'school' => (string)$member->school,
            'course' => (string)$member->course,
            'year_level' => (string)$member->year_level,
            'student_id' => (string)$member->student_id,
            'skills' => (string)$member->skills,
            'motivation' => (string)$member->motivation,
            'membership_status' => (string)$member->membership_status,
            'joined_date' => (string)$member->joined_date,
            'expiry_date' => (string)$member->expiry_date,
            'profile_photo' => (string)($member->profile_photo ?? '')
        ];
        $members[] = $memberData;
    }
    return $members;
}

/**
 * Get all orders
 */
function getAllOrders() {
    $xml = loadXML(DB_ORDERS);
    if (!$xml) return [];
    $orders = [];
    foreach ($xml->order as $order) {
        $items = [];
        if (isset($order->items)) {
            foreach ($order->items->item as $item) {
                $items[] = [
                    'product_id' => (string)$item->product_id,
                    'name' => (string)$item->name,
                    'price' => (float)$item->price,
                    'quantity' => (int)$item->quantity
                ];
            }
        }
        $orders[] = [
            'id' => (string)$order->id,
            'user_id' => (string)$order->user_id,
            'items' => $items,
            'total' => (float)$order->total,
            'payment_method' => (string)($order->payment_method ?? ''),
            'payment_status' => (string)($order->payment_status ?? 'pending'),
            'status' => (string)($order->status ?? 'pending'),
            'payment_info' => (string)($order->payment_info ?? ''),
            'created_at' => (string)$order->created_at
        ];
    }
    return $orders;
}

/**
 * Get order by id
 */
function getOrderById($id) {
    $orders = getAllOrders();
    foreach ($orders as $order) {
        if ($order['id'] === $id) return $order;
    }
    return null;
}

/**
 * Create order
 */
function createOrder($data) {
    $xml = loadXML(DB_ORDERS);
    if (!$xml) {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><orders></orders>');
    }
    $orderId = generateUniqueId('ord_');
    $order = $xml->addChild('order');
    $order->addChild('id', $orderId);
    $order->addChild('user_id', $data['user_id'] ?? '');
    $order->addChild('total', number_format((float)($data['total'] ?? 0), 2, '.', ''));
    $order->addChild('payment_method', $data['payment_method'] ?? 'onsite');
    $order->addChild('payment_status', $data['payment_status'] ?? 'pending');
    $order->addChild('status', $data['status'] ?? 'pending');
    $order->addChild('payment_info', $data['payment_info'] ?? '');
    $order->addChild('created_at', date('Y-m-d H:i:s'));

    // Items
    $itemsNode = $order->addChild('items');
    foreach ($data['items'] as $it) {
        $itemNode = $itemsNode->addChild('item');
        $itemNode->addChild('product_id', $it['product_id']);
        $itemNode->addChild('name', htmlspecialchars($it['name']));
        $itemNode->addChild('price', number_format((float)$it['price'], 2, '.', ''));
        $itemNode->addChild('quantity', (int)$it['quantity']);
    }

    return saveXML($xml, DB_ORDERS) ? $orderId : false;
}

/**
 * Update order
 */
function updateOrder($id, $data) {
    $xml = loadXML(DB_ORDERS);
    if (!$xml) return false;
    foreach ($xml->order as $order) {
        if ((string)$order->id === $id) {
            foreach ($data as $key => $value) {
                if (isset($order->$key)) {
                    $order->$key = $value;
                } else {
                    $order->addChild($key, $value);
                }
            }
            return saveXML($xml, DB_ORDERS);
        }
    }
    return false;
}

/**
 * Get member by ID
 */
function getMemberById($id) {
    $members = getAllMembers();
    foreach ($members as $member) {
        if ($member['id'] === $id) {
            return $member;
        }
    }
    return null;
}

/**
 * Get member by user ID
 */
function getMemberByUserId($userId) {
    $members = getAllMembers();
    foreach ($members as $member) {
        if ($member['user_id'] === $userId) {
            return $member;
        }
    }
    return null;
}

/**
 * Create member from registration
 */
function createMember($data, $userId = null) {
    $xml = loadXML(DB_MEMBERS);
    if (!$xml) return false;
    
    $member = $xml->addChild('member');
    $member->addChild('id', generateUniqueId('mem_'));
    $member->addChild('user_id', $userId ?? '');
    $member->addChild('member_id', 'JPCS-' . date('Y') . '-' . str_pad(count($xml->member) + 1, 4, '0', STR_PAD_LEFT));
    
    // Fields that are already handled or should not be stored in member record
    $excludeFields = ['id', 'user_id', 'member_id', 'password', 'membership_status', 'joined_date', 'expiry_date'];
    
    foreach ($data as $key => $value) {
        // Skip excluded fields to avoid duplicates or storing sensitive data
        if (in_array($key, $excludeFields)) {
            continue;
        }
        $member->addChild($key, htmlspecialchars($value, ENT_XML1, 'UTF-8'));
    }
    
    $member->addChild('membership_status', 'pending');
    $member->addChild('joined_date', date('Y-m-d'));
    $member->addChild('expiry_date', date('Y-m-d', strtotime('+1 year')));
    
    return saveXML($xml, DB_MEMBERS) ? (string)$member->id : false;
}

/**
 * Update member
 */
function updateMember($id, $data) {
    $xml = loadXML(DB_MEMBERS);
    if (!$xml) return false;
    
    foreach ($xml->member as $member) {
        if ((string)$member->id === $id) {
            foreach ($data as $key => $value) {
                if ($key === 'email' && empty($member->email)) {
                    // If email field is missing, add it
                    $member->addChild('email', $value);
                } elseif (isset($member->$key)) {
                    $member->$key = $value;
                } else {
                    $member->addChild($key, $value);
                }
            }
            return saveXML($xml, DB_MEMBERS);
        }
    }
    return false;
}

/**
 * Get all events
 */
function getAllEvents() {
    $xml = loadXML(DB_EVENTS);
    if (!$xml) return [];
    
    $events = [];
    foreach ($xml->event as $event) {
        $events[] = [
            'id' => (string)$event->id,
            'title' => (string)$event->title,
            'date' => (string)$event->date,
            'time' => (string)$event->time,
            'location' => (string)$event->location,
            'description' => (string)$event->description,
            'category' => (string)($event->category ?? 'general'),
            'max_participants' => (string)($event->max_participants ?? 0),
            'registration_fee' => (string)($event->registration_fee ?? 0),
            'registration_deadline' => (string)($event->registration_deadline ?? ''),
            'status' => (string)$event->status,
            'created_at' => (string)$event->created_at
        ];
    }
    
    // Sort by date descending
    usort($events, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
    
    return $events;
}

/**
 * Get event by ID
 */
function getEventById($id) {
    $events = getAllEvents();
    foreach ($events as $event) {
        if ($event['id'] === $id) {
            return $event;
        }
    }
    return null;
}

/**
 * Create event
 */
function createEvent($data) {
    $xml = loadXML(DB_EVENTS);
    if (!$xml) return false;
    
    $event = $xml->addChild('event');
    $event->addChild('id', generateUniqueId('evt_'));
    
    foreach ($data as $key => $value) {
        $event->addChild($key, $value);
    }
    
    $event->addChild('created_at', date('Y-m-d H:i:s'));
    
    return saveXML($xml, DB_EVENTS) ? (string)$event->id : false;
}

/**
 * Get all announcements
 */
function getAllAnnouncements() {
    $xml = loadXML(DB_ANNOUNCEMENTS);
    if (!$xml) return [];
    
    $announcements = [];
    foreach ($xml->announcement as $announcement) {
        $announcements[] = [
            'id' => (string)$announcement->id,
            'title' => (string)$announcement->title,
            'content' => (string)$announcement->content,
            'category' => (string)($announcement->category ?? 'general'),
            'badge' => (string)($announcement->badge ?? ''),
            'author' => (string)($announcement->author ?? 'Admin'),
            'posted_date' => (string)$announcement->posted_date,
            'attachment' => (string)($announcement->attachment ?? ''),
            'status' => (string)$announcement->status
        ];
    }
    
    // Sort by date descending
    usort($announcements, function($a, $b) {
        return strtotime($b['posted_date']) - strtotime($a['posted_date']);
    });
    
    return $announcements;
}

/**
 * Update event
 */
function updateEvent($id, $data) {
    $xml = loadXML(DB_EVENTS);
    if (!$xml) return false;
    
    foreach ($xml->event as $event) {
        if ((string)$event->id === $id) {
            foreach ($data as $key => $value) {
                if (isset($event->$key)) {
                    $event->$key = $value;
                } else {
                    $event->addChild($key, $value);
                }
            }
            return saveXML($xml, DB_EVENTS);
        }
    }
    return false;
}

/**
 * Delete event
 */
function deleteEvent($id) {
    $xml = loadXML(DB_EVENTS);
    if (!$xml) return false;
    
    $index = 0;
    foreach ($xml->event as $event) {
        if ((string)$event->id === $id) {
            unset($xml->event[$index]);
            return saveXML($xml, DB_EVENTS);
        }
        $index++;
    }
    return false;
}

/**
 * Get announcement by ID
 */
function getAnnouncementById($id) {
    $announcements = getAllAnnouncements();
    foreach ($announcements as $announcement) {
        if ($announcement['id'] === $id) {
            return $announcement;
        }
    }
    return null;
}

/**
 * Create announcement
 */
function createAnnouncement($data) {
    $xml = loadXML(DB_ANNOUNCEMENTS);
    if (!$xml) return false;
    
    $announcement = $xml->addChild('announcement');
    $announcement->addChild('id', generateUniqueId('ann_'));
    
    foreach ($data as $key => $value) {
        $announcement->addChild($key, htmlspecialchars($value));
    }
    
    $announcement->addChild('posted_date', date('Y-m-d H:i:s'));
    
    return saveXML($xml, DB_ANNOUNCEMENTS) ? (string)$announcement->id : false;
}

/**
 * Update announcement
 */
function updateAnnouncement($id, $data) {
    $xml = loadXML(DB_ANNOUNCEMENTS);
    if (!$xml) return false;
    
    foreach ($xml->announcement as $announcement) {
        if ((string)$announcement->id === $id) {
            foreach ($data as $key => $value) {
                if (isset($announcement->$key)) {
                    $announcement->$key = htmlspecialchars($value);
                } else {
                    $announcement->addChild($key, htmlspecialchars($value));
                }
            }
            return saveXML($xml, DB_ANNOUNCEMENTS);
        }
    }
    return false;
}

/**
 * Delete announcement
 */
function deleteAnnouncement($id) {
    $xml = loadXML(DB_ANNOUNCEMENTS);
    if (!$xml) return false;
    
    $index = 0;
    foreach ($xml->announcement as $announcement) {
        if ((string)$announcement->id === $id) {
            unset($xml->announcement[$index]);
            return saveXML($xml, DB_ANNOUNCEMENTS);
        }
        $index++;
    }
    return false;
}

/**
 * Get all products
 */
function getAllProducts() {
    $xml = loadXML(DB_PRODUCTS);
    if (!$xml) return [];
    
    $products = [];
    foreach ($xml->product as $product) {
        $products[] = [
            'id' => (string)$product->id,
            'name' => (string)$product->name,
            'description' => (string)$product->description,
            'price' => (string)$product->price,
            'stock' => (string)$product->stock,
            'image' => (string)$product->image,
            'category' => (string)$product->category,
            'status' => (string)$product->status,
            'created_at' => (string)$product->created_at
        ];
    }
    return $products;
}

/**
 * Get product by ID
 */
function getProductById($id) {
    $products = getAllProducts();
    foreach ($products as $product) {
        if ($product['id'] === $id) {
            return $product;
        }
    }
    return null;
}

/**
 * Create product
 */
function createProduct($data) {
    $xml = loadXML(DB_PRODUCTS);
    if (!$xml) return false;
    
    $product = $xml->addChild('product');
    $product->addChild('id', generateUniqueId('prd_'));
    
    foreach ($data as $key => $value) {
        $product->addChild($key, htmlspecialchars($value));
    }
    
    $product->addChild('created_at', date('Y-m-d H:i:s'));
    
    return saveXML($xml, DB_PRODUCTS) ? (string)$product->id : false;
}

/**
 * Update product
 */
function updateProduct($id, $data) {
    $xml = loadXML(DB_PRODUCTS);
    if (!$xml) return false;
    
    foreach ($xml->product as $product) {
        if ((string)$product->id === $id) {
            foreach ($data as $key => $value) {
                if (isset($product->$key)) {
                    $product->$key = htmlspecialchars($value);
                } else {
                    $product->addChild($key, htmlspecialchars($value));
                }
            }
            return saveXML($xml, DB_PRODUCTS);
        }
    }
    return false;
}

/**
 * Delete product
 */
function deleteProduct($id) {
    $xml = loadXML(DB_PRODUCTS);
    if (!$xml) return false;
    
    $index = 0;
    foreach ($xml->product as $product) {
        if ((string)$product->id === $id) {
            unset($xml->product[$index]);
            return saveXML($xml, DB_PRODUCTS);
        }
        $index++;
    }
    return false;
}

/**
 * Get all gallery items
 */
function getAllGalleryItems() {
    $xml = loadXML(DB_GALLERY);
    if (!$xml) return [];
    
    $items = [];
    foreach ($xml->item as $item) {
        $items[] = [
            'id' => (string)$item->id,
            'title' => (string)$item->title,
            'description' => (string)$item->description,
            'image' => (string)$item->image,
            'category' => (string)($item->category ?? $item->event ?? 'Other'),
            'event' => (string)$item->event,
            'uploaded_date' => (string)$item->uploaded_date,
            'status' => (string)$item->status
        ];
    }
    
    usort($items, function($a, $b) {
        return strtotime($b['uploaded_date']) - strtotime($a['uploaded_date']);
    });
    
    return $items;
}

/**
 * Get gallery item by ID
 */
function getGalleryItemById($id) {
    $items = getAllGalleryItems();
    foreach ($items as $item) {
        if ($item['id'] === $id) {
            return $item;
        }
    }
    return null;
}

/**
 * Create gallery item
 */
function createGalleryItem($data) {
    $xml = loadXML(DB_GALLERY);
    if (!$xml) return false;
    
    $item = $xml->addChild('item');
    $item->addChild('id', generateUniqueId('gal_'));
    
    foreach ($data as $key => $value) {
        $item->addChild($key, htmlspecialchars($value));
    }
    
    $item->addChild('uploaded_date', date('Y-m-d H:i:s'));
    
    return saveXML($xml, DB_GALLERY) ? (string)$item->id : false;
}

/**
 * Update gallery item
 */
function updateGalleryItem($id, $data) {
    $xml = loadXML(DB_GALLERY);
    if (!$xml) return false;
    
    foreach ($xml->item as $item) {
        if ((string)$item->id === $id) {
            foreach ($data as $key => $value) {
                if (isset($item->$key)) {
                    $item->$key = htmlspecialchars($value);
                } else {
                    $item->addChild($key, htmlspecialchars($value));
                }
            }
            return saveXML($xml, DB_GALLERY);
        }
    }
    return false;
}

/**
 * Delete gallery item
 */
function deleteGalleryItem($id) {
    $xml = loadXML(DB_GALLERY);
    if (!$xml) return false;
    
    $index = 0;
    foreach ($xml->item as $item) {
        if ((string)$item->id === $id) {
            // Delete image file
            $imagePath = '../assets/uploads/gallery/' . (string)$item->image;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
            unset($xml->item[$index]);
            return saveXML($xml, DB_GALLERY);
        }
        $index++;
    }
    return false;
}

/**
 * Get all registrations
 */
function getAllRegistrations() {
    $xml = loadXML(DB_REGISTRATIONS);
    if (!$xml) return [];
    
    $registrations = [];
    foreach ($xml->registration as $reg) {
        $registrations[] = [
            'id' => (string)$reg->id,
            'first_name' => (string)$reg->first_name,
            'last_name' => (string)$reg->last_name,
            'email' => (string)$reg->email,
            'phone' => (string)$reg->phone,
            'school' => (string)$reg->school,
            'course' => (string)$reg->course,
            'year_level' => (string)$reg->year_level,
            'status' => (string)$reg->status,
            'submitted_date' => (string)$reg->submitted_date
        ];
    }
    
    usort($registrations, function($a, $b) {
        return strtotime($b['submitted_date']) - strtotime($a['submitted_date']);
    });
    
    return $registrations;
}

/**
 * Get registration by ID
 */
function getRegistrationById($id) {
    $registrations = getAllRegistrations();
    foreach ($registrations as $reg) {
        if ($reg['id'] === $id) {
            return $reg;
        }
    }
    return null;
}

/**
 * Update registration status
 */
function updateRegistrationStatus($id, $status) {
    $xml = loadXML(DB_REGISTRATIONS);
    if (!$xml) return false;
    
    foreach ($xml->registration as $reg) {
        if ((string)$reg->id === $id) {
            $reg->status = $status;
            return saveXML($xml, DB_REGISTRATIONS);
        }
    }
    return false;
}

/**
 * Get all inquiries
 */
function getAllInquiries() {
    $xml = loadXML(DB_INQUIRIES);
    if (!$xml) return [];
    
    $inquiries = [];
    foreach ($xml->inquiry as $inquiry) {
        $inquiries[] = [
            'id' => (string)$inquiry->id,
            'name' => (string)$inquiry->name,
            'email' => (string)$inquiry->email,
            'subject' => (string)$inquiry->subject,
            'message' => (string)$inquiry->message,
            'status' => (string)$inquiry->status,
            'submitted_date' => (string)$inquiry->submitted_date,
            'replied_date' => (string)$inquiry->replied_date
        ];
    }
    
    usort($inquiries, function($a, $b) {
        return strtotime($b['submitted_date']) - strtotime($a['submitted_date']);
    });
    
    return $inquiries;
}

/**
 * Get inquiry by ID
 */
function getInquiryById($id) {
    $inquiries = getAllInquiries();
    foreach ($inquiries as $inquiry) {
        if ($inquiry['id'] === $id) {
            return $inquiry;
        }
    }
    return null;
}

/**
 * Update inquiry status
 */
function updateInquiryStatus($id, $status) {
    $xml = loadXML(DB_INQUIRIES);
    if (!$xml) return false;
    
    foreach ($xml->inquiry as $inquiry) {
        if ((string)$inquiry->id === $id) {
            $inquiry->status = $status;
            if ($status === 'replied') {
                $inquiry->replied_date = date('Y-m-d H:i:s');
            }
            return saveXML($xml, DB_INQUIRIES);
        }
    }
    return false;
}

/**
 * Delete member
 */
function deleteMember($id) {
    $xml = loadXML(DB_MEMBERS);
    if (!$xml) return false;
    
    $index = 0;
    foreach ($xml->member as $member) {
        if ((string)$member->id === $id) {
            unset($xml->member[$index]);
            return saveXML($xml, DB_MEMBERS);
        }
        $index++;
    }
    return false;
}

/**
 * Get all officers
 */
function getAllOfficers() {
    $xml = loadXML(DB_OFFICERS);
    if (!$xml) return [];
    
    $officers = [];
    foreach ($xml->officer as $officer) {
        $officers[] = [
            'id' => (string)$officer->id,
            'name' => (string)$officer->name,
            'position' => (string)$officer->position,
            'category' => (string)$officer->category,
            'order' => (string)$officer->order,
            'photo' => (string)$officer->photo,
            'email' => (string)$officer->email,
            'bio' => (string)$officer->bio,
            'status' => (string)$officer->status,
            'term_start' => (string)$officer->term_start,
            'term_end' => (string)$officer->term_end,
            'created_at' => (string)$officer->created_at
        ];
    }
    
    // Sort by order
    usort($officers, function($a, $b) {
        return (int)$a['order'] - (int)$b['order'];
    });
    
    return $officers;
}

/**
 * Get officer by ID
 */
function getOfficerById($id) {
    $officers = getAllOfficers();
    foreach ($officers as $officer) {
        if ($officer['id'] === $id) {
            return $officer;
        }
    }
    return null;
}

/**
 * Get officers by category
 */
function getOfficersByCategory($category) {
    $officers = getAllOfficers();
    return array_filter($officers, fn($o) => $o['category'] === $category);
}

/**
 * Create officer
 */
function createOfficer($data) {
    $xml = loadXML(DB_OFFICERS);
    if (!$xml) return false;
    
    $officer = $xml->addChild('officer');
    $officer->addChild('id', generateUniqueId('off_'));
    
    foreach ($data as $key => $value) {
        $officer->addChild($key, htmlspecialchars($value));
    }
    
    $officer->addChild('created_at', date('Y-m-d H:i:s'));
    
    return saveXML($xml, DB_OFFICERS) ? (string)$officer->id : false;
}

/**
 * Update officer
 */
function updateOfficer($id, $data) {
    $xml = loadXML(DB_OFFICERS);
    if (!$xml) return false;
    
    foreach ($xml->officer as $officer) {
        if ((string)$officer->id === $id) {
            foreach ($data as $key => $value) {
                if (isset($officer->$key)) {
                    $officer->$key = htmlspecialchars($value);
                } else {
                    $officer->addChild($key, htmlspecialchars($value));
                }
            }
            return saveXML($xml, DB_OFFICERS);
        }
    }
    return false;
}

/**
 * Delete officer
 */
function deleteOfficer($id) {
    $xml = loadXML(DB_OFFICERS);
    if (!$xml) return false;
    
    $index = 0;
    foreach ($xml->officer as $officer) {
        if ((string)$officer->id === $id) {
            unset($xml->officer[$index]);
            return saveXML($xml, DB_OFFICERS);
        }
        $index++;
    }
    return false;
}

/**
 * Create default admin user
 */
function createDefaultAdmin() {
    if (getUserByEmail(DEFAULT_ADMIN_EMAIL)) {
        return; // Admin already exists
    }
    
    createUser(
        DEFAULT_ADMIN_EMAIL,
        DEFAULT_ADMIN_PASSWORD,
        'System Administrator',
        ROLE_ADMIN
    );
}

/**
 * Update user email
 */
function updateUserEmail($userId, $newEmail) {
    $xml = loadXML(DB_USERS);
    if (!$xml) return false;
    
    // Check if email already exists
    $existingUser = getUserByEmail($newEmail);
    if ($existingUser && $existingUser['id'] !== $userId) {
        return false;
    }
    
    foreach ($xml->user as $user) {
        if ((string)$user->id === $userId) {
            $user->email = $newEmail;
            return saveXML($xml, DB_USERS);
        }
    }
    return false;
}

/**
 * Update user name
 */
function updateUserName($userId, $firstName, $lastName) {
    $xml = loadXML(DB_USERS);
    if (!$xml) return false;
    
    foreach ($xml->user as $user) {
        if ((string)$user->id === $userId) {
            $user->name = trim($firstName . ' ' . $lastName);
            return saveXML($xml, DB_USERS);
        }
    }
    return false;
}

/**
 * Update user password
 */
function updateUserPassword($userId, $newPassword) {
    $xml = loadXML(DB_USERS);
    if (!$xml) return false;
    
    foreach ($xml->user as $user) {
        if ((string)$user->id === $userId) {
            $user->password = hashPassword($newPassword);
            return saveXML($xml, DB_USERS);
        }
    }
    return false;
}

/**
 * Get all event registrations
 */
function getAllEventRegistrations() {
    $xml = loadXML(DB_EVENT_REGISTRATIONS);
    if (!$xml) return [];
    
    $registrations = [];
    foreach ($xml->registration as $reg) {
        $registrations[] = [
            'id' => (string)$reg->id,
            'event_id' => (string)$reg->event_id,
            'member_id' => (string)$reg->member_id,
            'user_id' => (string)$reg->user_id,
            'registration_date' => (string)$reg->registration_date,
            'status' => (string)$reg->status,
            'payment_status' => (string)$reg->payment_status,
            'payment_amount' => (string)$reg->payment_amount,
            'payment_proof' => (string)($reg->payment_proof ?? ''),
            'attended' => (string)$reg->attended === 'true',
            'certificate_issued' => (string)$reg->certificate_issued === 'true',
            'notes' => (string)$reg->notes
        ];
    }
    return $registrations;
}

/**
 * Get event registrations by user ID
 */
function getEventRegistrationsByUserId($userId) {
    $registrations = getAllEventRegistrations();
    return array_filter($registrations, fn($r) => $r['user_id'] === $userId);
}

/**
 * Get event registrations by event ID
 */
function getEventRegistrationsByEventId($eventId) {
    $registrations = getAllEventRegistrations();
    return array_filter($registrations, fn($r) => $r['event_id'] === $eventId);
}

/**
 * Check if user is registered for event
 */
function isUserRegisteredForEvent($userId, $eventId) {
    $registrations = getAllEventRegistrations();
    foreach ($registrations as $reg) {
        if ($reg['user_id'] === $userId && $reg['event_id'] === $eventId) {
            return $reg;
        }
    }
    return false;
}

/**
 * Register user for event
 */
function registerForEvent($eventId, $userId, $memberId, $paymentAmount = 0, $paymentProof = '') {
    $xml = loadXML(DB_EVENT_REGISTRATIONS);
    if (!$xml) return false;
    
    // Check if already registered
    if (isUserRegisteredForEvent($userId, $eventId)) {
        return false;
    }
    
    $registration = $xml->addChild('registration');
    $registration->addChild('id', generateUniqueId('reg_'));
    $registration->addChild('event_id', $eventId);
    $registration->addChild('member_id', $memberId);
    $registration->addChild('user_id', $userId);
    $registration->addChild('registration_date', date('Y-m-d H:i:s'));
    $registration->addChild('status', $paymentAmount > 0 ? 'pending' : 'confirmed'); // Pending if payment needs verification
    $registration->addChild('payment_status', $paymentAmount > 0 ? 'pending' : 'free');
    $registration->addChild('payment_amount', $paymentAmount);
    $registration->addChild('payment_proof', $paymentProof);
    $registration->addChild('attended', 'false');
    $registration->addChild('certificate_issued', 'false');
    $registration->addChild('notes', '');
    
    $saved = saveXML($xml, DB_EVENT_REGISTRATIONS);
    if (!$saved) {
        error_log('Failed to save event registration to ' . DB_EVENT_REGISTRATIONS);
        return false;
    }
    return (string)$registration->id;
}

/**
 * Cancel event registration
 */
function cancelEventRegistration($registrationId, $userId) {
    $xml = loadXML(DB_EVENT_REGISTRATIONS);
    if (!$xml) return false;
    
    $index = 0;
    foreach ($xml->registration as $reg) {
        if ((string)$reg->id === $registrationId && (string)$reg->user_id === $userId) {
            // Only allow cancellation if not attended
            if ((string)$reg->attended !== 'true') {
                unset($xml->registration[$index]);
                return saveXML($xml, DB_EVENT_REGISTRATIONS);
            }
            return false;
        }
        $index++;
    }
    return false;
}

/**
 * Update event registration attendance
 */
function updateEventAttendance($registrationId, $attended = true) {
    $xml = loadXML(DB_EVENT_REGISTRATIONS);
    if (!$xml) return false;
    
    foreach ($xml->registration as $reg) {
        if ((string)$reg->id === $registrationId) {
            $reg->attended = $attended ? 'true' : 'false';
            if ($attended) {
                $reg->status = 'completed';
            }
            return saveXML($xml, DB_EVENT_REGISTRATIONS);
        }
    }
    return false;
}

/**
 * Update event registration status
 */
function updateEventRegistrationStatus($id, $status, $paymentStatus = null) {
    $xml = loadXML(DB_EVENT_REGISTRATIONS);
    if (!$xml) return false;
    
    foreach ($xml->registration as $reg) {
        if ((string)$reg->id === $id) {
            $reg->status = $status;
            if ($paymentStatus !== null) {
                $reg->payment_status = $paymentStatus;
            }
            return saveXML($xml, DB_EVENT_REGISTRATIONS);
        }
    }
    return false;
}

/**
 * Issue certificate for event registration
 */
function issueCertificate($registrationId) {
    $xml = loadXML(DB_EVENT_REGISTRATIONS);
    if (!$xml) return false;
    
    foreach ($xml->registration as $reg) {
        if ((string)$reg->id === $registrationId) {
            $reg->certificate_issued = 'true';
            return saveXML($xml, DB_EVENT_REGISTRATIONS);
        }
    }
    return false;
}

/**
 * Add an email to newsletter subscribers
 */
function addNewsletterSubscriber($email) {
    $email = trim(strtolower($email));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;

    $xml = loadXML(DB_NEWSLETTER);
    if (!$xml) return false;

    // Prevent duplicates
    foreach ($xml->subscriber as $sub) {
        if (strtolower((string)$sub->email) === $email) return true;
    }

    $subscriber = $xml->addChild('subscriber');
    $subscriber->addChild('id', generateUniqueId('nl_'));
    $subscriber->addChild('email', $email);
    $subscriber->addChild('subscribed_at', date('Y-m-d H:i:s'));

    $saved = saveXML($xml, DB_NEWSLETTER);
    if (!$saved) {
        error_log('Failed to save newsletter subscriber to ' . DB_NEWSLETTER);
        return false;
    }
    return true;
}

/**
 * Count attended events by user
 */
function countAttendedEvents($userId) {
    $registrations = getEventRegistrationsByUserId($userId);
    return count(array_filter($registrations, fn($r) => $r['attended']));
}
?>
