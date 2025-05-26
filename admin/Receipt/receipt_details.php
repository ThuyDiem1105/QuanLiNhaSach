<?php
include __DIR__ . '/../../connect.php';
$maPN = $_GET['ma_pn'];

//lấy thông tin phiếu nhập có mã phiếu được chọn
$stmt = $mysqli->prepare("SELECT * FROM phieunhap WHERE MaPN = ?");
$stmt->bind_param("s", $maPN);
$stmt->execute();
$receipt = $stmt->get_result()->fetch_assoc();

//lấy danh sách các sách được nhập trong một phiếu nhập đó
$receiptBooks= [];
$res = $mysqli->query("SELECT * FROM chitiet_phieunhap WHERE MaPN = '$maPN'");
while ($row = $res->fetch_assoc()) {
    $receiptBooks[] = $row;
}

header('Content-Type: application/json');
echo json_encode(['receipt' => $receipt, 'receiptBooks' => $receiptBooks]);
?>