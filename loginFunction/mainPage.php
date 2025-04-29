<?php
session_start();
if (isset($_SESSION['account_loggedin'])) {
    header('Location: ../homePage.php');
    exit;
}

require_once __DIR__ . '/../mailer.php';
$usernameError = $passwordError = $message = '';

    //process when users click submit button
    if (isset($_POST['submit'])) {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($username)) {
            $usernameError = "<br />Đây là ô thông tin bắt buộc điền!";
        }

        $password = $_POST['password'] ?? '';
        if (empty($password)) {
            $passwordError = "<br />Đây là ô thông tin bắt buộc điền!";
        }

        if (!$usernameError && !$passwordError) {
            $connection = mysqli_connect('localhost', 'root', '', 'phplogin');
            if (mysqli_connect_errno()) {
                $message = '<br />Lỗi kết nối thất bại đến MySql: ' . mysqli_connect_error();
            } else {
                $stmt = $connection->prepare('SELECT MaNV, MatKhau FROM taikhoan WHERE TenDN = ?');
                $stmt->bind_param('s', $username);
                $stmt->execute();
                //lưu để kiểm tra nếu tk có tồn tại
                $stmt->store_result();
                //kiểm tra nếu tài khoản tồn tại với username đã nhập
                if ($stmt->num_rows > 0) {
                    //tài khoản tồn tại
                    $stmt->bind_result($id, $password);
                    $stmt->fetch();
                    if (password_verify($_POST['password'], $password)) {
                        //đúng mật khẩu
                        session_regenerate_id();
                        //biến này dùng để kiểm tra xem user đã đăng nhập chưa (later use)
                        $_SESSION['account_loggedin'] = TRUE;
                        $_SESSION['account_name'] = $_POST['username'];
                        $_SESSION['account_id'] = $id;
                        //$_SESSION['account_role'] = $role;
                        header('Location: ../homePage.php');
                        exit;
                    } else {
                        $passwordError = '<br />Sai mật khẩu. Vui lòng nhập lại!';
                    }
                } else {
                    $usernameError = '<br />Sai tên người dùng. Vui lòng nhập lại!';
                }
                $stmt->close();
            }
        }
    } 
?>


<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, minimum-scale=1">
        <title>ĐĂNG NHẬP</title>
    </head>
    <body>
        <div class="login">
            <h1>Đăng nhập</h1>
            <form action="" method="post" class="login-form" novalidate>
                <label class="label-form" for="username">Tên tài khoản</label>
                <div class="group-form">
                    <input class="input-form" type="text" name="username" placeholder="Username" id="username" value="<?= htmlspecialchars($username ?? '') ?>" required>
                    <span style="color: red;"><?php echo $usernameError ?></span>
                </div>
                <label class="label-form" for="password">Mật khẩu</label>
                <div class="group-form">
                    <input class="input-form" type="password" name="password" placeholder="Password" id="password" value="<?= htmlspecialchars($password ?? '') ?>" required>
                    <span style="color: red;"><?php echo $passwordError ?></span>
                </div>
                <button class="button" type="submit" name="submit">Đăng nhập</button>
                <p class="register-link"><a href="forgotPassword.php" class="link-form">Quên mật khẩu?</a></p>
            </form>
        </div>
    </body>
</html>