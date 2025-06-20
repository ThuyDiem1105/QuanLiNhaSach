<?php
header('Content-Type: application/json');
require_once '../connect.php';

if (!isset($mysqli) || !$mysqli) {
    echo json_encode([
        'orders_today' => 0,
        'books_sold_today' => 0,
        'revenue_today' => 0,
        'total_customers' => 0,
        'error' => 'Database connection failed.'
    ]);
    exit;
}

$today = date('Y-m-d');

// Đơn hàng hôm nay
$sql = "SELECT COUNT(*) as orders_today FROM hoadon WHERE DATE(NgayLap) = ?";
$stmt = mysqli_prepare($mysqli, $sql);
mysqli_stmt_bind_param($stmt, 's', $today);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($res);
$orders_today = (int)($row['orders_today'] ?? 0);
mysqli_stmt_close($stmt);

// Số sách bán ra hôm nay
$sql = "SELECT SUM(SoLuong) as books_sold_today FROM chitiet_hoadon od JOIN hoadon o ON od.MaHD = o.MaHD WHERE DATE(o.NgayLap) = ?";
$stmt = mysqli_prepare($mysqli, $sql);
mysqli_stmt_bind_param($stmt, 's', $today);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($res);
$books_sold_today = (int)($row['books_sold_today'] ?? 0);
mysqli_stmt_close($stmt);

// Doanh thu hôm nay
$sql = "SELECT SUM(TongTien) as revenue_today FROM hoadon WHERE DATE(NgayLap) = ?";
$stmt = mysqli_prepare($mysqli, $sql);
mysqli_stmt_bind_param($stmt, 's', $today);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($res);
$revenue_today = (int)($row['revenue_today'] ?? 0);
mysqli_stmt_close($stmt);

// Tổng số khách hàng
$sql = "SELECT COUNT(*) as total_customers FROM khachhang";
$result = mysqli_query($mysqli, $sql);
$row = mysqli_fetch_assoc($result);
$total_customers = (int)($row['total_customers'] ?? 0);

// Trả về JSON
echo json_encode([
    'orders_today' => $orders_today,
    'books_sold_today' => $books_sold_today,
    'revenue_today' => $revenue_today,
    'total_customers' => $total_customers
]);
