<?php
include '../database_connect.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $manv = $_POST['ma_nv'];
    $stmt = $mysqli->prepare("DELETE FROM nhanvien WHERE MaNV = ?");
    $stmt->bind_param("s", $manv);

    if ($stmt->execute()) {
        echo "OK";
    } else {
        echo "ERROR: " . $stmt->error;
    }
    $stmt->close();
    $mysqli->close();
}
?>
