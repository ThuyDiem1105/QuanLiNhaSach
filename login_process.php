<?php
session_start();
include __DIR__ . '/connect.php'; // Kết nối với database

// Chỉ cho phép phương thức POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Nhận dữ liệu từ form
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Kiểm tra nếu dữ liệu không trống
    if (empty($username) || empty($password)) {
        echo "<script>window.location.href = 'login.html?error=Vui lòng nhập đầy đủ thông tin';</script>";
        exit;
    }

    // Dùng prepared statement để an toàn SQL Injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE TenDangNhap = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Kiểm tra mật khẩu (không mã hóa)
        if ($password === $row['MatKhau']) {
            // Regenerate session ID để tránh session fixation
            session_regenerate_id(true);

            // Lưu thông tin session (nếu cần)
            $_SESSION['users'] = $row['TenDangNhap'];

            // Điều hướng người dùng đến trang dashboard
            header("Location: dashboard.html");
            exit;
        } else {
            echo "<script>window.location.href = 'login.html?error=Sai mật khẩu';</script>";
        }
    } else {
        echo "<script>window.location.href = 'login.html?error=Tài khoản không tồn tại';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>