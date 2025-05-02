<?php
// 开启严格错误报告
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 初始化输出缓冲
ob_start();

$page_title = "上传入口";
define('IN_SCRIPT', true);
require_once 'includes/config.php';
require_once 'includes/functions.php';

// 确保header.php没有输出
require_once 'includes/header.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // 清理缓冲区确保无额外输出
        ob_end_clean();
        header('Content-Type: application/json');

        // ---- 上传验证逻辑 Start ----
        $ip = $_SERVER['REMOTE_ADDR'];
        
        // 游客上传限制
        if (!isset($_SESSION['user'])) {
            $today_uploads = count(get_today_uploads($ip));
            if ($today_uploads >= MAX_GUEST_UPLOAD) {
                die(json_encode(['error' => '今日上传已达上限']));
            }
        }

        $file = $_FILES['image'];
        
        // 文件大小验证
        if ($file['size'] > 5 * 1024 * 1024) {
            throw new Exception("文件超过5MB限制");
        }

        // 文件类型验证
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif'];
        
        // 获取文件信息
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        // 双重验证
        if (!in_array($extension, $allowed_extensions)) {
            throw new Exception("不支持的文件格式，仅允许 JPG/PNG/GIF");
        }
        if (!in_array($mime_type, $allowed_mime_types)) {
            throw new Exception("文件类型不合法，检测到：{$mime_type}");
        }

        // 生成安全文件名
        $filename = uniqid() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
        $filename = preg_replace('/[^a-zA-Z0-9\-_.]/', '', $filename);

        // 确保上传目录存在
        if (!is_dir(UPLOAD_DIR)) {
            if (!mkdir(UPLOAD_DIR, 0755, true)) {
                throw new Exception("无法创建上传目录");
            }
        }

        $dest = rtrim(UPLOAD_DIR, '/') . '/' . $filename;

        // 移动文件
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            $lastError = error_get_last();
            throw new Exception("文件保存失败: " . ($lastError['message'] ?? '未知错误'));
        }

        // 保存元数据
        $image_data = [
            'id' => uniqid(),
            'filename' => $filename,
            'user_id' => $_SESSION['user']['id'] ?? null,
            'ip' => $ip,
            'upload_time' => time(),
            'delete_token' => generate_token(),
            'nickname' => $_SESSION['user']['nickname'] ?? '游客',
            'avatar' => $_SESSION['user']['avatar'] ?? ($_SESSION['guest_profile']['avatar_url'] ?? '')
        ];

        $images = get_data('images');
        $images[] = $image_data;
        save_data('images', $images);

        // 返回结果
        echo json_encode([
            'status' => 'success',
            'url' => "uploads/" . rawurlencode($filename),
            'delete_url' => "delete.php?token={$image_data['delete_token']}"
        ]);
        exit;

    } catch (Exception $e) {
        ob_end_clean();
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage(),
            'debug' => [
                'filename' => $filename ?? '未生成',
                'dest_path' => $dest ?? '未定义',
                'upload_dir' => UPLOAD_DIR
            ]
        ]);
        exit;
    }
}

// ---- HTML部分 Start ----
ob_end_flush();
?>
<!DOCTYPE html>
<html>
<head>
    <title>上传图片</title>
    <link rel="stylesheet" href="assets/css/sakura.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="upload-container">
        <h2>上传图片</h2>
        <form id="upload-form" method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
            
            <div class="upload-area">
                <input type="file" name="image" id="file-input" accept="image/*" required>
                <label for="file-input" class="upload-label">
                    <div class="upload-icon">📤</div>
                    <p>点击选择或拖放文件到此区域</p>
                    <p class="tip">支持 JPG/PNG/GIF 格式，最大 5MB</p>
                </label>
            </div>
            
            <button type="submit" class="sakura-button">开始上传</button>
        </form>
        
        <div id="preview"></div>
        <div id="upload-result"></div>
    </div>

    <script>
    document.getElementById('upload-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                return response.text().then(text => {
                    throw new Error(`服务器返回非JSON数据: ${text.substring(0, 100)}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                const resultHtml = `
                    <div class="sakura-alert sakura-alert-success">
                        <p>上传成功！图片地址：</p>
                        <input value="${location.origin}/${data.url}" readonly>
                        <img src="${location.origin}/${data.url}" style="max-width:200px;display:block;margin:10px 0;">
                        <p>删除链接：<a href="${data.delete_url}">${data.delete_url}</a></p>
                    </div>
                `;
                document.getElementById('upload-result').innerHTML = resultHtml;
            } else {
                const errorMsg = data.debug ? `${data.message}<br>调试信息：${JSON.stringify(data.debug)}` : data.message;
                document.getElementById('upload-result').innerHTML = `
                    <div class="sakura-alert sakura-alert-error">${errorMsg}</div>
                `;
            }
        })
        .catch(error => {
            document.getElementById('upload-result').innerHTML = `
                <div class="sakura-alert sakura-alert-error">
                    请求失败：${error.message}
                </div>
            `;
        });
    });
    </script>

    <?php require_once 'includes/footer.php'; ?>
</body>
</html>