<?php
include '../../database_connect.php';

$formMode = $_POST['form_mode'];
$maSach = $_POST['ma_sach'];
$tenSach = $_POST['ten_sach'];
$danhMuc = $_POST['danh_muc'];
$theLoaiStr = $_POST['the_loai'];
$theLoaiArr = explode(',', $theLoaiStr);
$tacGia = $_POST['tac_gia'];
$nhaxb = $_POST['nxb'];
$ngayxb = $_POST['ngay_xb'];
$ngonNgu = $_POST['ngon_ngu'];
$soluongTon = $_POST['sl_ton'];
$giaBan = $_POST['gia_ban'];

if ($formMode === "new"){
    // kiểm tra xem sách đã tồn tại chưa
    $stmt = $mysqli->prepare("SELECT MaSach FROM sach WHERE MaSach = ? AND TenSach = ? AND TacGia = ?");
    $stmt->bind_param("sss", $maSach, $tenSach, $tacGia);
    $stmt->execute();
    $stmt->store_result();

    //đã tồn tại rồi
    if ($stmt->num_rows > 0) {
        echo "book_exists";
        exit;
    } else {
        $stmt = $mysqli->prepare("INSERT INTO sach (MaSach, TenSach, MaDMS, TheLoai, NhaXuatBan, NgayXuatBan, TacGia, NgonNgu, GiaBan, SoLuongTon) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssssssdi', $maSach, $tenSach, $danhMuc, $theLoaiStr, $nhaxb, $ngayxb, $tacGia, $ngonNgu, $giaBan, $soluongTon);
        if ($stmt->execute()) {
            echo "OK";
        } else {
            echo "ERROR: " . $stmt->error;
        }
        $stmt->close();

        foreach ($theLoaiArr as $theloai){
        $stmt = $mysqli->prepare('INSERT INTO sach_theloai(MaSach, MaTL) VALUES(?, ?)');
        $stmt->bind_param('ss', $maSach, $theloai);
        $stmt->execute();
        $stmt->close();
        }
    }
} else if($formMode === "edit"){
    $stmt = $mysqli->prepare("DELETE FROM sach_theloai WHERE MaSach = ?");
    $stmt->bind_param('s', $maSach);
    $stmt->execute();
    $stmt->close();

    foreach ($theLoaiArr as $theloai){
        $stmt = $mysqli->prepare('INSERT INTO sach_theloai(MaSach, MaTL) VALUES(?, ?)');
        $stmt->bind_param('ss', $maSach, $theloai);
        $stmt->execute();
        $stmt->close();
    }

    $stmt = $mysqli->prepare("UPDATE sach SET MaDMS = ?, TheLoai = ?, NhaXuatBan = ?, NgayXuatBan = ?, TacGia = ?, NgonNgu = ?, GiaBan = ?, SoLuongTon = ? WHERE MaSach = ?");
    $stmt->bind_param('ssssssdis', $danhMuc, $theLoaiStr, $nhaxb, $ngayxb, $tacGia, $ngonNgu, $giaBan, $soluongTon, $maSach);
    if ($stmt->execute()) {
        echo "OK";
    } else {
        echo "ERROR: " . $stmt->error;
    }
    $stmt->close();
}

$mysqli->close();
?>
