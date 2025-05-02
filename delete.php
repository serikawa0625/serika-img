<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('IN_SCRIPT', true);
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

// 删除操作核心逻辑
try {
    // 验证 Token 是否存在
    if (!isset($_GET['token']) || empty($_GET['token'])) {
        throw new Exception("无效的删除请求：缺少 token 参数");
    }
    $token = sanitize($_GET['token']);

    // 加载图片数据
    $images = get_data('images');
    $found = false;
    
    foreach ($images as $index => $image) {
        if ($image['delete_token'] === $token) {
            $found = true;
            
            // 验证文件路径安全
            $file_path = UPLOAD_DIR . '/' . $image['filename'];
            if (!file_exists($file_path)) {
                throw new Exception("文件不存在或已被删除");
            }

            // 执行删除操作
            if (!unlink($file_path)) {
                throw new Exception("文件删除失败，请检查权限");
            }

            // 从数据中移除记录
            array_splice($images, $index, 1);
            save_data('images', $images);

            // 记录删除日志
            error_log("[DELETE] 文件 {$image['filename']} 已删除，操作IP: {$_SERVER['REMOTE_ADDR']}");

            // 显示成功消息
            show_alert("success", "文件删除成功");
            break;
        }
    }

    if (!$found) {
        throw new Exception("无效的删除令牌或文件已被删除");
    }

} catch (Exception $e) {
    // 错误处理
    error_log("[DELETE ERROR] " . $e->getMessage());
    show_alert("error", $e->getMessage());
}

// 公共提示函数
function show_alert($type, $message) {
    $alert_class = $type === 'success' ? 
        'sakura-alert-success' : 'sakura-alert-error';
    ?>
    <div class="sakura-container" style="min-height: 70vh">
        <div class="sakura-alert <?= $alert_class ?>" style="max-width:600px;margin:2rem auto">
            <h3><?= ucfirst($type) ?>!</h3>
            <p><?= $message ?></p>
            <div style="margin-top:1.5rem">
                <a href="/index.php" class="sakura-button">返回图库</a>
                <a href="/upload.php" class="sakura-button">继续上传</a>
            </div>
        </div>
    </div>
    <?php
}

require_once 'includes/footer.php';