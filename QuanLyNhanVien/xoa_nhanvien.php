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

// Logging
file_put_contents('log_delete.txt', "POST id: " . ($_POST['id'] ?? 'null') . PHP_EOL, FILE_APPEND);

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
    $queries = [
        'DELETE FROM lichlamviec WHERE MaNV = ?',
        'DELETE FROM taikhoan WHERE MaNV = ?',
        'DELETE FROM nhanvien WHERE MaNV = ?'
    ];

    foreach ($queries as $sql) {
        if ($stmt = $con->prepare($sql)) {
            $stmt->bind_param('i', $manv);
            if (!$stmt->execute()) {
                echo json_encode(['success' => false, 'error' => 'Lỗi khi xóa dữ liệu']);
                $stmt->close();
                $con->close();
                exit;
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'error' => 'Lỗi prepare SQL']);
            $con->close();
            exit;
        }
    }

    $con->close();
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => "Phương thức yêu cầu không hợp lệ!"]);
}
?>
