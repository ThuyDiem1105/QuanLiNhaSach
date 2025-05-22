<?php

$servername = "localhost:8080";
$username = "root";
$password = "";
$dbname = "nhasach";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
} else {
    echo "Kết nối thành công!";
}

// Thiết lập charset để tránh lỗi tiếng Việt
$conn->set_charset("utf8");
?>