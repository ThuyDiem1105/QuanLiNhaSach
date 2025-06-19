<?php
include __DIR__ . '/../../connect.php';

$maKH = $_GET['ma_kh'] ?? '';

if ($maKH) {
    $stmt = $mysqli->prepare("SELECT HoTen FROM khachhang WHERE MaKH = ?");
    $stmt->bind_param("s", $maKH);
    $stmt->execute();
    $stmt->bind_result($tenKH);
    if ($stmt->fetch()) {
        echo $tenKH;
    } else {
        echo "";
    }
    $stmt->close();
}
?>
