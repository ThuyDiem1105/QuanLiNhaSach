<?php
include '../../connect.php';

$formMode   = $_POST['form_mode']   ?? '';
$maKM       = $_POST['ma_km']       ?? '';
$TenKM      = $_POST['ten_km']      ?? '';
$NgayBatDau = $_POST['ngay_bat_dau'] ?? '';
$NgayKetThuc = $_POST['ngay_ket_thuc'] ?? '';
$DieuKienApDung = $_POST['dieu_kien_ap_dung'] ?? '';
$TrangThai  = $_POST['trang_thai'] ?? '';

if (
    $formMode === '' || $maKM === '' || $TenKM === '' ||
    $NgayBatDau === '' || $NgayKetThuc === '' || $DieuKienApDung === '' || $TrangThai === ''
) {
    echo "Nhập đầy đủ thông tin khuyến mãi.";
    $mysqli->close();
    exit;
}

// ...phần xử lý tiếp theo...
if ($formMode === "new") {
    // Kiểm tra xem khuyến mãi đã tồn tại chưa
    $stmt = $mysqli->prepare("SELECT MaKM FROM khuyenmai WHERE MaKM = ?");
    $stmt->bind_param("s", $maKM);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "deal_exists"; // Khuyến mãi đã tồn tại
        $stmt->close();
        $mysqli->close();
        exit;
    }
    $stmt->close();

    // Thêm khuyến mãi mới
    $stmt = $mysqli->prepare("INSERT INTO khuyenmai (MaKM, TenKM, NgayBatDau, NgayKetThuc, DieuKienApDung, TrangThai) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssss', $maKM, $TenKM, $NgayBatDau, $NgayKetThuc, $DieuKienApDung, $TrangThai);
    if ($stmt->execute()) {
        echo "OK"; // Thêm thành công
    } else {
        echo "ERROR: " . $stmt->error; // Lỗi khi thêm
    }
    $stmt->close();
} else if($formMode === "edit"){
    $stmt = $mysqli->prepare("UPDATE khuyenmai SET TenKM = ?, NgayBatDau = ?, NgayKetThuc = ?, DieuKienApDung = ?, TrangThai = ? WHERE MaKM = ?");
    $stmt->bind_param('ssssss', $TenKM, $NgayBatDau, $NgayKetThuc, $DieuKienApDung, $TrangThai, $maKM);
    if ($stmt->execute()) {
        echo "OK";
    } else {
        echo "ERROR: " . $stmt->error;
    }
    $stmt->close();
}

$mysqli->close();
?>
