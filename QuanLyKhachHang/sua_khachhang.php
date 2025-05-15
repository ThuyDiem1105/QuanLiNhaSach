<?php
session_start();
if (!isset($_SESSION['account_loggedin'])){
    header("Location: ../loginFunction/mainPage.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $customer_id = $_POST['id'];
} else {
    $customer_id = $_GET['id'] ?? '';
}

$fullname = $phonenum = $address = $email = '';
$fullnameError = $phonenumError = $addressError = $emailError = '';

$con = new mysqli('localhost','root','','phplogin');
if ($con->connect_errno) {
  die("DB failed: ".$con->connect_error);
}

if (isset($_GET['load'])) {
    header('Content-Type: application/json; charset=utf-8');
    if ($stmt = $con->prepare('SELECT HoTen, SDT, DiaChi, Email FROM khachhang WHERE MaKH = ?')) {
        $stmt->bind_param('i', $customer_id);
        $stmt->execute();
        $stmt->bind_result($fullname, $phonenum, $address, $email);
        if ($stmt->fetch()) {
            echo json_encode([
                "success" => true,
                "data" => [
                    "HoTen" => $fullname,
                    "SDT" => $phonenum,
                    "DiaChi" => $address,
                    "Email" => $email
                ]
            ]);
        } else {
            echo json_encode(["success" => false, "error" => "Không tìm thấy khách hàng"]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "error" => "Lỗi truy vấn"]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $customer_id) {
  $stmt = $con->prepare('SELECT HoTen, SDT, DiaChi, Email FROM khachhang WHERE MaKH = ?');
  $stmt->bind_param('i',$customer_id);
  $stmt->execute();
  $stmt->bind_result($fullname, $phonenum, $address, $email);
  $stmt->fetch();
  $stmt->close();
}

if (isset($_POST['submit_customer'])){
    $fullname = $_POST['fullname'] ?? '';
    $phonenum = $_POST['phonenum'] ?? '';
    $email = $_POST['email'] ?? '';
    $address = $_POST['address'] ?? '';

    if(empty($fullname)){
        $fullnameError = "<br />Vui lòng nhập đầy đủ họ tên nhân viên.";
    }
    if(empty($address)){
        $addressError = "<br />Vui lòng nhập đầy đủ số nhà, tên đường, quận, huyện, tỉnh/thành phố đang cư trú hiện tại;";
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
        $stmt = $con->prepare('UPDATE khachhang SET HoTen = ?, SDT = ?, DiaChi = ?, Email = ? WHERE MaKH = ?');
        $stmt->bind_param('ssssi', $fullname, $phonenum, $address, $email, $customer_id);
        $stmt->execute();
        $stmt->close();
        $con->close();
        echo <<<HTML
              <script>
                alert("Cập nhật thông tin khách hàng thành công!");
                window.location.href = "tracuu_khachhang.php"; 
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
        <title>CẬP NHẬT THÔNG TIN KHÁCH HÀNG</title>    
    </head>
    <body>
        <h1>Cập nhật thông tin tài khoản nhân viên</h1>
            <form action="" method="post" class="editCustomer-form" novalidate>
                <label class="label-form" for="id">Mã khách hàng</label>
                <div class="group-form">
                    <input class="input-form" type="text" name="id" placeholder="Id" id="id" value="<?= htmlspecialchars($customer_id ?? '') ?>" readonly required>
                </div>

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
                <button type="submit" name="submit_customer">Cập nhật khách hàng</button>
            </form>
    </body>
</html>
