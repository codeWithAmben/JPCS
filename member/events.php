<?php
require_once '../config.php';
requireLogin();

$user = getCurrentUser();
$member = getMemberByUserId($user['id']);

// Get all events
$allEvents = getAllEvents();
$myRegistrations = getEventRegistrationsByUserId($user['id']);

// Get registered event IDs
$registeredEventIds = array_column($myRegistrations, 'event_id');

// Filter events
$filter = $_GET['filter'] ?? 'upcoming';
$search = $_GET['search'] ?? '';

$filteredEvents = array_filter($allEvents, function($event) use ($filter, $search, $registeredEventIds) {
    // Search filter
    if ($search && stripos($event['title'], $search) === false && stripos($event['description'], $search) === false) {
        return false;
    }
    
    $eventDate = strtotime($event['date']);
    $now = time();
    
    switch ($filter) {
        case 'upcoming':
            return $eventDate >= $now && $event['status'] === 'active';
        case 'past':
            return $eventDate < $now;
        case 'registered':
            return in_array($event['id'], $registeredEventIds);
        case 'all':
        default:
            return $event['status'] === 'active';
    }
});

// Sort by date
usort($filteredEvents, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/member.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <div class="dashboard">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="top-bar">
                <div class="welcome-section">
                    <div class="welcome-text">
                        <h1>
                            <i data-lucide="calendar"></i>
                            Events
                        </h1>
                        <p>Browse and register for upcoming JPCS events</p>
                    </div>
                    <div class="user-actions">
                        <a href="../handlers/logout.php" class="btn-logout">
                            <i data-lucide="log-out"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
            
            <?php displayFlash(); ?>
            
            <!-- Stats -->
            <div class="stats-grid" style="margin-bottom: 30px;">
                <div class="stat-card blue">
                    <h3>Total Events</h3>
                    <div class="value"><?php echo count($allEvents); ?></div>
                </div>
                
                <div class="stat-card green">
                    <h3>Registered Events</h3>
                    <div class="value"><?php echo count($myRegistrations); ?></div>
                </div>
                
                <div class="stat-card orange">
                    <h3>Attended Events</h3>
                    <div class="value"><?php echo countAttendedEvents($user['id']); ?></div>
                </div>
                
                <div class="stat-card purple">
                    <h3>Upcoming Events</h3>
                    <div class="value"><?php echo count(array_filter($allEvents, fn($e) => strtotime($e['date']) >= time() && $e['status'] === 'active')); ?></div>
                </div>
            </div>
            
            <!-- Filters and Search -->
            <div class="filters-section">
                <form method="GET" class="filters-form">
                    <div style="flex: 1; min-width: 250px;">
                        <input type="text" name="search" class="form-control" placeholder="Search events..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    
                    <div>
                        <select name="filter" class="form-control" onchange="this.form.submit()">
                            <option value="upcoming" <?php echo $filter === 'upcoming' ? 'selected' : ''; ?>>Upcoming Events</option>
                            <option value="past" <?php echo $filter === 'past' ? 'selected' : ''; ?>>Past Events</option>
                            <option value="registered" <?php echo $filter === 'registered' ? 'selected' : ''; ?>>My Registered Events</option>
                            <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All Events</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="search"></i> Search
                    </button>
                    
                    <?php if ($search || $filter !== 'upcoming'): ?>
                    <a href="events.php" class="btn" style="background: #95a5a6;">
                        <i data-lucide="x"></i> Clear
                    </a>
                    <?php endif; ?>
                </form>
            </div>
            
            <!-- Events List -->
            <div class="section">
                <div class="section-header">
                    <h2>
                        <?php
                        echo match($filter) {
                            'upcoming' => 'Upcoming Events',
                            'past' => 'Past Events',
                            'registered' => 'My Registered Events',
                            'all' => 'All Events',
                            default => 'Events'
                        };
                        ?>
                        <span style="color: #95a5a6; font-size: 1rem; font-weight: normal;">
                            (<?php echo count($filteredEvents); ?> events)
                        </span>
                    </h2>
                </div>
                
                <?php if (empty($filteredEvents)): ?>
                    <p style="text-align: center; color: #95a5a6; padding: 40px;">
                        <i data-lucide="calendar-x" style="width: 64px; height: 64px; margin-bottom: 15px;"></i><br>
                        No events found.
                    </p>
                <?php else: ?>
                    <div style="display: grid; gap: 20px;">
                        <?php foreach ($filteredEvents as $event): 
                            $isRegistered = isUserRegisteredForEvent($user['id'], $event['id']);
                            $eventDate = strtotime($event['date']);
                            $isPast = $eventDate < time();
                            $registrations = getEventRegistrationsByEventId($event['id']);
                            $registrationCount = count($registrations);
                            $isFull = $event['max_participants'] > 0 && $registrationCount >= $event['max_participants'];
                        ?>
                        <div class="event-card" style="background: white; padding: 25px; border-radius: 10px; border-left: 4px solid #ff6a00; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                            <div style="display: grid; grid-template-columns: 1fr auto; gap: 20px;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                                        <h3 style="color: #2c3e50; margin: 0;"><?php echo htmlspecialchars($event['title']); ?></h3>
                                        <?php if ($isRegistered): ?>
                                            <span class="badge badge-active">
                                                <i data-lucide="check" style="width: 12px; height: 12px;"></i> Registered
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($isPast): ?>
                                            <span class="badge" style="background: #95a5a6;">Past Event</span>
                                        <?php endif; ?>
                                        <?php if ($isFull && !$isRegistered): ?>
                                            <span class="badge badge-expired">Full</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div style="display: flex; gap: 20px; margin-bottom: 15px; color: #7f8c8d; font-size: 0.9rem;">
                                        <div style="display: flex; align-items: center; gap: 5px;">
                                            <i data-lucide="calendar" style="width: 16px; height: 16px;"></i>
                                            <?php echo formatDate($event['date']); ?>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 5px;">
                                            <i data-lucide="clock" style="width: 16px; height: 16px;"></i>
                                            <?php echo formatTime($event['time']); ?>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 5px;">
                                            <i data-lucide="map-pin" style="width: 16px; height: 16px;"></i>
                                            <?php echo htmlspecialchars($event['location']); ?>
                                        </div>
                                    </div>
                                    
                                    <p style="color: #7f8c8d; margin-bottom: 15px;">
                                        <?php echo htmlspecialchars(substr($event['description'], 0, 200)) . (strlen($event['description']) > 200 ? '...' : ''); ?>
                                    </p>
                                    
                                    <div style="display: flex; gap: 15px; align-items: center; font-size: 0.85rem; color: #7f8c8d;">
                                        <?php if (!empty($event['category'])): ?>
                                        <div>
                                            <strong>Category:</strong> <?php echo htmlspecialchars($event['category']); ?>
                                        </div>
                                        <?php endif; ?>
                                        <?php if ($event['max_participants'] > 0): ?>
                                        <div>
                                            <strong>Capacity:</strong> 
                                            <?php echo $registrationCount; ?> / <?php echo $event['max_participants']; ?>
                                        </div>
                                        <?php endif; ?>
                                        <?php if ($event['registration_fee'] > 0): ?>
                                        <div>
                                            <strong>Fee:</strong> ₱<?php echo number_format($event['registration_fee'], 2); ?>
                                        </div>
                                        <?php else: ?>
                                        <div>
                                            <strong>FREE</strong>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div style="display: flex; flex-direction: column; gap: 10px; align-items: flex-end;">
                                    <?php if (!$isPast): ?>
                                        <?php if ($isRegistered): ?>
                                            <form method="POST" action="../handlers/event_registration.php" onsubmit="return confirm('Are you sure you want to cancel your registration?');">
                                                <input type="hidden" name="action" value="cancel">
                                                <input type="hidden" name="registration_id" value="<?php echo $isRegistered['id']; ?>">
                                                <input type="hidden" name="redirect" value="events.php">
                                                <button type="submit" class="btn" style="background: #e74c3c;">
                                                    <i data-lucide="x-circle"></i> Cancel Registration
                                                </button>
                                            </form>
                                            <?php if ($isRegistered['certificate_issued']): ?>
                                            <a href="../handlers/download_certificate.php?id=<?php echo $isRegistered['id']; ?>" class="btn" style="background: #27ae60;">
                                                <i data-lucide="download"></i> Download Certificate
                                            </a>
                                            <?php endif; ?>
                                        <?php elseif ($isFull): ?>
                                            <button class="btn" disabled style="background: #95a5a6; cursor: not-allowed;">
                                                <i data-lucide="users"></i> Event Full
                                            </button>
                                        <?php elseif ($member): ?>
                                            <form method="POST" action="../handlers/event_registration.php">
                                                <input type="hidden" name="action" value="register">
                                                <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                                <input type="hidden" name="redirect" value="events.php">
                                                <button type="submit" class="btn btn-primary">
                                                    <i data-lucide="user-plus"></i> Register Now
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <a href="../pages/registration.php" class="btn btn-primary">
                                                <i data-lucide="user-plus"></i> Complete Membership First
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    
                                    <?php if ($isRegistered): ?>
                                    <div style="text-align: right; font-size: 0.85rem; color: #7f8c8d;">
                                        <div><strong>Status:</strong> 
                                            <span class="badge <?php echo $isRegistered['attended'] ? 'badge-active' : 'badge-pending'; ?>">
                                                <?php echo $isRegistered['attended'] ? 'Attended' : 'Registered'; ?>
                                            </span>
                                        </div>
                                        <?php if ($isRegistered['payment_status'] !== 'free'): ?>
                                        <div style="margin-top: 5px;"><strong>Payment:</strong> 
                                            <span class="badge <?php echo $isRegistered['payment_status'] === 'paid' ? 'badge-active' : 'badge-pending'; ?>">
                                                <?php echo ucfirst($isRegistered['payment_status']); ?>
                                            </span>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
    
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
