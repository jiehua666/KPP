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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sale_id = $_POST['sale_id'];

    // 查询出售记录信息
    $sale_sql = "SELECT s.user_id as seller_id, s.card_id 
                 FROM sales s
                 WHERE s.id = $sale_id AND s.status = '待出售'";
    $sale_result = $conn->query($sale_sql);

    if ($sale_result->num_rows == 1) {
        $sale = $sale_result->fetch_assoc();
        $seller_id = $sale['seller_id'];
        $card_id = $sale['card_id'];

        // 更新出售记录状态为已出售
        $update_sale_sql = "UPDATE sales SET status = '已出售' WHERE id = $sale_id";
        if ($conn->query($update_sale_sql) === TRUE) {
            // 更新卖家的 user_cards 表，标记该卡牌已出售
            $update_seller_user_cards_sql = "UPDATE user_cards SET is_on_sale = false, is_tradable = false WHERE user_id = $seller_id AND card_id = $card_id";
            $conn->query($update_seller_user_cards_sql);

            // 插入买家的 user_cards 记录
            $insert_buyer_user_cards_sql = "INSERT INTO user_cards (user_id, card_id, is_tradable) VALUES (?, ?, true)";
            $insert_buyer_user_cards_stmt = $conn->prepare($insert_buyer_user_cards_sql);
            $insert_buyer_user_cards_stmt->bind_param("ii", $user_id, $card_id);
            if ($insert_buyer_user_cards_stmt->execute()) {
                $message = "购买成功！";
            } else {
                $message = "购买失败: " . $insert_buyer_user_cards_stmt->error;
            }
            $insert_buyer_user_cards_stmt->close();
        } else {
            $message = "购买失败: " . $conn->error;
        }
    } else {
        $message = "该卡牌已被出售或不存在。";
    }
}

// 重定向到出售商店页面，并携带消息
header("Location: sale_store.php?message=" . urlencode($message));
exit();