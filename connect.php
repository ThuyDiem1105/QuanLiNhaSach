<?php
$servername = "localhost";    // hoặc 127.0.0.1
$username = "root";           // tài khoản mặc định của XAMPP
$password = "";               // thường để trống với XAMPP
$dbname = "nhasach";          // tên CSDL bạn đã tạo

// Tạo kết nối
$mysqli = new mysqli($servername, $username, $password, $dbname);

// Thiết lập charset để tránh lỗi font tiếng Việt
if ($mysqli) {
    $mysqli->set_charset("utf8");
}

// Kiểm tra kết nối
if ($mysqli->connect_error) {
    die("Kết nối thất bại: " . $mysqli->connect_error);
}
?>