<?php
$page_title = "自定义标题";
require_once 'includes/config.php';
require_once 'includes/functions.php';
define('IN_SCRIPT', true);
require_once 'includes/header.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 验证授权码
    if ($_POST['auth_code'] !== AUTH_CODE) {
        die("授权码错误");
    }

    // 数据验证
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (strlen($username) < 4 || strlen($password) < 6) {
        die("用户名需4-20位，密码需6位以上");
    }

    // 检查用户是否存在
    $users = get_data('users');
    foreach ($users as $user) {
        if ($user['username'] === $username) {
            die("用户名已存在");
        }
    }

    // 创建新用户
    $new_user = [
        'id' => uniqid(),
        'username' => $username,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'role' => 'user',
        'avatar' => '',
        'nickname' => $username,
        'reg_time' => time()
    ];
    
    // 特殊授权码创建管理员账户
if ($_POST['auth_code'] === 'Your_key') { // 替换为真实管理员授权码(如123456)
    $new_user['role'] = 'admin';
}
    
    $users[] = $new_user;
    save_data('users', $users);
    
    $_SESSION['user'] = $new_user;
    header("Location: index.php");
    exit;
}

if (save_data('users', $users)) {
    // 自动登录处理
    $_SESSION['user'] = $new_user;
    
    // 清除输出缓冲确保header生效
    ob_clean();
    header('Location: index.php?register=success');
    exit;
} else {
    die('<script>alert("用户数据保存失败"); window.history.back();</script>');
}
?>

<!-- 注册表单界面同前 -->