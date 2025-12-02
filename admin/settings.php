<?php
require_once '../config.php';
requireAdmin();

$user = getCurrentUser();

// Handle settings updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_profile':
                $data = [
                    'name' => sanitize($_POST['name']),
                    'email' => sanitize($_POST['email'])
                ];
                
                if (!empty($_POST['new_password'])) {
                    $data['password'] = $_POST['new_password'];
                }
                
                if (updateUser($user['id'], $data)) {
                    setFlash('Profile updated successfully!', 'success');
                } else {
                    setFlash('Failed to update profile.', 'error');
                }
                redirect('settings.php');
                break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/topbar.php'; ?>
        
        <div class="content-wrapper">
            <?php displayFlash(); ?>
            
            <div class="page-header">
                <h1>Admin Settings</h1>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <h3 style="margin-bottom: 20px;">Profile Settings</h3>
                    <form method="POST" class="form-horizontal">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="form-group">
                            <label>Full Name *</label>
                            <input type="text" name="name" required 
                                   value="<?php echo htmlspecialchars($user['name']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="email" required 
                                   value="<?php echo htmlspecialchars($user['email']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>New Password (Leave blank to keep current)</label>
                            <input type="password" name="new_password" 
                                   placeholder="Enter new password">
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <h3 style="margin-bottom: 20px;">System Information</h3>
                    <table style="width: 100%; max-width: 600px;">
                        <tr>
                            <td style="padding: 10px; font-weight: 600;">Site Name:</td>
                            <td style="padding: 10px;"><?php echo SITE_NAME; ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; font-weight: 600;">Database Type:</td>
                            <td style="padding: 10px;">XML Database</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; font-weight: 600;">PHP Version:</td>
                            <td style="padding: 10px;"><?php echo phpversion(); ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; font-weight: 600;">Server:</td>
                            <td style="padding: 10px;"><?php echo $_SERVER['SERVER_SOFTWARE']; ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; font-weight: 600;">Total Members:</td>
                            <td style="padding: 10px;"><?php echo count(getAllMembers()); ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; font-weight: 600;">Total Events:</td>
                            <td style="padding: 10px;"><?php echo count(getAllEvents()); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <h3 style="margin-bottom: 20px; color: #e74c3c;">Danger Zone</h3>
                    <p style="margin-bottom: 15px; color: #7f8c8d;">
                        These actions cannot be undone. Please be careful.
                    </p>
                    <button class="btn btn-danger" onclick="alert('This feature is not yet implemented');">
                        Clear All Session Data
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
