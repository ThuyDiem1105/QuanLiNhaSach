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
        <title>Tạo mật khẩu mới</title>
        <meta charset="utf-8">
        <link rel="stylesheet">
    </head>
    <body>
        <h1>Tạo mật khẩu mới</h1>

        <form method="post" action="" novalidate>
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <div class="form-group">
                <label for="password">Nhập mật khẩu mới:</label>
                <input type="password" id="password" name="password" value="<?= htmlspecialchars($password ?? '') ?>" required>
                <span style="color: red;"><?php echo $passwordError ?></span>
            </div>

            <div>
                <label for="password_confirmation">Nhập lại mật khẩu:</label>
                <input type="password" id="password_confirmation" name="password_confirmation" value="<?= htmlspecialchars($password_confirm ?? '') ?>" required>
                <span style="color: red;"><?php echo $password_confirmError ?></span>
            </div>
            
            <?php if (!empty($error_message)) { ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php } ?>

            <button>Xác nhận</button>
        </form>
    </body>
</html>