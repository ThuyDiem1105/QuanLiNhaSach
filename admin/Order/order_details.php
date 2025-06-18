<?php
include __DIR__ . '/../../connect.php';
$maHD = $_GET['ma_hd'];

//lấy thông tin phiếu nhập có mã phiếu được chọn
$stmt = $mysqli->prepare("SELECT hd.MaHD, hd.MaKH, hd.NgayLap, hd.TienTra, hd.TienNo, hd.TongTien, kh.HoTen FROM hoadon hd JOIN khachhang kh ON hd.MaKH = kh.MaKH WHERE hd.MaHD = ?");
$stmt->bind_param("s", $maHD);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

//lấy danh sách các sách được mua trong hóa đơn đó
$orderBooks= [];
$result = $mysqli->query("SELECT cthd.MaSach, sach.TenSach, cthd.SoLuong, cthd.GiaBan, cthd.ThanhTien FROM chitiet_hoadon cthd JOIN sach ON cthd.MaSach = sach.MaSach WHERE MaHD = '$maHD'");
while ($row = $result->fetch_assoc()) {
    $orderBooks[] = $row;
}

header('Content-Type: application/json');
echo json_encode(['order' => $order, 'orderBooks' => $orderBooks]);
?>