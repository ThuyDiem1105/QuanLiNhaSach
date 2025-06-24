<?php
include __DIR__ . '/../../connect.php';

$data = json_decode(file_get_contents('php://input'), true);
$maHD = $data['ma_hd'] ?? '';
$tienNo = $data['tien_no'] ?? '';

if (!$maHD || !is_numeric($tienNo)) {
    echo "ERROR: Dữ liệu không hợp lệ";
    exit;
}

$stmt = $mysqli->prepare("UPDATE hoadon SET TienNo = ? WHERE MaHD = ?");
$stmt->bind_param("ds", $tienNo, $maHD);

if ($stmt->execute()) {
    echo "OK";
} else {
    echo "ERROR: " . $stmt->error;
}
$stmt->close();
$mysqli->close();
?> 