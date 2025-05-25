<?php
include '../../connect.php';

if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] === "POST") {
    $makh = $_POST['ma_kh'];
    $queries = [
        'DELETE FROM khachhang WHERE MaKH = ?'
    ];

    foreach ($queries as $sql) {
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param('s', $makh);
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
