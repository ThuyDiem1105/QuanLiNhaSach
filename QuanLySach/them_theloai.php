<?php
ini_set('display_errors', 0);
error_reporting(0);

session_start();
header('Content-Type: application/json; charset=utf-8');

// Check if user is logged in
if (!isset($_SESSION['account_loggedin'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Chưa đăng nhập!']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mads = $_POST['category_id'] ?? '';
    $genre = $_POST['genre'] ?? '';

    if (empty($mads)) {
        echo json_encode(['error' => 'Mã đầu sách không hợp lệ!']);
        exit;
    }

    $con = mysqli_connect('localhost', 'root', '', 'phplogin');
    if (mysqli_connect_errno()) {
        echo json_encode(['error' => 'Lỗi kết nối: ' . mysqli_connect_error()]);
        exit;
    }

    if ($stmt = $con->prepare('INSERT INTO theloai (MaDS, TenTheLoai) VALUES (?, ?)')) {
        $stmt->bind_param('is', $mads, $genre);
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
