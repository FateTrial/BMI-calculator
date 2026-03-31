<?php
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="3;url=index.php">
    <title>登出成功</title>
    <style>
        * {margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}
        body {background:#f5f7fa;display:flex;justify-content:center;align-items:center;height:100vh;padding:20px;}
        .card {background:#fff;border-radius:12px;padding:40px 30px;box-shadow:0 3px 20px rgba(0,0,0,0.1);text-align:center;max-width:400px;width:100%;}
        .success-icon {font-size:50px;color:#4CAF50;margin-bottom:20px;}
        h2 {color:#333;margin-bottom:15px;}
        p {color:#666;margin-bottom:25px;font-size:16px;}
        .countdown {color:#ff9800;font-weight:bold;}
        .btn {display:inline-block;padding:12px 25px;background:#2196F3;color:white;text-decoration:none;border-radius:8px;}
    </style>
</head>
<body>
    <div class="card">
        <div class="success-icon">✅</div>
        <h2>登出成功</h2>
        <p>您已安全退出账号，<span class="countdown">3</span> 秒后自动返回主页...</p>
        <a href="index.php" class="btn">立即返回</a>
    </div>

    <script>
        let seconds = 3;
        const countdownEl = document.querySelector('.countdown');
        setInterval(() => {
            seconds--;
            countdownEl.textContent = seconds;
            if(seconds <=0) window.location.href='index.php';
        },1000);
    </script>
</body>
</html>