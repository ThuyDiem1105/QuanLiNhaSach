<?php
header('Content-Type: application/json');
require_once '../connect.php';

if (!isset($mysqli) || !$mysqli) {
    echo json_encode([
        'labels' => [],
        'datasets' => [],
        'error' => 'Database connection failed.'
    ]);
    exit;
}

// Get last 7 months labels
$months = [];
$now = new DateTime();
for ($i = 6; $i >= 0; $i--) {
    $d = (clone $now)->modify("-{$i} months");
    $months[] = $d->format('m/Y');
}

$labels = $months;
$bookData = [];

// Get top 3 books overall in last 7 months
$sql = "SELECT b.MaSach, b.TenSach, SUM(od.SoLuong) as total_sold
        FROM chitiet_hoadon od
        JOIN hoadon o ON od.MaHD = o.MaHD
        JOIN Sach b ON od.MaSach = b.MaSach
        WHERE o.NgayLap >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 6 MONTH), '%Y-%m-01')
        GROUP BY b.MaSach, b.TenSach
        ORDER BY total_sold DESC
        LIMIT 3";
$result = mysqli_query($mysqli, $sql);
$books = [];
while ($row = mysqli_fetch_assoc($result)) {
    $books[] = [
        'MaSach' => $row['MaSach'],
        'TenSach' => $row['TenSach'],
    ];
}

// For each book, get sales per month
foreach ($books as $book) {
    $data = [];
    foreach ($months as $m) {
        list($month, $year) = explode('/', $m);
        $sql = "SELECT SUM(od.SoLuong) as qty
                FROM chitiet_hoadon od
                JOIN hoadon o ON od.MaHD = o.MaHD
                WHERE od.MaSach = ? AND MONTH(o.NgayLap) = ? AND YEAR(o.NgayLap) = ?";
        $stmt = mysqli_prepare($mysqli, $sql);
        mysqli_stmt_bind_param($stmt, 'sii', $book['MaSach'], $month, $year);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($res);
        $data[] = (int)($row['qty'] ?? 0);
        mysqli_stmt_close($stmt);
    }
    $bookData[] = [
        'label' => $book['TenSach'],
        'data' => $data
    ];
}

echo json_encode([
    'labels' => $labels,
    'datasets' => $bookData
]);
