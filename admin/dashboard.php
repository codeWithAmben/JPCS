<?php
require_once '../config.php';
requireAdmin();

$user = getCurrentUser();
$members = getAllMembers();
$events = getAllEvents();
$announcements = getAllAnnouncements();
$officers = getAllOfficers();

$stats = [
    'total_members' => count($members),
    'active_members' => count(array_filter($members, fn($m) => $m['membership_status'] === 'active')),
    'pending_members' => count(array_filter($members, fn($m) => $m['membership_status'] === 'pending')),
    'total_officers' => count($officers),
    'active_officers' => count(array_filter($officers, fn($o) => $o['status'] === 'active')),
    'total_events' => count($events),
    'upcoming_events' => count(array_filter($events, fn($e) => strtotime($e['date']) >= time())),
    'total_announcements' => count($announcements)
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        /* Dashboard-specific styles */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }
        
        .stat-card h3 {
            color: #7f8c8d;
            font-size: 0.85rem;
            margin-bottom: 12px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .stat-card .value {
            font-size: 2.5rem;
            color: #2c3e50;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .stat-card.blue {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        
        .stat-card.orange {
            background: linear-gradient(135deg, #f093fb, #f5576c);
            color: white;
        }
        
        .stat-card.green {
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            color: white;
        }
        
        .stat-card.purple {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
            color: white;
        }
        
        .stat-card.blue h3,
        .stat-card.orange h3,
        .stat-card.green h3,
        .stat-card.purple h3,
        .stat-card.blue .value,
        .stat-card.orange .value,
        .stat-card.green .value,
        .stat-card.purple .value {
            color: white;
        }
        
        .section {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            margin-bottom: 25px;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #ecf0f1;
        }
        
        .section-header h2 {
            color: #2c3e50;
            font-size: 1.5rem;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #ff6a00;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: 0.3s;
            font-weight: 600;
        }
        
        .btn:hover {
            background: #e05e00;
            transform: translateY(-2px);
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85rem;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background: #f8f9fa;
        }
        
        th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #2c3e50;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        td {
            padding: 12px;
            border-top: 1px solid #ecf0f1;
            color: #7f8c8d;
        }
        
        tbody tr:hover {
            background: #f8f9fa;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-active {
            background: #27ae60;
            color: white;
        }
        
        .badge-pending {
            background: #f39c12;
            color: white;
        }
        
        .badge-expired {
            background: #e74c3c;
            color: white;
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .action-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            text-align: center;
            text-decoration: none;
            color: #2c3e50;
            transition: 0.3s;
        }
        
        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.12);
            color: #ff6a00;
        }
        
        .action-card .icon {
            margin-bottom: 15px;
            display: flex;
            justify-content: center;
        }
        
        .action-card .icon i {
            width: 48px;
            height: 48px;
            stroke-width: 1.5;
            color: #ff6a00;
        }
        
        .action-card:hover .icon i {
            stroke-width: 2;
        }
        
        .action-card h4 {
            font-size: 1rem;
            margin-bottom: 5px;
        }
        
        /* Responsive styles for dashboard */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .stat-card .value {
                font-size: 1.8rem;
            }
            .section {
                padding: 20px;
            }
            .section-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            table {
                font-size: 0.85rem;
            }
            th, td {
                padding: 8px 6px;
            }
        }
        
        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .quick-actions {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="top-bar">
                <h1 class="welcome">Admin Dashboard</h1>
                <div class="user-menu">
                    <span style="color: #7f8c8d;">
                        <strong><?php echo htmlspecialchars($user['name']); ?></strong><br>
                        <small><?php echo htmlspecialchars($user['role']); ?></small>
                    </span>
                </div>
            </div>
            
            <?php displayFlash(); ?>
            
            <div class="stats-grid">
                <div class="stat-card blue">
                    <h3>Total Members</h3>
                    <div class="value"><?php echo $stats['total_members']; ?></div>
                </div>
                
                <div class="stat-card green">
                    <h3>Active Members</h3>
                    <div class="value"><?php echo $stats['active_members']; ?></div>
                </div>
                
                <div class="stat-card orange">
                    <h3>Pending Approvals</h3>
                    <div class="value"><?php echo $stats['pending_members']; ?></div>
                </div>
                
                <div class="stat-card purple">
                    <h3>Upcoming Events</h3>
                    <div class="value"><?php echo $stats['upcoming_events']; ?></div>
                </div>
            </div>
            
            <div class="section">
                <h3 style="margin-bottom: 20px; color: #2c3e50;">Quick Actions</h3>
                <div class="quick-actions">
                    <a href="members.php?action=add" class="action-card">
                        <div class="icon"><i data-lucide="user-plus"></i></div>
                        <h4>Add Member</h4>
                    </a>
                    
                    <a href="officers.php?action=add" class="action-card">
                        <div class="icon"><i data-lucide="user-circle"></i></div>
                        <h4>Add Officer</h4>
                    </a>
                    
                    <a href="events.php?action=add" class="action-card">
                        <div class="icon"><i data-lucide="calendar-plus"></i></div>
                        <h4>Create Event</h4>
                    </a>
                    
                    <a href="event_registrations.php" class="action-card">
                        <div class="icon"><i data-lucide="ticket"></i></div>
                        <h4>Event Registrations</h4>
                    </a>
                    
                    <a href="announcements.php?action=add" class="action-card">
                        <div class="icon"><i data-lucide="megaphone"></i></div>
                        <h4>Post Announcement</h4>
                    </a>
                    
                    <a href="gallery.php?action=upload" class="action-card">
                        <div class="icon"><i data-lucide="upload"></i></div>
                        <h4>Upload Photos</h4>
                    </a>
                </div>
            </div>
            
            <div class="section">
                <div class="section-header">
                    <h2>Recent Members</h2>
                    <a href="members.php" class="btn btn-sm">View All</a>
                </div>
                
                <?php if (!empty($members)): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Member ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Course</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($members, 0, 10) as $member): ?>
                        <tr>
                            <td data-label="Member ID"><strong><?php echo htmlspecialchars($member['member_id']); ?></strong></td>
                            <td data-label="Name"><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></td>
                            <td data-label="Email"><?php echo htmlspecialchars($member['email']); ?></td>
                            <td data-label="Course"><?php echo htmlspecialchars($member['course']); ?></td>
                            <td data-label="Status">
                                <?php 
                                $status = $member['membership_status'];
                                $badgeClass = $status === 'active' ? 'badge-active' : ($status === 'pending' ? 'badge-pending' : 'badge-expired');
                                echo '<span class="badge ' . $badgeClass . '">' . strtoupper($status) . '</span>';
                                ?>
                            </td>
                            <td data-label="Joined"><?php echo formatDate($member['joined_date'], 'M j, Y'); ?></td>
                            <td class="actions">
                                <a href="members.php?action=edit&id=<?php echo $member['id']; ?>" class="btn btn-sm">Edit</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p style="text-align: center; color: #95a5a6; padding: 40px;">No members yet.</p>
                <?php endif; ?>
            </div>
            
            <div class="section">
                <div class="section-header">
                    <h2>Upcoming Events</h2>
                    <a href="events.php" class="btn btn-sm">View All</a>
                </div>
                
                <?php 
                $upcomingEvents = array_filter($events, fn($e) => strtotime($e['date']) >= time() && $e['status'] === 'active');
                if (!empty($upcomingEvents)):
                ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Max Participants</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($upcomingEvents, 0, 5) as $event): ?>
                        <tr>
                            <td data-label="Title"><strong><?php echo htmlspecialchars($event['title']); ?></strong></td>
                            <td data-label="Date"><?php echo formatDate($event['date']); ?></td>
                            <td data-label="Location"><?php echo htmlspecialchars($event['location']); ?></td>
                            <td data-label="Max Participants"><?php echo htmlspecialchars($event['max_participants']); ?></td>
                            <td class="actions">
                                <a href="events.php?action=edit&id=<?php echo $event['id']; ?>" class="btn btn-sm">Edit</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p style="text-align: center; color: #95a5a6; padding: 40px;">No upcoming events.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
