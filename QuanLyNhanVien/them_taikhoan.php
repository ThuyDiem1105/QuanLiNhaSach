<?php
session_start();
//kiểm tra nếu chưa login thì cho về login
if (!isset($_SESSION['account_loggedin'])) {
    header('Location: ../loginFunction/mainPage.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $employee_id = $_POST['id'];
} else {
    $employee_id = $_GET['id'] ?? '';
}
//kiểm tra nếu đã login rồi nhưng ko phải là quản lý thì cho 
//quay về trang chủ với chức năng tương ứng được cấp quyền
// if (!isset($_SESSION['account_isManager'])) {
//     header('Location: adminHomePage.php');
//     exit;
// }

$usernameError = $passwordError = $emailError = $confirmpassError = $roleError = $message = '';

 if (isset($_POST['submit_user'])){
    //region ERROR: không điền đẩy đủ dữ liệu, sai định dạng
    $email = $_POST['email'] ?? '';
    $role = $_POST['role'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $rawpassword = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($username)) {
        $usernameError = "<br />Đây là ô thông tin bắt buộc điền!";
    } else {
        $username = trim($username);
        $username = htmlspecialchars($username);
        if (!preg_match("/^[a-zA-Z0-9]+$/", $username)){
            $usernameError = "<br />Tên tài khoản không được có dấu cách và kí tự đặc biệt!";
        }
    }
    if(empty($password)){
        $passwordError = "<br />Đây là ô thông tin bắt buộc điền!";
    } else {
        if(!preg_match("/^(?=.{8,20}$)(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).*$/", $password)){
            $passwordError = "<br />Mật khẩu phải chứa ít nhất một kí tự thường, một kí tự hoa, một số và một kí tự đặc biệt. Có độ dài từ 8-20 kí tự.";
        }
    }
    if(empty($confirm_password)){
        $confirmpassError = "<br />Đây là ô thông tin bắt buộc điền!";
    } else {
        if ($password !== $confirm_password){
            $confirmpassError = "<br />Mật khẩu và xác nhận phải giống nhau!";
        }
    }
    if(empty($email)){
        $emailError = "<br />Đây là ô thông tin bắt buộc điền!";
    } else {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $emailError = "<br />Định dạng địa chỉ email không hợp lệ!";
        }
    }
    if(empty($role)){
        $roleError = '<br />Vui lòng chọn quyền tương ứng cho tài khoản!';
    }
    //endregion

     //region THÊM thông tin tài khoản nhân viên mới vào csdl
    if(!$usernameError && !$passwordError && !$confirmpassError && !$emailError && !$roleError){
        $con = mysqli_connect('localhost', 'root', '', 'phplogin');
        if (mysqli_connect_errno()) {
            $message = 'Lỗi kết nối thất bại đến MySql: ' . mysqli_connect_error();
        }
        //kiểm tra xem username đã tồn tại chưa
        if ($stmt = $con->prepare('SELECT MaNV FROM taikhoan WHERE TenDN = ? OR Email = ?')) {
            $stmt->bind_param('ss', $username, $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $message = 'Tên tài khoản hoặc địa chỉ email đã tồn tại. Vui lòng nhập lại!';
            } else {
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                if ($stmt = $con->prepare('INSERT INTO taikhoan (MaNV, TenDN, MatKhau, RawMatKhau, Quyen, Email) VALUES (?, ?, ?, ?, ?, ?)')){
                    $stmt->bind_param('isssss', $employee_id, $username, $password, $rawpassword, $role, $email);
                    $stmt->execute();

                    echo <<<HTML
                        <script>
                            alert("Thêm tài khoản nhân viên thành công. Bạn sẽ được đưa đến trang tra cứu tài khoản!");
                            window.location.href = "tracuu_taikhoan.php"; 
                        </script>
                        HTML;
                    exit;
                } else {
                    $message = 'Lỗi câu lệnh truy vấn cơ sở dữ liệu!';
                }
            }
            $stmt->close();
        } else {
            $message = 'Lỗi truy vấn đến cơ sở dữ liệu!';
        }
        $con->close();
    }
    //endregion
}
?>

<!DOCTYPE hmtl>
<html>
    <head lang="vi">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, minimum-scale=1">
        <title>THÊM TÀI KHOẢN NHÂN VIÊN</title>    
    </head>
    <body>
        <a href="../adminHomePage.php">Trang chủ</a>
        <a href="quanly_nhanvien.php">Về Quản lý nhân viên</a>
        <div class="container">
            <?php if ($message): ?>
                <div class="alert" style="color:green;">
                <?= htmlspecialchars($message, ENT_QUOTES) ?></div>
            <?php endif; ?>

            <h1>Thêm tài khoản mới</h1>              
            <form action="" method="post" class="addUser-form" novalidate>
                <label class="label-form" for="id">Mã nhân viên</label>
                <div class="group-form">
                    <input class="input-form" type="text" name="id" placeholder="Employee Id" id="id" value="<?= htmlspecialchars($employee_id ?? '') ?>" readonly required>
                </div>

                <label class="label-form" for="username">Tên tài khoản</label>
                <div class="group-form">
                    <input class="input-form" type="text" name="username" placeholder="Username" id="username" value="<?= htmlspecialchars($username ?? '') ?>" required>
                    <span style="color: red;"><?php echo $usernameError ?></span>
                </div>

                <label class="label-form" for="password">Mật khẩu tài khoản</label>
                <div class="group-form">
                    <input class="input-form" type="password" name="password" placeholder="Password" id="password" value="<?= htmlspecialchars($password ?? '') ?>" required>
                    <span style="color: red;"><?php echo $passwordError ?></span>
                </div>

                <label class="label-form" for="confirm_password">Xác nhận lại mật khẩu</label>
                <div class="group-form">
                    <input class="input-form" type="password" name="confirm_password" placeholder="Confirm password" id="confirm_password" value="<?= htmlspecialchars($confirm_password ?? '') ?>" required>
                    <span style="color: red;"><?php echo $confirmpassError ?></span>
                </div>

                <label class="label-form" for="email">Địa chỉ email</label>
                <div class="group-form">
                    <input class="input-form" type="email" name="email" placeholder="Email" id="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
                    <span style="color: red;"><?php echo $emailError ?></span>
                </div>

                <label class="label-form" for="role">Quyền</label>
                <div class="group-form">
                    <select name="role" id="role">
                        <option value="">-Chọn quyền tương ứng-</option>
                        <option value="Manager">Quản lý</option>
                        <option value="Staff">Nhân viên</option>
                        <option value="Admin">Admin hệ thống</option>
                    </select>
                    <span style="color: red;"><?php echo $roleError ?></span>
                </div>

                <button type="submit" name="submit_user">Tạo tài khoản</button>
            </form>
        </div>
    </body>
</html>
