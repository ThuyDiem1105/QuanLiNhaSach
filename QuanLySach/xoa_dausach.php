<?php
ini_set('display_errors', 0);
error_reporting(0);

session_start();
header('Content-Type: application/json; charset=utf-8');

// Check if user is logged in
if (!isset($_SESSION['account_loggedin'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Chưa đăng nhâp!']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mads = $_POST['id'] ?? '';
    if (empty($mads)) {
        echo json_encode(['error' => 'Mã đầu sách không hợp lệ!']);
        exit;
    }

    $con = mysqli_connect('localhost', 'root', '', 'phplogin');
    if (mysqli_connect_errno()) {
        echo json_encode(['error' => 'Connection failed: ' . mysqli_connect_error()]);
        exit;
    }

    // $stmt = $con->prepare('UPDATE sach SET MaDS = NULL, MaTL = NULL WHERE MaDS = ?');
    // $stmt->bind_param('i', $mads);
    // $stmt->execute();
    // $stmt->close();

    $queries = [
        'DELETE FROM theloai WHERE MaDS = ?',
        'DELETE FROM dausach WHERE MaDS = ?',
    ];

    foreach ($queries as $sql) {
        if ($stmt = $con->prepare($sql)) {
            $stmt->bind_param('i', $mads);
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
