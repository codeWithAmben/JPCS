<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements - JPCS Malvar</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/announcements.css">
</head>

<body class="inner-page">

<header>
    <img src="../assets/images/LOGO.png" class="logo" alt="JPCS Logo">

    <nav class="desktop-nav">
        <a href="../index.php">Home</a>
        <a href="about.php">About</a>

        <div class="menu-button">
            <button id="menuBtn">Menu ▼</button>
            <div class="dropdown" id="dropdownMenu">
                <a href="events.php">Events</a>
                <a href="membership.php">Membership</a>
                <a href="announcements.php">Announcements</a>
                <a href="jpcsmart.php">JPCS.Mart</a>
                <a href="helpdesk.php">Help Desk</a>
                <a href="registration.php">Registration</a>
                <a href="gallery.php">Gallery</a>
            </div>
        </div>
    </nav>

    <div class="hamburger" id="hamburger">
        <span></span><span></span><span></span>
    </div>

    <nav class="mobile-nav" id="mobileNav">
        <a href="../index.php">Home</a>
        <a href="about.php">About</a>
        <a href="events.php">Events</a>
        <a href="membership.php">Membership</a>
        <a href="announcements.php">Announcements</a>
        <a href="jpcsmart.php">JPCS.Mart</a>
        <a href="helpdesk.php">Help Desk</a>
    </nav>
</header>

<section class="announcements-section">
    <h1 class="anton-font">Latest Announcements</h1>

    <div class="announcement-item">
        <div class="announcement-header">
            <h3>Membership Drive 2026 Now Open!</h3>
            <span class="announcement-badge new">NEW</span>
        </div>
        <p class="announcement-date">Posted: December 1, 2025</p>
        <div class="announcement-body">
            <p>We are excited to announce that our membership drive for 2026 is now officially open! This is your chance to become part of the JPCS Malvar Chapter family. Early bird registration is available until January 15, 2026, with special discounts for those who sign up early.</p>
            <p><strong>Benefits include:</strong> Access to all workshops, priority registration for events, exclusive networking opportunities, and much more!</p>
        </div>
    </div>

    <div class="announcement-item">
        <div class="announcement-header">
            <h3>Tech Summit 2026 - Call for Volunteers</h3>
            <span class="announcement-badge urgent">URGENT</span>
        </div>
        <p class="announcement-date">Posted: November 28, 2025</p>
        <div class="announcement-body">
            <p>We need enthusiastic volunteers to help make our Annual Tech Summit 2026 a grand success! If you're interested in gaining event management experience while contributing to our community, this is the perfect opportunity.</p>
            <p><strong>Positions available:</strong> Registration desk, technical support, logistics, and hospitality. Contact the organizing committee for more details.</p>
        </div>
    </div>

    <div class="announcement-item">
        <div class="announcement-header">
            <h3>Workshop Schedule for January Released</h3>
        </div>
        <p class="announcement-date">Posted: November 25, 2025</p>
        <div class="announcement-body">
            <p>Check out our exciting lineup of workshops for January 2026! Topics include Web Development with React, Python for Data Science, Mobile App Development, and Cybersecurity Fundamentals. Registration opens December 5th. Limited slots available!</p>
        </div>
    </div>

    <div class="announcement-item">
        <div class="announcement-header">
            <h3>Holiday Break Schedule</h3>
        </div>
        <p class="announcement-date">Posted: November 20, 2025</p>
        <div class="announcement-body">
            <p>The JPCS Malvar Chapter office will be closed from December 20, 2025, to January 5, 2026, for the holiday break. Regular operations will resume on January 6, 2026. Emergency inquiries can be sent to our official email, and we'll respond as soon as possible.</p>
            <p>Happy holidays to all our members and their families!</p>
        </div>
    </div>
</section>

<footer>
    <p>&copy; 2025 JPCS Malvar Chapter. All Rights Reserved.</p>
</footer>

<?php include '../includes/tawk_chat.php'; ?>

<script src="../js/script.js"></script>
</body>
</html>
