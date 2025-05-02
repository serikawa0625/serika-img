<?php
require_once 'config.php';

// ================== 没能力不要动这个文件============= //
// ================== 数据操作函数 ================== //
function get_data($type) {
    $file = DATA_DIR . $type . '.json';
    if (!file_exists($file)) return [];
    
    $content = file_get_contents($file);
    return json_decode($content, true) ?? [];
}

// 新增数据过滤函数
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function save_data($type, $data) {
    $file = DATA_DIR . $type . '.json';
    $fp = fopen($file, 'w');
    
    if (flock($fp, LOCK_EX)) {
        fwrite($fp, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        flock($fp, LOCK_UN);
    }
    fclose($fp);
}

function safe_get_data($type) {
    $file = DATA_DIR . $type . '.json';
    if (!file_exists($file)) {
        file_put_contents($file, '[]');
        chmod($file, 0644);
    }
    return json_decode(file_get_contents($file), true) ?? [];
}

// ================== 会话管理函数 ================== //
function is_logged_in() {
    return isset($_SESSION['user']);
}

function require_login() {
    if (!is_logged_in()) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header('Location: login.php');
        exit;
    }
}

function get_session_user() {
    return $_SESSION['user'] ?? null;
}

// ================== 安全相关函数 ================== //
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function generate_token() {
    return bin2hex(random_bytes(16));
}

// ================== 权限管理函数 ================== //
function is_admin() {
    return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
}

function require_admin() {
    if (!is_admin()) {
        header('HTTP/1.1 403 Forbidden');
        exit('<h2 style="text-align:center;margin-top:100px;">🔐 管理员权限不足</h2>');
    }
}

// 在权限管理函数区块添加
function get_avatar() {
    if (isset($_SESSION['user'])) {
        return !empty($_SESSION['user']['avatar']) ? 
            $_SESSION['user']['avatar'] : 
            'assets/images/default-avatar.png';
    } elseif (isset($_SESSION['guest_profile'])) {
        return !empty($_SESSION['guest_profile']['avatar_url']) ? 
            $_SESSION['guest_profile']['avatar_url'] : 
            'assets/images/default-avatar.png';
    }
    return 'assets/images/default-avatar.png';
}

function can_delete($image) {
    // 管理员可删除任意图片
    if (is_admin()) return true;
    
    // 普通用户只能删除自己的图片
    return isset($_SESSION['user']['id']) && 
           $_SESSION['user']['id'] === ($image['user_id'] ?? null);
}