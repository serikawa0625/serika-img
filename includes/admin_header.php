<?php require_admin(); ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理后台 - 樱花图床</title>
    <link rel="stylesheet" href="../assets/css/sakura.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* 后台专用样式 */
        :root {
            --admin-primary: #ff7e9d;
            --admin-bg: #f8f9fa;
        }

        .admin-container {
            display: grid;
            grid-template-columns: 220px 1fr;
            min-height: 100vh;
        }

        .admin-sidebar {
            background: #fff;
            box-shadow: 2px 0 8px rgba(0,0,0,0.1);
            padding: 1.5rem;
        }

        .admin-nav li {
            margin: 0.8rem 0;
        }

        .admin-nav a {
            color: #666;
            padding: 0.8rem;
            border-radius: 8px;
            display: block;
            transition: all 0.3s;
        }

        .admin-nav a:hover {
            background: var(--admin-primary);
            color: white;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <aside class="admin-sidebar">
            <h2 style="margin-bottom:2rem;">🌸 管理面板</h2>
            <nav>
                <ul class="admin-nav">
                    <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> 仪表盘</a></li>
                    <li><a href="images.php"><i class="fas fa-image"></i> 图片管理</a></li>
                    <li><a href="#"><i class="fas fa-users"></i> 用户管理</a></li>
                    <li><a href="#"><i class="fas fa-cog"></i> 系统设置</a></li>
                </ul>
            </nav>
        </aside>
        <main class="admin-main">