<?php
include 'config.php';

if (!isset($_GET['token']) || empty($_GET['token'])) {
    die("无效的验证链接！");
}

$token = trim($_GET['token']);

$query = $conn->prepare("SELECT * FROM users WHERE verify_token=? AND email_verified=0");
$query->bind_param("s", $token);
$query->execute();
$result = $query->get_result();

if ($result->num_rows == 1) {
    $update = $conn->prepare("UPDATE users SET email_verified=1, verify_token=NULL WHERE verify_token=?");
    $update->bind_param("s", $token);
    $update->execute();
    
    $msg = "邮箱验证成功！<br>3秒后自动跳转到登录页...";
    $type = "success";
    $url = "login.php";
} else {
    $msg = "验证链接无效或已激活！";
    $type = "error";
    $url = "register.php";
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="3;url=<?php echo $url; ?>">
    <title>邮箱验证</title>
    <style>
        * {margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}
        body {background:#f5f7fa;display:flex;justify-content:center;align-items:center;height:100vh;padding:20px;}
        .card {max-width:400px;background:#fff;border-radius:12px;padding:40px 30px;box-shadow:0 3px 20px rgba(0,0,0,0.1);text-align:center;}
        .icon {font-size:50px;margin-bottom:20px;}
        .success {color:#4CAF50;}
        .error {color:#f44336;}
        h2 {margin-bottom:15px;color:#333;}
        p {color:#666;margin-bottom:25px;line-height:1.6;}
        .btn {padding:12px 25px;background:#2196F3;color:white;text-decoration:none;border-radius:8px;}
    </style>
</head>
<body>
    <div class="card">
        <div class="icon <?php echo $type; ?>"><?php echo $type=='success' ? '✅' : '❌'; ?></div>
        <h2><?php echo $type=='success' ? '验证成功' : '验证失败'; ?></h2>
        <p><?php echo $msg; ?></p>
        <a href="<?php echo $url; ?>" class="btn">立即跳转</a>
    </div>
</body>
</html>