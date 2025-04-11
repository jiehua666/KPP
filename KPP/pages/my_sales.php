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

// 查询当前用户正在出售的卡牌信息
$my_sales_sql = "SELECT s.id, c.card_name, c.card_image, s.price 
                 FROM sales s
                 JOIN cards c ON s.card_id = c.id
                 WHERE s.user_id = $user_id AND s.status = '待出售'";
$my_sales_result = $conn->query($my_sales_sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>我的出售 - KPP 签到系统</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <?php require_once '../includes/menu.php'; ?>
    <div class="container mx-auto p-8">
        <h2 class="text-3xl font-bold mb-4">我的出售</h2>
        <?php if ($my_sales_result->num_rows > 0): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <?php while ($sale = $my_sales_result->fetch_assoc()): ?>
                    <div class="bg-white p-4 rounded shadow">
                        <img src="<?php echo '../image/' . $sale['card_image']; ?>" alt="卡牌" class="mb-2">
                        <p class="text-gray-700 mb-2"><?php echo $sale['card_name']; ?></p>
                        <p class="text-gray-700 mb-2">价格: <?php echo $sale['price']; ?> 元</p>
                        <form action="cancel_sale.php" method="post">
                            <input type="hidden" name="sale_id" value="<?php echo $sale['id']; ?>">
                            <button type="submit" class="bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600">取消出售</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-700">你目前没有卡牌在售。</p>
        <?php endif; ?>
    </div>
</body>

</html>