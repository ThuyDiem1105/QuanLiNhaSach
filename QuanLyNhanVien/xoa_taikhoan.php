<?php
ini_set('display_errors', 0);
error_reporting(0);

session_start();
header('Content-Type: application/json; charset=utf-8');

// Check if user is logged in
if (!isset($_SESSION['account_loggedin'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $manv = $_POST['id'] ?? '';
    if (empty($manv)) {
        echo json_encode(['error' => 'Mã nhân viên không hợp lệ!']);
        exit;
    }

    $con = mysqli_connect('localhost', 'root', '', 'phplogin');
    if (mysqli_connect_errno()) {
        echo json_encode(['error' => 'Connection failed: ' . mysqli_connect_error()]);
        exit;
    }

    // Prepare all deletes and track status
    if ($stmt = $con->prepare('DELETE FROM taikhoan WHERE MaNV = ?')) {
        $stmt->bind_param('i', $manv);
        if (!$stmt->execute()) {
            echo json_encode(['success' => false, 'error' => 'Lỗi khi xóa dữ liệu']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Lỗi prepare SQL']);
    }
    $stmt->close();
    $con->close();
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => "Phương thức yêu cầu không hợp lệ!"]);
}
?>
