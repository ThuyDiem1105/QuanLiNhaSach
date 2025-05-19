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
  </style>
</head>
<body>
  <div class="forgot-container">
    <h2>Quên mật khẩu</h2>
    <form action="sendResetLink.php" method="post">
      <label for="email">Tên đăng nhập hoặc Email:</label>
      <input type="text" id="email" name="email" required>
      <input type="submit" value="Gửi liên kết đặt lại">
    </form>
    <div class="back-link">
      <a href="login.php">← Quay lại đăng nhập</a>
    </div>
  </div>
</body>
</html>
