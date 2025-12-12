<?php
/**
 * Member Sidebar Navigation
 */
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <img src="../assets/images/LOGO.png" alt="JPCS Logo" class="admin-logo">
        <h2>JPCS Member Portal</h2>
        <p><?php echo isset($user['name']) ? htmlspecialchars($user['name']) : 'Member'; ?></p>
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
            <a href="../handlers/logout.php" class="logout-link">
                <i data-lucide="log-out"></i> Logout
            </a>
        </li>
    </ul>
</aside>

<!-- Sidebar Toggle Button (Mobile Only) -->
<button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
    <span></span><span></span><span></span>
</button>

<!-- Sidebar Overlay (Mobile Only) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

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
    
    // Close sidebar when clicking a link (mobile)
    const sidebarLinks = sidebar.querySelectorAll('a');
    sidebarLinks.forEach(function(link) {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                closeSidebar();
            }
        });
    });
});
</script>
