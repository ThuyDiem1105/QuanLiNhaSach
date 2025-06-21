<?php
// dashboard-orders.php
include __DIR__ . '/../connect.php';
// Lấy 5 đơn hàng gần nhất, join tên khách hàng
$sql = "SELECT h.NgayLap, h.MaHD, k.HoTen, h.TongTien
        FROM hoadon h
        LEFT JOIN khachhang k ON h.MaKH COLLATE utf8mb4_unicode_ci = k.MaKH COLLATE utf8mb4_unicode_ci
        ORDER BY h.NgayLap DESC, h.MaHD DESC
        LIMIT 5";
$result = $mysqli->query($sql);
$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}
header('Content-Type: application/json');
echo json_encode($orders);
