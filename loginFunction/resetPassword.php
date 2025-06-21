<?php
include __DIR__ . '/../connect.php';

$token = $_GET["token"];
$token_hash = hash("sha256", $token);

$stmt = $mysqli->prepare("SELECT * FROM taikhoan WHERE resetToken_hash = ?");
$stmt->bind_param("s", $token_hash);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();
if($user === null){
    echo "<script>
        alert('Không tìm thấy token hợp lệ!');
        window.location.href = 'forgotPassword.php';
    </script>";
    exit;
}

if(strtotime($user["resetToken_expiredAt"]) <= time()){
    echo "<script>
        alert('Token đã hết hạn! Vui lòng làm lại thao tác.');
        window.location.href = 'forgotPassword.php';
    </script>";
    exit;
}

$passwordError = $password_confirmError = $error_message = "";

if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] === "POST") {
    $password = trim($_POST['password'] ?? '');
    $password_confirm = trim($_POST['password_confirmation'] ?? '');

    if (empty($password) || empty($password_confirm)) {
        $error_message = "Vui lòng nhập đầy đủ mật khẩu!";
    }

    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,20}$/', $password)) {
        $passwordError = "Mật khẩu phải có ít nhất 1 chữ thường, 1 chữ hoa, 1 số, 1 ký tự đặc biệt và có độ dài từ 8 đến 20 ký tự.";
    }

    if ($password_confirm !== $password) {
        $password_confirmError = "Mật khẩu xác nhận không khớp!";
    }

    if (!empty($password) && !empty($password_confirm) && empty($passwordError) && empty($password_confirmError)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare("UPDATE taikhoan SET MatKhau = ?, resetToken_hash = NULL, resetToken_expiredAt = NULL WHERE MaNV = ?");
        $stmt->bind_param("ss", $password_hash, $user['MaNV']);
        if($stmt->execute()){
            echo "<script>
                alert('Đặt lại mật khẩu thành công! Bạn sẽ được chuyển đến trang đăng nhập.');
                window.location.href = 'login.php';
            </script>";
            exit;
        }
    }
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Tạo mật khẩu mới</title>
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

            .reset-container {
                display: flex;
                justify-content: center;
                align-items: center;
                width: 100%;
                min-height: 100vh;
            }

            .reset-card {
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

            .reset-header {
                margin-bottom: 35px;
            }

            .reset-header h2 {
                font-size: 32px; 
                margin-bottom: 8px;
                color: var(--text-dark);
                font-weight: 600; 
            }

            .reset-header p {
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

            .btn-reset {
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

            .btn-reset:hover {
                background-color: var(--primary-dark-color);
                transform: translateY(-3px); 
                box-shadow: 0 6px 15px var(--shadow-medium); 
            }

            .btn-reset:active {
                transform: translateY(0);
                box-shadow: 0 2px 5px var(--shadow-light);
            }

            @media (max-width: 600px) {
                .reset-card {
                    margin: 20px;
                    padding: 35px 25px;
                    border-radius: 12px;
                }
                .reset-header h2 {
                    font-size: 28px;
                }
                .reset-logo img { 
                    max-width: 150px; 
                }
                .btn-submit {
                    padding: 14px 20px;
                    font-size: 17px;
                }
            }
        </style>
    </head>
    <body>
        <div class="reset-container">
            <div class="reset-card">
                <div class="reset-header">
                    <h2>Đặt mật khẩu mới</h2>
                </div>
                <form method="post" action="" class="reset-form">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                    <div class="form-group">
                        <label for="password">Nhập mật khẩu mới:</label>
                        <input type="password" id="password" name="password" value="<?= htmlspecialchars($password ?? '') ?>" required>
                        <span style="color: red;"><?php echo $passwordError ?></span>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Nhập lại mật khẩu:</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" value="<?= htmlspecialchars($password_confirm ?? '') ?>" required>
                        <span style="color: red;"><?php echo $password_confirmError ?></span>
                    </div>
                    
                    <?php if (!empty($error_message)) { ?>
                        <div class="error-message"><?php echo $error_message; ?></div>
                    <?php } ?>

                    <button type="submit" class="btn btn-reset">Xác nhận</button>
                </form>
            </div>
        </div>
    </body>
</html>