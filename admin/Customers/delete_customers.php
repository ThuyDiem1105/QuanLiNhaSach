<?php
// delete_customers.php
require_once '../../connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Phương thức không hợp lệ';
    exit;
}

$ma_kh = $_POST['ma_kh'] ?? '';
if (!$ma_kh) {
    echo 'Thiếu mã khách hàng';
    exit;
}

$stmt = $mysqli->prepare("DELETE FROM khachhang WHERE ma_kh = ?");
$stmt->bind_param('s', $ma_kh);
if ($stmt->execute()) {
    echo 'OK';
} else {
    echo 'Lỗi: ' . $stmt->error;
}
$stmt->close();
$mysqli->close();
exit;
