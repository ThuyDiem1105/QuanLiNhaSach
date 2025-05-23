<?php
session_start();
if (!isset($_SESSION['account_loggedin'])){
    header('Location: ../loginFunction/mainPage.php');
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, minimum-scale=1">
    <title>QUẢN LÝ KHÁCH HÀNG</title>
</head>
<body>
    <header class="header">
        <div class="wrapper">
            <h1>Hệ thống quản lý nhà sách</h1>
            <nav class="menu">
                <a href="../adminHomePage.php">Trang chủ</a>
                <a href="../loginFunction/profile.php">Thông tin tài khoản</a>
                <a href="../loginFunction/logout.php">Đăng xuất</a>
            </nav>
        </div>
    </header>
    <h2>Quản lý khách hàng</h1>
    <ul>
        <li><a href="them_khachhang.php">Thêm khách hàng</a></li>
        <li><a href="tracuu_khachhang.php">Tra cứu khách hàng</a></li>
    </ul>
</body>
</html>