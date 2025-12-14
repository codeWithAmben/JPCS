<?php
/**
 * Member Sidebar Navigation
 */
$currentPage = basename($_SERVER['PHP_SELF']);
// Make sure $user and $member are available in this scope
if (!isset($user)) $user = getCurrentUser();
if (!isset($member) && $user) $member = getMemberByUserId($user['id']);
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
                <i data-lucide="layout-dashboard"></i> <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="profile.php" class="<?php echo $currentPage === 'profile.php' ? 'active' : ''; ?>">
                <i data-lucide="user"></i> <span>My Profile</span>
            </a>
        </li>
        <li>
            <a href="events.php" class="<?php echo $currentPage === 'events.php' ? 'active' : ''; ?>">
                <i data-lucide="calendar"></i> <span>Events</span>
            </a>
        </li>
        <li>
            <a href="announcements.php" class="<?php echo $currentPage === 'announcements.php' ? 'active' : ''; ?>">
                <i data-lucide="megaphone"></i> <span>Announcements</span>
            </a>
        </li>
        <li>
            <a href="../pages/gallery.php">
                <i data-lucide="image"></i> <span>Gallery</span>
            </a>
        </li>
        <li>
            <a href="../pages/jpcsmart.php">
                <i data-lucide="shopping-cart"></i> <span>JPCS.Mart</span>
            </a>
        </li>
        <?php if ($member && ($member['membership_status'] ?? '') === 'active'): ?>
        <li>
            <a href="certificate.php" class="<?php echo $currentPage === 'certificate.php' ? 'active' : ''; ?>">
                <i data-lucide="award"></i> <span>My Certificate</span>
            </a>
        </li>
        <?php endif; ?>
        <li>
            <a href="../pages/my_orders.php" class="<?php echo $currentPage === 'my_orders.php' ? 'active' : ''; ?>">
                <i data-lucide="shopping-cart"></i> <span>My Orders</span>
            </a>
        </li>
        <li>
            <a href="../handlers/logout.php" class="logout-link">
                <i data-lucide="log-out"></i> <span>Logout</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <button id="desktopSidebarToggle" class="sidebar-collapse-btn" title="Toggle Sidebar">
            <i data-lucide="chevron-left"></i>
        </button>
    </div>
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

    // Desktop Sidebar Collapse Logic
    const desktopToggle = document.getElementById('desktopSidebarToggle');
    
    // Check saved preference
    if (localStorage.getItem('jpcs_sidebar_mini') === 'true') {
        document.body.classList.add('sidebar-mini');
    }

    if (desktopToggle) {
        desktopToggle.addEventListener('click', function() {
            document.body.classList.toggle('sidebar-mini');
            const isMini = document.body.classList.contains('sidebar-mini');
            localStorage.setItem('jpcs_sidebar_mini', isMini);
            // Re-render icons if needed (sometimes required for dynamic resizing)
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    }
});
</script>
