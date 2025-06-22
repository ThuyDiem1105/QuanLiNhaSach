<?php
session_start();
include __DIR__ . '/../../connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ma_phieu_thu = $_POST['ma_phieu_thu'];

    if (empty($ma_phieu_thu)) {
        die("Dữ liệu không hợp lệ.");
    }

    $mysqli->begin_transaction();

    try {
        // Find the receipt to be deleted
        $stmt_find = $mysqli->prepare("SELECT MaKH, SoTienThu FROM phieuthutien WHERE MaPT = ?");
        $stmt_find->bind_param("s", $ma_phieu_thu);
        $stmt_find->execute();
        $result = $stmt_find->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("Không tìm thấy phiếu thu.");
        }
        
        $receipt = $result->fetch_assoc();
        $ma_kh = $receipt['MaKH'];
        $so_tien_thu = (float)$receipt['SoTienThu'];

        // Lock the customer row and update their debt (add back the collected amount)
        $stmt_update = $mysqli->prepare("UPDATE khachhang SET SoTienNo = SoTienNo + ? WHERE MaKH = ?");
        $stmt_update->bind_param("ds", $so_tien_thu, $ma_kh);
        $stmt_update->execute();

        // Delete the receipt
        $stmt_delete = $mysqli->prepare("DELETE FROM phieuthutien WHERE MaPT = ?");
        $stmt_delete->bind_param("s", $ma_phieu_thu);
        $stmt_delete->execute();

        $mysqli->commit();
        echo "OK";

    } catch (Exception $e) {
        $mysqli->rollback();
        die("Lỗi: " . $e->getMessage());
    }
} else {
    header('HTTP/1.0 405 Method Not Allowed');
    echo "Method Not Allowed";
}
?> 