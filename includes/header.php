<?php
/**
 * Reusable Header Navigation Component
 * Shows different navigation based on login status and user role
 */

// Determine if we're on home page or inner page
$isHomePage = basename($_SERVER['PHP_SELF']) === 'index.php' && strpos($_SERVER['PHP_SELF'], '/pages/') === false;
$bodyClass = $isHomePage ? 'home-page' : 'inner-page';

// Get current user if logged in
$currentUser = isLoggedIn() ? getCurrentUser() : null;
$isAdmin = $currentUser && (hasRole(ROLE_ADMIN) || hasRole(ROLE_OFFICER));
$isMember = $currentUser && hasRole(ROLE_MEMBER);

// Determine base path for links
$basePath = '';
if (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) {
    $basePath = '../';
} elseif (strpos($_SERVER['PHP_SELF'], '/member/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/') !== false) {
    $basePath = '../';
}
?>
<header>
    <a href="<?php echo $basePath; ?>index.php">
        <img src="<?php echo $basePath; ?>assets/images/LOGO.png" class="logo" alt="JPCS Logo">
    </a>

    <nav class="desktop-nav">
        <a href="<?php echo $basePath; ?>index.php">Home</a>
        <a href="<?php echo $basePath; ?>pages/about.php">About</a>
        
        <?php if ($currentUser): ?>
            <!-- Logged in user navigation -->
            <?php if ($isAdmin): ?>
                <a href="<?php echo $basePath; ?>admin/dashboard.php">Dashboard</a>
            <?php else: ?>
                <a href="<?php echo $basePath; ?>member/dashboard.php">Dashboard</a>
            <?php endif; ?>
            
            <div class="menu-button">
                <button id="menuBtn">
                    <span class="user-name"><?php echo htmlspecialchars(explode(' ', $currentUser['name'])[0]); ?></span> ▼
                </button>
                <div class="dropdown" id="dropdownMenu">
                    <a href="<?php echo $basePath; ?>pages/events.php">Events</a>
                    <a href="<?php echo $basePath; ?>pages/announcements.php">Announcements</a>
                    <a href="<?php echo $basePath; ?>pages/jpcsmart.php">JPCS.Mart</a>
                    <a href="<?php echo $basePath; ?>pages/gallery.php">Gallery</a>
                    <?php if ($isAdmin): ?>
                        <a href="<?php echo $basePath; ?>admin/dashboard.php">Admin Panel</a>
                    <?php else: ?>
                        <a href="<?php echo $basePath; ?>member/profile.php">My Profile</a>
                    <?php endif; ?>
                    <a href="<?php echo $basePath; ?>handlers/logout.php" class="logout-link">Logout</a>
                </div>
            </div>
        <?php else: ?>
            <!-- Guest navigation -->
            <a href="<?php echo $basePath; ?>login.php">Login</a>
            
            <div class="menu-button">
                <button id="menuBtn">Menu ▼</button>
                <div class="dropdown" id="dropdownMenu">
                    <a href="<?php echo $basePath; ?>pages/events.php">Events</a>
                    <a href="<?php echo $basePath; ?>pages/membership.php">Membership</a>
                    <a href="<?php echo $basePath; ?>pages/announcements.php">Announcements</a>
                    <a href="<?php echo $basePath; ?>pages/jpcsmart.php">JPCS.Mart</a>
                    <a href="<?php echo $basePath; ?>pages/helpdesk.php">Help Desk</a>
                    <a href="<?php echo $basePath; ?>pages/registration.php">Registration</a>
                    <a href="<?php echo $basePath; ?>pages/gallery.php">Gallery</a>
                </div>
            </div>
        <?php endif; ?>
    </nav>

    <div class="hamburger" id="hamburger">
        <span></span><span></span><span></span>
    </div>

    <nav class="mobile-nav" id="mobileNav">
        <div class="mobile-nav-header">
            <?php if ($currentUser): ?>
                <div class="mobile-user-info">
                    <span class="mobile-user-avatar"><?php echo strtoupper(substr($currentUser['name'], 0, 1)); ?></span>
                    <span class="mobile-user-name"><?php echo htmlspecialchars($currentUser['name']); ?></span>
                </div>
            <?php endif; ?>
        </div>
        
        <a href="<?php echo $basePath; ?>index.php">Home</a>
        <a href="<?php echo $basePath; ?>pages/about.php">About</a>
        
        <?php if ($currentUser): ?>
            <?php if ($isAdmin): ?>
                <a href="<?php echo $basePath; ?>admin/dashboard.php">Dashboard</a>
            <?php else: ?>
                <a href="<?php echo $basePath; ?>member/dashboard.php">Dashboard</a>
                <a href="<?php echo $basePath; ?>member/profile.php">My Profile</a>
            <?php endif; ?>
        <?php else: ?>
            <a href="<?php echo $basePath; ?>login.php">Login</a>
            <a href="<?php echo $basePath; ?>pages/membership.php">Membership</a>
            <a href="<?php echo $basePath; ?>pages/registration.php">Registration</a>
        <?php endif; ?>
        
        <a href="<?php echo $basePath; ?>pages/events.php">Events</a>
        <a href="<?php echo $basePath; ?>pages/announcements.php">Announcements</a>
        <a href="<?php echo $basePath; ?>pages/jpcsmart.php">JPCS.Mart</a>
        <a href="<?php echo $basePath; ?>pages/gallery.php">Gallery</a>
        <a href="<?php echo $basePath; ?>pages/helpdesk.php">Help Desk</a>
        
        <?php if ($currentUser): ?>
            <a href="<?php echo $basePath; ?>handlers/logout.php" class="mobile-logout">Logout</a>
        <?php endif; ?>
    </nav>
</header>

<!-- Mobile Navigation Overlay -->
<div class="mobile-nav-overlay" id="mobileNavOverlay"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Menu dropdown
    const menuBtn = document.getElementById('menuBtn');
    const dropdown = document.getElementById('dropdownMenu');
    
    if (menuBtn && dropdown) {
        menuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('active');
        });
        
        document.addEventListener('click', function() {
            dropdown.classList.remove('active');
        });
    }
    
    // Mobile nav
    const hamburger = document.getElementById('hamburger');
    const mobileNav = document.getElementById('mobileNav');
    const mobileNavOverlay = document.getElementById('mobileNavOverlay');
    
    function openMobileNav() {
        if (mobileNav) mobileNav.classList.add('active');
        if (hamburger) hamburger.classList.add('active');
        if (mobileNavOverlay) mobileNavOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    function closeMobileNav() {
        if (mobileNav) mobileNav.classList.remove('active');
        if (hamburger) hamburger.classList.remove('active');
        if (mobileNavOverlay) mobileNavOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    if (hamburger) {
        hamburger.addEventListener('click', function() {
            if (mobileNav && mobileNav.classList.contains('active')) {
                closeMobileNav();
            } else {
                openMobileNav();
            }
        });
    }
    
    // Close on overlay click
    if (mobileNavOverlay) {
        mobileNavOverlay.addEventListener('click', closeMobileNav);
    }
    
    // Close on link click
    if (mobileNav) {
        mobileNav.querySelectorAll('a').forEach(function(link) {
            link.addEventListener('click', closeMobileNav);
        });
    }
    
    // Close on window resize if larger than tablet
    window.addEventListener('resize', function() {
        if (window.innerWidth > 900) {
            closeMobileNav();
        }
    });
});
</script>
