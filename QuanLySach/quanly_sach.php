<?php
session_start();
if (isset($_POST['account_loggedin'])){
    header('Location: ../loginFunction/mainPage.php');
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, minimum-scale=1">
    <title>Quản lý sách</title>
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
    <h2>QUẢN LÝ SÁCH</h1>
    <ul>
        <li><a href="them_sach.php">Thêm sách</a></li>
        <li><a href="them_phieunhap.php">Thêm phiếu nhập sách</a></li>
        <li><a href="tracuu_phieunhap.php">Tra cứu phiếu nhập sách</a></li>
        <li><a href="tracuu_sach.php">Tra cứu sách</a></li>
    </ul>
</body>
</html>