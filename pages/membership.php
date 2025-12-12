<?php require_once '../config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membership - JPCS Malvar</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/membership.css">
</head>

<body class="inner-page">

<?php include '../includes/header.php'; ?>

<section class="membership-section">
    <h1 class="anton-font">Join JPCS Malvar Chapter</h1>
    <p class="membership-intro">
        Become part of a vibrant community of aspiring IT professionals and unlock endless opportunities for growth, learning, and networking.
    </p>

    <div class="benefits-grid">
        <div class="benefit-card">
            <span class="benefit-icon">🎓</span>
            <h3>Learning Opportunities</h3>
            <p>Access to workshops, seminars, and training programs to enhance your technical skills.</p>
        </div>

        <div class="benefit-card">
            <span class="benefit-icon">🤝</span>
            <h3>Networking</h3>
            <p>Connect with like-minded individuals, industry professionals, and potential mentors.</p>
        </div>

        <div class="benefit-card">
            <span class="benefit-icon">🏆</span>
            <h3>Competitions</h3>
            <p>Participate in hackathons, coding challenges, and tech competitions.</p>
        </div>

        <div class="benefit-card">
            <span class="benefit-icon">💼</span>
            <h3>Career Development</h3>
            <p>Get career guidance, job opportunities, and industry exposure.</p>
        </div>

        <div class="benefit-card">
            <span class="benefit-icon">🌟</span>
            <h3>Leadership Skills</h3>
            <p>Develop leadership abilities through organizing events and projects.</p>
        </div>

        <div class="benefit-card">
            <span class="benefit-icon">🎉</span>
            <h3>Fun Activities</h3>
            <p>Enjoy team building events, social gatherings, and community outreach programs.</p>
        </div>
    </div>

    <div class="requirements-section">
        <h2>Membership Requirements</h2>
        <ul>
            <li>Must be a currently enrolled student at an accredited institution</li>
            <li>Interest in computer science, information technology, or related fields</li>
            <li>Willingness to actively participate in chapter activities</li>
            <li>Good academic standing (maintain passing grades)</li>
            <li>Payment of annual membership fee</li>
            <li>Attendance at orientation program for new members</li>
        </ul>
    </div>

    <div class="join-cta">
        <h2>Ready to Join Us?</h2>
        <p>Take the first step towards an exciting journey in the world of technology!</p>
        <a href="registration.php" class="btn">Register Now</a>
    </div>
</section>

<footer>
    <p>&copy; 2025 JPCS Malvar Chapter. All Rights Reserved.</p>
</footer>

<?php include '../includes/tawk_chat.php'; ?>

<script src="../js/script.js"></script>
</body>
</html>
