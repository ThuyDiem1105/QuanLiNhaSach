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

$avatarDir = __DIR__ . '/../assets/avatars/';
$avatarUrlPath = '../assets/avatars/';

// Lấy tất cả các file ảnh trong thư mục (jpg, jpeg, png, webp)
$avatarFiles = glob($avatarDir . '*.{jpg,jpeg,png,webp}', GLOB_BRACE);

// Kiểm tra và chọn ngẫu nhiên
if ($avatarFiles && count($avatarFiles) > 0) {
    $randomAvatarPath = $avatarFiles[array_rand($avatarFiles)];
    // Chuyển đường dẫn vật lý thành đường dẫn URL tương đối
    $avatar = $avatarUrlPath . basename($randomAvatarPath);
} else {
    // Nếu không có avatar nào thì dùng avatar mặc định
    $avatar = '../../assets/avatar4.png';
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>THÔNG TIN CÁ NHÂN</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/rules-style.css">
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
        background: #f0f4f8;
        color: #2c3e50;
    }

    .content {
        padding: 30px 20px;
        background: linear-gradient(to right, #A3E4D7, #E8F8F5);
        text-align: center;
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }

    .page-title h2 {
        font-size: 36px;
        margin-bottom: 5px;
        font-weight: 600;
    }

    .page-title p {
        font-size: 16px;
        color: #555;
    }

    .block {
        max-width: 700px;
        margin: 30px auto;
        background-color: #fff;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.05);
    }

    .profile-detail {
        padding: 12px 0;
        border-bottom: 1px solid #e0e0e0;
        font-size: 18px;
    }

    .profile-detail:last-child {
        border-bottom: none;
    }

    .profile-detail strong {
        color: #34495e;
        display: inline-block;
        width: 160px;
    }

    @media (max-width: 600px) {
        .profile-detail {
            font-size: 16px;
        }
        .profile-detail strong {
            width: 120px;
        }
    }
    .avatar-container {
        display: flex;
        justify-content: center;
        margin-bottom: 25px;
    }

    .avatar-image {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: transform 0.3s ease;
    }

    .avatar-image:hover {
        transform: scale(1.05);
    }

    </style>

</head>
<body>
    <div class="content">
        <div class="avatar-container">
            <img src="<?= htmlspecialchars($avatar) ?>" alt="Avatar ngẫu nhiên" class="avatar-image">
        </div>

        <div class="page-title">
            <div class="wrap">
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
</body>
</html>