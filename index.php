<?php
// ------------------- 设备检测 -------------------
$userAgent = $_SERVER['HTTP_USER_AGENT'];
$mobileAgents = ['iPhone','iPad','Android','webOS','BlackBerry','iPod','Symbian','Windows Phone'];

foreach ($mobileAgents as $agent) {
    if (stripos($userAgent, $agent) !== false) {
        header("Location: index_mobile.php");
        exit;
    }
}

// ------------------- 访问计数逻辑 -------------------
$counterFile = 'visit_count.txt';
$dailyFile   = 'daily_count.txt';

// 如果文件不存在则初始化
if (!file_exists($counterFile)) file_put_contents($counterFile, '0');
if (!file_exists($dailyFile))   file_put_contents($dailyFile, date("Y-m-d") . "|0");

// 读取总访问数
$visitCount = (int) file_get_contents($counterFile);

// 读取每日访问数
$dailyDataRaw = file_get_contents($dailyFile);
$dailyData = explode("|", $dailyDataRaw);
$todayDate = date("Y-m-d");
$dailyDate = isset($dailyData[0]) ? $dailyData[0] : $todayDate;
$dailyCount = isset($dailyData[1]) ? (int)$dailyData[1] : 0;

// 检查 cookie 避免重复计数
if (!isset($_COOKIE['visited'])) {
    // 总访问量 +1
    $visitCount++;
    file_put_contents($counterFile, $visitCount, LOCK_EX);

    // 今日访问量 +1 或重置
    if ($dailyDate === $todayDate) {
        $dailyCount++;
    } else {
        $dailyDate = $todayDate;
        $dailyCount = 1;
    }
    file_put_contents($dailyFile, $dailyDate . "|" . $dailyCount, LOCK_EX);

    // 设置 cookie 30 天有效
    setcookie("visited", "true", time() + 30*24*60*60, "/");
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>你被骗了 - 电脑版</title>
<style>
body {
    background: linear-gradient(135deg, #6e8efb, #a777e3);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
.container {
    background-color: rgba(255, 255, 255, 0.95);
    border-radius: 20px;
    box-shadow: 0 15px 30px rgba(0,0,0,0.2);
    padding: 40px;
    text-align: center;
    max-width: 600px;
    width: 100%;
}
h1 { color:#333; margin-bottom:30px; font-size:2.5rem; position:relative; padding-bottom:15px;}
h1:after { content:''; position:absolute; bottom:0; left:50%; transform:translateX(-50%); width:80px; height:4px; background:linear-gradient(to right,#6e8efb,#a777e3); border-radius:2px;}
.counter { font-size:5rem; font-weight:bold; color:#6e8efb; margin:20px 0; text-shadow:2px 2px 4px rgba(0,0,0,0.1);}
.message { font-size:1.5rem; color:#555; margin-bottom:15px;}
.today { font-size:1.2rem; color:#888; margin-bottom:30px;}
.footer { margin-top:20px; font-size:0.9rem; color:#777;}
.footer a { color:#6e8efb; text-decoration:none;}
.video iframe { width:100%; height:300px; border:0; margin-top:20px;}
</style>
</head>
<body>
<div class="container">
<h1>你被骗了</h1>
<div class="counter"><?php echo $visitCount; ?></div>
<p class="message">您是第 <?php echo $visitCount; ?> 位被骗</p>
<p class="today">今天已有 <?php echo $dailyCount; ?> 被骗</p>
<div class="footer">
<a href="https://beian.miit.gov.cn/" target="_blank">XXXXXX</a>
</div>
<div class="video">
<iframe src="//player.bilibili.com/player.html?isOutside=true&aid=1706416465&bvid=BV1UT42167xb&cid=1641702404&p=1" scrolling="no" allowfullscreen="true"></iframe>
</div>
</div>
</body>
</html>