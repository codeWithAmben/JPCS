<?php
require_once '../config.php';
requireAdmin();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $regId = $_POST['id'] ?? '';
    
    if ($_POST['action'] === 'approve_payment') {
        if (updateEventRegistrationStatus($regId, 'confirmed', 'paid')) {
            setFlash('Payment approved and registration confirmed.', 'success');
        } else {
            setFlash('Failed to update registration.', 'error');
        }
    } elseif ($_POST['action'] === 'reject_payment') {
        if (updateEventRegistrationStatus($regId, 'cancelled', 'rejected')) {
            setFlash('Registration rejected.', 'success');
        } else {
            setFlash('Failed to update registration.', 'error');
        }
    } elseif ($_POST['action'] === 'mark_attended') {
        if (updateEventAttendance($regId, true)) {
            setFlash('Marked as attended.', 'success');
        } else {
            setFlash('Failed to update attendance.', 'error');
        }
    }
    redirect('event_registrations.php');
}

// Get data
$registrations = getAllEventRegistrations();
$events = getAllEvents();
$users = getAllUsers();

// Create lookup maps for performance
$eventsMap = [];
foreach ($events as $e) $eventsMap[$e['id']] = $e;

$usersMap = [];
foreach ($users as $u) $usersMap[$u['id']] = $u;

// Filter by event if selected
$filterEventId = $_GET['event_id'] ?? '';
if ($filterEventId) {
    $registrations = array_filter($registrations, fn($r) => $r['event_id'] === $filterEventId);
}

// Sort by date descending
usort($registrations, function($a, $b) {
    return strtotime($b['registration_date']) - strtotime($a['registration_date']);
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Registrations - Admin</title>
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
                <h1>Event Registrations</h1>
                
                <form method="GET" style="display: flex; gap: 10px;">
                    <select name="event_id" class="form-control" onchange="this.form.submit()" style="min-width: 200px;">
                        <option value="">All Events</option>
                        <?php foreach ($events as $e): ?>
                            <option value="<?php echo $e['id']; ?>" <?php echo $filterEventId === $e['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($e['title']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Event</th>
                                    <th>Participant</th>
                                    <th>Date</th>
                                    <th>Fee</th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($registrations)): ?>
                                    <?php foreach ($registrations as $reg): 
                                        $event = $eventsMap[$reg['event_id']] ?? null;
                                        $user = $usersMap[$reg['user_id']] ?? null;
                                        $userName = $user ? $user['name'] : 'Unknown User';
                                        $eventTitle = $event ? $event['title'] : 'Unknown Event';
                                    ?>
                                    <tr>
                                        <td data-label="Event"><strong><?php echo htmlspecialchars($eventTitle); ?></strong></td>
                                        <td data-label="Participant">
                                            <?php echo htmlspecialchars($userName); ?>
                                            <div style="font-size: 0.8rem; color: #7f8c8d;"><?php echo htmlspecialchars($user['email'] ?? ''); ?></div>
                                        </td>
                                        <td data-label="Date"><?php echo formatDate($reg['registration_date']); ?></td>
                                        <td data-label="Fee">₱<?php echo number_format((float)$reg['payment_amount'], 2); ?></td>
                                        <td data-label="Payment">
                                            <?php 
                                            $pStatus = $reg['payment_status'];
                                            $pClass = $pStatus === 'paid' ? 'badge-success' : 
                                                     ($pStatus === 'pending' ? 'badge-warning' : 'badge-secondary');
                                            echo '<span class="badge ' . $pClass . '">' . ucfirst($pStatus) . '</span>';
                                            
                                            if (!empty($reg['payment_proof'])) {
                                                echo '<div style="margin-top: 5px;"><a href="../' . htmlspecialchars($reg['payment_proof']) . '" target="_blank" style="color: var(--primary); font-size: 0.85rem;">View Proof</a></div>';
                                            }
                                            ?>
                                        </td>
                                        <td data-label="Status">
                                            <?php 
                                            $status = $reg['status'];
                                            $sClass = $status === 'confirmed' ? 'badge-success' : 
                                                     ($status === 'pending' ? 'badge-warning' : 
                                                     ($status === 'completed' ? 'badge-info' : 'badge-danger'));
                                            echo '<span class="badge ' . $sClass . '">' . ucfirst($status) . '</span>';
                                            
                                            if ($reg['attended']) {
                                                echo '<span class="badge badge-success" style="margin-left: 5px;">Attended</span>';
                                            }
                                            ?>
                                        </td>
                                        <td class="actions">
                                            <?php if ($reg['payment_status'] === 'pending' && (float)$reg['payment_amount'] > 0): ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="action" value="approve_payment">
                                                    <input type="hidden" name="id" value="<?php echo $reg['id']; ?>">
                                                    <button type="submit" class="btn-sm" style="background: #27ae60; color: white; border: none; cursor: pointer;">Approve</button>
                                                </form>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="action" value="reject_payment">
                                                    <input type="hidden" name="id" value="<?php echo $reg['id']; ?>">
                                                    <button type="submit" class="btn-sm btn-danger">Reject</button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <?php if ($reg['status'] === 'confirmed' && !$reg['attended']): ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="action" value="mark_attended">
                                                    <input type="hidden" name="id" value="<?php echo $reg['id']; ?>">
                                                    <button type="submit" class="btn-sm btn-info">Mark Attended</button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 40px;">No registrations found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>