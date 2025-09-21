<?php
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
    $visitCount++;
    file_put_contents($counterFile, $visitCount, LOCK_EX);

    if ($dailyDate === $todayDate) {
        $dailyCount++;
    } else {
        $dailyDate = $todayDate;
        $dailyCount = 1;
    }
    file_put_contents($dailyFile, $dailyDate . "|" . $dailyCount, LOCK_EX);

    setcookie("visited", "true", time() + 30*24*60*60, "/");
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>你被骗了 - 手机版</title>
<style>
body { 
    font-family: Arial, sans-serif; 
    margin:0; 
    padding:0; 
    background: linear-gradient(135deg, #6e8efb, #a777e3);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align:center; 
}
.container { 
    padding:25px; 
    margin:20px; 
    background:rgba(255,255,255,0.95); 
    border-radius:20px; 
    box-shadow:0 10px 25px rgba(0,0,0,0.2);
    max-width: 90%;
    width: 100%;
}
h1 { 
    font-size:2rem; 
    margin-bottom:20px; 
    color: #333;
    position: relative;
    padding-bottom: 10px;
}
h1:after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 3px;
    background: linear-gradient(to right, #6e8efb, #a777e3);
    border-radius: 2px;
}
.counter, .today { 
    font-size:1.4rem; 
    margin:15px 0; 
    color:#444; 
}
.counter {
    font-weight: bold;
    color: #6e8efb;
    font-size: 2.5rem;
}
.footer { 
    margin-top:25px; 
    font-size:0.8rem; 
    color:#666; 
}
.footer a { 
    color:#6e8efb; 
    text-decoration:none; 
}
.video iframe {
    width: 100%;
    height: 200px;
    border: 0;
    margin-top: 20px;
    border-radius: 10px;
}
</style>
</head>
<body>
<div class="container">
<h1>你被骗了</h1>
<div class="counter"><?php echo $visitCount; ?></div>
<div class="message">您是第 <?php echo $visitCount; ?> 位被骗</div>
<div class="today">今天已有 <?php echo $dailyCount; ?> 被骗</div>
<div class="video">
<iframe src="//player.bilibili.com/player.html?isOutside=true&aid=1706416465&bvid=BV1UT42167xb&cid=1641702404&p=1" 
    scrolling="no" allowfullscreen="true"></iframe>
</div>
<div class="footer">
<a href="https://beian.miit.gov.cn/" target="_blank">XXXXXX</a>
</div>
</div>
</body>
</html>