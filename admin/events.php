<?php
require_once '../config.php';
requireAdmin();

$action = $_GET['action'] ?? 'list';
$eventId = $_GET['id'] ?? null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $data = [
                    'title' => sanitize($_POST['title']),
                    'date' => sanitize($_POST['date']),
                    'time' => sanitize($_POST['time']),
                    'location' => sanitize($_POST['location']),
                    'description' => sanitize($_POST['description']),
                    'max_participants' => sanitize($_POST['max_participants']),
                    'registration_deadline' => sanitize($_POST['registration_deadline']),
                    'status' => sanitize($_POST['status'])
                ];
                
                if (createEvent($data)) {
                    setFlash('Event created successfully!', 'success');
                } else {
                    setFlash('Failed to create event.', 'error');
                }
                redirect('events.php');
                break;
                
            case 'edit':
                $data = [
                    'title' => sanitize($_POST['title']),
                    'date' => sanitize($_POST['date']),
                    'time' => sanitize($_POST['time']),
                    'location' => sanitize($_POST['location']),
                    'description' => sanitize($_POST['description']),
                    'max_participants' => sanitize($_POST['max_participants']),
                    'registration_deadline' => sanitize($_POST['registration_deadline']),
                    'status' => sanitize($_POST['status'])
                ];
                
                if (updateEvent($_POST['id'], $data)) {
                    setFlash('Event updated successfully!', 'success');
                } else {
                    setFlash('Failed to update event.', 'error');
                }
                redirect('events.php');
                break;
                
            case 'delete':
                if (deleteEvent($_POST['id'])) {
                    setFlash('Event deleted successfully!', 'success');
                } else {
                    setFlash('Failed to delete event.', 'error');
                }
                redirect('events.php');
                break;
        }
    }
}

$events = getAllEvents();
$currentEvent = null;
if ($action === 'edit' && $eventId) {
    $currentEvent = getEventById($eventId);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events - Admin</title>
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
                <h1>Manage Events</h1>
                <a href="?action=add" class="btn btn-primary">
                    <span>➕</span> Create New Event
                </a>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Location</th>
                                    <th>Max Participants</th>
                                    <th>Deadline</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($events)): ?>
                                    <?php foreach ($events as $event): ?>
                                    <tr>
                                        <td data-label="Title"><strong><?php echo htmlspecialchars($event['title']); ?></strong></td>
                                        <td data-label="Date"><?php echo formatDate($event['date']); ?></td>
                                        <td data-label="Time"><?php echo htmlspecialchars($event['time']); ?></td>
                                        <td data-label="Location"><?php echo htmlspecialchars($event['location']); ?></td>
                                        <td data-label="Max Participants"><?php echo htmlspecialchars($event['max_participants']); ?></td>
                                        <td data-label="Deadline"><?php echo formatDate($event['registration_deadline']); ?></td>
                                        <td data-label="Status">
                                            <?php 
                                            $status = $event['status'];
                                            $badgeClass = $status === 'active' ? 'badge-success' : 
                                                        ($status === 'cancelled' ? 'badge-danger' : 'badge-secondary');
                                            echo '<span class="badge ' . $badgeClass . '">' . strtoupper($status) . '</span>';
                                            ?>
                                        </td>
                                        <td class="actions">
                                            <a href="?action=edit&id=<?php echo $event['id']; ?>" class="btn-sm btn-info">Edit</a>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this event?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $event['id']; ?>">
                                                <button type="submit" class="btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" style="text-align: center; padding: 40px;">No events found.</td>
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
                <h1><?php echo $action === 'add' ? 'Create New Event' : 'Edit Event'; ?></h1>
                <a href="events.php" class="btn btn-secondary">
                    <span>←</span> Back to List
                </a>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <form method="POST" class="form-horizontal">
                        <input type="hidden" name="action" value="<?php echo $action; ?>">
                        <?php if ($action === 'edit'): ?>
                            <input type="hidden" name="id" value="<?php echo $currentEvent['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label>Event Title *</label>
                            <input type="text" name="title" required 
                                   value="<?php echo $currentEvent['title'] ?? ''; ?>">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Event Date *</label>
                                <input type="date" name="date" required 
                                       value="<?php echo $currentEvent['date'] ?? ''; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label>Event Time *</label>
                                <input type="time" name="time" required 
                                       value="<?php echo $currentEvent['time'] ?? ''; ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Location *</label>
                            <input type="text" name="location" required 
                                   value="<?php echo $currentEvent['location'] ?? ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Description *</label>
                            <textarea name="description" rows="5" required><?php echo $currentEvent['description'] ?? ''; ?></textarea>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Max Participants *</label>
                                <input type="number" name="max_participants" required min="1" 
                                       value="<?php echo $currentEvent['max_participants'] ?? ''; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label>Registration Deadline *</label>
                                <input type="date" name="registration_deadline" required 
                                       value="<?php echo $currentEvent['registration_deadline'] ?? ''; ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Status *</label>
                            <select name="status" required>
                                <option value="active" <?php echo ($currentEvent['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="completed" <?php echo ($currentEvent['status'] ?? '') === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="cancelled" <?php echo ($currentEvent['status'] ?? '') === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <?php echo $action === 'add' ? 'Create Event' : 'Update Event'; ?>
                            </button>
                            <a href="events.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
