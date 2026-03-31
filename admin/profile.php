<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include '../config.php';
$msg = '';
$admin_id = $_SESSION['admin_id'];

// 获取当前信息
$stmt = $conn->prepare("SELECT username, password FROM admin WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = trim($_POST['username']);
    $old_password = trim($_POST['old_pwd']);
    $new_password = trim($_POST['new_pwd']);

    // MD5验证原密码
    if (md5($old_password) !== $admin['password']) {
        $msg = "<p style='color:red'>❌ 原密码错误</p>";
    } else {
        // 更新用户名
        $stmt = $conn->prepare("UPDATE admin SET username = ? WHERE id = ?");
        $stmt->bind_param("si", $new_username, $admin_id);
        $stmt->execute();

        // 更新密码（MD5加密）
        if (!empty($new_password)) {
            $pwd_md5 = md5($new_password);
            $stmt = $conn->prepare("UPDATE admin SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $pwd_md5, $admin_id);
            $stmt->execute();
        }

        // 强制退出，新账号密码登录
        session_destroy();
        echo "<p style='color:green'>✅ 修改成功！即将跳转到登录页</p>";
        echo "<script>setTimeout(()=>location.href='login.php',1500)</script>";
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>修改资料</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}
        body{background:#f5f7fa;display:flex;}
        .sidebar{width:250px;background:#2c3e50;height:100vh;position:fixed;color:#fff;padding:20px 0;}
        .sidebar h3{text-align:center;padding:20px 0;border-bottom:1px solid #34495e;margin-bottom:20px;}
        .sidebar a{display:block;padding:15px 25px;color:#ecf0f1;text-decoration:none;transition:0.3s;}
        .sidebar a:hover,.sidebar a.active{background:#3498db;border-left:4px solid #fff;}
        .main{flex:1;margin-left:250px;padding:30px;}
        .card{background:#fff;padding:30px;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,0.1);max-width:600px;}
        .form-group{margin-bottom:20px;}
        label{display:block;margin-bottom:8px;color:#333;font-weight:50px;}
        input{width:100%;padding:12px 15px;border:2px solid #eee;border-radius:8px;font-size:16px;}
        .btn{padding:12px 30px;background:#3498db;color:#fff;border:none;border-radius:8px;cursor:pointer;}
    </style>
</head>
<body>
    <div class="sidebar">
        <h3>管理后台</h3>
        <a href="index.php">控制台</a>
        <a href="config.php">网站配置</a>
        <a href="users.php">用户管理</a>
        <a href="profile.php" class="active">修改资料</a>
        <a href="logout.php">退出登录</a>
    </div>
    <div class="main">
        <div class="card">
            <h2>修改管理员资料</h2>
            <br><?=$msg?>
            <form method="post">
                <div class="form-group">
                    <label>用户名</label>
                    <input type="text" name="username" value="<?=$admin['username']?>" required>
                </div>
                <div class="form-group">
                    <label>原密码（必填）</label>
                    <input type="password" name="old_pwd" placeholder="请输入原密码" required>
                </div>
                <div class="form-group">
                    <label>新密码（不填不修改）</label>
                    <input type="password" name="new_pwd" placeholder="留空不修改">
                </div>
                <button type="submit" class="btn">保存修改</button>
            </form>
        </div>
    </div>
</body>
</html>