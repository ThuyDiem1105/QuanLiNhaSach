<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include __DIR__ . '/../../connect.php';

// Decode the JSON payload from the request
$payload = json_decode(file_get_contents('php://input'), true);

if (!$payload) {
    http_response_code(400);
    echo "Lỗi: Không nhận được dữ liệu.";
    exit;
}

// Extract data from the payload
$maPN = $payload['ma_pn'];
$ngayLap = $payload['ngay_lap'];
$ngayNhap = $payload['ngay_nhap'];
$tongTien = $payload['tong_tien'];
$books = $payload['books'];
$tiLeBan = isset($_GET['tile_ban']) ? (float)$_GET['tile_ban'] : 1.1;

// Start transaction for data consistency
$mysqli->begin_transaction();

try {
    // Check if the receipt already exists
    $stmt_check = $mysqli->prepare("SELECT MaPN FROM phieunhap WHERE MaPN = ?");
    $stmt_check->bind_param("s", $maPN);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $exists = $result_check->num_rows > 0;
    $stmt_check->close();

    if ($exists) {
        // If it exists, update the main receipt record
        $stmt_update_pn = $mysqli->prepare("UPDATE phieunhap SET NgayLapPhieu = ?, NgayNhap = ?, TongTien = ? WHERE MaPN = ?");
        $stmt_update_pn->bind_param("ssds", $ngayLap, $ngayNhap, $tongTien, $maPN);
        $stmt_update_pn->execute();
        $stmt_update_pn->close();

        // And delete its old details to replace them
        $stmt_delete_ctpn = $mysqli->prepare("DELETE FROM chitiet_phieunhap WHERE MaPN = ?");
        $stmt_delete_ctpn->bind_param("s", $maPN);
        $stmt_delete_ctpn->execute();
        $stmt_delete_ctpn->close();
    } else {
        // If it's a new receipt, insert it
        $stmt_insert_pn = $mysqli->prepare("INSERT INTO phieunhap (MaPN, NgayLapPhieu, NgayNhap, TongTien) VALUES (?, ?, ?, ?)");
        $stmt_insert_pn->bind_param("sssd", $maPN, $ngayLap, $ngayNhap, $tongTien);
        $stmt_insert_pn->execute();
        $stmt_insert_pn->close();
    }

    // Prepare statements for inserting details and updating book inventory/prices
    $stmt_insert_ctpn = $mysqli->prepare("INSERT INTO chitiet_phieunhap (MaPN, MaSach, SoLuong, DonGiaNhap, ThanhTien) VALUES (?, ?, ?, ?, ?)");
    $stmt_update_sach = $mysqli->prepare("UPDATE sach SET SoLuongTon = SoLuongTon + ?, GiaBan = ? WHERE MaSach = ?");

    foreach ($books as $book) {
        // Insert new receipt details
        $stmt_insert_ctpn->bind_param("ssidd", $maPN, $book['ma_sach'], $book['so_luong'], $book['don_gia'], $book['thanh_tien']);
        $stmt_insert_ctpn->execute();

        // Update book's stock quantity and selling price
        $giaBanMoi = $book['don_gia'] * $tiLeBan;
        $stmt_update_sach->bind_param("ids", $book['so_luong'], $giaBanMoi, $book['ma_sach']);
        $stmt_update_sach->execute();
    }
    
    // Close prepared statements
    $stmt_insert_ctpn->close();
    $stmt_update_sach->close();

    // If all queries were successful, commit the transaction
    $mysqli->commit();
    echo "OK";

} catch (mysqli_sql_exception $exception) {
    // If any query fails, roll back the entire transaction
    $mysqli->rollback();
    http_response_code(500);
    echo "Lỗi CSDL: " . $exception->getMessage();
} finally {
    $mysqli->close();
}
?>
