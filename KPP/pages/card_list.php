<?php
require_once '../includes/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 查询用户拥有的卡牌
$sql = "SELECT c.id, c.card_name, c.card_description, c.card_image 
        FROM user_cards uc
        JOIN cards c ON uc.card_id = c.id
        WHERE uc.user_id = $user_id";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>我的卡牌列表</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <!-- 导航栏 -->
    <nav class="bg-blue-500 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-white text-2xl">KPP 签到系统</h1>
            <ul class="flex space-x-4">
                <li><a href="index.php" class="text-white hover:underline">主页</a></li>
                <li><a href="sign_in.php" class="text-white hover:underline">签到</a></li>
                <li><a href="card_list.php" class="text-white hover:underline">卡牌</a></li>
                <li><a href="../pages/trade.php" class="text-white hover:underline">交易</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mx-auto p-8">
        <h2 class="text-3xl font-bold mb-4">我的卡牌列表</h2>
        <?php if ($result->num_rows > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="bg-white p-4 rounded shadow">
                        <img src="../images/<?php echo $row['card_image']; ?>" alt="<?php echo $row['card_name']; ?>" class="mb-2">
                        <h3 class="text-xl font-bold mb-1"><?php echo $row['card_name']; ?></h3>
                        <p class="text-gray-700"><?php echo $row['card_description']; ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-700">你还没有任何卡牌。</p>
        <?php endif; ?>
    </div>
</body>

</html>    