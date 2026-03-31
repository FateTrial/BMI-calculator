<?php
session_start();
include 'config.php';

// 安全校验：未登录禁止操作
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 获取记录ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: history.php?del=0");
    exit();
}

$record_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// 预处理语句：仅删除当前用户的记录（防越权）
$delete = $conn->prepare("DELETE FROM records WHERE id=? AND user_id=?");
$delete->bind_param("ii", $record_id, $user_id);
$delete->execute();

// 判断是否删除成功
if ($delete->affected_rows > 0) {
    header("Location: history.php?del=1");
} else {
    header("Location: history.php?del=0");
}

$delete->close();
$conn->close();
?>