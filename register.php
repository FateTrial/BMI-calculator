<?php
include 'config.php';
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 接收表单数据
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);

    // 非空校验
    if (empty($username) || empty($password) || empty($email)) {
        $error = "所有字段不能为空！";
    } else {
        // 检查用户名/邮箱是否重复
        $check = $conn->prepare("SELECT * FROM users WHERE username=? OR email=?");
        $check->bind_param("ss", $username, $email);
        $check->execute();
        $result = $check->get_result();
        
        if ($result->num_rows > 0) {
            $error = "用户名或邮箱已被注册！";
        } else {
            // 密码加密 + 生成验证令牌
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $verify_token = md5(uniqid() . time());
            
            // 插入用户数据
            $insert = $conn->prepare("INSERT INTO users (username, password, email, verify_token) VALUES (?,?,?,?)");
            $insert->bind_param("ssss", $username, $hashed_password, $email, $verify_token);
            
            if ($insert->execute()) {
                // 修复：定义 $user 变量，彻底解决未定义报错
                $user = $username;
                // 注册成功，自动发送验证邮件
                try {
                    $mail = new PHPMailer(true);
                    // 邮件服务器配置
                    $mail->CharSet = 'UTF-8';
                    $mail->isSMTP();
                    $mail->Host       = SMTP_HOST;
                    $mail->SMTPAuth   = true;
                    $mail->Username   = SMTP_USER;
                    $mail->Password   = SMTP_PASS;
                    $mail->SMTPSecure = 'ssl';
                    $mail->Port       = SMTP_PORT;

                    // 发件人 & 收件人
                    $mail->setFrom(SMTP_USER, SITE_NAME);
                    $mail->addAddress($email);

                    // 邮件内容
                    $mail->isHTML(true);
                    $mail->Subject = SITE_NAME . ' - 邮箱验证';
                    $verify_link = SITE_URL . "/verify.php?token=" . $verify_token;
                    $mail->Body    = "
                        <h3>您好，{$user}！</h3>
                        <p>感谢您的注册，请点击下方链接验证您的邮箱：</p>
                        <p><a href='{$verify_link}' target='_blank'>【点击立即验证邮箱】</a></p>
                        <p>验证完成后即可登录使用全部功能</p>
                    ";

                    $mail->send();
                    $success = "注册成功！验证邮件已发送至您的邮箱，请查收验证~";
                } catch (Exception $e) {
                    $success = "注册成功！邮件发送失败：" . $mail->ErrorInfo;
                }
            } else {
                $error = "注册失败：" . $conn->error;
            }
            $insert->close();
        }
        $check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户注册</title>
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
        .btn-register {
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
        .btn-register:hover {
            opacity:0.9;
        }
        .error {
            color:#ef4444;
            text-align:center;
            margin-bottom:15px;
            font-weight:500;
        }
        .success {
            color:#16a34a;
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
        <h2>📝 用户注册</h2>
        <?php if($error) echo "<p class='error'>$error</p>"; ?>
        <?php if($success) echo "<p class='success'>$success</p>"; ?>
        <form method="post">
            <div class="form-group">
                <input type="text" name="username" placeholder="用户名" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="邮箱" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="密码" required>
            </div>
            <button type="submit" class="btn-register">注册</button>
        </form>
        <div class="link">已有账号？<a href="login.php">立即登录</a></div>
        <div class="link"><a href="index.php">返回计算器</a></div>
    </div>
</body>
</html>