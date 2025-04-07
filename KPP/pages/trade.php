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

// 查询用户可交易的卡牌
$sql_tradable_cards = "SELECT c.id, c.card_name, c.card_image 
                       FROM user_cards uc
                       JOIN cards c ON uc.card_id = c.id
                       WHERE uc.user_id = $user_id AND c.is_tradable = true AND uc.is_tradable = true";
$result_tradable_cards = $conn->query($sql_tradable_cards);

// 查询用户不可交易的卡牌
$sql_non_tradable_cards = "SELECT c.id, c.card_name, c.card_image 
                           FROM user_cards uc
                           JOIN cards c ON uc.card_id = c.id
                           WHERE uc.user_id = $user_id AND (c.is_tradable = false OR uc.is_tradable = false)";
$result_non_tradable_cards = $conn->query($sql_non_tradable_cards);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['trade_card_id'])) {
        $trade_card_id = $_POST['trade_card_id'];
        // 示例：将交易的卡牌标记为不可交易
        $update_sql = "UPDATE user_cards SET is_tradable = false WHERE user_id = $user_id AND card_id = $trade_card_id";
        if ($conn->query($update_sql) === TRUE) {
            $message = "交易成功！";
        } else {
            $message = "交易失败: " . $conn->error;
        }
    } elseif (isset($_POST['toggle_tradable']) && isset($_POST['card_id'])) {
        $card_id = $_POST['card_id'];
        $toggle_tradable = $_POST['toggle_tradable'] === 'true'? true : false;
        $update_sql = "UPDATE user_cards SET is_tradable = $toggle_tradable WHERE user_id = $user_id AND card_id = $card_id";
        if ($conn->query($update_sql) === TRUE) {
            $message = "卡牌交易状态已更新！";
        } else {
            $message = "更新失败: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>交易 - KPP 签到系统</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <?php require_once '../includes/menu.php'; ?>
    <div class="container mx-auto p-8">
        <h2 class="text-3xl font-bold mb-4">交易中心</h2>
        <?php if (isset($message)): ?>
            <p class="text-green-500 mb-4"><?php echo $message; ?></p>
        <?php endif; ?>

        <!-- 可交易卡牌 -->
        <h3 class="text-2xl font-bold mb-2">可交易的卡牌</h3>
        <?php if ($result_tradable_cards->num_rows > 0): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <?php while ($card = $result_tradable_cards->fetch_assoc()): ?>
                    <div class="bg-white p-4 rounded shadow">
                        <img src="<?php echo '../image/' . $card['card_image']; ?>" alt="卡牌" class="mb-2">
                        <p class="text-gray-700 mb-2"><?php echo $card['card_name']; ?></p>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <input type="hidden" name="trade_card_id" value="<?php echo $card['id']; ?>">
                            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">交易此卡牌</button>
                        </form>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <input type="hidden" name="card_id" value="<?php echo $card['id']; ?>">
                            <input type="hidden" name="toggle_tradable" value="false">
                            <button type="submit" class="bg-yellow-500 text-white py-2 px-4 rounded hover:bg-yellow-600 mt-2">设为不可交易</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-700">你没有可交易的卡牌。</p>
        <?php endif; ?>

        <!-- 不可交易卡牌 -->
        <h3 class="text-2xl font-bold mb-2 mt-8">不可交易的卡牌</h3>
        <?php if ($result_non_tradable_cards->num_rows > 0): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <?php while ($card = $result_non_tradable_cards->fetch_assoc()): ?>
                    <div class="bg-white p-4 rounded shadow">
                        <img src="<?php echo '../image/' . $card['card_image']; ?>" alt="卡牌" class="mb-2">
                        <p class="text-gray-700 mb-2"><?php echo $card['card_name']; ?></p>
                        <p class="text-gray-500">此卡牌不可交易</p>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <input type="hidden" name="card_id" value="<?php echo $card['id']; ?>">
                            <input type="hidden" name="toggle_tradable" value="true">
                            <button type="submit" class="bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600 mt-2">设为可交易</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-700">你没有不可交易的卡牌。</p>
        <?php endif; ?>
    </div>
</body>

</html>    