<?php
require_once '../config.php';
requireLogin();

$user = getCurrentUser();
$member = getMemberByUserId($user['id']);
$events = getAllEvents();
$announcements = array_slice(getAllAnnouncements(), 0, 5); // Latest 5

$upcomingEvents = array_filter($events, function($event) {
    return strtotime($event['date']) >= time() && $event['status'] === 'active';
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Dashboard - <?php echo SITE_NAME; ?></title>
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
                            <i data-lucide="sparkles"></i>
                            Welcome back, <?php echo htmlspecialchars(explode(' ', $user['name'])[0]); ?>!
                        </h1>
                        <p>Here's what's happening with your membership today</p>
                    </div>
                </div>
            </div>
            
            <?php displayFlash(); ?>
            
            <?php if ($member): ?>
            <div class="profile-hero">
                <div class="profile-hero-content">
                    <div class="profile-avatar">
                        <?php if (!empty($member['profile_photo'])): ?>
                            <img src="../<?php echo htmlspecialchars($member['profile_photo']); ?>" 
                                 alt="Profile">
                        <?php else: ?>
                            <i data-lucide="user"></i>
                        <?php endif; ?>
                    </div>
                    
                    <div class="profile-details">
                        <h2><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></h2>
                        <div class="profile-meta">
                            <div class="profile-meta-item">
                                <i data-lucide="id-card"></i>
                                <span><?php echo htmlspecialchars($member['member_id']); ?></span>
                            </div>
                            <div class="profile-meta-item">
                                <i data-lucide="graduation-cap"></i>
                                <span><?php echo htmlspecialchars($member['course']); ?> - Year <?php echo $member['year_level']; ?></span>
                            </div>
                            <div class="profile-meta-item">
                                <i data-lucide="mail"></i>
                                <span><?php echo htmlspecialchars($member['email']); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="membership-badge">
                        <h4>Membership Status</h4>
                        <div class="status">
                            <?php echo strtoupper($member['membership_status']); ?>
                        </div>
                        <div style="margin-top: 10px; font-size: 0.85rem; opacity: 0.9;">
                            Expires: <?php echo formatDate($member['expiry_date']); ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="section">
                <div class="section-body">
                    <div class="empty-state">
                        <i data-lucide="alert-circle"></i>
                        <p>You haven't completed your membership registration yet.</p>
                        <a href="../pages/registration.php" class="btn" style="margin-top: 20px;">
                            <i data-lucide="user-plus"></i> Complete Registration
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="stats-grid">
                <div class="stat-card" style="--card-accent: #ff6a00; --icon-bg: rgba(255, 106, 0, 0.1);">
                    <div class="stat-card-header">
                        <h3>Upcoming Events</h3>
                        <div class="stat-icon">
                            <i data-lucide="calendar"></i>
                        </div>
                    </div>
                    <div class="value"><?php echo count($upcomingEvents); ?></div>
                    <div class="trend">
                        <i data-lucide="trending-up"></i>
                        Ready to join
                    </div>
                </div>
                
                <div class="stat-card" style="--card-accent: #ff8c42; --icon-bg: rgba(255, 140, 66, 0.1);">
                    <div class="stat-card-header">
                        <h3>Events Attended</h3>
                        <div class="stat-icon">
                            <i data-lucide="check-circle"></i>
                        </div>
                    </div>
                    <div class="value"><?php echo countAttendedEvents($user['id']); ?></div>
                    <div class="trend">
                        <i data-lucide="award"></i>
                        Total participation
                    </div>
                </div>
                
                <div class="stat-card" style="--card-accent: #e05e00; --icon-bg: rgba(224, 94, 0, 0.1);">
                    <div class="stat-card-header">
                        <h3>New Announcements</h3>
                        <div class="stat-icon">
                            <i data-lucide="megaphone"></i>
                        </div>
                    </div>
                    <div class="value"><?php echo count($announcements); ?></div>
                    <div class="trend">
                        <i data-lucide="bell"></i>
                        Latest updates
                    </div>
                </div>
            </div>
            
            <div class="section">
                <div class="section-header">
                    <h2>
                        <i data-lucide="calendar-days"></i>
                        Upcoming Events
                    </h2>
                    <a href="events.php" class="view-all-link">
                        View All <i data-lucide="arrow-right"></i>
                    </a>
                </div>
                <div class="section-body">
                    <?php if (!empty($upcomingEvents)): ?>
                    <div class="event-list">
                        <?php foreach (array_slice($upcomingEvents, 0, 4) as $event): ?>
                        <div class="event-item">
                            <div class="event-item-header">
                                <div>
                                    <h4><?php echo htmlspecialchars($event['title']); ?></h4>
                                    <div class="event-meta">
                                        <div class="event-meta-item">
                                            <i data-lucide="calendar"></i>
                                            <span><?php echo formatDate($event['date']); ?></span>
                                        </div>
                                        <div class="event-meta-item">
                                            <i data-lucide="clock"></i>
                                            <span><?php echo formatTime($event['time']); ?></span>
                                        </div>
                                        <div class="event-meta-item">
                                            <i data-lucide="map-pin"></i>
                                            <span><?php echo htmlspecialchars($event['location']); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p><?php echo htmlspecialchars(substr($event['description'], 0, 150)) . '...'; ?></p>
                            <a href="events.php" class="btn">
                                <i data-lucide="arrow-right"></i> View Details
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="empty-state">
                        <i data-lucide="calendar-x"></i>
                        <p>No upcoming events at the moment.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="section">
                <div class="section-header">
                    <h2>
                        <i data-lucide="bell"></i>
                        Latest Announcements
                    </h2>
                    <a href="announcements.php" class="view-all-link">
                        View All <i data-lucide="arrow-right"></i>
                    </a>
                </div>
                <div class="section-body">
                    <?php if (!empty($announcements)): ?>
                    <div class="announcement-list">
                        <?php foreach ($announcements as $announcement): ?>
                        <div class="announcement-item">
                            <div class="announcement-header">
                                <div>
                                    <h4>
                                        <?php echo htmlspecialchars($announcement['title']); ?>
                                        <?php if (!empty($announcement['badge'])): ?>
                                        <span class="badge badge-active"><?php echo htmlspecialchars($announcement['badge']); ?></span>
                                        <?php endif; ?>
                                    </h4>
                                    <div class="announcement-date">
                                        <i data-lucide="clock"></i>
                                        Posted: <?php echo formatDate($announcement['posted_date']); ?>
                                    </div>
                                </div>
                            </div>
                            <p><?php echo htmlspecialchars(substr($announcement['content'], 0, 180)) . '...'; ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="empty-state">
                        <i data-lucide="inbox"></i>
                        <p>No announcements available.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
