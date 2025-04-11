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
    $sale_sql = "SELECT card_id 
                 FROM sales 
                 WHERE id = $sale_id AND user_id = $user_id AND status = '待出售'";
    $sale_result = $conn->query($sale_sql);

    if ($sale_result->num_rows == 1) {
        $sale = $sale_result->fetch_assoc();
        $card_id = $sale['card_id'];

        // 更新出售记录状态为已取消
        $update_sale_sql = "UPDATE sales SET status = '已取消' WHERE id = $sale_id";
        if ($conn->query($update_sale_sql) ===