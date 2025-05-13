<?php
// login.php

// Kết nối với cơ sở dữ liệu
$host = '127.0.0.1';
$dbname = 'nhasach';
$username = 'root';
$password = '';
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Kiểm tra xem có dữ liệu POST không
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Kiểm tra nếu các giá trị từ form được gửi qua POST
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Truy vấn kiểm tra tên đăng nhập và mật khẩu
        $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
        $result = $conn->query($sql);

        // Kiểm tra nếu có người dùng khớp với thông tin đăng nhập
        if ($result->num_rows > 0) {
            // Lưu thông tin người dùng vào session (ví dụ như user_id, role...)
            session_start();
            $_SESSION['username'] = $username;
            $_SESSION['loggedin'] = true;
            
            // Chuyển hướng đến trang quản lý sau khi đăng nhập thành công
            header("Location: dashboard.php");
        } else {
            // Nếu đăng nhập không đúng, chuyển về trang đăng nhập với thông báo lỗi
            header("Location: index.php?error=true");
        }
    }
}

$conn->close();
?>
