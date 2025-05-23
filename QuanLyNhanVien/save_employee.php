<?php
include '../database_connect.php';

$maNV = $_POST['ma_nv'];
$hoTen = $_POST['ho_ten'];
$ngaySinh = $_POST['ngay_sinh'];
$sdt = $_POST['sdt'];
$noiO = $_POST['noi_o'];
$chucVu = $_POST['chuc_vu'];
$caLam = $_POST['ca_lam'];
$luong = $_POST['luong'];

//kiển tra xem nhân viên đã tồn tại chưa
$stmt = $mysqli->prepare("SELECT MaNV FROM nhanvien WHERE MaNV = ?");
$stmt->bind_param("s", $maNV);
$stmt->execute();
$stmt->store_result();

//đã tồn tại nhân viên
if ($stmt->num_rows > 0) {
    $stmt = $mysqli->prepare("UPDATE nhanvien SET HoTen=?, NgaySinh=?, SDT=?, NoiO=?, ChucVu=?, CaLam=?, Luong=? WHERE MaNV=?");
    $stmt->bind_param("ssssssds", $hoTen, $ngaySinh, $sdt, $noiO, $chucVu, $caLam, $luong, $maNV);
} else {
    //chưa tồn tại
    $stmt = $mysqli->prepare("INSERT INTO nhanvien (MaNV, HoTen, NgaySinh, SDT, NoiO, ChucVu, CaLam, Luong) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssd", $maNV, $hoTen, $ngaySinh, $sdt, $noiO, $chucVu, $caLam, $luong);
}

if ($stmt->execute()) {
  echo "OK";
} else {
  echo "ERROR: " . $stmt->error;
}
$stmt->close();
$mysqli->close();
?>
