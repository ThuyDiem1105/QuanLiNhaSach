<?php
session_start();
include __DIR__ . '/../connect.php';

//nếu chưa login thì quay lại trang đăng nhập
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] === 'Admin'){     
    header('Location: ../../loginFunction/login.php'); 
    exit;
}

$id = $_SESSION['id'];
$stmt = $mysqli->prepare("SELECT HoTen, NgaySinh, SDT, NoiO, ChucVu, Email, TenDN FROM taikhoan tk JOIN nhanvien nv on tk.MaNV = nv.MaNV WHERE nv.MaNV = ?");
$stmt->bind_param('s', $id);
$stmt->execute();
$stmt->bind_result($name, $dob, $phone, $address, $position, $email, $username);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>THÔNG TIN CÁ NHÂN</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/rules-style.css">
</head>
<body>
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
            <strong>Họ tên: </strong><?=htmlspecialchars($name)?>
        </div>            
        <div class="profile-detail">
            <strong>Chức vụ: </strong><?=htmlspecialchars($position)?>
        </div>
        <div class="profile-detail">
            <strong>Ngày sinh: </strong><?=htmlspecialchars($dob)?>
        </div>
        <div class="profile-detail">
            <strong>Số điện thoại: </strong><?=htmlspecialchars($phone)?>
        </div>
        <div class="profile-detail">
            <strong>Quê quán: </strong><?=htmlspecialchars($address)?>
        </div>
        <div class="profile-detail">
            <strong>Tên tài khoản: </strong><?=htmlspecialchars($username)?>
        </div>
        <div class="profile-detail">
            <strong>Địa chỉ email: </strong><?=htmlspecialchars($email)?>
        </div>
    </div>

    <script>
        
    </script>
</body>
</html>
