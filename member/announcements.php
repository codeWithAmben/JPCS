<?php
require_once '../config.php';
requireLogin();

$user = getCurrentUser();

// Get all announcements
$allAnnouncements = getAllAnnouncements();

// Filter and search
$category = $_GET['category'] ?? 'all';
$search = $_GET['search'] ?? '';

$filteredAnnouncements = array_filter($allAnnouncements, function($announcement) use ($category, $search) {
    // Category filter
    if ($category !== 'all' && $announcement['category'] !== $category) {
        return false;
    }
    
    // Search filter
    if ($search && stripos($announcement['title'], $search) === false && stripos($announcement['content'], $search) === false) {
        return false;
    }
    
    return true;
});

// Sort by date (newest first)
usort($filteredAnnouncements, function($a, $b) {
    return strtotime($b['posted_date']) - strtotime($a['posted_date']);
});

// Get unique categories
$categories = array_unique(array_column($allAnnouncements, 'category'));
sort($categories);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/member.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <div class="dashboard">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="top-bar">
                <div class="welcome-section">
                    <div class="welcome-text">
                        <h1>
                            <i data-lucide="megaphone"></i>
                            Announcements
                        </h1>
                        <p>Stay updated with the latest JPCS announcements</p>
                    </div>
                    <div class="user-actions">
                        <a href="../handlers/logout.php" class="btn-logout">
                            <i data-lucide="log-out"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
            
            <?php displayFlash(); ?>
            
            <!-- Filters and Search -->
            <div class="filters-section">
                <form method="GET" class="filters-form">
                    <div style="flex: 1; min-width: 250px;">
                        <input type="text" name="search" class="form-control" placeholder="Search announcements..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    
                    <div>
                        <select name="category" class="form-control" onchange="this.form.submit()">
                            <option value="all" <?php echo $category === 'all' ? 'selected' : ''; ?>>All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat); ?>" 
                                        <?php echo $category === $cat ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars(ucfirst($cat)); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="search"></i> Search
                    </button>
                    
                    <?php if ($search || $category !== 'all'): ?>
                    <a href="announcements.php" class="btn" style="background: #95a5a6;">
                        <i data-lucide="x"></i> Clear
                    </a>
                    <?php endif; ?>
                </form>
            </div>
            
            <!-- Announcements List -->
            <div class="section">
                <div class="section-header">
                    <h2>
                        <?php echo $category !== 'all' ? ucfirst($category) . ' ' : ''; ?>Announcements
                        <span style="color: #95a5a6; font-size: 1rem; font-weight: normal;">
                            (<?php echo count($filteredAnnouncements); ?> announcements)
                        </span>
                    </h2>
                </div>
                
                <?php if (empty($filteredAnnouncements)): ?>
                    <p style="text-align: center; color: #95a5a6; padding: 40px;">
                        <i data-lucide="megaphone" style="width: 64px; height: 64px; margin-bottom: 15px;"></i><br>
                        No announcements found.
                    </p>
                <?php else: ?>
                    <div style="display: grid; gap: 20px;">
                        <?php foreach ($filteredAnnouncements as $announcement): 
                            $isNew = strtotime($announcement['posted_date']) > strtotime('-7 days');
                        ?>
                        <div class="announcement-card" style="background: white; padding: 25px; border-radius: 10px; border-left: 4px solid #ff6a00; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: 0.3s;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                                <div style="flex: 1;">
                                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px; flex-wrap: wrap;">
                                        <h3 style="color: #2c3e50; margin: 0;"><?php echo htmlspecialchars($announcement['title']); ?></h3>
                                        
                                        <?php if ($isNew): ?>
                                            <span class="badge" style="background: #3498db;">
                                                <i data-lucide="sparkles" style="width: 12px; height: 12px;"></i> New
                                            </span>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($announcement['badge'])): ?>
                                            <span class="badge badge-active">
                                                <?php echo htmlspecialchars($announcement['badge']); ?>
                                            </span>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($announcement['category'])): ?>
                                        <span class="badge" style="background: #95a5a6;">
                                            <?php echo htmlspecialchars(ucfirst($announcement['category'])); ?>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div style="display: flex; gap: 20px; color: #7f8c8d; font-size: 0.85rem; margin-bottom: 15px;">
                                        <div style="display: flex; align-items: center; gap: 5px;">
                                            <i data-lucide="calendar" style="width: 14px; height: 14px;"></i>
                                            <?php echo formatDate($announcement['posted_date']); ?>
                                        </div>
                                        <?php if (!empty($announcement['author'])): ?>
                                        <div style="display: flex; align-items: center; gap: 5px;">
                                            <i data-lucide="user" style="width: 14px; height: 14px;"></i>
                                            <?php echo htmlspecialchars($announcement['author']); ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div style="color: #555; line-height: 1.6;">
                                <?php echo nl2br(htmlspecialchars($announcement['content'])); ?>
                            </div>
                            
                            <?php if (!empty($announcement['attachment'])): ?>
                            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ecf0f1;">
                                <a href="../<?php echo htmlspecialchars($announcement['attachment']); ?>" 
                                   class="btn" style="background: #3498db;" target="_blank">
                                    <i data-lucide="paperclip"></i> View Attachment
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
    
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
