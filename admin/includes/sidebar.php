<?php $user = getCurrentUser(); ?>
<aside class="sidebar" id="sidebar">
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
        <li><a href="event_registrations.php" <?php echo basename($_SERVER['PHP_SELF']) === 'event_registrations.php' ? 'class="active"' : ''; ?>>
            <i data-lucide="ticket"></i> Event Registrations
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
        <li><a href="orders.php" <?php echo basename($_SERVER['PHP_SELF']) === 'orders.php' ? 'class="active"' : ''; ?>>
            <i data-lucide="package"></i> Orders
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
        <li><a href="../handlers/logout.php" class="logout-link">
            <i data-lucide="log-out"></i> Logout
        </a></li>
    </ul>
</aside>

<!-- Admin Sidebar Toggle Button (Mobile Only) -->
<button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
    <span></span><span></span><span></span>
</button>

<!-- Admin Sidebar Overlay (Mobile Only) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    
    // Sidebar toggle functionality
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
    function toggleSidebar() {
        sidebarToggle.classList.toggle('active');
        sidebar.classList.toggle('active');
        sidebarOverlay.classList.toggle('active');
        document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
    }
    
    function closeSidebar() {
        sidebarToggle.classList.remove('active');
        sidebar.classList.remove('active');
        sidebarOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', toggleSidebar);
    }
    
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
    }
    
    // Close sidebar on window resize if larger than tablet
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            closeSidebar();
        }
    });
});
</script>
