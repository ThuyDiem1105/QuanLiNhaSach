<?php
session_start();
include __DIR__ . '/../admin/connect.php';  // Kết nối CSDL, đảm bảo đường dẫn đúng

// Xử lý form khi submit
if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        $stmt = $conn->prepare("SELECT * FROM users WHERE TenDangNhap = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if ($password === $user['MatKhau']) {

                session_regenerate_id(true);
                $_SESSION['username'] = $username;
                $_SESSION['loggedin'] = true;
                $stmt->close();
                $conn->close();
                header("Location: home.html");  // Chuyển hướng nếu đăng nhập đúng
                exit;
            }
        }

        // Lưu lỗi vào session
        $_SESSION['login_error'] = "Tên đăng nhập hoặc mật khẩu không đúng.";
        $stmt->close();
        $conn->close();
        header("Location: login.html");
        exit;
    }
}
