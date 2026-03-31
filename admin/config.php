<?php
session_start();
if(!isset($_SESSION['admin_id'])){ header("Location: login.php"); exit(); }
include '../config.php';
$msg = '';
$cfg = $conn->query("SELECT * FROM site_config LIMIT 1")->fetch_assoc();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $title = trim($_POST['title']);
    
    // 上传图标
    $icon = $cfg['site_icon'];
    if(!empty($_FILES['icon']['name'])){
        $ext = pathinfo($_FILES['icon']['name'],PATHINFO_EXTENSION);
        $fileName = 'icon_'.time().'.'.$ext;
        $path = '../uploads/'.$fileName;
        move_uploaded_file($_FILES['icon']['tmp_name'],$path);
        $icon = 'uploads/'.$fileName;
    }
    
    $conn->query("UPDATE site_config SET site_title='$title', site_icon='$icon' WHERE id=1");
    $msg = "<p style='color:green;padding:10px;background:#d4edda;border-radius:6px;'>✅ 保存成功！</p>";
    header("Refresh:1");
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>网站配置</title>
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        label{display:block;margin-bottom:8px;color:#333;font-weight:500;}
        input{width:100%;padding:12px 15px;border:2px solid #eee;border-radius:8px;font-size:16px;}
        .btn{padding:12px 30px;background:#3498db;color:#fff;border:none;border-radius:8px;cursor:pointer;}
        img{max-width:100px;margin-top:10px;border-radius:6px;}
    </style>
</head>
<body>
    <div class="sidebar">
        <h3><i class="fa-solid fa-user-shield"></i> 管理后台</h3>
        <a href="index.php"><i class="fa-solid fa-house"></i> 控制台</a>
        <a href="config.php" class="active"><i class="fa-solid fa-gear"></i> 网站配置</a>
        <a href="users.php"><i class="fa-solid fa-users"></i> 用户管理</a>
        <a href="profile.php"><i class="fa-solid fa-user-pen"></i> 修改资料</a>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> 退出登录</a>
    </div>
    <div class="main">
        <div class="card">
            <h2><i class="fa-solid fa-gear"></i> 网站配置</h2>
            <br><?=$msg?>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label>网站标题</label>
                    <input type="text" name="title" value="<?=$cfg['site_title']?>" required>
                </div>
                <div class="form-group">
                    <label>网站图标</label>
                    <?php if($cfg['site_icon']): ?>
                        <img src="../<?=$cfg['site_icon']?>">
                    <?php endif; ?>
                    <input type="file" name="icon" accept="image/*">
                </div>
                <button type="submit" class="btn">保存配置</button>
            </form>
        </div>
    </div>
</body>
</html>