<?php
require_once '../config.php';
requireLogin();

$user = getCurrentUser();
$member = getMemberByUserId($user['id']);
// Auto-fill email from user record if missing in member record
if (empty($member['email']) && !empty($user['email'])) {
    $member['email'] = $user['email'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_profile' && $member) {
        $data = [
            'id' => $member['id'],
            'first_name' => sanitizeInput($_POST['first_name']),
            'last_name' => sanitizeInput($_POST['last_name']),
            'email' => sanitizeInput($_POST['email']),
            'phone' => sanitizeInput($_POST['phone']),
            'course' => sanitizeInput($_POST['course']),
            'year_level' => sanitizeInput($_POST['year_level']),
            'address' => sanitizeInput($_POST['address']),
            'city' => sanitizeInput($_POST['city']),
            'province' => sanitizeInput($_POST['province']),
            'zip_code' => sanitizeInput($_POST['zip_code'])
        ];
        
        // Handle profile photo upload
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $upload = handleFileUpload($_FILES['profile_photo'], 'profiles');
            if ($upload['success']) {
                // Delete old photo if exists
                if (!empty($member['profile_photo']) && file_exists('../' . $member['profile_photo'])) {
                    unlink('../' . $member['profile_photo']);
                }
                $data['profile_photo'] = $upload['path'];
            }
        } else {
            $data['profile_photo'] = $member['profile_photo'];
        }
        
        if (updateMember($data['id'], $data)) {
            // Update user email if changed
            if ($data['email'] !== $user['email']) {
                updateUserEmail($user['id'], $data['email']);
            }
            
            // Update user name in users table
            updateUserName($user['id'], $data['first_name'], $data['last_name']);
            
            setFlash('Profile updated successfully!', 'success');
            header('Location: profile.php');
            exit;
        } else {
            setFlash('Failed to update profile.', 'error');
        }
    } elseif ($action === 'change_password') {
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];
        
        if (password_verify($currentPassword, $user['password'])) {
            if ($newPassword === $confirmPassword) {
                if (strlen($newPassword) >= 6) {
                    if (updateUserPassword($user['id'], $newPassword)) {
                        setFlash('Password changed successfully!', 'success');
                        header('Location: profile.php');
                        exit;
                    } else {
                        setFlash('Failed to change password.', 'error');
                    }
                } else {
                    setFlash('Password must be at least 6 characters.', 'error');
                }
            } else {
                setFlash('New passwords do not match.', 'error');
            }
        } else {
            setFlash('Current password is incorrect.', 'error');
        }
    }
}

// Reload member data
$member = getMemberByUserId($user['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/member.css">
    <link rel="stylesheet" href="../css/profile.css">
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
                            <i data-lucide="user"></i>
                            My Profile
                        </h1>
                        <p>Manage your personal information and account settings</p>
                    </div>
                </div>
            </div>
            
            <div class="content-inner">
            
            <?php displayFlash(); ?>
            
            <?php if (!$member): ?>
            <div class="section">
                <div class="section-body">
                    <div class="alert alert-warning">
                        <i data-lucide="alert-circle"></i>
                        <div>
                            <p><strong>Complete Your Membership</strong></p>
                            <p>You haven't completed your membership registration yet.</p>
                            <a href="../pages/registration.php" class="btn" style="margin-top: 10px;">
                                <i data-lucide="user-plus"></i> Complete Registration
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php else: ?>

            <!-- Dashboard-style profile hero -->
            <div class="profile-hero">
                <div class="profile-hero-content">
                    <div class="profile-avatar">
                        <?php if (!empty($member['profile_photo'])): ?>
                            <img src="../<?php echo htmlspecialchars($member['profile_photo'] ?? ''); ?>" alt="Profile">
                        <?php else: ?>
                            <i data-lucide="user"></i>
                        <?php endif; ?>
                    </div>
                    <div class="profile-details">
                        <h2><?php echo htmlspecialchars(($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? '')); ?></h2>
                        <div class="profile-meta">
                            <div class="profile-meta-item">
                                <i data-lucide="id-card"></i>
                                <span><?php echo htmlspecialchars($member['member_id'] ?? ''); ?></span>
                            </div>
                            <div class="profile-meta-item">
                                <i data-lucide="graduation-cap"></i>
                                <span><?php echo htmlspecialchars($member['course'] ?? ''); ?> - Year <?php echo htmlspecialchars($member['year_level'] ?? ''); ?></span>
                            </div>
                            <div class="profile-meta-item">
                                <i data-lucide="mail"></i>
                                <span><?php echo htmlspecialchars($member['email'] ?? ''); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="membership-badge">
                        <h4>Membership Status</h4>
                        <div class="status"><?php echo strtoupper($member['membership_status'] ?? ''); ?></div>
                        <div style="margin-top: 10px; font-size: 0.85rem; opacity: 0.9;">Expires: <?php echo formatDate($member['expiry_date'] ?? ''); ?></div>
                    </div>
                </div>
            </div>

            <?php endif; ?>

            
            <div class="section">
                <div class="section-header">
                    <h2>Personal Information</h2>
                </div>
                <div class="section-body">
                    <form method="POST" enctype="multipart/form-data" class="profile-form">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="profile-photo-section">
                            <?php if (!empty($member['profile_photo'])): ?>
                                <img src="../<?php echo htmlspecialchars($member['profile_photo'] ?? ''); ?>" 
                                     alt="Profile Photo" 
                                     class="profile-photo-preview">
                            <?php else: ?>
                                <div class="profile-photo-placeholder">
                                    <i data-lucide="user"></i>
                                </div>
                            <?php endif; ?>
                            <div class="form-group">
                                <label>Change Profile Photo</label>
                                <input type="file" name="profile_photo" accept="image/*" class="form-control">
                                <small class="form-text">Max size: 5MB. Formats: JPG, PNG, GIF</small>
                            </div>
                        </div>
                    
                        <div class="form-row form-row-2">
                            <div class="form-group">
                                <label>First Name *</label>
                                <input type="text" name="first_name" class="form-control" 
                                       value="<?php echo htmlspecialchars($member['first_name'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Last Name *</label>
                                <input type="text" name="last_name" class="form-control" 
                                       value="<?php echo htmlspecialchars($member['last_name'] ?? ''); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-row form-row-2">
                            <div class="form-group">
                                <label>Email *</label>
                                <input type="email" name="email" class="form-control" 
                                       value="<?php echo htmlspecialchars($member['email'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Phone *</label>
                                <input type="tel" name="phone" class="form-control" 
                                       value="<?php echo htmlspecialchars($member['phone'] ?? ''); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group" style="flex: 2;">
                                <label>Course *</label>
                                <input type="text" name="course" class="form-control" 
                                       value="<?php echo htmlspecialchars($member['course'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="form-group" style="flex: 1;">
                                <label>Year Level *</label>
                                <select name="year_level" class="form-control" required>
                                    <option value="1" <?php echo ($member['year_level'] ?? '') == '1' ? 'selected' : ''; ?>>1st Year</option>
                                    <option value="2" <?php echo ($member['year_level'] ?? '') == '2' ? 'selected' : ''; ?>>2nd Year</option>
                                    <option value="3" <?php echo ($member['year_level'] ?? '') == '3' ? 'selected' : ''; ?>>3rd Year</option>
                                    <option value="4" <?php echo ($member['year_level'] ?? '') == '4' ? 'selected' : ''; ?>>4th Year</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Address *</label>
                            <input type="text" name="address" class="form-control" 
                                   value="<?php echo htmlspecialchars($member['address'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group" style="flex: 2;">
                                <label>City *</label>
                                <input type="text" name="city" class="form-control" 
                                       value="<?php echo htmlspecialchars($member['city'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="form-group" style="flex: 2;">
                                <label>Province *</label>
                                <input type="text" name="province" class="form-control" 
                                       value="<?php echo htmlspecialchars($member['province'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="form-group" style="flex: 1;">
                                <label>Zip Code *</label>
                                <input type="text" name="zip_code" class="form-control" 
                                       value="<?php echo htmlspecialchars($member['zip_code'] ?? ''); ?>" required>
                            </div>
                        </div>
                    
                        <div class="member-info-box">
                            <p><strong>Member ID:</strong> <?php echo htmlspecialchars($member['member_id'] ?? ''); ?></p>
                            <p><strong>Membership Status:</strong> 
                                <?php 
                                $status = $member['membership_status'] ?? '';
                                $badgeClass = $status === 'active' ? 'badge-active' : ($status === 'pending' ? 'badge-pending' : 'badge-expired');
                                echo '<span class="badge ' . $badgeClass . '">' . strtoupper($status ?? '') . '</span>';
                                ?>
                            </p>
                            <p><strong>Joined:</strong> <?php echo formatDate($member['joined_date'] ?? ''); ?></p>
                            <p><strong>Expires:</strong> <?php echo formatDate($member['expiry_date'] ?? ''); ?></p>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="save"></i> Update Profile
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="section">
                <div class="section-header">
                    <h2>Change Password</h2>
                </div>
                <div class="section-body">
                    <form method="POST" class="password-form">
                        <input type="hidden" name="action" value="change_password">
                        
                        <div class="form-group">
                            <label>Current Password *</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label>New Password *</label>
                            <input type="password" name="new_password" class="form-control" required minlength="6">
                            <small class="form-text">Must be at least 6 characters</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Confirm New Password *</label>
                            <input type="password" name="confirm_password" class="form-control" required minlength="6">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="key"></i> Change Password
                        </button>
                    </form>
                </div>
            </div>
            
            </div><!-- .content-inner -->
        </main>
    </div>
    
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
