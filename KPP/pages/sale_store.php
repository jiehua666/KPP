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

// 查询当前用户在售的卡牌数量
$user_on_sale_count_sql = "SELECT COUNT(*) as count FROM sales WHERE user_id = $user_id AND status = '待出售'";
$user_on_sale_count_result = $conn->query($user_on_sale_count_sql);
$user_on_sale_count = $user_on_sale_count_result->fetch_assoc()['count'];

// 查询所有待出售的卡牌信息
$all_sales_sql = "SELECT s.id, c.card_name, c.card_image, s.price, u.username 
                  FROM sales s
                  JOIN cards c ON s.card_id = c.id
                  JOIN users u ON s.user_id = u.id
                  WHERE s.status = '待出售'";
$all_sales_result = $conn->query($all_sales_sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>出售商店 - KPP 签到系统</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <?php require_once '../includes/menu.php'; ?>
    <div class="container mx-auto p-8">
        <h2 class="text-3xl font-bold mb-4">出售商店</h2>
        <p class="text-gray-700 mb-4">你当前有 <?php echo $user_on_sale_count; ?> 个卡牌在售。</p>
        <?php if ($all_sales_result->num_rows > 0): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <?php while ($sale = $all_sales_result->fetch_assoc()): ?>
                    <div class="bg-white p-4 rounded shadow">
                        <img src="<?php echo '../image/' . $sale['card_image']; ?>" alt="卡牌" class="mb-2">
                        <p class="text-gray-700 mb-2"><?php echo $sale['card_name']; ?></p>
                        <p class="text-gray-700 mb-2">价格: <?php echo $sale['price']; ?> 元</p>
                        <p class="text-gray-700 mb-2">出售者: <?php echo $sale['username']; ?></p>
                        <form action="buy_card.php" method="post">
                            <input type="hidden" name="sale_id" value="<?php echo $sale['id']; ?>">
                            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">购买此卡牌</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-700">目前没有卡牌在售。</p>
        <?php endif; ?>
    </div>
</body>

</html>