<?php
session_start();
if (!isset($_SESSION['account_loggedin'])) {
    header('Location: loginFunction/mainPage.php');
    exit;
}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, minimum-scale=1">
    <title>TRANG CHỦ</title>
    <title>Trang quản lý nhà sách - Backend</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
        }
        h1 {
            color: #2c3e50;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        li {
            margin: 10px 0;
        }
        a {
            color: #2980b9;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            color: #c0392b;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="wrapper">
            <h1>Hệ thống quản lý nhà sách</h1>
            <nav class="menu">
                <a href="adminHomePage.php">Trang chủ</a>
                <a href="loginFunction/profile.php">Thông tin tài khoản</a>
                <a href="loginFunction/logout.php">Đăng xuất</a>
            </nav>
        </div>
    </header>
    <div class="content">
        <div class="wrap">
            <h2>Trang chủ</h2>
            <p>Chào mừng quay trở lại, <?htmlspecialchars($_SESSION['account_name'], ENT_QUOTES)?>!</p>
        </div>
    </div>
    <div class="block">
        <p>Đây là trang chủ của hệ thống. Bạn đã đăng nhập thành công!</p>
    </div>
    <h3>Danh sách các chức năng API (backend)</h1>
    <ul>
        <li><a href="QuanLySach/quanly_sach.php">Quản lý sách</a></li>
        <li><a href="QuanLyNhanVien/quanly_nhanvien.php">Quản lý nhân viên</a></li>
        <li><a href="QuanLyKhachHang/quanly_khachhang.php">Quản lý khách hàng</a></li>
        <li><a href="baocao_congno.php">Báo cáo công nợ</a></li>
        <li><a href="baocao_doanhthu.php">Báo cáo doanh thu</a></li>
        <li><a href="baocao_kho.php">Báo cáo kho</a></li>
        <li><a href="phanquyen.php">Phân quyền</a></li>
        <li><a href="them_hoadon.php">Thêm hóa đơn</a></li>
        <li><a href="them_khuyenmai.php">Thêm khuyến mãi</a></li>
        <li><a href="tracuu_hoadon.php">Tra cứu hóa đơn</a></li>
        <li><a href="tracuu_khuyenmai.php">Tra cứu khuyến mãi</a></li>
        <li><a href="tracuu_sanpham.php">Tra cứu sản phẩm</a></li>
    </ul>
</body>
</html>
