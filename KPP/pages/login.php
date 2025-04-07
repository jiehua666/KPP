<?php
require_once '../includes/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT id FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        session_start();
        $_SESSION['user_id'] = $result->fetch_assoc()['id'];
        header("Location: sign_in.php");
        exit();
    } else {
        $error = "用户名或密码错误";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户登录</title>
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
                <li><a href="login.php" class="text-white hover:underline">签到</a></li>
                <li><a href="#" class="text-white hover:underline">卡牌</a></li>
                <li><a href="#" class="text-white hover:underline">交易</a></li>
                <li><a href="register.php" class="text-white hover:underline">注册</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mx-auto p-8">
        <h2 class="text-3xl font-bold mb-4">用户登录</h2>
        <?php if (isset($error)): ?>
            <p class="text-red-500 mb-4"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="mb-4">
                <label for="username" class="block text-gray-700">用户名:</label>
                <input type="text" id="username" name="username" class="border border-gray-300 p-2 w-full" required>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700">密码:</label>
                <input type="password" id="password" name="password" class="border border-gray-300 p-2 w-full" required>
            </div>
            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">登录</button>
        </form>
        <p class="mt-4">还没有账号？<a href="register.php" class="text-blue-500 hover:underline">立即注册</a></p>
    </div>
</body>

</html>