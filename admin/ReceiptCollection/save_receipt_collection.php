<?php
session_start();
include __DIR__ . '/../../connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ma_kh = $_POST['ma_kh'];
    $ngay_thu = $_POST['ngay_thu'];
    $so_tien_thu = (float)$_POST['so_tien_thu'];

    if (empty($ma_kh) || empty($ngay_thu) || $so_tien_thu <= 0) {
        die("Dữ liệu không hợp lệ.");
    }

    $mysqli->begin_transaction();

    try {
        // Lock the customer row to prevent race conditions
        $stmt_check = $mysqli->prepare("SELECT SoTienNo FROM khachhang WHERE MaKH = ? FOR UPDATE");
        $stmt_check->bind_param("s", $ma_kh);
        $stmt_check->execute();
        $result = $stmt_check->get_result();
        $customer = $result->fetch_assoc();
        $current_debt = (float)$customer['SoTienNo'];

        if ($so_tien_thu > $current_debt) {
            throw new Exception("Số tiền thu không được vượt quá số nợ hiện tại của khách hàng.");
        }
        
        // Generate new receipt ID
        $stmt_count = $mysqli->prepare("SELECT COUNT(*) as count FROM phieuthutien");
        $stmt_count->execute();
        $count_result = $stmt_count->get_result();
        $count_row = $count_result->fetch_assoc();
        $next_id = $count_row['count'] + 1;
        $ma_pt = 'PT' . str_pad($next_id, 3, '0', STR_PAD_LEFT);
        
        // Insert into phieuthutien table
        $stmt_insert = $mysqli->prepare("INSERT INTO phieuthutien (MaPT, MaKH, NgayThu, SoTienThu) VALUES (?, ?, ?, ?)");
        $stmt_insert->bind_param("sssd", $ma_pt, $ma_kh, $ngay_thu, $so_tien_thu);
        $stmt_insert->execute();

        // Update customer's debt
        $new_debt = $current_debt - $so_tien_thu;
        $stmt_update = $mysqli->prepare("UPDATE khachhang SET SoTienNo = ? WHERE MaKH = ?");
        $stmt_update->bind_param("ds", $new_debt, $ma_kh);
        $stmt_update->execute();

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