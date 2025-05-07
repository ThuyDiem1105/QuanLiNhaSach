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
//     header('Location: homePage.php');
//     exit;
// }

$con = mysqli_connect('localhost', 'root', '', 'phplogin');
if (mysqli_connect_errno()) {
    $message = 'Lỗi kết nối thất bại đến MySql: ' . mysqli_connect_error();
}

$bookid = $_POST['bookid'] ?? '';
$importday = $_POST['importday'] ?? '';
$provider = $_POST['provider'] ?? '';
$price = $_POST['price'] ?? '';
$quantity = $_POST['quantity'] ?? '';
$bill = $_POST['bill'] ?? '';

$bookidError = $importdayError = $providerError = $priceError = $quantityError = $billError = $message = '';
if (isset($_POST['submit_bookTicket'])){

    if(empty($bookid)){
        $bookidError = '<br />Vui lòng nhập mã sách nhập!';
    } else {
        $stmt = $con->prepare('SELECT 1 FROM sach WHERE MaSach = ?');
        $stmt->bind_param('i', $bookid);
        $stmt->execute();
        $stmt->store_result();
        if($stmt->num_rows() <= 0){
            $bookidError = '<br />Không tồn tại loại sách này trong kho! Vui lòng thêm sách trước khi thêm phiếu nhập sách!';
        }
        $stmt->close();
    }
    if(empty($importday)){
        $importdayError = '<br />Vui lòng chọn ngày nhập sách!';
    }
    if(empty($provider)){
        $providerError = '<br />Vui lòng nhập nguồn nhập sách về!';
    }
    if(empty($price)){
        $priceError = '<br />Vui lòng nhập đơn giá nhập của sách!';
    }
    if(empty($quantity)){
        $quantityError = '<br />Vui lòng nhập số lượng sách nhập về!';
    }
    if(empty($bill)){
        $billError = '<br />Lỗi tính toán, vui lòng kiểm tra lại đơn giá và số lượng sách nhập!';
    }

    if(!$bookidError && !$importdayError && !$providerError && !$priceError && !$quantityError && !$billError){
        if ($stmt = $con->prepare('INSERT INTO phieunhapsach(MaSach, NgayNhap, SoLuong, DonGiaNhap, NguonNhap, ThanhTien) VALUES (?, ?, ?, ?, ?, ?)')) {
            $stmt->bind_param('isidsd', $bookid, $importday, $quantity, $price, $provider, $bill);
            $stmt->execute();
            $message = 'Thêm phiếu nhập sách thành công!';
            $bookid = $importday = $provider = $price = $quantity = $bill = '';
        }
        else {
            $message = 'Lỗi truy vấn cơ sở dữ liệu.';
        }
    }
}
 
?>

<!DOCTYPE hmtl>
<html>
    <head lang="vi">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, minimum-scale=1">
        <title>PHIẾU NHẬP SÁCH</title>    
    </head>
    <body>
        <nav>
            <a href="../homePage.php">Về Trang chủ</a>
            <a href="quanly_sach.php">Về Quản lý sách</a>
        </nav>
        <div class="container">
            <h1>Thêm Phiếu nhập sách</h1>
            <form action="" method="post" class="addBookTicket-form" novalidate>
                <label class="label-form" for="bookid">Mã sách nhập</label>
                <div class="group-form">
                    <input class="input-form" type="text" name="bookid" placeholder="Book Id" id="bookid" value="<?= htmlspecialchars($bookid?? '') ?>" required>
                    <span style="color: red;"><?php echo $bookidError ?></span>
                </div>

                <label class="label-form" for="importday">Ngày nhập sách</label>
                <div class="group-form">
                    <input class="input-form" type="date" name="importday" placeholder="Importing Day" id="importday" value="<?= htmlspecialchars($importday ?? '') ?>" required>
                    <span style="color: red;"><?php echo $importdayError ?></span>
                </div>

                <label class="label-form" for="provider">Nguồn nhập sách</label>
                <div class="group-form">
                    <input class="input-form" type="text" name="provider" placeholder="Provider" id="provider" value="<?= htmlspecialchars($provider ?? '') ?>" required>
                    <span style="color: red;"><?php echo $providerError ?></span>
                </div>

                <label class="label-form" for="price">Đơn giá nhập</label>
                <div class="group-form">
                    <input class="input-form" type="text" name="price" placeholder="Price" id="price" value="<?= htmlspecialchars($price ?? '') ?>" oninput="thanhTien()" required>
                    <span style="color: red;"><?php echo $priceError ?></span>
                </div>

                <label class="label-form" for="quantity">Số lượng nhập</label>
                <div class="group-form">
                    <input class="input-form" type="text" name="quantity" placeholder="Quantity" id="quantity" value="<?= htmlspecialchars($quantity ?? '') ?>" oninput="thanhTien()" required>
                    <span style="color: red;"><?php echo $quantityError ?></span>
                </div>
                
                <label class="label-form" for="bill">Tổng thành tiền</label>
                <div class="group-form">
                    <input class="input-form" type="text" name="bill" placeholder="Bill" id="bill" readonly required>
                    <span style="color: red;"><?php echo $billError ?></span>
                </div>
                <button type="submit" name="submit_bookTicket" value="1">Thêm phiếu nhập sách</button>
            </form>
            <?php if ($message): ?>
                <div class="alert" style="color:green;">
                <?= htmlspecialchars($message, ENT_QUOTES) ?></div>
            <?php endif; ?>
        </div>
    </body>
</html>

<script>
    function thanhTien() {
        const price = parseFloat(document.getElementById('price').value) || 0;
        const quantity = parseInt(document.getElementById('quantity').value) || 0;
        const total = price * quantity;
        document.getElementById('bill').value = total.toFixed(2);
    }
</script>