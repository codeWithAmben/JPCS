<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Desk - JPCS Malvar</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/helpdesk.css">
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

<section class="helpdesk-section">
    <h1 class="anton-font">Help Desk</h1>
    <p class="helpdesk-intro">
        Need assistance? We're here to help! Reach out to us through any of the following channels.
    </p>

    <div class="contact-grid">
        <div class="contact-card">
            <span class="contact-icon">📧</span>
            <h3>Email Us</h3>
            <p>Send us your inquiries anytime.<br>
            <a href="mailto:jpcs.malvar@edu.ph">jpcs.malvar@edu.ph</a></p>
        </div>

        <div class="contact-card">
            <span class="contact-icon">📱</span>
            <h3>Call/Text Us</h3>
            <p>Available Mon-Fri, 9AM-5PM<br>
            <a href="tel:+639123456789">+63 912 345 6789</a></p>
        </div>

        <div class="contact-card">
            <span class="contact-icon">📍</span>
            <h3>Visit Us</h3>
            <p>BatStateU TNEU Malvar Campus<br>
            Malvar, Batangas</p>
        </div>
    </div>

    <div class="faq-section">
        <h2>Frequently Asked Questions</h2>

        <div class="faq-item">
            <div class="faq-question">How do I become a member of JPCS?</div>
            <div class="faq-answer">You can register through our online registration form or visit our office during enrollment period. Fill out the membership form and pay the membership fee to complete your application.</div>
        </div>

        <div class="faq-item">
            <div class="faq-question">What are the benefits of joining JPCS?</div>
            <div class="faq-answer">Members enjoy access to exclusive workshops, seminars, networking events, hackathons, and career development opportunities. You'll also receive priority registration for events and discounts on merchandise.</div>
        </div>

        <div class="faq-item">
            <div class="faq-question">How much is the membership fee?</div>
            <div class="faq-answer">The annual membership fee is ₱500 for regular members. Early bird registrations may qualify for special discounts.</div>
        </div>

        <div class="faq-item">
            <div class="faq-question">Can non-IT students join JPCS?</div>
            <div class="faq-answer">Yes! While we primarily cater to IT and Computer Science students, anyone with a passion for technology is welcome to join our community.</div>
        </div>

        <div class="faq-item">
            <div class="faq-question">How can I volunteer for JPCS events?</div>
            <div class="faq-answer">Keep an eye on our announcements for volunteer calls. You can also express your interest by contacting us directly through email or visiting our office.</div>
        </div>
    </div>

    <div class="inquiry-form">
        <h2>Send Us a Message</h2>
        <form onsubmit="event.preventDefault(); alert('Thank you for your message! We will get back to you soon.');">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" required placeholder="Enter your full name">
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" required placeholder="Enter your email">
            </div>
            
            <div class="form-group">
                <label for="subject">Subject</label>
                <select id="subject" required>
                    <option value="">Select a subject</option>
                    <option value="membership">Membership Inquiry</option>
                    <option value="events">Events Information</option>
                    <option value="technical">Technical Support</option>
                    <option value="feedback">Feedback/Suggestions</option>
                    <option value="other">Other</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" required placeholder="Type your message here..."></textarea>
            </div>
            
            <button type="submit" class="submit-btn">Send Message</button>
        </form>
    </div>
</section>

<footer>
    <p>&copy; 2025 JPCS Malvar Chapter. All Rights Reserved.</p>
</footer>

<script src="../js/script.js"></script>
</body>
</html>
