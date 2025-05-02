<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// 基础配置
define('UPLOAD_DIR', '/a/a/a/a/tuchuang/uploads/'); //填入自己的服务器路径
define('DATA_DIR', '/a/a/a/a/tuchuang/data/'); //填入自己的服务器路径
define('MAX_GUEST_UPLOAD', 10);
define('AUTH_CODE', 'your keys'); //填入自己的注册密钥

// 自动创建必要目录
$dirs = [UPLOAD_DIR, DATA_DIR];
foreach ($dirs as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
        file_put_contents($dir . '.htaccess', 'Deny from all'); // 禁止直接访问
    }
}

// 初始化数据文件
$data_files = [
    'users' => [],
    'images' => [],
    'comments' => []
];

foreach ($data_files as $file => $default) {
    $path = DATA_DIR . $file . '.json';
    if (!file_exists($path)) {
        file_put_contents($path, json_encode($default));
        chmod($path, 0644);
    }
}
?>