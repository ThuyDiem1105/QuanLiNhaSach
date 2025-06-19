<?php
session_start();
include __DIR__ . '/../connect.php';
$error_message = "";

if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] === "POST") {

  $email = trim($_POST['email'] ?? '');
  if (empty($email)) {
      $error_message = "Vui lòng nhập đầy đủ địa chỉ email!";
  }

  if (!empty($email) && empty($error_message)) {
    $stmt = $mysqli->prepare('SELECT MaNV FROM taikhoan WHERE Email = ?');
    $stmt->bind_param("s", $email);
    $stmt->execute();

    //lưu để kiểm tra nếu tài khoản có tồn tại
    $stmt->store_result();

    //kiểm tra nếu tài khoản tồn tại có email đã nhập
    if ($stmt->num_rows > 0) {
      $token = bin2hex(random_bytes(16));
      $token_hash = hash("sha256", $token);
      $expiry = date("Y-m-d H:i:s", time() + 60 * 30);

      $stmt = $mysqli->prepare("UPDATE taikhoan SET resetToken_hash = ?, resetToken_expiredAt = ? WHERE Email = ?");
      $stmt->bind_param('sss', $token_hash, $expiry, $email);
      if($stmt->execute()){
        $mail = require __DIR__ . "/mailer.php";
        $mail->setFrom("noreply@gmail.com", "Booktopia Support");
        $mail->addAddress($email);
        $mail->Subject = "PASSWORD RESET";
        $mail->Body = <<<END

        <p style="color: #000000; font-family: Arial, sans-serif; font-size: 15px;">
          Vui lòng bấm vào 
          <a href="https://8342-2402-800-6388-107d-8ccd-2c33-ec52-3569.ngrok-free.app/loginFunction/resetPassword.php?token=$token" 
            style="color: #1a0dab; text-decoration: underline;">
            đây
          </a> 
          để tạo mật khẩu mới cho tài khoản của bạn!
        </p>

        END;

        try {
          $mail->send();
          echo "<script>
                alert('Gửi mail thành công! Vui lòng kiểm tra email của bạn để đặt lại mật khẩu.');
            </script>";

        } catch (Exception $e){
          echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
        }
      }

    } else {
        $error_message = 'Địa chỉ email không tồn tại. Vui lòng nhập lại!';
    }
      $stmt->close();
  }
}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quên mật khẩu</title>
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: Arial;
      background-color: #f2f2f2;
    }

    .forgot-container {
      width: 300px;
      margin: 100px auto;
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
    }

    input[type="text"], input[type="email"] {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    input[type="submit"] {
      width: 100%;
      background-color: #4CAF50;
      color: white;
      padding: 10px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    input[type="submit"]:hover {
      background-color: #4CAF50;
    }

    .back-link {
      text-align: center;
      margin-top: 15px;
    }

    .back-link a {
      text-decoration: none;
      color: #4CAF50;
    }

    .back-link a:hover {
      text-decoration: underline;
    }

    .error-message {
      background-color: var(--error-bg);
      color: var(--error-text);
      border: 1px solid #f8d7da; 
      border-radius: 10px;
      padding: 12px 18px; 
      margin-bottom: 25px;
      font-size: 15px;
      text-align: left;
      animation: slideInError 0.4s ease-out;
    }
  </style>
</head>
<body>
  <div class="forgot-container">
    <h2>Quên mật khẩu</h2>
    <form action="" method="post">
      <label for="email">Nhập địa chỉ email:</label>
      <input type="text" id="email" name="email" required>
      <input type="submit" value="Gửi">
    </form>

    <?php if (!empty($error_message)) { ?>
      <div class="error-message"><?php echo $error_message; ?></div>
    <?php } ?>

    <div class="back-link">
      <a href="login.php">← Quay lại đăng nhập</a>
    </div>
  </div>
</body>
</html>
