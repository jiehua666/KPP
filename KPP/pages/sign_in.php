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

// 检查用户记录是否存在
$check_user_sql = "SELECT id FROM users WHERE id = $user_id";
$check_user_result = $conn->query($check_user_sql);

if ($check_user_result->num_rows == 0) {
    $message = "用户记录不存在，请重新登录或联系管理员。";
} else {
    $today = date('Y-m-d');

    // 检查用户今天是否已经签到
    $sql = "SELECT id FROM sign_ins WHERE user_id = $user_id AND sign_in_date = '$today'";
    $result = $conn->query($sql);

    if ($result->num_rows == 0) {
        // 用户今天还未签到，插入签到记录
        $insert_sql = "INSERT INTO sign_ins (user_id, sign_in_date) VALUES ($user_id, '$today')";
        if ($conn->query($insert_sql) === TRUE) {
            // 随机选择一张卡牌发放给用户
            $card_sql = "SELECT id, card_image FROM cards ORDER BY RAND() LIMIT 1";
            $card_result = $conn->query($card_sql);
            if ($card_result->num_rows == 1) {
                $card = $card_result->fetch_assoc();
                $card_id = $card['id'];
                $card_image = $card['card_image'];
                // 插入用户卡牌记录
                $user_card_sql = "INSERT INTO user_cards (user_id, card_id) VALUES ($user_id, $card_id)";
                if ($conn->query($user_card_sql) === TRUE) {
                    $message = "签到成功！你获得了一张卡牌。";
                } else {
                    $message = "签到成功，但发放卡牌失败: " . $conn->error;
                }
            } else {
                $message = "签到成功，但没有可用的卡牌发放。";
            }
        } else {
            $message = "签到失败: " . $conn->error;
        }
    } else {
        $message = "你今天已经签到过了。";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户签到</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <?php require_once '../includes/menu.php'; ?>
    <div class="container mx-auto p-8">
        <h2 class="text-3xl font-bold mb-4">用户签到</h2>
        <p class="mb-4"><?php echo $message; ?></p>
        <?php if (isset($card_image)): ?>
            <?php
            // 判断协议
            if (isset($_SERVER['REQUEST_SCHEME'])) {
                $scheme = $_SERVER['REQUEST_SCHEME'];
            } else {
                $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            }
            $image_url = $scheme . '://' . $_SERVER['HTTP_HOST'] . '/image/' . $card_image;
            ?>
            <img src="<?php echo $image_url; ?>" alt="获得的卡牌" class="mb-4">
        <?php endif; ?>
        <a href="index.php" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">返回主页</a>
    </div>
</body>

</html>    