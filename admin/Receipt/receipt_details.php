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
$stmt2 = $mysqli->prepare("SELECT MaSach, SoLuong, DonGiaNhap, ThanhTien FROM chitiet_phieunhap WHERE MaPN = ?");
$stmt2->bind_param("s", $maPN);
$stmt2->execute();
$result = $stmt2->get_result();
while ($row = $result->fetch_assoc()) {
    $receiptBooks[] = $row;
}
$stmt2->close();

header('Content-Type: application/json');
echo json_encode(['receipt' => $receipt, 'receiptBooks' => $receiptBooks]);

$stmt->close();
?>