<?php $user = getCurrentUser(); ?>
<aside class="sidebar">
    <div class="sidebar-header">
        <img src="../assets/images/LOGO.png" alt="JPCS Logo" class="admin-logo">
        <h2>JPCS Admin Panel</h2>
        <p><?php echo htmlspecialchars($user['name']); ?></p>
    </div>
    
    <ul class="sidebar-menu">
        <li><a href="dashboard.php" <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'class="active"' : ''; ?>>
            <i data-lucide="layout-dashboard"></i> Dashboard
        </a></li>
        <li><a href="members.php" <?php echo basename($_SERVER['PHP_SELF']) === 'members.php' ? 'class="active"' : ''; ?>>
            <i data-lucide="users"></i> Manage Members
        </a></li>
        <li><a href="officers.php" <?php echo basename($_SERVER['PHP_SELF']) === 'officers.php' ? 'class="active"' : ''; ?>>
            <i data-lucide="user-circle"></i> Manage Officers
        </a></li>
        <li><a href="events.php" <?php echo basename($_SERVER['PHP_SELF']) === 'events.php' ? 'class="active"' : ''; ?>>
            <i data-lucide="calendar"></i> Manage Events
        </a></li>
        <li><a href="announcements.php" <?php echo basename($_SERVER['PHP_SELF']) === 'announcements.php' ? 'class="active"' : ''; ?>>
            <i data-lucide="megaphone"></i> Announcements
        </a></li>
        <li><a href="gallery.php" <?php echo basename($_SERVER['PHP_SELF']) === 'gallery.php' ? 'class="active"' : ''; ?>>
            <i data-lucide="image"></i> Gallery
        </a></li>
        <li><a href="products.php" <?php echo basename($_SERVER['PHP_SELF']) === 'products.php' ? 'class="active"' : ''; ?>>
            <i data-lucide="shopping-cart"></i> Products
        </a></li>
        <li><a href="registrations.php" <?php echo basename($_SERVER['PHP_SELF']) === 'registrations.php' ? 'class="active"' : ''; ?>>
            <i data-lucide="clipboard-list"></i> Registrations
        </a></li>
        <li><a href="inquiries.php" <?php echo basename($_SERVER['PHP_SELF']) === 'inquiries.php' ? 'class="active"' : ''; ?>>
            <i data-lucide="message-square"></i> Inquiries
        </a></li>
        <li><a href="settings.php" <?php echo basename($_SERVER['PHP_SELF']) === 'settings.php' ? 'class="active"' : ''; ?>>
            <i data-lucide="settings"></i> Settings
        </a></li>
        <li><a href="../index.php">
            <i data-lucide="globe"></i> View Website
        </a></li>
        <li><a href="../handlers/logout.php">
            <i data-lucide="log-out"></i> Logout
        </a></li>
    </ul>
    
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
</aside>
