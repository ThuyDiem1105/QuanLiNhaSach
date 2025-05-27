<?php
include __DIR__ . '/../../connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $maNV = $_POST['ma_nv'];
    $stmt = $mysqli->prepare("DELETE FROM taikhoan WHERE MaNV = ?");
    $stmt->bind_param('s', $maNV);
    if ($stmt->execute()) {
        echo "OK";
        $stmt->close();
        $mysqli->close();
    } else {
        echo "Lỗi: " . $stmt->error;
        exit;
    }
}
?>
