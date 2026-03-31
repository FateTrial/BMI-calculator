<?php
include 'config.php';
$error = '';

// 数据库连接检查
if (!$conn) {
    $error = "数据库连接失败！";
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = trim($_POST['username']);
    $pass = trim($_POST['password']);

    // 关键修复：查询所有字段，永无字段报错
    $sql = $conn->prepare("SELECT * FROM users WHERE username=?");
    
    if (!$sql) {
        $error = "数据库错误：".$conn->error;
    } else {
        $sql->bind_param("s", $user);
        $sql->execute();
        $result = $sql->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            // 验证密码
            if (password_verify($pass, $row['password'])) {
                // 安全判断邮箱验证状态
                if (!isset($row['email_verified']) || $row['email_verified'] == 0) {
                    $error = "请先验证邮箱再登录！";
                } else {
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['username'] = $row['username'];
                    header("Location: index.php");
                    exit();
                }
            } else {
                $error = "密码错误！";
            }
        } else {
            $error = "用户名不存在！";
        }
        $sql->close();
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录</title>
    <style>
        *{
            box-sizing:border-box;
            margin:0;
            padding:0;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        body{
            background: linear-gradient(135deg, #f0f4ff 0%, #e8f0fe 100%);
            min-height: 100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            padding:20px;
        }
        .card {
            width:100%;
            max-width:400px;
            background:#ffffff;
            border-radius:16px;
            padding:30px;
            box-shadow:0 3px 15px rgba(0,0,0,0.06);
        }
        .card h2 {
            text-align:center;
            color:#2d3748;
            margin-bottom:25px;
            font-size:24px;
        }
        .form-group {
            margin-bottom:18px;
        }
        input {
            width:100%;
            padding:14px 16px;
            border:1px solid #e2e8f0;
            border-radius:10px;
            font-size:15px;
            outline:none;
            transition:0.2s;
        }
        input:focus {
            border-color:#3b82f6;
            box-shadow:0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .btn-login {
            width:100%;
            padding:15px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color:white;
            border:none;
            border-radius:10px;
            font-size:16px;
            font-weight:600;
            cursor:pointer;
            transition:0.2s;
        }
        .btn-login:hover {
            opacity:0.9;
        }
        .error {
            color:#ef4444;
            text-align:center;
            margin-bottom:15px;
            line-height:1.6;
            font-weight:500;
        }
        .link {
            text-align:center;
            margin-top:15px;
            color:#64748b;
        }
        .link a {
            color:#3b82f6;
            text-decoration:none;
            font-weight:500;
            transition:0.2s;
        }
        .link a:hover {
            color:#1d4ed8;
        }
        /* 响应式适配 */
        @media (max-width: 768px) {
            .card{
                padding:20px;
            }
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>🔐 用户登录</h2>
        <?php if($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="post">
            <div class="form-group">
                <input type="text" name="username" placeholder="用户名" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="密码" required>
            </div>
            <button type="submit" class="btn-login">登录</button>
        </form>
        <div class="link">没有账号？<a href="register.php">立即注册</a></div>
        <div class="link"><a href="index.php">返回计算器</a></div>
    </div>
</body>
</html>