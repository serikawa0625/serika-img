<?php
function require_admin() {
    if (!is_admin()) {
        $_SESSION['error'] = '管理员权限不足';
        header('Location: index.php');
        exit;
    }
}

function require_login() {
    if (!isset($_SESSION['user'])) {
        $_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
        header('Location: login.php');
        exit;
    }
}
?>