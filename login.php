<?php
// login.php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// 已登录用户重定向
if (isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 验证CSRF令牌
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error = 'CSRF验证失败，请重试';
    } else {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        try {
            // 获取用户数据（带文件锁）
            $users = get_data('users');
            
            // 查找匹配用户
            $found_user = null;
            foreach ($users as $user) {
                if ($user['username'] === $username) {
                    $found_user = $user;
                    break;
                }
            }

            if ($found_user && password_verify($password, $found_user['password'])) {
                // 更新最后登录时间
                $found_user['last_login'] = time();
                foreach ($users as &$u) {
                    if ($u['id'] === $found_user['id']) {
                        $u = $found_user;
                        break;
                    }
                }
                save_data('users', $users);

                // 设置会话信息
                $_SESSION['user'] = $found_user;
                
                // 重定向到来源页面或首页
                $redirect = $_GET['redirect'] ?? 'index.php';
                header("Location: $redirect");
                exit;
            } else {
                $error = '用户名或密码错误';
            }
        } catch (Exception $e) {
            $error = '登录失败：' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录 - 樱花图床</title>
    <link rel="stylesheet" href="assets/css/sakura.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 5rem auto;
            padding: 2.5rem;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        .login-title {
            text-align: center;
            margin-bottom: 2rem;
            color: #ff7e9d;
            font-size: 1.8rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: #666;
        }

        .form-input {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            transition: border-color 0.3s ease;
        }

        .form-input:focus {
            border-color: #ff7e9d;
            outline: none;
        }

        .submit-btn {
            width: 100%;
            padding: 0.8rem;
            background: #ff7e9d;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: opacity 0.3s;
        }

        .submit-btn:hover {
            opacity: 0.9;
        }

        .error-message {
            color: #ff4d4d;
            margin: 1rem 0;
            text-align: center;
        }

        .links {
            margin-top: 1.5rem;
            text-align: center;
        }

        .links a {
            color: #ff7e9d;
            text-decoration: none;
            transition: opacity 0.3s;
        }

        .links a:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="login-container">
        <h1 class="login-title">🌸 欢迎回来</h1>

        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
            
            <div class="form-group">
                <label class="form-label">用户名</label>
                <input type="text" 
                       name="username" 
                       class="form-input"
                       value="<?= htmlspecialchars($username) ?>"
                       required
                       autofocus>
            </div>

            <div class="form-group">
                <label class="form-label">密码</label>
                <input type="password" 
                       name="password" 
                       class="form-input"
                       required>
            </div>

            <button type="submit" class="submit-btn">立即登录</button>

            <div class="links">
                <a href="register.php">注册新账号</a>
                <span style="margin: 0 0.5rem">|</span>
                <a href="forgot-password.php">忘记密码？</a>
            </div>
        </form>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>