<?php
require_once '../config.php';
requireLogin();

$user = getCurrentUser();
$myRegistrations = getEventRegistrationsByUserId($user['id']);
$events = getAllEvents();

// Create a lookup map for events for better performance
$eventsMap = [];
foreach ($events as $event) {
    $eventsMap[$event['id']] = $event;
}

// Sort registrations by event date descending
usort($myRegistrations, function($a, $b) use ($eventsMap) {
    $dateA = isset($eventsMap[$a['event_id']]) ? strtotime($eventsMap[$a['event_id']]['date']) : 0;
    $dateB = isset($eventsMap[$b['event_id']]) ? strtotime($eventsMap[$b['event_id']]['date']) : 0;
    return $dateB - $dateA;
});

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Event Registrations - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css"> <!-- Reusing admin table styles -->
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
                            <i data-lucide="ticket"></i>
                            My Event Registrations
                        </h1>
                        <p>Here are all the events you've registered for.</p>
                    </div>
                </div>
            </div>
            
            <?php displayFlash(); ?>
            
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Event Title</th>
                                    <th>Event Date</th>
                                    <th>Registration Date</th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($myRegistrations)): ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 40px;">
                                            You have not registered for any events yet.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($myRegistrations as $reg): 
                                        $event = $eventsMap[$reg['event_id']] ?? null;
                                        if (!$event) continue; // Skip if event data is missing
                                        $isPast = strtotime($event['date']) < time();
                                    ?>
                                    <tr>
                                        <td data-label="Event Title">
                                            <strong><?php echo htmlspecialchars($event['title']); ?></strong>
                                        </td>
                                        <td data-label="Event Date"><?php echo formatDate($event['date']); ?></td>
                                        <td data-label="Registration Date"><?php echo formatDate($reg['registration_date']); ?></td>
                                        <td data-label="Payment">
                                            <?php 
                                            $pStatus = $reg['payment_status'];
                                            $pClass = $pStatus === 'paid' ? 'badge-success' : 
                                                     ($pStatus === 'pending' ? 'badge-warning' : 'badge-secondary');
                                            echo '<span class="badge ' . $pClass . '">' . ucfirst($pStatus) . '</span>';
                                            ?>
                                        </td>
                                        <td data-label="Status">
                                            <?php 
                                            $status = $reg['status'];
                                            $sClass = $status === 'confirmed' ? 'badge-success' : 
                                                     ($status === 'pending' ? 'badge-warning' : 
                                                     ($status === 'completed' ? 'badge-info' : 'badge-danger'));
                                            echo '<span class="badge ' . $sClass . '">' . ucfirst($status) . '</span>';
                                            ?>
                                        </td>
                                        <td class="actions">
                                            <?php if (!$isPast && $reg['status'] !== 'cancelled'): ?>
                                                <form method="POST" action="../handlers/event_registration.php" onsubmit="return confirm('Are you sure you want to cancel your registration?');">
                                                    <input type="hidden" name="action" value="cancel">
                                                    <input type="hidden" name="registration_id" value="<?php echo $reg['id']; ?>">
                                                    <input type="hidden" name="redirect" value="<?php echo rtrim(SITE_URL, '/'); ?>/member/my_registrations.php">
                                                    <button type="submit" class="btn-sm btn-danger">Cancel</button>
                                                </form>
                                            <?php else: ?>
                                                <span style="color: #95a5a6;">—</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        lucide.createIcons();
    </script>
</body>
</html>