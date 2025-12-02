<?php
require_once 'config.php';
require_once 'includes/db_helper.php';

// Get active products from database (limit to 4 for homepage)
$allProducts = getAllProducts();
$products = array_filter($allProducts, function($product) {
    return ($product['status'] ?? 'active') === 'active' || ($product['status'] ?? '') === 'available';
});
$products = array_slice($products, 0, 4); // Show only first 4 products on homepage

// Get gallery items from database (limit to 6 for homepage)
$allGalleryItems = getAllGalleryItems();
$galleryItems = array_slice($allGalleryItems, 0, 6); // Show only first 6 images on homepage
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>JPCS Malvar Chapter - Home</title>
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/pages.css" />
    <link rel="stylesheet" href="css/index.css" />
</head>

<body class="home-page">

<header>
    <img src="assets/images/LOGO.png" class="logo" alt="JPCS Logo">

    <nav class="desktop-nav">
        <a href="index.php">Home</a>
        <a href="pages/about.php">About</a>
        <a href="login.php">Login</a>

        <div class="menu-button">
            <button id="menuBtn">Menu ▼</button>
            <div class="dropdown" id="dropdownMenu">
                <a href="pages/events.php">Events</a>
                <a href="pages/membership.php">Membership</a>
                <a href="pages/announcements.php">Announcements</a>
                <a href="pages/jpcsmart.php">JPCS.Mart</a>
                <a href="pages/helpdesk.php">Help Desk</a>
                <a href="pages/registration.php">Registration</a>
                <a href="pages/gallery.php">Gallery</a>
            </div>
        </div>
    </nav>

    <div class="hamburger" id="hamburger">
        <span></span><span></span><span></span>
    </div>

    <nav class="mobile-nav" id="mobileNav">
        <a href="index.php">Home</a>
        <a href="pages/about.php">About</a>
        <a href="login.php">Login</a>
        <a href="pages/events.php">Events</a>
        <a href="pages/membership.php">Membership</a>
        <a href="pages/announcements.php">Announcements</a>
        <a href="pages/jpcsmart.php">JPCS.Mart</a>
        <a href="pages/helpdesk.php">Help Desk</a>
    </nav>
</header>

<section class="hero">
    <img src="assets/images/JPCS.gif" class="hero-gif" alt="JPCS Background">
</section>

<section class="intro">
    <div>
        <h3>Not yet a JPCS-Malvar Chapter Member?</h3>
        <h1 class="anton-font">
            INITIATE YOUR<br>
            <span class="highlight">REMARKABLE JOURNEY</span><br>
            WITH US!
        </h1>
        <button class="btn" onclick="window.location.href='pages/membership.php'">JOIN US</button>
    </div>

    <div class="intro-right">
        <p>
            Welcome to a <strong>world of limitless possibilities</strong>,
            where the <strong>journey is as exhilarating as the destination</strong>.
        </p>
        <br>
        <p>
            Navigate the intricate tapestry of existence and unfold choices
            filled with creativity, courage, and purpose.
        </p>
    </div>
</section>

<section class="event">
    <h2 class="anton-font" style="font-size: 2.5rem; margin-bottom: 20px;">Featured Upcoming Event</h2>
    <div id="eventContainer">Loading event details...</div>
</section>

<section class="quick-links">
    <h2 class="anton-font" style="font-size: 2rem;">Quick Links</h2>
    <div class="links-grid">
        <a href="pages/membership.php" class="qcard">Membership</a>
        <a href="pages/events.php" class="qcard">Events</a>
        <a href="pages/announcements.php" class="qcard">Announcements</a>
        <a href="pages/helpdesk.php" class="qcard">Help Desk</a>
        <a href="pages/jpcsmart.php" class="qcard">JPCS.Mart</a>
    </div>
</section>

<section class="newsletter">
    <h2 class="anton-font" style="font-size: 2rem; margin-bottom: 20px;">Subscribe to Our Newsletter</h2>
    <form onsubmit="event.preventDefault(); alert('Subscribed!');">
        <input type="email" placeholder="Your email address" required>
        <button class="btn">Subscribe</button>
    </form>
</section>

<footer>
    <p><strong>JPCS Malvar Chapter</strong></p>
    <p>Batangas State University TNEU - JPLPC Malvar</p>
    <p>Email: jpcs.malvar@g.batstate-u.edu.ph</p>
</footer>

<script src="js/script.js"></script>
</body>
</html>