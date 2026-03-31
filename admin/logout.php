<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>退出成功</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}
        body{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);height:100vh;display:flex;align-items:center;justify-content:center;}
        .box{text-align:center;background:#fff;padding:50px;border-radius:16px;box-shadow:0 10px 40px rgba(0,0,0,0.2);}
        h2{color:#333;margin-bottom:20px;}
        p{color:#666;margin-bottom:30px;}
        a{padding:12px 30px;background:#667eea;color:#fff;text-decoration:none;border-radius:8px;}
    </style>
</head>
<body>
    <div class="box">
        <h2>✅ 退出登录成功</h2>
        <p>您已安全退出管理员后台</p>
        <a href="login.php">返回登录页</a>
    </div>
</body>
</html>