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

$days = [];
$now = new DateTime();
for ($i = 6; $i >= 0; $i--) {
    $d = (clone $now)->modify("-{$i} days");
    $days[] = $d->format('d/m');
}
$labels = $days;
$revenueData = [];

foreach ($days as $d) {
    list($day, $month) = explode('/', $d);
    $year = $now->format('Y');
    // Doanh thu
    $sql = "SELECT SUM(TongTien) as revenue FROM hoadon WHERE DAY(NgayLap) = ? AND MONTH(NgayLap) = ? AND YEAR(NgayLap) = ? ";
    $stmt = mysqli_prepare($mysqli, $sql);
    mysqli_stmt_bind_param($stmt, 'iii', $day, $month, $year);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    $revenueData[] = (float)($row['revenue'] ?? 0);
    mysqli_stmt_close($stmt);
}

echo json_encode([
    'labels' => $labels,
    'datasets' => [
        ['label' => 'Doanh thu', 'data' => $revenueData]
    ]
]);