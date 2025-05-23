<?php
session_start();
//kiểm tra nếu chưa login thì cho về login
if (!isset($_SESSION['account_loggedin'])) {
    header('Location: ../loginFunction/mainPage.php');
    exit;
}
//kiểm tra nếu đã login rồi nhưng ko phải là quản lý thì cho 
//quay về trang chủ với chức năng tương ứng được cấp quyền
// if (!isset($_SESSION['account_isManager'])) {
//     header('Location: adminHomePage.php');
//     exit;
// }

$con = mysqli_connect('localhost', 'root', '', 'phplogin');
if (mysqli_connect_errno()) {
    $message = 'Lỗi kết nối thất bại đến MySql: ' . mysqli_connect_error();
}

$fullnameError = $phonenumError = $dobError = $salaryError = $shiftError = '';
$employee_id = $addressError = $positionError = $message = '';
$availableShifts = [];

$fullname = $_POST['fullname'] ?? '';
$phonenum = $_POST['phonenum'] ?? '';
$dob = $_POST['dob'] ?? '';
$address = $_POST['address'] ?? '';
$position = $_POST['position'] ?? '';
$shift = $_POST['shifts'] ?? [];
$shift_string = implode(',', $shift);
$salary = $_POST['salary'] ?? [];

if ($stmt = $con->prepare('SELECT MaCa FROM lichlamviec')) {
    $stmt->execute();
    $stmt->bind_result($maCa);
    while ($stmt->fetch()) {
        $availableShifts[] = $maCa;
    }
    $stmt->close();
}

//khi user nhấn button thêm nhân viên
if (isset($_POST['submit_employee'])){

    //region ERROR: không điền đẩy đủ dữ liệu, sai định dạng
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


    //endregion 

    //region THÊM thông tin nhân viên mới vào csdl
    if (!$fullnameError && !$phonenumError && !$dobError && !$addressError && !$positionError && !$salaryError && !$shiftError){
        //kiểm tra xem nhân viên đã tồn tại chưa
        if ($stmt = $con->prepare('SELECT MaNV FROM nhanvien WHERE SDT = ?')) {
            $stmt->bind_param('s', $phonenum);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $phonenumError = 'Số điện thoại đã tồn tại. Vui lòng chọn số khác!';
            } else {
                if ($stmt_nv = $con->prepare('INSERT INTO nhanvien (HoTen, NgaySinh, SDT, NoiO, ChucVu, CaLam, Luong) VALUES (?, ?, ?, ?, ?, ?, ?)')) {
                    $stmt_nv->bind_param('ssssssd', $fullname, $dob, $phonenum, $address, $position, $shift_string, $salary);
                    $stmt_nv->execute();  
                    $stmt_nv->close();

                    $employee_id = $con->insert_id;
                    foreach ($shift as $theShift){
                        $stmt_nv = $con->prepare('INSERT INTO lichlamviec(MaNV, MaCa) VALUES(?, ?)');
                        $stmt_nv->bind_param('is', $employee_id, $theShift);
                        $stmt_nv->execute(); 
                        $stmt_nv->close();
                    } 
                    echo <<<HTML
                        <script>
                            alert("Thêm nhân viên thành công. Bạn sẽ được đưa đến trang tra cứu nhân viên!");
                            window.location.href = "tracuu_nhanvien.php"; 
                        </script>
                        HTML;
                    exit;             
                } else { $message = 'Lỗi câu lệnh truy vấn cơ sở dữ liệu!'; }
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
        <title>THÊM NHÂN VIÊN</title>    
    </head>
    <body>
        <a href="../adminHomePage.php">Trang chủ</a>
        <a href="quanly_nhanvien.php">Về Quản lý nhân viên</a>
        <div class="container">
            <?php if ($message): ?>
                <div class="alert" style="color:green;">
                <?= htmlspecialchars($message, ENT_QUOTES) ?></div>
            <?php endif; ?>

            <h1>Thêm nhân viên mới</h1>
            <form action="" method="post" class="addEmployee-form" novalidate>
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
                        <option value="Quản lý">Quản lý</option>
                        <option value="Bảo vệ">Bảo vệ</option>
                        <option value="Bán hàng">Nhân viên bán hàng</option>
                        <option value="Thu ngân">Nhân viên thu ngân</option>
                        <option value="Marketing và chăm sóc khách hàng">Nhân viên marketing và chăm sóc khách hàng</option>
                    </select>
                    <span style="color: red;"><?php echo $positionError ?></span>
                </div>

                <label class="label-form" for="shift">Đăng ký ca làm</label>
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
                            <?= in_array("{$key}-ca{$i}", $_POST['shifts'] ?? [], true) ? 'checked' : '' ?>
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

                <label class="label-form" for="salary">Lương cơ bản</label>
                <div class="group-form">
                    <select name="salary" id="salary" required>
                        <option value="">-Chọn mức lương tương ứng</option>
                        <option value="25000">Lương hạng D: 25k/giờ</option>
                        <option value="35000">Lương hạng C: 35k/giờ</option>
                        <option value="50000">Lương hạng B: 50k/giờ</option>
                        <option value="65000">Lương hạng A: 65k/giờ</option>
                    </select>
                    <span style="color: red;"><?php echo $salaryError ?></span>
                </div>
                
                <button class="button" type="submit" name="submit_employee">Thêm nhân viên</button>
            </form>
        </div>
    </body>
</html>
