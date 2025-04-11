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

// 查询用户可出售的卡牌
$available_cards_sql = "SELECT c.id, c.card_name, c.card_image 
                        FROM user_cards uc
                        JOIN cards c ON uc.card_id = c.id
                        WHERE uc.user_id = $user_id AND uc.is_tradable = true AND uc.is_on_sale = false";
$available_cards_result = $conn->query($available_cards_sql);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $card_id = $_POST['card_id'];
    $price = $_POST['price'];

    // 插入出售记录
    $insert_sale_sql = "INSERT INTO sales (user_id, card_id, price) VALUES (?, ?, ?)";
    $insert_sale_stmt = $conn->prepare($insert_sale_sql);
    $insert_sale_stmt->bind_param("iid", $user_id, $card_id, $price);

    if ($insert_sale_stmt->execute()) {
        // 更新 user_cards 表，标记该卡牌正在出售
        $update_user_cards_sql = "UPDATE user_cards SET is_on_sale = true WHERE user_id = $user_id AND card_id = $card_id";
        $conn->query($update_user_cards_sql);
        $message = "卡牌已成功上架出售！";
    } else {
        $message = "上架出售失败: " . $insert_sale_stmt->error;
    }
    $insert_sale_stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>出售卡牌 - KPP 签到系统</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <?php require_once '../includes/menu.php'; ?>
    <div class="container mx-auto p-8">
        <h2 class="text-3xl font-bold mb-4">出售卡牌</h2>
        <?php if (isset($message)): ?>
            <p class="text-green-500 mb-4"><?php echo $message; ?></p>
        <?php endif; ?>
        <?php if ($available_cards_result->num_rows > 0): ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="mb-4">
                    <label for="card_id" class="block text-gray-700">选择卡牌:</label>
                    <select id="card_id" name="card_id" class="border border-gray-300 p-2 w-full" required>
                        <?php while ($card = $available_cards_result->fetch_assoc()): ?>
                            <option value="<?php echo $card['id']; ?>"><?php echo $card['card_name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="price" class="block text-gray-700">出售价格:</label>
                    <input type="number" id="price" name="price" step="0.01" class="border border-gray-300 p-2 w-full" required>
                </div>
                <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">上架出售</button>
            </form>
        <?php else: ?>
            <p class="text-gray-700">你没有可出售的卡牌。</p>
        <?php endif; ?>
    </div>
</body>

</html>