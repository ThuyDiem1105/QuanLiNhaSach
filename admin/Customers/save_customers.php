<?php
session_start();
include __DIR__ . '/../../connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Invalid request.";
    exit;
}

$ma_kh = trim($_POST['ma_kh'] ?? '');
$ten_kh = trim($_POST['ten_kh'] ?? '');
$sdt = trim($_POST['sdt'] ?? '');
$diachi = trim($_POST['diachi'] ?? '');
$email = trim($_POST['email'] ?? '');
$loai = trim($_POST['loai'] ?? '');
$so_tien_no = floatval($_POST['so_tien_no'] ?? 0);
$form_mode = trim($_POST['form_mode'] ?? '');

// Validate required fields
if ($ma_kh === '' || $ten_kh === '' || $sdt === '' || $diachi === '' || $email === '' || $loai === '') {
    echo "Vui lòng nhập đầy đủ thông tin.";
    exit;
}

if (!in_array($loai, ['Thường', 'VIP'])) {
    echo "Loại khách hàng không hợp lệ.";
    exit;
}

if ($form_mode === 'new') {
    // Check if MaKH already exists
    $stmt = $mysqli->prepare("SELECT MaKH FROM khachhang WHERE MaKH = ?");
    $stmt->bind_param("s", $ma_kh);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo "Khách hàng đã tồn tại.";
        exit;
    }
    $stmt->close();

    // Insert new customer
    $stmt = $mysqli->prepare("INSERT INTO khachhang (MaKH, HoTen, SDT, DiaChi, Email, Loai, SoTienNo) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssd", $ma_kh, $ten_kh, $sdt, $diachi, $email, $loai, $so_tien_no);
    if ($stmt->execute()) {
        echo "OK";
    } else {
        echo "Lỗi khi thêm khách hàng: " . $stmt->error;
    }
    $stmt->close();
} elseif ($form_mode === 'edit') {
    // Update existing customer
    $stmt = $mysqli->prepare("UPDATE khachhang SET HoTen = ?, SDT = ?, DiaChi = ?, Email = ?, Loai = ?, SoTienNo = ? WHERE MaKH = ?");
    $stmt->bind_param("ssssssd", $ten_kh, $sdt, $diachi, $email, $loai, $so_tien_no, $ma_kh);
    if ($stmt->execute()) {
        echo "OK";
    } else {
        echo "Lỗi khi cập nhật khách hàng: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Dữ liệu form không hợp lệ.";
}

$mysqli->close();
