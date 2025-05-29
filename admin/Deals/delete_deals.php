<?php
include '../../connect.php';

if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] === "POST") {
    $makm = $_POST['ma_km'];
    $queries = [
        'DELETE FROM khuyenmai WHERE MaKM = ?'
    ];

    foreach ($queries as $sql) {
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param('s', $makm);
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
