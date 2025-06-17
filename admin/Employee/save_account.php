<?php
include __DIR__ . '/../../connect.php';

$maNV = $_POST['tk_ma_nv'];
$tenDN = $_POST['ten_dn'];
$matkhau = $_POST['matkhau'];
$email = $_POST['email'];
$quyen = $_POST['quyen'];

//kiểm tra xem tên đăng nhập đã tồn tại chưa
$stmt = $mysqli->prepare("SELECT MaNV FROM taikhoan WHERE TenDN = ? AND MaNV != ?");
$stmt->bind_param("ss", $tenDN, $maNV);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo "tenDN_exists";
    exit;
}
$stmt->close();

//kiểm tra xem email đã tồn tại chưa
$stmt = $mysqli->prepare("SELECT MaNV FROM taikhoan WHERE Email = ? AND MaNV != ?");
$stmt->bind_param("ss", $email, $maNV);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo "email_exists";
    exit;
}
$stmt->close();

//kiển tra xem tài khoản đã tồn tại chưa
$stmt = $mysqli->prepare("SELECT MaNV FROM taikhoan WHERE MaNV = ?");
$stmt->bind_param("s", $maNV);
$stmt->execute();
$stmt->store_result();

//đã tồn tại tài khoản
if ($stmt->num_rows > 0) {
    $stmt = $mysqli->prepare("UPDATE taikhoan SET TenDN = ?, Email = ?, Quyen = ? WHERE MaNV = ?");
    $stmt->bind_param("ssss", $tenDN, $email, $quyen, $maNV);

} else { 
  //chưa tồn tại tài khoản
    $matKhau = password_hash($matkhau, PASSWORD_DEFAULT);
    $stmt = $mysqli->prepare("INSERT INTO taikhoan (MaNV, TenDN, Email, MatKhau, Quyen) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $maNV, $tenDN, $email, $matKhau, $quyen);
}

if ($stmt->execute()) {
    echo "OK";
} else {
    echo "ERROR: " . $stmt->error;
}
$stmt->close();
$mysqli->close();
?>
