<?php
require_once 'config.php';
require_once 'functions.php';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title : '芹香图床' ?></title>
    <link rel="stylesheet" href="assets/css/sakura.css">
    <link rel="stylesheet" href="assets/css/gallery.css">
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
</head>
<body>
    <header class="sakura-header">
        <nav class="navbar sakura-container">
            <div class="logo">
                <a href="index.php">🌸 芹香图床</a>
            </div>
            
            <div class="nav-links">
                <a href="index.php" class="nav-item">首页</a>
                <a href="upload.php" class="nav-item">上传</a>
                
                <?php if (isset($_SESSION['user'])): ?>
                    <?php if (is_admin()): ?>
                        <a href="admin/dashboard.php" class="nav-item">后台</a>
                    <?php endif; ?>
                    <div class="user-profile">
                        <img src="<?= get_avatar() ?>" 
                             class="user-avatar-sm" 
                             alt="<?= $_SESSION['user']['nickname'] ?? $_SESSION['user']['username'] ?>">
                        <span class="user-name">
                            <?= sanitize($_SESSION['user']['nickname'] ?? $_SESSION['user']['username']) ?>
                        </span>
                        <a href="logout.php" class="logout-btn">退出</a>
                    </div>
                <?php else: ?>
                    <?php if (isset($_SESSION['guest_profile'])): ?>
                        <div class="guest-profile">
                            <img src="<?= $_SESSION['guest_profile']['avatar_url'] ?? 'assets/images/default-avatar.png' ?>" 
                                 class="user-avatar-sm" 
                                 alt="游客">
                            <span class="user-name">
                                <?= sanitize($_SESSION['guest_profile']['nickname'] ?? '游客') ?>
                            </span>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="nav-item">登录</a>
                        <a href="register.php" class="nav-item">注册</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <div class="sakura-alert-container">
        <?php if (isset($_SESSION['error_msg'])): ?>
            <div class="sakura-alert sakura-alert-error">
                <?= $_SESSION['error_msg'] ?>
                <span class="close-btn" onclick="this.parentElement.remove()">&times;</span>
            </div>
            <?php unset($_SESSION['error_msg']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success_msg'])): ?>
            <div class="sakura-alert sakura-alert-success">
                <?= $_SESSION['success_msg'] ?>
                <span class="close-btn" onclick="this.parentElement.remove()">&times;</span>
            </div>
            <?php unset($_SESSION['success_msg']); ?>
        <?php endif; ?>
    </div>