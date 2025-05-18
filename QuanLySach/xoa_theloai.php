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
    $matl = $_POST['id'] ?? '';
    if (empty($matl)) {
        echo json_encode(['error' => 'Mã thể loại không hợp lệ!']);
        exit;
    }

    $con = mysqli_connect('localhost', 'root', '', 'phplogin');
    if (mysqli_connect_errno()) {
        echo json_encode(['error' => 'Connection failed: ' . mysqli_connect_error()]);
        exit;
    }

    // Prepare all deletes and track status
    if ($stmt = $con->prepare('DELETE FROM theloai WHERE MaTL = ?')) {
        $stmt->bind_param('i', $matl);
        if (!$stmt->execute()) {
            echo json_encode(['success' => false, 'error' => 'Lỗi khi xóa dữ liệu']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Lỗi prepare SQL']);
    }
    $stmt->close();

    $stmt = $con->prepare('UPDATE sach SET MaTL = ? WHERE MaTL = ?');
    $stmt->bind_param('ii', '', $matl);
    $stmt->execute();

    $con->close();
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => "Phương thức yêu cầu không hợp lệ!"]);
}
?>
