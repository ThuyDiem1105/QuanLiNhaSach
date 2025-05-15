<?php
session_start();
if (!isset($_SESSION['account_loggedin'])){
    header("Location: ../loginFunction/mainPage.php");
}

$con = new mysqli('localhost','root','','phplogin');
if ($con->connect_errno) {
  die("DB failed: ".$con->connect_error);
}

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $employee_id = $_POST['id'];
} else {
    $employee_id = $_GET['id'] ?? '';
}

$fullname = $dob = $phonenum = $address = $position = $shift_string = $salary = '';
$shiftArr = $availableShifts = [];

if (isset($_GET['load'])) {
    header('Content-Type: application/json; charset=utf-8');
    if ($stmt = $con->prepare('SELECT HoTen, NgaySinh, SDT, NoiO, ChucVu, CaLam, Luong FROM nhanvien WHERE MaNV = ?')) {
        $stmt->bind_param('i', $employee_id);
        $stmt->execute();
        $stmt->bind_result($fullname, $dob, $phonenum, $address, $position, $shift, $salary);
        if ($stmt->fetch()) {
            echo json_encode([
                "success" => true,
                "data" => [
                    "HoTen" => $fullname,
                    "NgaySinh" => $dob,
                    "SDT" => $phonenum,
                    "NoiO" => $address,
                    "ChucVu" => $position,
                    "CaLam" => $shift,
                    "Luong" => $salary
                ]
            ]);
        } else {
            echo json_encode(["success" => false, "error" => "Không tìm thấy nhân viên"]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "error" => "Lỗi truy vấn"]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $employee_id) {
  $stmt = $con->prepare('SELECT HoTen, NgaySinh, SDT, NoiO, ChucVu, CaLam, Luong FROM nhanvien WHERE MaNV=?');
  $stmt->bind_param('i',$employee_id);
  $stmt->execute();
  $stmt->bind_result($fullname, $dob, $phonenum, $address, $position, $shift_string, $salary);
  $stmt->fetch();
  $stmt->close();
  $shiftArr = explode(',', $shift_string);

    $stmt = $con->prepare('SELECT MaCa FROM lichlamviec WHERE MaNV != ?');
    $stmt->bind_param('i', $employee_id);
    $stmt->execute();
    $stmt->bind_result($maCa);
    while ($stmt->fetch()) {
        $availableShifts[] = $maCa;
    }
    $stmt->close();
}

$fullnameError = $usernameError = $phonenumError = $dobError = $passwordError = $salaryError = '';
$emailError = $addressError = $positionError  = $confirmpassError = $message =  $shiftError = '';

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['submit'])){
    $fullname = $_POST['fullname'] ?? '';
    $phonenum = $_POST['phonenum'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $address = $_POST['address'] ?? '';
    $position = $_POST['position'] ?? '';
    $shift = $_POST['shifts'] ?? [];
    $shift_string = implode(',', $shift);
    $salary = $_POST['salary'] ?? [];

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
    if(empty($dob)){
        $dobError = "<br />Đây là ô thông tin bắt buộc điền!";
    }
    if(empty($position)){
        $positionError = "<br />Vui lòng chọn chức vụ tương ứng!";
    }
    if(empty($salary)){
        $salaryError = "<br />Vui lòng chọn mức lương phù hợp!";
    }
    if (empty($shift)){
        $shiftError = '<br />Vui lòng chọn ít nhất 4 ca làm trong một tuần!';
    } elseif (count($shift) < 4){
        $shiftError = '<br />Phải đăng ký tối thiểu 4 ca trong một tuần!';
    }

    if (!$fullnameError && !$phonenumError && !$dobError && !$addressError && !$positionError && !$salaryError && !$shiftError){
        $stmt = $con->prepare("UPDATE nhanvien SET HoTen = ?, NgaySinh = ?, SDT = ?, NoiO = ?, ChucVu = ?, CaLam = ?, Luong = ? WHERE MaNV = ?");
        $stmt->bind_param('ssssssdi', $fullname, $dob, $phonenum, $address, $position, $shift_string, $salary, $employee_id);
        $stmt->execute();
        $stmt->close();

        $stmt = $con->prepare("DELETE FROM lichlamviec WHERE MaNV = ?");
        $stmt->bind_param('i', $employee_id);
        $stmt->execute();
        $stmt->close();

        foreach ($shiftArr as $theShift){
            $stmt = $con->prepare('INSERT INTO lichlamviec(MaNV, MaCa) VALUES(?, ?)');
            $stmt->bind_param('is', $employee_id, $theShift);
            $stmt->execute();
            $stmt->close();
        } 

        echo <<<HTML
              <script>
                alert("Cập nhật thông tin nhân viên thành công!");
                window.location.href = "tracuu_nhanvien.php"; 
              </script>
            HTML;
        exit;
        $con->close();
    }
}
?>

<!DOCTYPE html>
<html>
    <head lang="vi">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, minimum-scale=1">
        <title>CẬP NHẬT THÔNG TIN NHÂN VIÊN</title>    
    </head>
    <body>
        <h1>Cập nhật thông tin nhân viên</h1>
            <form action="" method="post" class="editEmployee-form" novalidate>
                <label class="label-form" for="id">Mã nhân viên</label>
                    <div class="group-form">
                        <input class="input-form" type="text" name="id" placeholder="Employee Id" id="id" value="<?= htmlspecialchars($employee_id ?? '') ?>" readonly required>
                    </div>

                <label class="label-form" for="fullname">Họ tên nhân viên</label>
                <div class="group-form">
                    <input class="input-form" type="text" name="fullname" placeholder="Fullname" id="fullname" value="<?= htmlspecialchars($fullname ?? '') ?>" required>
                    <span style="color: red;"><?php echo $fullnameError ?></span>
                </div>

                <label class="label-form" for="dob">Ngày sinh</label>
                <div class="group-form">
                    <input class="input-form" type="date" name="dob" placeholder="Dob" id="dob" value="<?= htmlspecialchars($dob ?? '') ?>" required>
                    <span style="color: red;"><?php echo $dobError ?></span>
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

                <label class="label-form" for="position">Chức vụ</label>
                <div class="group-form">
                    <select name="position" id="position">
                    <option value="">-Chọn chức vụ-</option>
                    <option value="Quản lý" <?= $position === 'Quản lý' ? 'selected' : ''?>>Quản lý</option>
                    <option value="Bảo vệ" <?= $position === 'Bảo vệ' ? 'selected' : ''?>>Bảo vệ</option>
                    <option value="Bán hàng" <?= $position === 'Bán hàng' ? 'selected' : '' ?>>Nhân viên bán hàng</option>
                    <option value="Thu ngân" <?= $position === 'Thu ngân' ? 'selected' : '' ?>>Nhân viên thu ngân</option>
                    <option value="Marketing và chăm sóc khách hàng"
                    <?= $position === 'Marketing và chăm sóc khách hàng' ? 'selected' : '' ?>>Nhân viên marketing và chăm sóc khách hàng</option>
                    </select>
                    <span style="color: red;"><?php echo $positionError ?></span>
                </div>

                <label class="label-form" for="shifts">Thay đổi ca làm</label>
                <div class="group-form">
                    <table border="1" cellpadding="8">
                        <thead>
                        <tr>
                            <th>Ngày</th>
                            <th>Ca 1 (sáng) từ 7:00-10:30</th>
                            <th>Ca 2 (trưa) từ 10:30-14:00</th>
                            <th>Ca 3 (chiều) từ 14:00-17:30</th>
                            <th>Ca 4 (tối) từ 17:30-21:00</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $days = ['Mon'=>'Thứ 2','Tue'=>'Thứ 3','Wed'=>'Thứ 4','Thu'=>'Thứ 5','Fri'=>'Thứ 6','Sat'=>'Thứ 7','Sun'=>'Chủ nhật'];
                        foreach ($days as $key => $label): ?>
                        <tr>
                            <td><?= $label ?></td>
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                            <td style="text-align:center">
                            <input
                                type="checkbox"
                                name="shifts[]"
                                value="<?= "{$key}-ca{$i}" ?>"
                                <?= in_array("{$key}-ca{$i}", $shiftArr, true) ? 'checked' : '' ?> 
                                <?= in_array("{$key}-ca{$i}", $availableShifts, true) ? 'disabled' : '' ?> 
                            >
                            </td>
                            <?php endfor; ?>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <span style="color: red;"><?php echo $shiftError ?></span>
                </div>

                <label class="label-form" for="salary">Thay đổi lương cơ bản</label>
                <div class="group-form">
                    <select name="salary" id="salary" required>
                    <option value="">-Chọn mức lương-</option>
                    <option value="25000" <?= $salary == 25000 ? 'selected' : '' ?>>Lương hạng D: 25k/giờ</option>
                    <option value="35000" <?= $salary == 35000 ? 'selected' : '' ?>>Lương hạng C: 35k/giờ</option>
                    <option value="50000" <?= $salary == 50000 ? 'selected' : '' ?>>Lương hạng B: 50k/giờ</option>
                    <option value="65000" <?= $salary == 65000 ? 'selected' : '' ?>>Lương hạng A: 65k/giờ</option>
                    </select>
                    <span style="color: red;"><?php echo $salaryError ?></span>
                </div>
                
                <button class="button" type="submit" name="submit">Cập nhật thông tin nhân viên</button>
            </form>
    </body>
</html>
