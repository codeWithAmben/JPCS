<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration - JPCS Malvar</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/registration.css">
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

<section class="registration-section">
    <h1 class="anton-font">Member Registration</h1>
    <p class="registration-intro">
        Join the JPCS Malvar Chapter family! Fill out the form below to start your membership application. 
        Fields marked with <span class="required">*</span> are required.
    </p>

    <div class="info-note">
        <strong>📝 Note:</strong> Please ensure all information provided is accurate. You will receive a confirmation email once your application has been reviewed.
    </div>

    <form class="registration-form" onsubmit="event.preventDefault(); alert('Thank you for registering! We will review your application and contact you soon.');">
        
        <div class="form-section">
            <h2>Personal Information</h2>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="firstName">First Name <span class="required">*</span></label>
                    <input type="text" id="firstName" required placeholder="Enter your first name">
                </div>
                <div class="form-group">
                    <label for="middleName">Middle Name</label>
                    <input type="text" id="middleName" placeholder="Enter your middle name">
                </div>
                <div class="form-group">
                    <label for="lastName">Last Name <span class="required">*</span></label>
                    <input type="text" id="lastName" required placeholder="Enter your last name">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="birthdate">Date of Birth <span class="required">*</span></label>
                    <input type="date" id="birthdate" required>
                </div>
                <div class="form-group">
                    <label for="gender">Gender <span class="required">*</span></label>
                    <select id="gender" required>
                        <option value="">Select gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                        <option value="prefer-not-to-say">Prefer not to say</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2>Contact Information</h2>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email Address <span class="required">*</span></label>
                    <input type="email" id="email" required placeholder="your.@batstate-u.edu.ph">
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number <span class="required">*</span></label>
                    <input type="tel" id="phone" required placeholder="09XX XXX XXXX">
                </div>
            </div>

            <div class="form-group">
                <label for="address">Complete Address <span class="required">*</span></label>
                <textarea id="address" required placeholder="Enter your complete address"></textarea>
            </div>
        </div>

        <div class="form-section">
            <h2>Academic Information</h2>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="studentId">SR-CODE <span class="required">*</span></label>
                    <input type="text" id="studentId" required placeholder="e.g., 25-76345">
                </div>
                <div class="form-group">
                    <label for="yearLevel">Year Level <span class="required">*</span></label>
                    <select id="yearLevel" required>
                        <option value="">Select year level</option>
                        <option value="1">1st Year</option>
                        <option value="2">2nd Year</option>
                        <option value="3">3rd Year</option>
                        <option value="4">4th Year</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="course">Course/Program <span class="required">*</span></label>
                <select id="course" required>
                    <option value="">Select your course</option>
                    <option value="bscs">BS Computer Science</option>
                    <option value="bsit">BS Information Technology</option>
                    <option value="bsis">BS Information Systems</option>
                    <option value="other">Other (IT-related)</option>
                </select>
            </div>
        </div>

        <div class="checkbox-group">
            <div class="checkbox-item">
                <input type="checkbox" id="terms" required>
                <label for="terms">I agree to the <a href="#">Terms and Conditions</a> and <a href="#">Privacy Policy</a> of JPCS Malvar Chapter. <span class="required">*</span></label>
            </div>
            <div class="checkbox-item">
                <input type="checkbox" id="newsletter">
                <label for="newsletter">I would like to receive newsletters and updates about JPCS events and activities.</label>
            </div>
        </div>

        <button type="submit" class="submit-btn">Submit Registration</button>
    </form>
</section>

<footer>
    <p>&copy; 2025 JPCS Malvar Chapter. All Rights Reserved.</p>
</footer>

<?php include '../includes/tawk_chat.php'; ?>

<script src="../js/script.js"></script>
</body>
</html>
