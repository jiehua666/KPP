<?php
require_once '../includes/config.php';
// 检查会话是否已经启动
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
// 查询用户信息，假设用户表名为 users，有 username 字段
$sql_user = "SELECT username FROM users WHERE id = $user_id";
$result_user = $conn->query($sql_user);

if ($result_user->num_rows == 1) {
    $user = $result_user->fetch_assoc();
    $username = $user['username'];
} else {
    $username = "未知用户";
}

// 查询用户累计签到次数
$sql_sign_in_count = "SELECT COUNT(*) as count FROM sign_ins WHERE user_id = $user_id";
$result_sign_in_count = $conn->query($sql_sign_in_count);
if ($result_sign_in_count->num_rows == 1) {
    $sign_in_count = $result_sign_in_count->fetch_assoc()['count'];
} else {
    $sign_in_count = 0;
}

// 查询用户最近一次签到日期
$sql_last_sign_in = "SELECT sign_in_date FROM sign_ins WHERE user_id = $user_id ORDER BY sign_in_date DESC LIMIT 1";
$result_last_sign_in = $conn->query($sql_last_sign_in);
if ($result_last_sign_in->num_rows == 1) {
    $last_sign_in_date = $result_last_sign_in->fetch_assoc()['sign_in_date'];
} else {
    $last_sign_in_date = "无";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>个人中心 - KPP 签到系统</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <?php require_once '../includes/menu.php'; ?>
    <div class="container mx-auto p-8">
        <h2 class="text-3xl font-bold mb-4">个人中心 - <?php echo $username; ?></h2>
        <div class="bg-white p-4 rounded shadow mb-4">
            <h3 class="text-2xl font-bold mb-2">累计签到次数</h3>
            <p class="text-gray-700"><?php echo $sign_in_count; ?> 次</p>
        </div>
        <div class="bg-white p-4 rounded shadow mb-4">
            <h3 class="text-2xl font-bold mb-2">最近一次签到日期</h3>
            <p class="text-gray-700"><?php echo $last_sign_in_date; ?></p>
        </div>
    </div>
</body>

</html>    