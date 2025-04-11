<?php
require_once '../includes/config.php';
// 检查会话是否已经启动
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// 获取当前网站的根URL
if (isset($_SERVER['REQUEST_SCHEME'])) {
    $scheme = $_SERVER['REQUEST_SCHEME'];
} else {
    $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
}
$root_url = $scheme . '://' . $_SERVER['HTTP_HOST'];
?>
<nav class="bg-blue-500 p-4">
    <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-white text-2xl">KPP 签到系统</h1>
        <ul class="flex space-x-4">
            <li><a href="<?php echo $root_url; ?>/pages/index.php" class="text-white hover:underline">主页</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="<?php echo $root_url; ?>/pages/sign_in.php" class="text-white hover:underline">签到</a></li>
                <li><a href="<?php echo $root_url; ?>/pages/user_profile.php" class="text-white hover:underline">个人中心</a></li>
                <li><a href="<?php echo $root_url; ?>/pages/trade.php" class="text-white hover:underline">交易</a></li>
                <li><a href="<?php echo $root_url; ?>/pages/store.php" class="text-white hover:underline">商店</a></li>
                <li><a href="<?php echo $root_url; ?>/pages/logout.php" class="text-white hover:underline">退出登录</a></li>
            <?php else: ?>
                <li><a href="<?php echo $root_url; ?>/pages/login.php" class="text-white hover:underline">签到</a></li>
            <?php endif; ?>
            <li><a href="<?php echo $root_url; ?>/pages/card_list.php" class="text-white hover:underline">卡牌</a></li>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <li><a href="<?php echo $root_url; ?>/pages/register.php" class="text-white hover:underline">注册</a></li>
                <li><a href="<?php echo $root_url; ?>/pages/login.php" class="text-white hover:underline">登录</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>    