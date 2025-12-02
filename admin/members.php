<?php
require_once '../config.php';
requireAdmin();

$action = $_GET['action'] ?? 'list';
$memberId = $_GET['id'] ?? null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $data = [
                    'first_name' => sanitize($_POST['first_name']),
                    'middle_name' => sanitize($_POST['middle_name']),
                    'last_name' => sanitize($_POST['last_name']),
                    'email' => sanitize($_POST['email']),
                    'phone' => sanitize($_POST['phone']),
                    'address' => sanitize($_POST['address']),
                    'school' => sanitize($_POST['school']),
                    'course' => sanitize($_POST['course']),
                    'year_level' => sanitize($_POST['year_level']),
                    'student_id' => sanitize($_POST['student_id']),
                    'membership_status' => sanitize($_POST['membership_status']),
                    'gender' => sanitize($_POST['gender']),
                    'birthdate' => sanitize($_POST['birthdate'])
                ];
                
                if (createMember($data)) {
                    setFlash('Member added successfully!', 'success');
                } else {
                    setFlash('Failed to add member.', 'error');
                }
                redirect('members.php');
                break;
                
            case 'edit':
                $data = [
                    'first_name' => sanitize($_POST['first_name']),
                    'middle_name' => sanitize($_POST['middle_name']),
                    'last_name' => sanitize($_POST['last_name']),
                    'email' => sanitize($_POST['email']),
                    'phone' => sanitize($_POST['phone']),
                    'address' => sanitize($_POST['address']),
                    'school' => sanitize($_POST['school']),
                    'course' => sanitize($_POST['course']),
                    'year_level' => sanitize($_POST['year_level']),
                    'student_id' => sanitize($_POST['student_id']),
                    'membership_status' => sanitize($_POST['membership_status']),
                    'gender' => sanitize($_POST['gender']),
                    'birthdate' => sanitize($_POST['birthdate'])
                ];
                
                if (updateMember($_POST['id'], $data)) {
                    setFlash('Member updated successfully!', 'success');
                } else {
                    setFlash('Failed to update member.', 'error');
                }
                redirect('members.php');
                break;
                
            case 'delete':
                if (deleteMember($_POST['id'])) {
                    setFlash('Member deleted successfully!', 'success');
                } else {
                    setFlash('Failed to delete member.', 'error');
                }
                redirect('members.php');
                break;
        }
    }
}

$members = getAllMembers();
$currentMember = null;
if ($action === 'edit' && $memberId) {
    $currentMember = getMemberById($memberId);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Members - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/topbar.php'; ?>
        
        <div class="content-wrapper">
            <?php displayFlash(); ?>
            
            <?php if ($action === 'list'): ?>
            <!-- List View -->
            <div class="page-header">
                <h1>Manage Members</h1>
                <a href="?action=add" class="btn btn-primary">
                    <span>➕</span> Add New Member
                </a>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Member ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>School</th>
                                    <th>Course</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($members)): ?>
                                    <?php foreach ($members as $member): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($member['member_id']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($member['email']); ?></td>
                                        <td><?php echo htmlspecialchars($member['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($member['school']); ?></td>
                                        <td><?php echo htmlspecialchars($member['course']); ?></td>
                                        <td>
                                            <?php 
                                            $status = $member['membership_status'];
                                            $badgeClass = $status === 'active' ? 'badge-success' : 
                                                        ($status === 'pending' ? 'badge-warning' : 'badge-danger');
                                            echo '<span class="badge ' . $badgeClass . '">' . strtoupper($status) . '</span>';
                                            ?>
                                        </td>
                                        <td><?php echo formatDate($member['joined_date']); ?></td>
                                        <td class="actions">
                                            <a href="?action=edit&id=<?php echo $member['id']; ?>" class="btn-sm btn-info">Edit</a>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this member?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $member['id']; ?>">
                                                <button type="submit" class="btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" style="text-align: center; padding: 40px;">No members found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <?php elseif ($action === 'add' || $action === 'edit'): ?>
            <!-- Add/Edit Form -->
            <div class="page-header">
                <h1><?php echo $action === 'add' ? 'Add New Member' : 'Edit Member'; ?></h1>
                <a href="members.php" class="btn btn-secondary">
                    <span>←</span> Back to List
                </a>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <form method="POST" class="form-horizontal">
                        <input type="hidden" name="action" value="<?php echo $action; ?>">
                        <?php if ($action === 'edit'): ?>
                            <input type="hidden" name="id" value="<?php echo $currentMember['id']; ?>">
                        <?php endif; ?>
                        
                        <h3>Personal Information</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label>First Name *</label>
                                <input type="text" name="first_name" required 
                                       value="<?php echo $currentMember['first_name'] ?? ''; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label>Middle Name</label>
                                <input type="text" name="middle_name" 
                                       value="<?php echo $currentMember['middle_name'] ?? ''; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label>Last Name *</label>
                                <input type="text" name="last_name" required 
                                       value="<?php echo $currentMember['last_name'] ?? ''; ?>">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Gender *</label>
                                <select name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male" <?php echo ($currentMember['gender'] ?? '') === 'Male' ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?php echo ($currentMember['gender'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
                                    <option value="Other" <?php echo ($currentMember['gender'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Birthdate *</label>
                                <input type="date" name="birthdate" required 
                                       value="<?php echo $currentMember['birthdate'] ?? ''; ?>">
                            </div>
                        </div>
                        
                        <h3>Contact Information</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Email *</label>
                                <input type="email" name="email" required 
                                       value="<?php echo $currentMember['email'] ?? ''; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label>Phone *</label>
                                <input type="text" name="phone" required 
                                       value="<?php echo $currentMember['phone'] ?? ''; ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Address *</label>
                            <textarea name="address" rows="3" required><?php echo $currentMember['address'] ?? ''; ?></textarea>
                        </div>
                        
                        <h3>Academic Information</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label>School *</label>
                                <input type="text" name="school" required 
                                       value="<?php echo $currentMember['school'] ?? ''; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label>Student ID *</label>
                                <input type="text" name="student_id" required 
                                       value="<?php echo $currentMember['student_id'] ?? ''; ?>">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Course *</label>
                                <input type="text" name="course" required 
                                       value="<?php echo $currentMember['course'] ?? ''; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label>Year Level *</label>
                                <select name="year_level" required>
                                    <option value="">Select Year</option>
                                    <option value="1st Year" <?php echo ($currentMember['year_level'] ?? '') === '1st Year' ? 'selected' : ''; ?>>1st Year</option>
                                    <option value="2nd Year" <?php echo ($currentMember['year_level'] ?? '') === '2nd Year' ? 'selected' : ''; ?>>2nd Year</option>
                                    <option value="3rd Year" <?php echo ($currentMember['year_level'] ?? '') === '3rd Year' ? 'selected' : ''; ?>>3rd Year</option>
                                    <option value="4th Year" <?php echo ($currentMember['year_level'] ?? '') === '4th Year' ? 'selected' : ''; ?>>4th Year</option>
                                </select>
                            </div>
                        </div>
                        
                        <h3>Membership Status</h3>
                        <div class="form-group">
                            <label>Status *</label>
                            <select name="membership_status" required>
                                <option value="pending" <?php echo ($currentMember['membership_status'] ?? '') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="active" <?php echo ($currentMember['membership_status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="expired" <?php echo ($currentMember['membership_status'] ?? '') === 'expired' ? 'selected' : ''; ?>>Expired</option>
                                <option value="suspended" <?php echo ($currentMember['membership_status'] ?? '') === 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                            </select>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <?php echo $action === 'add' ? 'Add Member' : 'Update Member'; ?>
                            </button>
                            <a href="members.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
