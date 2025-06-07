<?php
// save_customers.php
require_once '../../connect.php';

// Kiểm tra phương thức POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Phương thức không hợp lệ';
    exit;
}

// Lấy dữ liệu từ POST (dùng đúng tên trường phía client gửi lên)
$form_mode = $_POST['form_mode'] ?? '';
$ma_kh = $_POST['ma_kh'] ?? '';
$ten_kh = $_POST['ten_kh'] ?? '';
$sdt = $_POST['sdt'] ?? '';
$diachi = $_POST['diachi'] ?? '';
$email = $_POST['email'] ?? '';
$loai = $_POST['loai'] ?? '';
$so_tien_no = isset($_POST['so_tien_no']) ? $_POST['so_tien_no'] : null;

// Validate cơ bản
if ($form_mode === 'add') {
    if (!$ten_kh || !$diachi || !$email || !$sdt || !$loai || $so_tien_no === null) {
        echo 'Thiếu thông tin bắt buộc';
        exit;
    }
    // Tạo mã khách hàng mới
    $result = $mysqli->query("SELECT MAX(CAST(SUBSTRING(MaKH, 3) AS UNSIGNED)) AS max_id FROM khachhang");
    $row = $result ? $result->fetch_assoc() : null;
    $next_id = (int)($row['max_id'] ?? 0) + 1;
    $ma_kh = 'KH' . str_pad($next_id, 3, '0', STR_PAD_LEFT);
    $stmt = $mysqli->prepare("INSERT INTO khachhang (MaKH, HoTen, SDT, DiaChi, Email, Loai, SoTienNo) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo 'Lỗi: Không thể chuẩn bị truy vấn.';
        $mysqli->close();
        exit;
    }
    $stmt->bind_param('ssssssi', $ma_kh, $ten_kh, $sdt, $diachi, $email, $loai, $so_tien_no);
    if ($stmt->execute()) {
        echo 'OK';
    } else {
        echo 'Lỗi: ' . $stmt->error;
    }
    $stmt->close();
    $mysqli->close();
    exit;
} elseif ($form_mode === 'edit') {
    if (!$ma_kh || !$ten_kh || !$sdt || !$diachi || !$email || !$loai) {
        echo 'Thiếu thông tin bắt buộc';
        exit;
    }
    $stmt = $mysqli->prepare("UPDATE khachhang SET HoTen = ?, SDT = ?, DiaChi = ?, Email = ?, Loai = ?, SoTienNo = ? WHERE MaKH = ?");
    if (!$stmt) {
        echo 'Lỗi: Không thể chuẩn bị truy vấn.';
        $mysqli->close();
        exit;
    }
    $stmt->bind_param('sssssis', $ten_kh, $sdt, $diachi, $email, $loai, $so_tien_no, $ma_kh);
    if ($stmt->execute()) {
        echo 'OK';
    } else {
        echo 'Lỗi: ' . $stmt->error;
    }
    $stmt->close();
    $mysqli->close();
    exit;
} else {
    echo 'Chế độ không hợp lệ';
    exit;
}
