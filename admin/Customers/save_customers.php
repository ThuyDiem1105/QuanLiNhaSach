<?php
include __DIR__ . '/../../connect.php';

$formMode = $_POST['form_mode'] ?? '';
$maKH = $_POST['ma_kh'] ?? '';
$hoTen = $_POST['ten_kh'] ?? '';    
$sdt = $_POST['sdt'] ?? '';
$diaChi = $_POST['diachi'] ?? '';
$email = $_POST['email'] ?? '';
$loai = $_POST['loai'] ?? '';
$sotienno = $_POST['so_tien_no'] ?? '';

// Đảm bảo số tiền nợ là số
$sotienno = is_numeric($sotienno) ? (float)$sotienno : 0;

if ($formMode === '' || $maKH === '' || $hoTen === '' || $sdt === '' || $diaChi === '' || $email === '' || $loai === '' || $sotienno === '') {
    echo "Nhập đầy đủ thông tin khách hàng.";
    $mysqli->close();
    exit;
}

if ($formMode === "new") {
    //Kiem tra khach hang da ton tai chua
    $stmt = $mysqli->prepare("SELECT MaKH FROM khachhang WHERE MaKH = ? OR Email = ?");
    $stmt->bind_param("ss", $maKH, $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo "Khách hàng đã tồn tại.";
        exit;
    } else {
        $stmt = $mysqli->prepare("INSERT INTO khachhang (MaKH, HoTen, SDT, DiaChi, Email, Loai, SoTienNo) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssssd', $maKH, $hoTen, $sdt, $diaChi, $email, $loai, $sotienno);
        if ($stmt->execute()) {
            echo "OK";
        } else {
            echo "ERROR: " . $stmt->error;
        }
        $stmt->close();
    }
} else if ($formMode === "edit") {
    // Cập nhật thông tin khách hàng
    $stmt = $mysqli->prepare("UPDATE khachhang SET HoTen = ?, SDT = ?, DiaChi = ?, Email = ?, Loai = ?, SoTienNo = ? WHERE MaKH = ?");
    $stmt->bind_param('ssssssd', $hoTen, $sdt, $diaChi, $email, $loai, $sotienno, $maKH);
    if ($stmt->execute()) {
        echo "OK";
    } else {
        echo "ERROR: " . $stmt->error;
    }
    $stmt->close();
}

$mysqli->close();
?>
