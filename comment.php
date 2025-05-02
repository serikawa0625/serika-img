<?php
$page_title = "自定义标题";
require_once 'includes/config.php';
require_once 'includes/functions.php';
define('IN_SCRIPT', true);
require_once 'includes/header.php'; 

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

try {
    // 验证CSRF令牌
    if (!verify_csrf_token($_POST['csrf_token'])) {
        throw new Exception('CSRF验证失败');
    }

    // 获取用户信息
    $nickname = '游客';
    $avatar_url = '';
    if (isset($_SESSION['user'])) {
        $nickname = $_SESSION['user']['nickname'] ?? $_SESSION['user']['username'];
        $avatar_url = $_SESSION['user']['avatar_url'];
    } elseif (isset($_SESSION['guest_profile'])) {
        $nickname = $_SESSION['guest_profile']['nickname'] ?? '游客';
        $avatar_url = $_SESSION['guest_profile']['avatar_url'] ?? '';
    }

    // 验证评论内容
    $content = trim($_POST['content']);
    if (empty($content) || mb_strlen($content) > 200) {
        throw new Exception('评论内容需在1-200字之间');
    }

    // 保存评论
    $stmt = $pdo->prepare("
        INSERT INTO comments 
        (image_id, user_id, content, nickname, avatar_url)
        VALUES (?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $_POST['image_id'],
        isset($_SESSION['user']) ? $_SESSION['user']['id'] : null,
        $content,
        $nickname,
        $avatar_url
    ]);

    header("Location: index.php?page=" . $_POST['page']);
} catch (Exception $e) {
    $_SESSION['comment_error'] = $e->getMessage();
    header("Location: index.php?page=" . $_POST['page']);
}
exit;