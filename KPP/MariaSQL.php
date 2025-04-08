<?php
// 数据库配置
$servername = "8.138.89.207";
$username = "kpp_user";
$password = "123456";
$dbname = "kpp";

// 创建数据库连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接是否成功
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
} else {
    echo "数据库连接成功";
}
?>