<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 获取图片数据
$all_images = get_data('images');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>图片广场 - serikaのblog图床</title> //改为自己网站标题
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/sakura.css">
    <style>
        :root {
            --primary-color: #ff7e9d;
            --card-bg: rgba(255, 255, 255, 0.95);
        }

        .gallery-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            padding: 1rem 0;
        }

        .image-card {
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .image-card:hover {
            transform: translateY(-5px);
        }

        .image-header {
            display: flex;
            align-items: center;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.03);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 0.8rem;
        }

        .user-nickname {
            font-weight: 500;
            color: var(--primary-color);
        }

        .thumbnail {
            width: 100%;
            height: 300px;
            object-fit: cover;
            cursor: zoom-in;
            transition: transform 0.3s ease;
        }

        .image-meta {
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            color: #666;
            font-size: 0.9rem;
        }

        .delete-btn {
            margin-left: auto;
            color: #ff4d4d;
            cursor: pointer;
            padding: 0.3rem 0.5rem;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .delete-btn:hover {
            background: rgba(255, 77, 77, 0.1);
        }

        .empty-tip {
            text-align: center;
            padding: 4rem 2rem;
            background: var(--card-bg);
            border-radius: 12px;
            margin: 2rem 0;
        }

        .upload-promote {
            text-align: center;
            margin: 2rem 0;
        }

        @media (max-width: 768px) {
            .image-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="gallery-container">
        <div class="upload-promote">
            <a href="upload.php" class="sakura-button">
                <i class="fas fa-cloud-upload-alt"></i>
                立即上传
            </a>
        </div>

        // 修改图片展示部分
<div class="image-grid">
    <?php if (!empty($all_images)): ?>
        <?php foreach (array_reverse($all_images) as $img): 
            // 确保文件存在
            $file_path = UPLOAD_DIR . $img['filename'];
            if (!file_exists($file_path)) continue;
        ?>
        <div class="image-card">
                    <div class="image-header">
                        <img src="<?= htmlspecialchars($img['avatar'] ?? 'assets/images/default-avatar.png') ?>" 
                             class="user-avatar" 
                             alt="用户头像">
                        <span class="user-nickname">
                            <?= htmlspecialchars($img['nickname'] ?? '匿名用户') ?>
                        </span>
                        <?php if (can_delete($img)): ?>
                        <a href="delete.php?token=<?= urlencode($img['delete_token'] ?? '') ?>" 
                           class="delete-btn" 
                           title="删除图片"
                           onclick="return confirm('确定删除这张图片吗？')">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                    <a href="uploads/<?= htmlspecialchars($img['filename']) ?>" target="_blank">
                        <img src="uploads/<?= htmlspecialchars($img['filename']) ?>" 
                             class="thumbnail"
                             alt="用户上传图片"
                             loading="lazy">
                    </a>
                    <div class="image-meta">
                        <span>
                            <i class="fas fa-clock"></i>
                            <?= date('Y-m-d H:i', $img['upload_time'] ?? time()) ?>
                        </span>
                        <span>
                            <i class="fas fa-eye"></i>
                            <?= number_format($img['views'] ?? 0) ?>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-tip">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">😢</div>
                    <h2>还没有人上传图片</h2>
                    <p>点击上方按钮，成为第一个分享者吧！</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script>
        // 图片悬停放大效果
        document.querySelectorAll('.thumbnail').forEach(img => {
            img.addEventListener('mouseenter', () => {
                img.style.transform = 'scale(1.05)';
            });
            img.addEventListener('mouseleave', () => {
                img.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>