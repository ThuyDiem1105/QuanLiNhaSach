<?php
session_start();

$token = $_GET['token'] ?? '';
if (!$token) {
    die('Token không hợp lệ.');
}
$conn = mysqli_connect('localhost', 'root', '', 'phplogin');
$stmt = $conn->prepare(
  'SELECT pr.user_id
   FROM password_resets pr
   WHERE pr.token = ? AND pr.expires_at > NOW()'
);
$stmt->bind_param('s', $token);
$stmt->execute();
$stmt->bind_result($userId);
if (!$stmt->fetch()) {
    $stmt->close();
    die('Liên kết đã hết hạn hoặc không tồn tại.');
}
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pw1 = $_POST['password'] ?? '';
    $pw2 = $_POST['confirm'] ?? '';
    if (!$pw1 || $pw1 !== $pw2) {
        $error = "Mật khẩu không khớp hoặc trống.";
    } else {
        // update password
        $hash = password_hash($pw1, PASSWORD_DEFAULT);
        $up = $conn->prepare('UPDATE accounts SET password = ? WHERE id = ?');
        $up->bind_param('si', $hash, $userId);
        $up->execute();
        $up->close();

        // delete the token
        $del = $conn->prepare('DELETE FROM password_resets WHERE user_id = ?');
        $del->bind_param('i', $userId);
        $del->execute();
        $del->close();

        $success = "Mật khẩu đã được đặt lại. Bạn có thể <a href=\"login.php\">đăng nhập</a>.";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Đặt lại mật khẩu</title></head>
<body>
  <h1>Đặt lại mật khẩu</h1>
  <?php if (!empty($error)): ?><p style="color:red;"><?= $error ?></p><?php endif; ?>
  <?php if (!empty($success)): ?><p style="color:green;"><?= $success ?></p><?php else: ?>
    <form method="post">
      <label>Mật khẩu mới: <input type="password" name="password" required></label><br>
      <label>Nhập lại: <input type="password" name="confirm" required></label><br>
      <button type="submit">Đặt lại</button>
    </form>
  <?php endif; ?>
</body>
</html>
