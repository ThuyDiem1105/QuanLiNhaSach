<?php
// link chức năng vào ở đuây
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đăng nhập</title>
  <link rel="stylesheet" href="style.css"> 
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: Arial;
      background-color: #f2f2f2;
    }

    .login-container {
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

    input[type="text"], input[type="password"] {
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
      background-color: #45a049;
    }

    .forgot-password {
      text-align: center;
      margin-top: 10px;
    }

    .forgot-password a {
      color: #4CAF50;
      text-decoration: none;
      font-size: 14px;
    }

    .forgot-password a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h2>Đăng nhập</h2>
    <form action="mainPage.php" method="post">
      <label for="username">Tên đăng nhập:</label>
      <input type="text" id="username" name="username" required>

      <label for="password">Mật khẩu:</label>
      <input type="password" id="password" name="password" required>

      <input type="submit" value="Đăng nhập">
      <div class="forgot-password">
        <a href="fpasswordPage.php">Quên mật khẩu?</a>
      </div>
    </form>
  </div>
</body>
</html>
