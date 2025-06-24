<?php
include __DIR__ . '/../../connect.php';

$maNV = $_POST['ma_nv'];
$hoTen = $_POST['ho_ten'];
$ngaySinh = $_POST['ngay_sinh'];
$sdt = $_POST['sdt'];
$noiO = $_POST['noi_o'];
$chucVu = $_POST['chuc_vu'];
$caLamStr = $_POST['ca_lam'];
$caLamArr = explode(',', $caLamStr);
$luong = $_POST['luong'];

$stmt = $mysqli->prepare("SELECT MaNV FROM nhanvien WHERE SDT = ? AND MaNV != ?");
$stmt->bind_param("ss", $sdt, $maNV);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
  echo "sdt_exists";
  exit;
}
$stmt->close();

//kiển tra xem nhân viên đã tồn tại chưa
$stmt = $mysqli->prepare("SELECT MaNV FROM nhanvien WHERE MaNV = ?");
$stmt->bind_param("s", $maNV);
$stmt->execute();
$stmt->store_result();

//đã tồn tại nhân viên
if ($stmt->num_rows > 0) {
  $stmt = $mysqli->prepare("DELETE FROM lichlamviec WHERE MaNV = ?");
  $stmt->bind_param('s', $maNV);
  $stmt->execute();
  $stmt->close();

  foreach ($caLamArr as $calam){
      $stmt = $mysqli->prepare('INSERT INTO lichlamviec(MaNV, MaCa) VALUES(?, ?)');
      $stmt->bind_param('ss', $maNV, $calam);
      $stmt->execute();
      $stmt->close();
  } 

  $stmt = $mysqli->prepare("UPDATE nhanvien SET HoTen=?, NgaySinh=?, SDT=?, NoiO=?, ChucVu=?, CaLam=?, Luong=? WHERE MaNV=?");
  $stmt->bind_param("ssssssds", $hoTen, $ngaySinh, $sdt, $noiO, $chucVu, $caLamStr, $luong, $maNV);
  if ($stmt->execute()) {
    echo "OK";
  } else {
    echo "ERROR: " . $stmt->error;
  }
  $stmt->close();

} else { 
  //chưa tồn tại nhân viên
  $stmt = $mysqli->prepare("INSERT INTO nhanvien (MaNV, HoTen, NgaySinh, SDT, NoiO, ChucVu, CaLam, Luong) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("sssssssd", $maNV, $hoTen, $ngaySinh, $sdt, $noiO, $chucVu, $caLamStr, $luong);
  if ($stmt->execute()) {
    echo "OK";
  } else {
    echo "ERROR: " . $stmt->error;
  }
  $stmt->close();

  foreach ($caLamArr as $calam){
    $stmt = $mysqli->prepare('INSERT INTO lichlamviec(MaNV, MaCa) VALUES(?, ?)');
    $stmt->bind_param('ss', $maNV, $calam);
    $stmt->execute();
    $stmt->close();
  }

}

$mysqli->close();
?>
