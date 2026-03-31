<?php
session_start();
if(!isset($_SESSION['admin_id'])){ header("Location: login.php"); exit(); }
include '../config.php';
if(isset($_GET['del'])){ $id=(int)$_GET['del']; $conn->query("DELETE FROM users WHERE id=$id"); header("Location: users.php"); }
$users = $conn->query("SELECT * FROM users");
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户管理</title>
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}
        body{background:#f5f7fa;display:flex;}
        .sidebar{width:250px;background:#2c3e50;height:100vh;position:fixed;color:#fff;padding:20px 0;}
        .sidebar h3{text-align:center;padding:20px 0;border-bottom:1px solid #34495e;margin-bottom:20px;}
        .sidebar a{display:block;padding:15px 25px;color:#ecf0f1;text-decoration:none;transition:0.3s;}
        .sidebar a:hover,.sidebar a.active{background:#3498db;border-left:4px solid #fff;}
        .main{flex:1;margin-left:250px;padding:30px;}
        .card{background:#fff;padding:30px;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
        table{width:100%;border-collapse:collapse;margin-top:20px;}
        th,td{padding:15px;text-align:left;border-bottom:1px solid #eee;}
        th{background:#f8f9fa;}
        .btn-danger{color:#e74c3c;text-decoration:none;}
    </style>
</head>
<body>
    <div class="sidebar">
        <h3><i class="fa-solid fa-user-shield"></i> 管理后台</h3>
        <a href="index.php"><i class="fa-solid fa-house"></i> 控制台</a>
        <a href="config.php"><i class="fa-solid fa-gear"></i> 网站配置</a>
        <a href="users.php" class="active"><i class="fa-solid fa-users"></i> 用户管理</a>
        <a href="profile.php"><i class="fa-solid fa-user-pen"></i> 修改资料</a>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> 退出登录</a>
    </div>
    <div class="main">
        <div class="card">
            <h2><i class="fa-solid fa-users"></i> 用户管理</h2>
            <table>
                <tr><th>ID</th><th>用户名</th><th>邮箱</th><th>状态</th><th>操作</th></tr>
                <?php while($row=$users->fetch_assoc()): ?>
                <tr>
                    <td><?=$row['id']?></td>
                    <td><?=$row['username']?></td>
                    <td><?=$row['email']?></td>
                    <td><?=$row['email_verified']?'已验证':'未验证'?></td>
                    <td><a href="?del=<?=$row['id']?>" class="btn-danger" onclick="return confirm('确定删除？')">删除</a></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>