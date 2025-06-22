<?php
session_start();
include __DIR__ . '/../../connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['loggedin'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$response = ['error' => 'Không tìm thấy phiếu thu'];

if (isset($_GET['ma_pt'])) {
    $ma_pt = $_GET['ma_pt'];
    
    $stmt = $mysqli->prepare(
        "SELECT pt.MaPT, pt.MaKH, kh.HoTen, kh.SDT, pt.NgayThu, pt.SoTienThu, kh.SoTienNo
         FROM phieuthutien pt
         JOIN khachhang kh ON pt.MaKH = kh.MaKH
         WHERE pt.MaPT = ?"
    );
    
    if ($stmt) {
        $stmt->bind_param("s", $ma_pt);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            // Tính toán lại số nợ của khách hàng *trước khi* phiếu thu này được thực hiện
            $row['SoTienNo'] = (float)$row['SoTienNo'] + (float)$row['SoTienThu'];
            $response = $row;
        }
        $stmt->close();
    } else {
        $response = ['error' => 'Lỗi truy vấn: ' . $mysqli->error];
    }
}

echo json_encode($response);
?>
