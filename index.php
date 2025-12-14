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

<?php include 'includes/header.php'; ?>

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

<!-- Interactive Image Map Navigation -->
<?php include 'includes/google_map.php'; ?>
<?php include 'includes/leaflet_map.php'; ?>
<section class="quick-links-map">
    <?php
    // Use Leaflet + OpenStreetMap by default (no API key required)
    $markers = [
        // Batangas State University Malvar Campus coordinates (OpenStreetMap)
        ['lat' => 14.0449123, 'lng' => 121.1563294, 'title' => 'Batangas State University Malvar Campus', 'description' => 'JPCS Malvar Chapter - Batangas State University Malvar Campus', 'link' => 'pages/map.php', 'navigateOnClick' => true]
    ];
    echo '<div class="map-instructions"><strong>How it works:</strong> Click the map marker to launch our <strong>Interactive Campus Map</strong>. You can also use pinch/scroll to zoom and drag to pan around the campus vicinity.</div>';
    echo renderLeafletMap($markers, ['center' => ['lat' => 14.0449123, 'lng' => 121.1563294], 'zoom' => 17, 'id' => 'orgLeaflet', 'height' => '420px']);
    ?>
</section>

<!-- Quick Links removed per request -->

<section class="newsletter">
    <h2 class="anton-font" style="font-size: 2rem; margin-bottom: 20px;">Subscribe to Our Newsletter</h2>
    <form id="footerNewsletterForm">
        <input type="email" id="footerEmail" name="email" placeholder="Your email address" required>
        <button type="submit" class="btn">Subscribe</button>
    </form>
    <div id="footerMsg" style="margin-top: 10px; font-weight: 500;"></div>
</section>

<footer>
    <p><strong>JPCS Malvar Chapter</strong></p>
    <p>Batangas State University TNEU - JPLPC Malvar</p>
    <p>Email: jpcs.malvar@g.batstate-u.edu.ph</p>

    <div class="footer-social" aria-label="Share this page">
        <a href="#" class="btn btn-outline" data-share="facebook" aria-label="Share on Facebook">📘 Facebook</a>
        <a href="#" class="btn btn-outline" data-share="twitter" aria-label="Share on Twitter">🐦 Twitter</a>
        <a href="#" class="btn btn-outline" data-share="linkedin" aria-label="Share on LinkedIn">🔗 LinkedIn</a>
        <a href="#" class="btn btn-outline" data-share="whatsapp" aria-label="Share on WhatsApp">💬 WhatsApp</a>
        <a href="#" class="btn btn-outline" data-share="copy" aria-label="Copy link">📋 Copy Link</a>
    </div>
</footer>

<?php include 'includes/tawk_chat.php'; ?>

<script src="js/script.js"></script>
</body>
</html>