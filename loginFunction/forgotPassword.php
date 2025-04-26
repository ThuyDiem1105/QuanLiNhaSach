<?php
session_start();
require_once __DIR__ . '/../mailer.php';  // your PHPMailer setup

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Vui lòng nhập email hợp lệ.";
    } else {
        $conn = mysqli_connect('localhost', 'root', '', 'phplogin');
        // 1) find user
        $stmt = $conn->prepare('SELECT id FROM accounts WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->bind_result($userId);
        if ($stmt->fetch()) {
            $stmt->close();

            // 2) generate & store token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour
            $ins = $conn->prepare(
              'INSERT INTO password_resets (user_id, token, expires_at)
               VALUES (?, ?, ?)
               ON DUPLICATE KEY UPDATE token = VALUES(token), expires_at = VALUES(expires_at)'
            );
            $ins->bind_param('iss', $userId, $token, $expires);
            $ins->execute();
            $ins->close();

            // 3) send email
            $resetLink = sprintf(
              '%s/resetPassword.php?token=%s',
              ($_SERVER['REQUEST_SCHEME'] ?? 'https').'://'.$_SERVER['HTTP_HOST'],
              $token
            );

            $mail->addAddress($email);
            $mail->Subject = 'Đặt lại mật khẩu';
            $mail->Body    = "Click vào đây để đặt lại mật khẩu (hết hạn sau 1 giờ):\n\n$resetLink";
            if ($mail->send()) {
                $success = "Một email đặt lại mật khẩu đã được gửi.";
            } else {
                $error = "Không thể gửi email. Vui lòng thử lại sau.";
            }
        } else {
            // don't reveal that email doesn't exist
            $success = "Một email đặt lại mật khẩu đã được gửi.";
        }
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Quên mật khẩu</title></head>
<body>
  <h1>Quên mật khẩu</h1>
  <?php if (!empty($error)): ?><p style="color:red;"><?= $error ?></p><?php endif; ?>
  <?php if (!empty($success)): ?><p style="color:green;"><?= $success ?></p><?php endif; ?>
  <form method="post">
    <label>Email: <input type="email" name="email" required></label><br>
    <button type="submit">Gửi liên kết đặt lại</button>
  </form>
</body>
</html>
