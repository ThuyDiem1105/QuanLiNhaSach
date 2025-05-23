<?php
include '../../database_connect.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $masach = $_POST['ma_sach'];
    $queries = [
        'DELETE FROM sach_theloai WHERE MaSach = ?',
        'DELETE FROM sach WHERE MaSach = ?'
    ];

    foreach ($queries as $sql) {
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param('s', $masach);
            if (!$stmt->execute()) {
                echo "ERROR: " . $stmt->error;
                $stmt->close();
                $mysqli->close();
                exit;
            }
        } else {
            echo "Lỗi truy vấn SQL. ";
            $mysqli->close();
            exit;
        }
    }
    $mysqli->close();
    echo "OK";
}
?>
