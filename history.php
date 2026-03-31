<?php
include 'config.php';
checkLogin();

$user_id = $_SESSION['user_id'];
// 查询成功提示
$msg = '';
if(isset($_GET['del'])){
    $msg = $_GET['del'] == 1 ? "✅ 删除成功！" : "❌ 删除失败！";
}

$sql = "SELECT * FROM records WHERE user_id='$user_id' ORDER BY calculated_at DESC";
$query = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>计算历史记录</title>
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
            padding: 20px;
        }
        .container{
            max-width: 900px;
            margin: 0 auto;
        }
        .nav{
            background: #ffffff;
            padding: 18px 25px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .nav a{
            color: #3b82f6;
            text-decoration: none;
            margin-left: 15px;
            font-weight: 500;
            transition: 0.2s;
        }
        .nav a:hover{
            color: #1d4ed8;
        }
        .card{
            background: #ffffff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.06);
        }
        .card h2{
            color: #2d3748;
            margin-bottom: 20px;
            text-align: center;
            font-size: 24px;
        }
        .tip{
            text-align: center;
            color: #16a34a;
            margin-bottom: 15px;
            font-weight: 500;
            font-size: 15px;
        }
        table{
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            border-radius: 10px;
            overflow: hidden;
        }
        th{
            background: #3b82f6;
            color: white;
            padding: 12px;
            text-align: center;
            font-weight: 600;
        }
        td{
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
            background: #fafbfc;
        }
        .empty{
            text-align: center;
            padding: 30px;
            color: #64748b;
        }
        /* 删除按钮样式 - 统一美化 */
        .del-btn {
            padding: 6px 12px;
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: 0.2s;
        }
        .del-btn:hover {
            background: #dc2626;
        }
        /* 响应式适配 */
        @media (max-width: 768px) {
            .nav{
                justify-content: center;
                flex-wrap: wrap;
                gap: 10px;
            }
            .card{
                padding: 20px;
            }
            table{
                font-size: 13px;
            }
            td,th{
                padding: 8px 4px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="nav">
            <a href="index.php">返回计算器</a>
            <a href="logout.php">退出登录</a>
        </div>
        <div class="card">
            <h2>📋 我的计算历史记录</h2>
            <!-- 删除提示 -->
            <?php if($msg): ?>
                <p class="tip"><?php echo $msg; ?></p>
            <?php endif; ?>
            
            <table>
                <tr>
                    <th>身高(cm)</th><th>体重(kg)</th><th>年龄</th><th>性别</th><th>BMI</th><th>BMR</th><th>TDEE</th><th>计算时间</th><th>操作</th>
                </tr>
                <?php if (mysqli_num_rows($query) == 0): ?>
                    <tr><td colspan="9" class="empty">暂无计算记录</td></tr>
                <?php else: ?>
                    <?php while ($row = mysqli_fetch_assoc($query)) { ?>
                    <tr>
                        <td><?php echo $row['height']; ?></td>
                        <td><?php echo $row['weight']; ?></td>
                        <td><?php echo $row['age']; ?></td>
                        <td><?php echo $row['gender']=='male'?'男':'女'; ?></td>
                        <td><?php echo $row['bmi']; ?></td>
                        <td><?php echo $row['bmr']; ?></td>
                        <td><?php echo $row['tdee']; ?></td>
                        <td><?php echo $row['calculated_at']; ?></td>
                        <!-- 删除按钮 + 防误删弹窗 -->
                        <td>
                            <button class="del-btn" onclick="delRecord(<?php echo $row['id']; ?>)">删除</button>
                        </td>
                    </tr>
                    <?php } ?>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <script>
        // 删除确认弹窗
        function delRecord(id) {
            if(confirm('确定要删除这条记录吗？此操作不可恢复！')){
                window.location.href = 'delete_record.php?id=' + id;
            }
        }
    </script>
</body>
</html>