<?php
// File: them_hoadon.php
include 'connect.php'; // file kết nối đến database

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $maKhachHang = $_POST['maKhachHang'];
    $ngayLap = $_POST['ngayLap'];
    $soTienTra = floatval($_POST['soTienTra']);
    $sanPham = $_POST['sanPham']; // mảng chứa các sách: [ [maSach, soLuong], ... ]

    $tongTien = 0;
    $dsChiTiet = [];
    $error = '';

    foreach ($sanPham as $item) {
        $maSach = $item['maSach'];
        $soLuong = intval($item['soLuong']);

        // Lấy thông tin sách
        $stmt = $conn->prepare("SELECT SoLuongTon, DonGiaNhap FROM sach WHERE MaSach = ?");
        $stmt->bind_param("s", $maSach);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $error = "Sách không tồn tại: $maSach";
            break;
        }

        $row = $result->fetch_assoc();
        $soLuongTon = $row['SoLuongTon'];
        $donGiaNhap = $row['DonGiaNhap'];

        if ($soLuongTon - $soLuong < 20) {
            $error = "Sách $maSach không đủ số lượng tồn sau bán (phải ≥ 20).";
            break;
        }

        $donGiaBan = round($donGiaNhap * 1.05, 2);
        $thanhTien = $donGiaBan * $soLuong;
        $tongTien += $thanhTien;

        $dsChiTiet[] = [
            'maSach' => $maSach,
            'soLuong' => $soLuong,
            'donGiaBan' => $donGiaBan,
            'thanhTien' => $thanhTien
        ];
    }

    if ($error === '') {
        // Tạo mã hóa đơn
        $maHD = uniqid('HD');
        $conLai = $tongTien - $soTienTra;

        // Thêm hóa đơn
        $stmt = $conn->prepare("INSERT INTO hoadon (MaHoaDon, MaKhachHang, NgayLap, TongTien, DaThanhToan, ConLai) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssddd", $maHD, $maKhachHang, $ngayLap, $tongTien, $soTienTra, $conLai);
        $stmt->execute();

        // Thêm chi tiết hóa đơn và cập nhật tồn kho
        foreach ($dsChiTiet as $ct) {
            $stmt = $conn->prepare("INSERT INTO ct_hoadon (MaHoaDon, MaSach, SoLuong, DonGiaBan, ThanhTien) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssidd", $maHD, $ct['maSach'], $ct['soLuong'], $ct['donGiaBan'], $ct['thanhTien']);
            $stmt->execute();

            // Cập nhật tồn kho
            $stmt = $conn->prepare("UPDATE sach SET SoLuongTon = SoLuongTon - ? WHERE MaSach = ?");
            $stmt->bind_param("is", $ct['soLuong'], $ct['maSach']);
            $stmt->execute();
        }

        echo json_encode(["success" => true, "message" => "Thêm hóa đơn thành công"]);
    } else {
        echo json_encode(["success" => false, "message" => $error]);
    }
}
?>
