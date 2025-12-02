<?php $user = getCurrentUser(); ?>
<div class="topbar">
    <h1><?php echo ucwords(str_replace('.php', '', basename($_SERVER['PHP_SELF']))); ?></h1>
    <div class="user-info">
        <span style="color: #7f8c8d;">
            <strong><?php echo htmlspecialchars($user['name']); ?></strong><br>
            <small><?php echo htmlspecialchars($user['role']); ?></small>
        </span>
        <a href="../handlers/logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
</div>
