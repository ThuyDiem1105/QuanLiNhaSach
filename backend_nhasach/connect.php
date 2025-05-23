<?php
$servername = "localhost";    // hoặc 127.0.0.1
$username = "root";           // tài khoản mặc định của XAMPP
$password = "";               // thường để trống với XAMPP
$dbname = "phplogin";          // tên CSDL bạn đã tạo

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Thiết lập charset để tránh lỗi font tiếng Việt
$conn->set_charset("utf8");
?>
