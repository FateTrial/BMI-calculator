<?php
session_start();
if(!isset($_SESSION['admin_id'])){ header("Location: login.php"); exit(); }
include '../config.php';
$cfg = $conn->query("SELECT * FROM site_config LIMIT 1")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理后台</title>
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}
        body{background:#f5f7fa;display:flex;}
        .sidebar{width:250px;background:#2c3e50;height:100vh;position:fixed;color:#fff;padding:20px 0;}
        .sidebar h3{text-align:center;padding:20px 0;border-bottom:1px solid #34495e;margin-bottom:20px;}
        .sidebar a{display:block;padding:15px 25px;color:#ecf0f1;text-decoration:none;transition:0.3s;}
        .sidebar a:hover,.sidebar a.active{background:#3498db;border-left:4px solid #fff;}
        .main{flex:1;margin-left:250px;padding:30px;}
        .header{background:#fff;padding:20px;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,0.1);margin-bottom:30px;display:flex;justify-content:space-between;align-items:center;}
        .card{background:#fff;padding:30px;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
        .info-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:20px;margin-top:20px;}
        .info-card{padding:25px;border-radius:10px;background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;}
        .info-card h4{font-size:20px;margin-bottom:10px;}
    </style>
</head>
<body>
    <div class="sidebar">
        <h3><i class="fa-solid fa-user-shield"></i> 管理后台</h3>
        <a href="index.php" class="active"><i class="fa-solid fa-house"></i> 控制台</a>
        <a href="config.php"><i class="fa-solid fa-gear"></i> 网站配置</a>
        <a href="users.php"><i class="fa-solid fa-users"></i> 用户管理</a>
        <a href="profile.php"><i class="fa-solid fa-user-pen"></i> 修改资料</a>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> 退出登录</a>
    </div>
    <div class="main">
        <div class="header">
            <h2>控制台</h2>
            <p>欢迎您，<?=$_SESSION['admin_name']?></p>
        </div>
        <div class="card">
            <h3>系统信息</h3>
            <div class="info-grid">
                <div class="info-card">
                    <h4>网站标题</h4>
                    <p><?=$cfg['site_title']?></p>
                </div>
                <div class="info-card">
                    <h4>当前管理员</h4>
                    <p><?=$_SESSION['admin_name']?></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>