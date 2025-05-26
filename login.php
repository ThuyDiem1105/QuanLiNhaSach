<?php
session_start();
include __DIR__ . '/connect.php';

if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] === "POST") {
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        $stmt = $mysqli->prepare("SELECT * FROM taikhoan WHERE TenDN = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Không dùng hash → so sánh trực tiếp
            if ($password === $user['MatKhauGoc']) {
                $_SESSION['username'] = $username;
                $_SESSION['loggedin'] = true;
                header("Location: admin/home.html");
                exit;
            }
        }

        // Nếu sai tài khoản hoặc mật khẩu
        $_SESSION['login_error'] = "Tên đăng nhập hoặc mật khẩu không đúng.";
        header("Location: ../login.html"); // Điều hướng lại trang login
        exit;
    }
}
?>