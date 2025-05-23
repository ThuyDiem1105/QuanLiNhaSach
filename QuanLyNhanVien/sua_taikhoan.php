<?php
session_start();
if (!isset($_SESSION['account_loggedin'])){
    header("Location: ../loginFunction/mainPage.php");
}

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $employee_id = $_POST['id'];
} else {
    $employee_id = $_GET['id'] ?? '';
}

$username = $password = $role = $email = '';
$usernameError = $emailError = $confirmpassError = $passwordError = $message =  $roleError = '';

$con = new mysqli('localhost','root','','phplogin');
if ($con->connect_errno) {
  die("DB failed: ".$con->connect_error);
}

if (isset($_GET['load'])) {
    header('Content-Type: application/json; charset=utf-8');
    if ($stmt = $con->prepare('SELECT TenDN, RawMatKhau, Quyen, Email FROM taikhoan WHERE MaNV = ?')) {
        $stmt->bind_param('i', $employee_id);
        $stmt->execute();
        $stmt->bind_result($username, $password, $role, $email);
        if ($stmt->fetch()) {
            echo json_encode([
                "success" => true,
                "data" => [
                    "TenDN" => $username,
                    "RawMatKhau" => $password,
                    "Quyen" => $role,
                    "Email" => $email
                ]
            ]);
        } else {
            echo json_encode(["success" => false, "error" => "Không tìm thấy tài khoản"]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "error" => "Lỗi truy vấn"]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $employee_id) {
  $stmt = $con->prepare('SELECT TenDN, RawMatKhau, Quyen, Email FROM taikhoan WHERE MaNV=?');
  $stmt->bind_param('i',$employee_id);
  $stmt->execute();
  $stmt->bind_result($username, $password, $role, $email);
  $stmt->fetch();
  $stmt->close();
}

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['submit_user'])){
    $email = $_POST['email'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $rawpassword = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';
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

    if (!$usernameError && !$passwordError && !$confirmpassError && !$emailError && !$roleError){
        $password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $con->prepare("UPDATE taikhoan SET TenDN = ?, MatKhau = ?, RawMatKhau = ?, Quyen = ?, Email = ? WHERE MaNV = ?");
        $stmt->bind_param('sssssi', $username, $password, $rawpassword, $role, $email, $employee_id);
        $stmt->execute();
        $stmt->close();
        $con->close();
        echo <<<HTML
              <script>
                alert("Cập nhật thông tin tài khoản nhân viên thành công!");
                window.location.href = "tracuu_taikhoan.php"; 
              </script>
            HTML;
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
    <head lang="vi">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, minimum-scale=1">
        <title>CẬP NHẬT THÔNG TIN TÀI KHOẢN NHÂN VIÊN</title>    
    </head>
    <body>
        <h1>Cập nhật thông tin tài khoản nhân viên</h1>
            <form action="" method="post" class="editAccount-form" novalidate>
                <label class="label-form" for="id">Mã nhân viên</label>
                    <div class="group-form">
                        <input class="input-form" type="text" name="id" placeholder="Employee Id" id="id" value="<?= htmlspecialchars($employee_id ?? '') ?>" readonly required>
                    </div>

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

                    <label class="label-form" for="role">Phân quyền tài khoản</label>
                    <div class="group-form">
                        <select name="role" id="role" required>
                            <option value="Admin" <?= $role === 'Admin' ? 'selected' : ''?>>Admin hệ thống</option>
                            <option value="Manager" <?= $role === 'Manager' ? 'selected' : ''?>>Quản lý</option>
                            <option value="Staff" <?= $role === 'Nhân viên' ? 'selected' : '' ?>>Nhân viên</option>
                        </select>                        
                        <span style="color: red;"><?php echo $roleError ?></span>
                    </div>
                    <button type="submit" name="submit_user">Cập nhật tài khoản</button>
            </form>
    </body>
</html>
