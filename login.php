<?php
session_start();
include __DIR__ . '/connect.php';

$error_message = "";
if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] === "POST") {
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        $stmt = $mysqli->prepare("SELECT * FROM taikhoan WHERE TenDN = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Không dùng hash → so sánh trực tiếp
            if ($password === $user['MatKhauGoc']) {
                $_SESSION['username'] = $username;
                $_SESSION['loggedin'] = true;
                header("Location: admin/home.html");
                exit;
            }
        }

        // Nếu sai tài khoản hoặc mật khẩu
        $_SESSION['login_error'] = "Tên đăng nhập hoặc mật khẩu không đúng.";
        $error_message = "Tên đăng nhập hoặc mật khẩu không đúng.";
    } else{ $error_message = "Vui lòng nhập tên đăng nhập và mật khẩu.";}
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Booktopia</title>
    
    <link rel="stylesheet" href="../../assets/style.css" type="text/css">

    <style>
        :root {
            --primary-color: #A3E4D7; 
            --primary-dark-color: #8FDED2; 
            --text-dark: #2C3E50; 
            --text-medium: #34495E; 
            --text-light: #555; 
            --bg-light: #F8F8F8; 
            --border-light: #E0E0E0; 
            --card-bg: #FFFFFF; 
            --shadow-light: rgba(0, 0, 0, 0.08); 
            --shadow-medium: rgba(0, 0, 0, 0.15); 
            --error-bg: #ffebeb; 
            --error-text: #e74c3c; 
        }

        body {
            font-family: fontweb;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(to right, var(--primary-color) 0%, var(--bg-light) 100%); 
            background-size: cover;
            color: var(--text-medium);
            overflow: hidden; 
        }

        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            min-height: 100vh;
        }

        .login-card {
            background-color: var(--card-bg);
            border-radius: 16px; 
            box-shadow: 0 12px 30px var(--shadow-medium); 
            padding: 45px 40px; 
            width: 100%;
            max-width: 420px; 
            text-align: center;
            box-sizing: border-box;
            transform: translateY(0); 
            animation: fadeInScale 0.7s ease-out forwards;
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .login-header {
            margin-bottom: 35px;
        }

        .login-logo img {
            max-width: 180px; 
            height: auto;
            margin-bottom: 20px; 
        }

        .login-header h2 {
            font-size: 32px; 
            margin-bottom: 8px;
            color: var(--text-dark);
            font-weight: 600; 
        }

        .login-header p {
            font-size: 16px;
            color: var(--text-light);
            margin-top: 0;
            font-weight: 300; 
        }

        .form-group {
            margin-bottom: 25px; 
            text-align: left;
        }

        .form-group label {
            display: block;
            font-size: 15px; 
            margin-bottom: 8px;
            font-weight: 500; 
            color: var(--text-medium);
        }

        .form-group input[type="text"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 14px 18px; 
            border: 1px solid var(--border-light);
            border-radius: 10px; 
            font-size: 17px; 
            color: var(--text-dark);
            box-sizing: border-box;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            font-family: fontweb;
        }

        .form-group input::placeholder {
            font-family: 'Roboto', sans-serif; 
        }
        .form-group input::-webkit-input-placeholder { font-family: 'Roboto', sans-serif; }
        .form-group input:-moz-placeholder { font-family: 'Roboto', sans-serif; }
        .form-group input::-moz-placeholder { font-family: 'Roboto', sans-serif; }
        .form-group input:-ms-input-placeholder { font-family: 'Roboto', sans-serif; }


        .form-group input[type="text"]:focus,
        .form-group input[type="password"]:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(163, 228, 215, 0.4); 
            outline: none;
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

        @keyframes slideInError {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .btn-login {
            width: 100%;
            padding: 16px 25px; 
            background-color: var(--primary-color);
            color: var(--text-dark); 
            border: none;
            border-radius: 10px; 
            font-size: 19px; 
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); 
            font-family: fontweb;

        }

        .btn-login:hover {
            background-color: var(--primary-dark-color);
            transform: translateY(-3px); 
            box-shadow: 0 6px 15px var(--shadow-medium); 
        }

        .btn-login:active {
            transform: translateY(0);
            box-shadow: 0 2px 5px var(--shadow-light);
        }

        @media (max-width: 600px) {
            .login-card {
                margin: 20px;
                padding: 35px 25px;
                border-radius: 12px;
            }
            .login-header h2 {
                font-size: 28px;
            }
            .login-logo img { 
                max-width: 150px; 
            }
            .btn-login {
                padding: 14px 20px;
                font-size: 17px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <img src="assets/logo.png" alt="Booktopia Logo">
                </div>
                <h2>Booktopia</h2>
                </div>
            <form action="login.php" method="post" class="login-form">
                <div class="form-group">
                    <label for="username">Tên đăng nhập</label>
                    <input type="text" id="username" name="username" placeholder="Nhập tên đăng nhập của bạn" required>
                </div>
                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password" id="password" name="password" placeholder="Nhập mật khẩu của bạn" required>
                </div>
                <?php if (!empty($error_message)) { ?>
                    <div class="error-message"><?php echo $error_message; ?></div>
                <?php } ?>
                <button type="submit" class="btn btn-login">Đăng nhập</button>
            </form>
            </div>
    </div>
</body>
</html>