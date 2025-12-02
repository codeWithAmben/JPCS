<?php
/**
 * Member Sidebar Navigation
 */
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <img src="../assets/images/LOGO.png" alt="JPCS Logo" class="admin-logo">
        <h2>JPCS Member Portal</h2>
        <p><?php echo isset($user['name']) ? htmlspecialchars($user['name']) : 'member'; ?></p>
    </div>
    
    <ul class="sidebar-menu">
        <li>
            <a href="dashboard.php" class="<?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">
                <i data-lucide="layout-dashboard"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="profile.php" class="<?php echo $currentPage === 'profile.php' ? 'active' : ''; ?>">
                <i data-lucide="user"></i> My Profile
            </a>
        </li>
        <li>
            <a href="events.php" class="<?php echo $currentPage === 'events.php' ? 'active' : ''; ?>">
                <i data-lucide="calendar"></i> Events
            </a>
        </li>
        <li>
            <a href="announcements.php" class="<?php echo $currentPage === 'announcements.php' ? 'active' : ''; ?>">
                <i data-lucide="megaphone"></i> Announcements
            </a>
        </li>
        <li>
            <a href="../pages/gallery.php">
                <i data-lucide="image"></i> Gallery
            </a>
        </li>
        <li>
            <a href="../pages/jpcsmart.php">
                <i data-lucide="shopping-cart"></i> JPCS.Mart
            </a>
        </li>
        <li>
            <a href="../handlers/logout.php">
                <i data-lucide="log-out"></i> Logout
            </a>
        </li>
    </ul>
    
    <script>
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    </script>
</aside>
