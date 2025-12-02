<?php
require_once '../config.php';
require_once '../includes/db_helper.php';

// Get all gallery items from database
$galleryItems = getAllGalleryItems();

// Get unique categories
$categories = ['All'];
foreach ($galleryItems as $item) {
    $cat = $item['category'] ?? 'Other';
    if (!in_array($cat, $categories)) {
        $categories[] = $cat;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery - JPCS Malvar</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/pages.css">
    <link rel="stylesheet" href="../css/gallery.css">
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

<section class="gallery-section">
    <h1 class="anton-font">Photo Gallery</h1>
    <p class="gallery-intro">
        Relive our memorable moments through these photos from our events and activities.
    </p>

    <div class="gallery-categories">
        <?php foreach ($categories as $cat): ?>
            <button class="category-btn <?php echo $cat === 'All' ? 'active' : ''; ?>" data-category="<?php echo htmlspecialchars(strtolower(str_replace(' ', '-', $cat))); ?>">
                <?php echo htmlspecialchars($cat); ?>
            </button>
        <?php endforeach; ?>
    </div>

    <div class="gallery-grid">
        <?php if (!empty($galleryItems)): ?>
            <?php foreach ($galleryItems as $item): ?>
                <div class="gallery-item" data-category="<?php echo htmlspecialchars(strtolower(str_replace(' ', '-', $item['category'] ?? 'other'))); ?>">
                    <div class="gallery-image">
                        <?php if (!empty($item['image'])): ?>
                            <img src="../assets/uploads/gallery/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title'] ?? 'Gallery Image'); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <?php
                            $icon = '📸';
                            $category = strtolower($item['category'] ?? '');
                            if (strpos($category, 'event') !== false) $icon = '🎤';
                            elseif (strpos($category, 'workshop') !== false) $icon = '💻';
                            elseif (strpos($category, 'outreach') !== false) $icon = '🤝';
                            elseif (strpos($category, 'team') !== false) $icon = '🎉';
                            elseif (strpos($category, 'hackathon') !== false) $icon = '🏆';
                            ?>
                            <?php echo $icon; ?>
                        <?php endif; ?>
                    </div>
                    <div class="gallery-info">
                        <h3><?php echo htmlspecialchars($item['title'] ?? 'Untitled'); ?></h3>
                        <p><?php echo htmlspecialchars($item['description'] ?? ''); ?></p>
                        <p class="gallery-date">
                            <?php 
                            if (!empty($item['uploaded_date'])) {
                                echo date('F Y', strtotime($item['uploaded_date']));
                            } elseif (!empty($item['date'])) {
                                echo date('F Y', strtotime($item['date']));
                            }
                            ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Default placeholder items when database is empty -->
            <div class="gallery-item" data-category="events">
                <div class="gallery-image">📸</div>
                <div class="gallery-info">
                    <h3>Tech Summit 2025</h3>
                    <p>Highlights from our Annual Tech Summit featuring keynote speakers and networking sessions.</p>
                    <p class="gallery-date">March 2025</p>
                </div>
            </div>

            <div class="gallery-item" data-category="workshops">
                <div class="gallery-image">💻</div>
                <div class="gallery-info">
                    <h3>Web Dev Workshop</h3>
                    <p>Participants learning modern web development techniques with hands-on exercises.</p>
                    <p class="gallery-date">February 2025</p>
                </div>
            </div>

            <div class="gallery-item" data-category="outreach">
                <div class="gallery-image">🤝</div>
                <div class="gallery-info">
                    <h3>Community Outreach</h3>
                    <p>Teaching computer basics to students in underserved communities.</p>
                    <p class="gallery-date">January 2025</p>
                </div>
            </div>

            <div class="gallery-item" data-category="team-building">
                <div class="gallery-image">🎉</div>
                <div class="gallery-info">
                    <h3>Team Building 2025</h3>
                    <p>Fun activities and bonding moments with JPCS members.</p>
                    <p class="gallery-date">December 2024</p>
                </div>
            </div>

            <div class="gallery-item" data-category="events">
                <div class="gallery-image">🏆</div>
                <div class="gallery-info">
                    <h3>Hackathon Winners</h3>
                    <p>Celebrating the winners of our 48-hour coding challenge.</p>
                    <p class="gallery-date">November 2024</p>
                </div>
            </div>

            <div class="gallery-item" data-category="workshops">
                <div class="gallery-image">🐍</div>
                <div class="gallery-info">
                    <h3>Python Workshop</h3>
                    <p>Introduction to Python programming for beginners.</p>
                    <p class="gallery-date">October 2024</p>
                </div>
            </div>

            <div class="gallery-item" data-category="events">
                <div class="gallery-image">🎤</div>
                <div class="gallery-info">
                    <h3>Industry Talks</h3>
                    <p>Guest speakers sharing insights about careers in tech.</p>
                    <p class="gallery-date">September 2024</p>
                </div>
            </div>

            <div class="gallery-item" data-category="team-building">
                <div class="gallery-image">⛺</div>
                <div class="gallery-info">
                    <h3>Outdoor Adventure</h3>
                    <p>Team camping and outdoor activities for JPCS members.</p>
                    <p class="gallery-date">August 2024</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<footer>
    <p>&copy; 2025 JPCS Malvar Chapter. All Rights Reserved.</p>
</footer>

<script src="../js/script.js"></script>
<script>
    // Gallery category filter
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const category = this.getAttribute('data-category');
            document.querySelectorAll('.gallery-item').forEach(item => {
                if (category === 'all' || item.getAttribute('data-category') === category) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
</script>
</body>
</html>
