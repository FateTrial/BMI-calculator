<?php
session_start();

// =============================================
// 数据库配置 → 【自定义修改】，可填任意数据库名
// =============================================
$host = 'localhost';      // 数据库地址（默认localhost）
$dbname = '';             // 填写你的自定义数据库名（无限制）
$db_user = '';        // 数据库用户名
$db_pass = '';            // 数据库密码

// 连接数据库
$conn = mysqli_connect($host, $db_user, $db_pass, $dbname);
if (!$conn) {
    die("数据库连接失败：" . mysqli_connect_error());
}
mysqli_set_charset($conn, 'utf8mb4');

// 登录验证
function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

// 判断登录状态
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// =============================================
// 邮箱配置 → 自行修改（QQ/163/企业邮箱均可）
// =============================================
define('SMTP_HOST', 'smtp.qq.com');
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('SMTP_PORT', 465);
define('SITE_NAME', 'BMI&TDEE健康计算器');
define('SITE_URL', '');
?>