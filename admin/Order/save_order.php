<?php
include __DIR__ . '/../../connect.php';

$data = json_decode(file_get_contents("php://input"), true);

$maHD = $data['ma_hd'];
$maKH = $data['ma_kh'];
$tenKH = $data['ten_kh'];
$ngayLap = $data['ngay_lap'];
$tongTien = $data['tong_tien'];
$tienTra = $data['tien_tra'];
$tienNo = $data['tien_no'];
$sachBan = $data['books'];


error_log(print_r($sachBan, true));
$mysqli->begin_transaction();

try {
    // Kiểm tra xem đã tồn tại hóa đơn chưa
    $stmt = $mysqli->prepare("SELECT MaHD FROM hoadon WHERE MaHD = ?");
    $stmt->bind_param('s', $maHD);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows() > 0){
        echo 'order_exists';
        exit;
    }
    $stmt->free_result();
    $stmt->close();

    // Kiểm tra xem KH tồn tại chưa
    $stmt = $mysqli->prepare("SELECT MaKH FROM khachhang WHERE MaKH = ?");
    $stmt->bind_param('s', $maKH);
    $stmt->execute();
    $stmt->store_result();
    // chưa tồn tại khách hàng
    if ($stmt->num_rows() <= 0){ 
        $stmt = $mysqli->prepare("INSERT INTO khachhang (MaKH, TenKH) VALUES (?, ?)");
        $stmt->bind_param("ss", $maKH, $tenKH);
        $stmt->execute();
    }
    $stmt->free_result();
    $stmt->close();

    // Thêm hóa đơn
    $stmt = $mysqli->prepare("INSERT INTO hoadon (MaHD, MaKH, NgayLap, TongTien, TienTra, TienNo) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssddd", $maHD, $maKH, $ngayLap, $tongTien, $tienTra, $tienNo);
    $stmt->execute();
    $stmt->close();

    //Thêm vào tiền nợ của khách hàng
    $stmt = $mysqli->prepare("UPDATE khachhang SET SoTienNo = SoTienNo + ? WHERE MaKH = ?");
    $stmt->bind_param("is", $tienNo, $maKH);
    $stmt->execute();
    $stmt->close();

    // Thêm vào chi tiết hóa đơn
    $stmtBook = $mysqli->prepare("INSERT INTO chitiet_hoadon (MaHD, MaSach, SoLuong, GiaBan, ThanhTien) VALUES (?, ?, ?, ?, ?)");
    foreach ($sachBan as $book) {
        $stmtBook->bind_param( "ssidd", $maHD, $book['ma_sach'], $book['so_luong'], $book['gia_ban'], $book['thanh_tien']);
        $stmtBook->execute();

        // Update số lượng tồn của sách được mua
        $stmtUpdate = $mysqli->prepare("UPDATE sach SET SoLuongTon = SoLuongTon - ? WHERE MaSach = ?");
        $stmtUpdate->bind_param("is", $book['so_luong'], $book['ma_sach']);
        $stmtUpdate->execute();
        $stmtUpdate->close();
    }
    $stmtBook->close();
    $mysqli->commit();
    echo "OK";

} catch (Exception $e) {
  $mysqli->rollback();
  echo "ERROR: " . $e->getMessage();
}

?>
