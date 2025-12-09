<?php
require_once '../config.php';

// Get all officers from database
$officers = getAllOfficers();
$activeOfficers = array_filter($officers, fn($o) => $o['status'] === 'active');

// Sort by order
usort($activeOfficers, fn($a, $b) => (int)$a['order'] - (int)$b['order']);

// Group officers by category
$executives = array_filter($activeOfficers, fn($o) => $o['category'] === 'Executive');
$governors = array_filter($activeOfficers, fn($o) => $o['category'] === 'Governor');
$directors = array_filter($activeOfficers, fn($o) => $o['category'] === 'Director');
$members = array_filter($activeOfficers, fn($o) => $o['category'] === 'Member');

// Sort each group by order
usort($executives, fn($a, $b) => (int)$a['order'] - (int)$b['order']);
usort($governors, fn($a, $b) => (int)$a['order'] - (int)$b['order']);
usort($directors, fn($a, $b) => (int)$a['order'] - (int)$b['order']);
usort($members, fn($a, $b) => (int)$a['order'] - (int)$b['order']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - JPCS Malvar</title>
    
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/about.css">
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


<section class="intro-section">
    <h1 class="intro-title">
        <span class="intro-orange">JPCS MALVAR CHAPTER</span><br>
        <span class="intro-sub">Get to Know Us Better</span>
    </h1>
</section>

<section class="history">
    <h2>History of the Organization</h2>
    <div class="history-container">
        <p class="history-item">In <span class="highlight">January 1989</span>, the Philippine Computer Society formed the <span class="highlight">PCS–Junior Programmer’s Guild</span>.</p>
        <p class="history-item">In <span class="highlight">July 1990</span>, it became the <span class="highlight">Junior Philippine Computer Society (JPCS)</span>.</p>
        <p class="history-item">By <span class="highlight">2021–2022</span>, the <span class="highlight">JPLPC–Malvar Chapter</span> was founded.</p>
        <p class="history-item">Currently, <span class="highlight">President Jaynellan Almary O. Magpantay</span> leads the <span class="highlight">2025–2026</span> term.</p>
    </div>
</section>


<section class="officers">
    <h2>Meet the Officers</h2>

    <div class="officers-container">
        
        <?php if (!empty($activeOfficers)): ?>
            
            <!-- Adviser (Order 1) -->
            <?php 
            $adviser = array_filter($executives, fn($o) => (int)$o['order'] === 1);
            if (!empty($adviser)): 
                $adviser = array_values($adviser)[0];
            ?>
            <div class="officer-row grid-1">
                <div class="card">
                    <img src="../assets/profiles/<?php echo htmlspecialchars($adviser['photo']); ?>" alt="<?php echo htmlspecialchars($adviser['position']); ?>">
                    <h3><?php echo htmlspecialchars($adviser['name']); ?></h3>
                    <p><?php echo htmlspecialchars($adviser['position']); ?></p>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- President (Order 2) -->
            <?php 
            $president = array_filter($executives, fn($o) => (int)$o['order'] === 2);
            if (!empty($president)): 
                $president = array_values($president)[0];
            ?>
            <div class="officer-row grid-1">
                <div class="card">
                    <img src="../assets/profiles/<?php echo htmlspecialchars($president['photo']); ?>" alt="<?php echo htmlspecialchars($president['position']); ?>">
                    <h3><?php echo htmlspecialchars($president['name']); ?></h3>
                    <p><?php echo htmlspecialchars($president['position']); ?></p>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- VP's (Order 3-4) -->
            <?php
            $vps = array_filter($executives, fn($o) => stripos($o['position'], 'VP') !== false || stripos($o['position'], 'Vice President') !== false);
            if (!empty($vps)):
            ?>
            <div class="officer-row grid-2">
                <?php foreach ($vps as $vp): ?>
                    <div class="card">
                        <img src="../assets/profiles/<?php echo htmlspecialchars($vp['photo']); ?>" alt="<?php echo htmlspecialchars($vp['position']); ?>">
                        <h3><?php echo htmlspecialchars($vp['name']); ?></h3>
                        <p><?php echo htmlspecialchars($vp['position']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <!-- Secretary (Order 5) -->
            <?php
            $secretaries = array_filter($executives, fn($o) => stripos($o['position'], 'Secretary') !== false);
            if (!empty($secretaries)):
            ?>
            <div class="officer-row grid-1">
                <?php foreach ($secretaries as $sec): ?>
                    <div class="card">
                        <img src="../assets/profiles/<?php echo htmlspecialchars($sec['photo']); ?>" alt="<?php echo htmlspecialchars($sec['position']); ?>">
                        <h3><?php echo htmlspecialchars($sec['name']); ?></h3>
                        <p><?php echo htmlspecialchars($sec['position']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <!-- Finance and Auditor (Order 6-7) -->
            <?php
            $financeAudit = array_filter($executives, fn($o) => 
                stripos($o['position'], 'Finance') !== false || stripos($o['position'], 'Auditor') !== false
            );
            if (!empty($financeAudit)):
            ?>
            <div class="officer-row grid-2">
                <?php foreach ($financeAudit as $fin): ?>
                    <div class="card">
                        <img src="../assets/profiles/<?php echo htmlspecialchars($fin['photo']); ?>" alt="<?php echo htmlspecialchars($fin['position']); ?>">
                        <h3><?php echo htmlspecialchars($fin['name']); ?></h3>
                        <p><?php echo htmlspecialchars($fin['position']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <!-- PRO (Order 8) -->
            <?php
            $pro = array_filter($executives, fn($o) => stripos($o['position'], 'P.R.O') !== false || stripos($o['position'], 'PRO') !== false);
            if (!empty($pro)):
            ?>
            <div class="officer-row grid-1">
                <?php foreach ($pro as $p): ?>
                    <div class="card">
                        <img src="../assets/profiles/<?php echo htmlspecialchars($p['photo']); ?>" alt="<?php echo htmlspecialchars($p['position']); ?>">
                        <h3><?php echo htmlspecialchars($p['name']); ?></h3>
                        <p><?php echo htmlspecialchars($p['position']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <!-- Governors -->
            <?php if (!empty($governors)): ?>
            <h3 class="section-title">Year Level Governors</h3>
            <div class="officer-row grid-3">
                <?php foreach ($governors as $gov): ?>
                    <div class="card">
                        <img src="../assets/profiles/<?php echo htmlspecialchars($gov['photo']); ?>" alt="<?php echo htmlspecialchars($gov['position']); ?>">
                        <h3><?php echo htmlspecialchars($gov['name']); ?></h3>
                        <p><?php echo htmlspecialchars($gov['position']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <!-- Directors -->
            <?php if (!empty($directors)): ?>
            <h3 class="section-title">Board of Directors</h3>
            <div class="officer-row grid-4">
                <?php foreach ($directors as $director): ?>
                    <div class="card">
                        <img src="../assets/profiles/<?php echo htmlspecialchars($director['photo']); ?>" alt="<?php echo htmlspecialchars($director['position']); ?>">
                        <h3><?php echo htmlspecialchars($director['name']); ?></h3>
                        <p><?php echo htmlspecialchars($director['position']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <!-- Members -->
            <?php if (!empty($members)): ?>
            <h3 class="section-title">Active Members</h3>
            <div class="officer-row grid-5">
                <?php foreach ($members as $member): ?>
                    <div class="card">
                        <img src="../assets/profiles/<?php echo htmlspecialchars($member['photo']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>">
                        <h3><?php echo htmlspecialchars($member['name']); ?></h3>
                        <p><?php echo htmlspecialchars($member['position']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
        <?php else: ?>
            <p style="text-align: center; padding: 40px; color: #7f8c8d;">
                No officers information available at this time.
            </p>
        <?php endif; ?>

    </div>
</section>

<footer>
    <p><strong>JPCS Malvar Chapter</strong></p>
    <p>Batangas State University TNEU - JPLPC Malvar</p>
    <p>Email: jpcs.malvar@g.batstate-u.edu.ph</p>
</footer>

<?php include '../includes/tawk_chat.php'; ?>

<script src="../js/script.js"></script>
</body>
</html>
