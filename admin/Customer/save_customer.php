<?php
include '../../connect.php';

$formMode   = $_POST['form_mode']   ?? '';
$maKH       = $_POST['ma_kh']       ?? '';
$hoTen      = $_POST['ho_ten']      ?? '';
$sdt        = $_POST['sdt']         ?? '';
$loai       = $_POST['loai']        ?? '';
$soTienNo   = $_POST['so_tien_no'] ?? '';

if (
    $formMode === '' || $maKH === '' || $hoTen === '' ||
    $sdt === '' || $loai === '' || $soTienNo === ''
) {
    echo "Nhập đầy đủ thông tin khách hàng.";
    $mysqli->close();
    exit;
}

// ...phần xử lý tiếp theo...
if ($formMode === "new"){
    // kiểm tra xem khách hàng đã tồn tại chưa
    $stmt = $mysqli->prepare("SELECT MaKH FROM khachhang WHERE MaKH = ? AND HoTen = ? AND SDT = ?");
    $stmt->bind_param("sss", $maKH, $hoTen, $sdt);
    $stmt->execute();
    $stmt->store_result();

    //đã tồn tại rồi
    if ($stmt->num_rows > 0) {
        echo "book_exists";
        exit;
    } else {
        $stmt = $mysqli->prepare("INSERT INTO khachhang (MaKH, HoTen, SDT, Loai, SoTienNo) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssi', $maKH, $hoTen, $sdt, $loai, $soTienNo);
        if ($stmt->execute()) {
            echo "OK";
        } else {
            echo "ERROR: " . $stmt->error;
        }
        $stmt->close();

    }
} else if($formMode === "edit"){
    $stmt = $mysqli->prepare("DELETE FROM khachhang WHERE MaKH = ?");
    $stmt->bind_param('s', $maKH);
    $stmt->execute();
    $stmt->close();

    }

    $stmt = $mysqli->prepare("UPDATE khachhang SET HoTen = ?, SDT = ?, Loai = ?, SoTienNo = ? WHERE MaKH = ?");
    $stmt->bind_param('ssssi', $hoTen, $sdt, $loai, $soTienNo, $maKH);
    if ($stmt->execute()) {
        echo "OK";
    } else {
        echo "ERROR: " . $stmt->error;
    }
    $stmt->close();

$mysqli->close();
?>
