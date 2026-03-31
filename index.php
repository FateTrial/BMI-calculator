<?php
session_start();
include 'config.php';
$site = $conn->query("SELECT * FROM site_config LIMIT 1")->fetch_assoc();
$title = $site['site_title'] ?? 'BMI健康计算器';
$icon = $site['site_icon'] ?? '';

$bmi = $bmr = $tdee = $result = $level = '';
$suggestions = [];
$fit_advice = ''; // 新增：BMI专属健身建议
// 国际健康标准：最低安全摄入热量
$safe_min_calorie = 1200;
$activityLevels = [
    ['value'=>1.2,'text'=>'久坐（办公室工作）'],
    ['value'=>1.375,'text'=>'轻度活动'],
    ['value'=>1.55,'text'=>'中度活动'],
    ['value'=>1.725,'text'=>'高度活动'],
    ['value'=>1.9,'text'=>'极高活动']
];

if($_SERVER['REQUEST_METHOD']=='POST'){
    $height = (float)$_POST['height'];
    $weight = (float)$_POST['weight'];
    $age = (int)$_POST['age'];
    $gender = $_POST['gender'];
    $activity = (float)$_POST['activity'];

    // 核心计算公式（完全保留你的原版逻辑）
    $bmi = round($weight/($height/100)**2,2);
    if($gender=='male'){
        $bmr = round(10*$weight + 6.25*$height - 5*$age +5);
    }else{
        $bmr = round(10*$weight + 6.25*$height - 5*$age -161);
    }
    $tdee = round($bmr*$activity);

    // BMI等级判定
    if($bmi<18.5) $level='偏瘦⚠️';
    elseif($bmi<24) $level='正常✅';
    elseif($bmi<28) $level='超重🔔';
    else $level='肥胖🚨';

    $result = "BMI：$bmi ($level) | 基础代谢：$bmr 大卡 | 每日消耗：$tdee 大卡";
    
    // 国际健康标准：每周安全体重变化 ≤ 体重的1%
    $safe_max = round($weight*0.01,2);    // 极限变化
    $recommend_gain = round($safe_max/2,2); // 推荐变化
    $mild_gain = round($safe_max/4,2);     // 轻度变化

    // 国际健康公式：1kg体重 ≈ 7700 大卡
    $calorie_safe = round(7700 * $safe_max /7);       // 极限热量盈余
    $calorie_recommend = round(7700 * $recommend_gain /7); // 推荐热量盈余

    // 完整饮食建议（减重+维持+增重，国际标准）
    $suggestions = [
        ['name'=>'极端减重','speed'=>"-$safe_max kg/周",'calorie'=>max($tdee-$calorie_safe, $safe_min_calorie)],
        ['name'=>'推荐减重','speed'=>"-$recommend_gain kg/周",'calorie'=>max($tdee-$calorie_recommend, $safe_min_calorie)],
        ['name'=>'维持体重','speed'=>'0 kg/周','calorie'=>$tdee],
        ['name'=>'轻度增重','speed'=>"+$mild_gain kg/周",'calorie'=>$tdee+round($calorie_recommend/2)],
        ['name'=>'推荐增重','speed'=>"+$recommend_gain kg/周",'calorie'=>$tdee+$calorie_recommend],
        ['name'=>'极限增重','speed'=>"+$safe_max kg/周",'calorie'=>$tdee+$calorie_safe],
    ];

    // 保存记录（完全保留原版逻辑）
    if(isLoggedIn()){
        $uid = $_SESSION['user_id'];
        $conn->query("INSERT INTO records(user_id,height,weight,age,gender,activity,bmi,bmr,tdee) VALUES('$uid','$height','$weight','$age','$gender','$activity','$bmi','$bmr','$tdee')");
    }
    
    $_SESSION['calc'] = [$result,$suggestions,$bmi]; // 仅新增传递BMI值，无逻辑修改
    header("Location: index.php"); 
    exit();
}

if(isset($_SESSION['calc'])){
    list($result,$suggestions,$bmi) = $_SESSION['calc'];
    unset($_SESSION['calc']);

    // ========== 新增：根据BMI自动标注推荐/不推荐 + 生成健身建议 ==========
    if($bmi < 18.5){
        // 偏瘦：禁止减重，推荐增重
        $fit_advice = "💡 你的体型偏瘦，建议以增重为主，严禁减重！";
        $tags = ['⚠️','⚠️','✅','✅','✅','⚠️'];
    }elseif($bmi < 24){
        // 正常：维持为主，可自由调整
        $fit_advice = "💡 你的体型标准，建议维持体重，可轻度增重/减重！";
        $tags = ['⚠️','✅','✅','✅','✅','⚠️'];
    }elseif($bmi < 28){
        // 超重：推荐减重，禁止增重
        $fit_advice = "💡 你的体型超重，建议以减重为主，严禁增重！";
        $tags = ['⚠️','✅','✅','⚠️','⚠️','⚠️'];
    }else{
        // 肥胖：必须减重，禁止增重
        $fit_advice = "💡 你的体型肥胖，建议严格减重，严禁增重！";
        $tags = ['⚠️','✅','✅','⚠️','⚠️','⚠️'];
    }

    // 为每个方案添加标识（不修改原有数据结构）
    foreach($suggestions as $k => $item){
        $suggestions[$k]['name'] = $tags[$k] . $item['name'];
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$title?></title>
    <?php if($icon):?><link rel="icon" href="<?=$icon?>"><?php endif?>
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
            max-width: 800px;
            margin: 0 auto;
        }
        .nav{
            background: #ffffff;
            padding: 18px 25px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .nav h3{
            color: #2d3748;
            font-size: 20px;
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
            margin-bottom: 20px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.06);
        }
        .card h3{
            color: #2d3748;
            margin-bottom: 15px;
            font-size: 18px;
        }
        input,select{
            width: 100%;
            padding: 14px 16px;
            margin: 10px 0;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-size: 15px;
            transition: 0.2s;
        }
        input:focus,select:focus{
            border-color: #3b82f6;
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        button{
            padding: 15px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            border: none;
            border-radius: 10px;
            width: 100%;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            margin-top: 10px;
        }
        button:hover{
            opacity: 0.9;
        }
        table{
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border-radius: 10px;
            overflow: hidden;
        }
        th{
            background: #3b82f6;
            color: white;
            padding: 12px;
            font-weight: 600;
        }
        td{
            padding: 12px;
            border: 1px solid #e2e8f0;
            text-align: center;
            background: #fafbfc;
        }
        /* 高亮推荐/极限建议 */
        td:has(✅){
            background-color: #dcfce7 !important;
            color: #166534;
            font-weight: 600;
        }
        td:has(⚠️){
            background-color: #fffbeb !important;
            color: #92400e;
            font-weight: 600;
        }
        /* 新增：健身建议样式 */
        .advice{
            margin: 15px 0;
            padding: 12px;
            background: #f0f7ff;
            border-radius: 8px;
            color: #2d3748;
            font-size: 15px;
        }
        /* 响应式适配 */
        @media (max-width: 768px) {
            .nav{
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
            .card{
                padding: 20px;
            }
            table{
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="nav">
            <h3><?=$title?></h3>
            <div>
                <?php if(isLoggedIn()):?>
                    <a href="history.php">历史</a>
                    <a href="logout.php">退出</a>
                <?php else:?>
                    <a href="login.php">登录</a>
                    <a href="register.php">注册</a>
                <?php endif?>
            </div>
        </div>

        <div class="card">
            <form method="post">
                <input name="height" placeholder="身高(cm)" required>
                <input name="weight" placeholder="体重(kg)" required>
                <input name="age" placeholder="年龄" required>
                <select name="gender" required>
                    <option value="male">男</option>
                    <option value="female">女</option>
                </select>
                <select name="activity" required>
                    <?php foreach($activityLevels as $v):?>
                        <option value="<?=$v['value']?>"><?=$v['text']?></option>
                    <?php endforeach?>
                </select>
                <button>🧮 计算健康数据</button>
            </form>
        </div>

        <?php if($result):?>
        <div class="card">
            <h3><?=$result?></h3>
            <!-- 新增：BMI专属健身建议 -->
            <div class="advice"><?=$fit_advice?></div>
            <table>
                <tr>
                    <th>健身目标</th>
                    <th>每周体重变化</th>
                    <th>每日建议摄入</th>
                </tr>
                <?php foreach($suggestions as $v):?>
                <tr>
                    <td><?=$v['name']?></td>
                    <td><?=$v['speed']?></td>
                    <td><?=$v['calorie']?> 大卡</td>
                </tr>
                <?php endforeach?>
            </table>
        </div>
        <?php endif?>
    </div>
</body>
</html>