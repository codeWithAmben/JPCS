<?php require_once '../config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Map - JPCS Malvar</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/pages.css">
</head>
<body class="inner-page">

<?php include '../includes/header.php'; ?>

<section class="page-section">
    <div style="margin-bottom: 20px;">
        <a href="../index.php" class="btn btn-outline">← Back to Home</a>
    </div>
    <h1 class="page-title">Interactive Campus Map</h1>
    <p class="page-intro">Explore our campus hub. Click on the buildings to navigate to different sections.</p>
    
    <div class="map-container">
        <img src="../assets/images/nav-map.png" class="interactive-map-img" alt="JPCS Navigation Map" width="1200" height="800">
        <a href="membership.php" class="map-zone" style="top: 55%; left: 2%; width: 18%; height: 25%;" title="Membership"></a>
        <a href="events.php" class="map-zone" style="top: 10%; left: 2%; width: 25%; height: 35%;" title="Events"></a>
        <a href="announcements.php" class="map-zone" style="top: 5%; left: 42%; width: 10%; height: 30%;" title="Announcements"></a>
        <a href="jpcsmart.php" class="map-zone" style="top: 15%; left: 58%; width: 20%; height: 25%;" title="JPCS.Mart"></a>
        <a href="helpdesk.php" class="map-zone" style="top: 45%; left: 82%; width: 12%; height: 18%;" title="Help Desk"></a>
        <a href="gallery.php" class="map-zone" style="top: 65%; left: 68%; width: 28%; height: 25%;" title="Gallery"></a>
        <a href="about.php" class="map-zone" style="top: 45%; left: 32%; width: 25%; height: 35%;" title="About Us"></a>
        <a href="../login.php" class="map-zone" style="top: 5%; left: 80%; width: 15%; height: 20%;" title="Login"></a>
    </div>
</section>

<footer>
    <p>&copy; 2025 JPCS Malvar Chapter. All Rights Reserved.</p>
</footer>

<?php include '../includes/tawk_chat.php'; ?>

<script src="../js/script.js"></script>
</body>
</html>