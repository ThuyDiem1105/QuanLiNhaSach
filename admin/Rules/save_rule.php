<?php
session_start();
include __DIR__ . '/../../connect.php';

$maQD = $_GET['ma_qd'];
$nhapMin = $_POST['min_import'];
$tonMax = $_POST['max_stock'];
$tonMinSauBan = $_POST['min_stock_aftersale'];
$tonMaxDeNhap = $_POST['max_stock_toimport'];
$calamMin = $_POST['min_shifts'];
$tiLeBan = $_POST['price_rate'];
$noMaxThuong = $_POST['max_debt_normal'];
$noMaxVip = $_POST['max_debt_vip'];

$tiLeBan = floatval($tiLeBan) / 100;

$stmt = $mysqli->prepare("INSERT INTO quydinh (MaQD, TonKhoMax, TonMinSauBan, SLNhapMin, TonMaxDeNhap, SoCaMin, TiLeBan, NoThuongMax, NoVipMax) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("siiiiiddd", $maQD, $tonMax, $tonMinSauBan, $nhapMin, $tonMaxDeNhap, $calamMin, $tiLeBan, $noMaxThuong, $noMaxVip);
if ($stmt->execute()) {
echo "OK";
} else {
echo "ERROR: " . $stmt->error;
}
$stmt->close();

$result = $mysqli->query("SELECT * FROM quydinh ORDER BY NgayTao DESC LIMIT 1");
$latestRule = $result->fetch_assoc();
$_SESSION['latest_rule'] = $latestRule;
$mysqli->close();
?>
