<?php
session_start();
//kiểm tra nếu chưa login thì cho về login
if (!isset($_SESSION['account_loggedin'])) {
    header('Location: ../loginFunction/mainPage.php');
    exit;
}

$fullnameError = $emailError = $phonenumError = $addressError = $message = '';

if (isset($_POST['submit_customer'])){
    $fullname = $_POST['fullname'] ?? '';
    $phonenum = $_POST['phonenum'] ?? '';
    $email = $_POST['email'] ?? '';
    $address = $_POST['address'] ?? '';

    if(empty($fullname)){
        $fullnameError = "<br />Đây là ô thông tin bắt buộc điền!\nVui lòng nhập đầy đủ họ tên nhân viên.";
    }
    if(empty($address)){
        $addressError = "<br />Đây là ô thông tin bắt buộc điền!\nVui lòng nhập đầy đủ số nhà, tên đường, quận, huyện, tỉnh/thành phố đang cư trú hiện tại;";
    }
    if(empty($phonenum)){
        $phonenumError = "<br />Đây là ô thông tin bắt buộc điền!";
    } else {
        if (!preg_match("/^(?:09|05|03|07|08)[0-9]{8}$/", $phonenum)) {
            $phonenumError = "<br />Số điện thoại phải bắt đầu với 09/03/05/07/08 và gồm 8 chữ số theo sau!";
        }        
    }
    if(empty($email)){
        $emailError = "<br />Đây là ô thông tin bắt buộc điền!";
    } else {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $emailError = "<br />Định dạng địa chỉ email không hợp lệ!";
        }
    }

    if (!$fullnameError && !$phonenumError && !$addressError && !$emailError){
        $con = mysqli_connect('localhost', 'root', '', 'phplogin');
        if (mysqli_connect_errno()) {
            $message = 'Lỗi kết nối thất bại đến MySql: ' . mysqli_connect_error();
        }
        //kiểm tra xem khách hàng đã tồn tại chưa
        if ($stmt = $con->prepare('SELECT MaKH FROM khachhang WHERE Email = ? OR SDT = ?')) {
            $stmt->bind_param('ss', $email, $phonenum);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $message = 'Số điện thoại hoặc địa chỉ email đã được đăng ký. Vui lòng nhập lại hoặc thử đăng nhập tài khoản cũ!';
            } else {
                if ($stmt = $con->prepare('INSERT INTO khachhang (HoTen, SDT, DiaChi, Email) VALUES (?, ?, ?, ?)')){
                    $stmt->bind_param('ssss', $fullname, $phonenum, $address, $email);
                    $stmt->execute();
                    $message = 'Thêm khách hàng mới thành công!'; 
                    $fullname = $email = $phonenum = $address = '';
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
}
?>

<!DOCTYPE hmtl>
<html>
    <head lang="vi">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, minimum-scale=1">
        <title>THÊM KHÁCH HÀNG</title>    
    </head>
    <body>
        <a href="../adminHomePage.php">Về Trang chủ</a>
        <a href="quanly_khachhang.php">Về Quản lý khách hàng</a>
        <div class="container">
            <h1>Thêm khách hàng mới</h1>
            <form action="" method="post" class="addCustomer-form" novalidate>
                <label class="label-form" for="fullname">Họ tên khách hàng</label>
                <div class="group-form">
                    <input class="input-form" type="text" name="fullname" placeholder="Fullname" id="fullname" value="<?= htmlspecialchars($fullname ?? '') ?>" required>
                    <span style="color: red;"><?php echo $fullnameError ?></span>
                </div>

                <label class="label-form" for="phonenum">Số điện thoại</label>
                <div class="group-form">
                    <input class="input-form" type="tel" name="phonenum" placeholder="Phonenum" id="phonenum" value="<?= htmlspecialchars($phonenum ?? '') ?>" required>
                    <span style="color: red;"><?php echo $phonenumError ?></span>
                </div>

                <label class="label-form" for="address">Địa chỉ nơi ở</label>
                <div class="group-form">
                    <input class="input-form" type="text" name="address" placeholder="Address" id="address" value="<?= htmlspecialchars($address ?? '') ?>" required>
                    <span style="color: red;"><?php echo $addressError ?></span>
                </div>

                <label class="label-form" for="email">Địa chỉ email</label>
                <div class="group-form">
                    <input class="input-form" type="email" name="email" placeholder="email" id="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
                    <span style="color: red;"><?php echo $emailError ?></span>
                </div>
                <button type="submit" name="submit_customer">Thêm khách hàng</button>
            </form>
            <?php if ($message): ?>
                <div class="alert" style="color:green;">
                <?= htmlspecialchars($message, ENT_QUOTES) ?></div>
            <?php endif; ?>
        </div>
    </body>
</html>