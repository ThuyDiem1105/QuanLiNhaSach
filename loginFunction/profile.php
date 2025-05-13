<?php
session_start();
//nếu chưa login thì quay lại trang đăng nhập
if (!isset($_SESSION['account_loggedin'])) {
    header('Location: mainPage.php');
    exit;
}

$connection = mysqli_connect('localhost', 'root', '', 'phplogin');
if (mysqli_connect_errno()) {
    $message = '<br />Lỗi kết nối thất bại đến MySql: ' . mysqli_connect_error();
}

$id = $_SESSION['account_id'];
$stmt = $connection->prepare('SELECT Email, TenDN, Quyen FROM taikhoan WHERE MaNV = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->bind_result($email, $username, $role);
$stmt->fetch();
$stmt->close();
?>


<!DOCTYPE html>
<html>
    <header>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, minimum-scale=1">
        <title>TRANG CHỦ</title>
        <link href="style.css" rel="stylesheet" type="text/css">
    </header>
    <body>
        <header class="header">
            <div class="wrapper">
                <h1>Hệ thống quản lý nhà sách</h1>
                <nav class="menu">
                    <a href="../homePage.php">Trang chủ</a>
                    <a href="profile.php">Thông tin tài khoản</a>
                    <a href="logout.php">Đăng xuất</a>
                </nav>
            </div>
        </header>
        <div class="content">
            <div class="page-title">
                <div class="wrap">
                <h2>Profile</h2>
                <p>Đây là thông tin cá nhân chi tiết của bạn.</p>
                </div>
            </div>
        </div>

        <div class="block">
            <div class="profile-detail">
                <strong>Mã nhân viên: </strong><?=htmlspecialchars($id)?>
            </div>
            <div class="profile-detail">
                <strong>Tên tài khoản: </strong><?=htmlspecialchars($username)?>
            </div>
            <div class="profile-detail">
                <strong>Quyền: </strong><?=htmlspecialchars($role)?>
            </div>
            <div class="profile-detail">
                <strong>Địa chỉ email:</strong><?=htmlspecialchars($email)?>
            </div>
        </div>
    </body>
</html>