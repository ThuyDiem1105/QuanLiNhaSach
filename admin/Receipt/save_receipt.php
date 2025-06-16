<?php
include __DIR__ . '/../../connect.php';

$data = json_decode(file_get_contents("php://input"), true);

$tile_ban = $_GET['tile_ban'];
$maPN = $data['ma_pn'];
$ngayLap = $data['ngay_lap'];
$ngayNhap = $data['ngay_nhap'];
$tongTien = $data['tong_tien'];
$sachNhap = $data['books'];
$markup = 1.05;

error_log(print_r($sachNhap, true));
$mysqli->begin_transaction();

try {
    // Kiểm tra xem đã tồn tại phiếu nhập chưa
    $stmt = $mysqli->prepare("SELECT MaPN FROM phieunhap WHERE MaPN = ?");
    $stmt->bind_param('s', $maPN);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows() > 0){
        echo 'receipt_exists';
        exit;
    }
    $stmt->free_result();
    $stmt->close();

    // Thêm phiếu nhập
    $stmt = $mysqli->prepare("INSERT INTO phieunhap (MaPN, NgayLapPhieu, NgayNhap, TongTien) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssd", $maPN, $ngayLap, $ngayNhap, $tongTien);
    $stmt->execute();
    $stmt->close();

    // Thêm vào chi tiết phiếu nhập
    $stmtBook = $mysqli->prepare("INSERT INTO chitiet_phieunhap (MaPN, MaSach, SoLuong, DonGiaNhap, ThanhTien) VALUES (?, ?, ?, ?, ?)");
    foreach ($sachNhap as $book) {
        $stmtBook->bind_param( "ssidd", $maPN, $book['ma_sach'], $book['so_luong'], $book['don_gia'], $book['thanh_tien']);
        $stmtBook->execute();

        // Update số lượng tồn của sách được chọn nhập
        $stmtUpdate = $mysqli->prepare("UPDATE sach SET SoLuongTon = SoLuongTon + ? WHERE MaSach = ?");
        $stmtUpdate->bind_param("is", $book['so_luong'], $book['ma_sach']);
        $stmtUpdate->execute();
        $stmtUpdate->close();

        $giaban = $book['don_gia'] * (1 + $tile_ban);
        // Update giá bán của sách được chọn nhập, giá bán = 105% giá nhập
        $stmtUpdate = $mysqli->prepare("UPDATE sach SET GiaBan = ? WHERE MaSach = ?");
        $stmtUpdate->bind_param("is", $giaban, $book['ma_sach']);
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
