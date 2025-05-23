<?php
include __DIR__ . '/../../database_connect.php';

$shifts = [];
$manv = $_GET['ma_nv'] ?? null;

if($manv){
  $stmt = $mysqli->prepare("SELECT MaCa FROM lichlamviec WHERE MaNV !=?");
  $stmt->bind_param('s', $manv);
  $stmt->execute();
  $result = $stmt->get_result();
} else {
  $result = $mysqli->query("SELECT MaCa FROM lichlamviec");
}

while ($row = $result->fetch_assoc()) {
  $shifts[] = $row['MaCa'];
}

header('Content-Type: application/json');
echo json_encode($shifts);
?>