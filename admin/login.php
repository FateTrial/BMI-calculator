<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
include '../config.php';

$msg = $_SESSION['msg'] ?? '';
unset($_SESSION['msg']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $code     = trim($_POST['code']);
    $code_fix = trim($_POST['code_fix']);

    // MD5加密密码
    $password_md5 = md5($password);

    if (empty($username) || empty($password) || empty($code)) {
        $_SESSION['msg'] = "❌ 所有字段均为必填！";
    } elseif ($code !== $code_fix) {
        $_SESSION['msg'] = "❌ 验证码错误！";
    } else {
        // MD5查询数据库
        $stmt = $conn->prepare("SELECT id, username, password FROM admin WHERE username = ? AND password = ? LIMIT 1");
        $stmt->bind_param("ss", $username, $password_md5);
        $stmt->execute();
        $admin = $stmt->get_result()->fetch_assoc();

        if ($admin) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['username'];
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['msg'] = "❌ 用户名或密码错误！";
        }
    }
    header("Location: login.php");
    exit();
}

$code = rand(100000, 999999);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理员登录</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}
        body{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);height:100vh;display:flex;align-items:center;justify-content:center;}
        .login-card{width:450px;background:#fff;border-radius:16px;box-shadow:0 10px 40px rgba(0,0,0,0.2);padding:50px 40px;}
        .login-card h2{text-align:center;color:#333;margin-bottom:30px;font-size:28px;}
        .form-group{margin-bottom:24px;}
        .form-control{width:100%;padding:14px 18px;border:2px solid #eee;border-radius:10px;font-size:16px;transition:0.3s;}
        .form-control:focus{border-color:#667eea;outline:none;}
        .code-group{display:flex;gap:12px;}
        .code-btn{width:140px;background:#f8f9fa;border:2px solid #eee;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:bold;cursor:pointer;user-select:none;}
        .btn-primary{width:100%;padding:15px;background:#667eea;color:#fff;border:none;border-radius:10px;font-size:18px;font-weight:bold;cursor:pointer;transition:0.3s;margin-top:10px;}
        .btn-primary:hover{background:#5a6edb;}
        .error{color:#dc3545;text-align:center;margin-bottom:20px;font-size:15px;}
    </style>
</head>
<body>
    <div class="login-card">
        <h2>🔒 管理员登录</h2>
        <?php if($msg):?><p class="error"><?=$msg?></p><?php endif;?>
        <form method="post" autocomplete="off">
            <div class="form-group">
                <input type="text" class="form-control" name="username" placeholder="管理员用户名" required>
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="登录密码" required>
            </div>
            <div class="form-group code-group">
                <input type="text" class="form-control" name="code" placeholder="6位数字验证码" required>
                <div class="code-btn" onclick="location.reload()"><?=$code?></div>
                <input type="hidden" name="code_fix" value="<?=$code?>">
            </div>
            <button type="submit" class="btn-primary">登 录</button>
        </form>
    </div>
</body>
</html>