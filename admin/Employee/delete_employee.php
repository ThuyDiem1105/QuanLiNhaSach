<?php
include __DIR__ . '/../../connect.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $manv = $_POST['ma_nv'];
    $queries = [
        'DELETE FROM lichlamviec WHERE MaNV = ?',
        'DELETE FROM nhanvien WHERE MaNV = ?'
    ];

    foreach ($queries as $sql) {
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param('s', $manv);
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
