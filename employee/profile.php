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
    <link rel="stylesheet" href="../assets/style.css" type="text/css">
    <style>
        body {
            font-family: fontweb;
            background-color: #f7faff;
            margin: 0;
            padding: 30px;
            color: #495057;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            box-sizing: border-box;
        }

        .container {
            display: grid;
            grid-template-columns: 280px auto;
            align-items: center;
            gap: 30px;
            background: #ffffff;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            max-height: 650px;
            overflow-y: auto;
        }

        .profile-header {
            text-align: center;
        }

        .avatar-image {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 4px solid #eef7ff;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .user-name {
            font-size: 28px;
            font-weight: 700;
            color: #0d3c6b;
            margin: 0 0 5px 0;
        }

        .user-position {
            font-size: 18px;
            color: #1c5083;
            margin: 0;
            font-weight: 500;
        }

        .profile-details-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            text-align: left;
        }

        .detail-item {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 12px;
            border: 1px solid #e9ecef;
            flex: 1 1 220px;
        }

        .detail-item.full-width {
            flex-basis: 100%;
        }

        .detail-item strong {
            display: block;
            color: #0d3c6b;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .detail-item span {
            font-size: 16px;
            color: #34495e;
        }

        @media (max-width: 768px) {
            .container {
                padding: 30px 25px;
                grid-template-columns: 1fr;
                gap: 30px;
                align-items: stretch;
            }
        }
    </style>

</head>
<body>
    <div class="container">
        <div class="profile-header">
            <img src="<?= htmlspecialchars($avatar) ?>" alt="Avatar" class="avatar-image">
            <h2 class="user-name"><?= htmlspecialchars($name) ?></h2>
            <p class="user-position"><?= htmlspecialchars($position) ?></p>
        </div>

        <div class="profile-details-grid">
            <div class="detail-item">
                <strong>Mã nhân viên:</strong>
                <span><?= htmlspecialchars($id) ?></span>
            </div>
            <div class="detail-item">
                <strong>Ngày sinh:</strong>
                <span><?= htmlspecialchars($dob) ?></span>
            </div>
            <div class="detail-item">
                <strong>Số điện thoại:</strong>
                <span><?= htmlspecialchars($phone) ?></span>
            </div>
            <div class="detail-item">
                <strong>Quê quán:</strong>
                <span><?= htmlspecialchars($address) ?></span>
            </div>
            <div class="detail-item full-width">
                <strong>Tên tài khoản:</strong>
                <span><?= htmlspecialchars($username) ?></span>
            </div>
            <div class="detail-item full-width">
                <strong>Địa chỉ email:</strong>
                <span><?= htmlspecialchars($email) ?></span>
            </div>
        </div>
    </div>
</body>
</html>
