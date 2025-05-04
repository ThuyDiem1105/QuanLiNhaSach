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

//region: đọc dữ liệu về đầu sách và các thể loại lên 
$mysqli = new mysqli("localhost", "root", "", "phplogin");
$mysqli->set_charset("utf8");
if ($mysqli->connect_errno) {
    die("Lỗi kết nối: " . $mysqli->connect_error);
}
//đọc đầu sách
$categories = [];
$result = $mysqli->query("SELECT MaDS, TenDauSach FROM dausach");
while ($row = $result->fetch_assoc()) {
    $categories[$row['MaDS']] = $row['TenDauSach'];
}
$result->free();

//đọc thể loại
$subGenres = [];
$result = $mysqli->query("SELECT TenTheLoai, MaDS FROM theloai");
while ($row = $result->fetch_assoc()) {
    $id = $row['MaDS'];
    $subGenres[$id][] = $row['TenTheLoai']; 
}
$result->free();
$mysqli->close();
//endregion
  
$selectedCat = $_POST['category'] ?? '';
$selectedSub = $_POST['genre']  ?? '';
$subgenres = $subGenres[$selectedCat] ?? [];

$name = $_POST['name'] ?? '';
$author = $_POST['author'] ?? '';
$publisher = $_POST['publisher'] ?? '';
$publishDay = $_POST['publishDay'] ?? '';
$language = $_POST['language'] ?? '';
$sellPrice = $_POST['sellPrice'] ?? '';
$MaDS = $selectedCat;
$MaTL = '';

$nameError = $categoryError = $genreError = $authorError = $publisherError = '';
$publishDayError = $languageError = $sellPriceError = $message = '';

if (isset($_POST['submit_book']) || isset($_POST['submit_category']) || isset($_POST['submit_genre'])){

    if(empty($name)){
        $nameError = "<br />Đây là ô thông tin bắt buộc điền. Vui lòng nhập đầy đủ tên sách!";
    }
    if(empty($selectedSub)){
        $genreError = "<br />Vui lòng chọn thể loại của đầu sách!";
    }
    if(empty($selectedCat)){
        $categoryError = "<br />Vui lòng chọn đầu sách tương ứng!";
    }
    if(empty($author)){
        $authorError = "<br />Đây là ô thông tin bắt buộc điền! Vui lòng nhập đầy đủ tên tác giả!";
    }
    if(empty($publisher)){
        $publisherError = "<br />Đây là ô thông tin bắt buộc điền! Vui lòng nhập đầy đủ nhà xuất bản!";
    }
    if(empty($publishDay)){
        $publishDayError = "<br /> Vui lòng chọn ngày xuất bản!";
    }
    if(empty($language)){
        $languageError = "<br /> Vui lòng chọn ngôn ngữ của sách!";
    }
    if(empty($sellPrice)){
        $sellPriceError = "<br /> Vui lòng nhập giá bán của sách!";
    } elseif (!is_numeric($sellPrice)) {
        $sellPriceError = "<br /> Giá bán phải là số!";
    }
    

    if (!$nameError && !$genreError && !$categoryError && !$authorError && !$publisherError && !$publishDayError && !$languageError  && !$sellPriceError){
        $con = mysqli_connect('localhost', 'root', '', 'phplogin');
        if (mysqli_connect_errno()) {
            $message = 'Lỗi kết nối thất bại đến MySql: ' . mysqli_connect_error();
        }

        //lấy mã đầu sách và mã thể loại tương ứng 

        $stmt = $con->prepare('SELECT MaTL FROM theloai WHERE TenTheLoai = ? AND MaDS = ?');
        $stmt->bind_param('si', $selectedSub, $MaDS);
        $stmt->execute();
        $stmt->bind_result($MaTL);
        if ($stmt->fetch()){
            $message = "Tìm thấy thể loại tương ứng.";
        } else {
            $message = "Đầu sách không có thể loại này.";
        }
        $stmt->close();

        //kiểm tra xem sách đã có trong nhà sách chưa
        if ($stmt = $con->prepare('SELECT MaSach FROM sach WHERE TenSach = ? AND TacGia = ?')) {
            $stmt->bind_param('ss', $name, $author);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $message = 'Sách đã tồn tại. Vui lòng thử lại!';
            } else {
                if ($stmt = $con->prepare('INSERT INTO sach (MaDS, MaTL, TenSach, NhaXuatBan, NgayXuatBan, TacGia, NgonNgu, GiaBan ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)')){
                    $stmt->bind_param('iisssssd', $MaDS, $MaTL, $name, $publisher, $publishDay, $author, $language, $sellPrice);
                    $stmt->execute();
                    $message = 'Thêm sách mới mới thành công!'; 
                    $MaDS = $MaTL = $name = $publisher = $publishDay = $author = $language = $sellPrice = $selectedCat = $selectedSub = '';
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
        <title>THÊM SÁCH</title>    
    </head>
    <body>
        <a href="../homePage.php">Về Trang chủ</a>
        <div class="container">
            <h1>Thêm sách mới</h1>
            <form action="" method="post" class="addBook-form" novalidate>
                <input type="hidden" name="form_action" id="form_action" value="">
                <label class="label-form" for="name">Tên sách</label>
                <div class="group-form">
                    <input class="input-form" type="text" name="name" placeholder="Name" id="name" value="<?= htmlspecialchars($name?? '') ?>" required>
                    <span style="color: red;"><?php echo $nameError ?></span>
                </div>

                <label class="label-form" for="category">Đầu sách</label>
                <div class="group-form">
                    <select id="category" name="category" required>
                        <option value="">-- Chọn đầu sách --</option>
                        <?php foreach ($categories as $key => $label): ?>
                            <option value="<?= $key ?>"<?= strval($key) === strval($selectedCat) ? 'selected' : '' ?>> <?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span style="color: red;"><?php echo $categoryError ?></span>
                    <button type="submit" name="submit_category">Lọc</button>
                </div>

                <label class="label-form" for="genre">Thể loại tương ứng</label>
                <div class="group-form">
                    <select id="genre" name="genre" required>
                    <option value="">-- Chọn thể loại tương ứng --</option>
                    <?php foreach ($subgenres as $option): ?>
                        <option value="<?= $option ?>"<?= strval($option) === strval($selectedSub) ? 'selected' : '' ?>><?= $option ?></option>
                    <?php endforeach; ?>
                    </select>
                    <span style="color: red;"><?php echo $genreError ?></span>
                    <button type="submit" name="submit_genre">Thêm thể loại</button>

                </div>
                
                <label class="label-form" for="author">Tác giả</label>
                <div class="group-form">
                    <input class="input-form" type="text" name="author" placeholder="Author" id="author" value="<?= htmlspecialchars($author ?? '') ?>" required>
                    <span style="color: red;"><?php echo $authorError ?></span>
                </div>

                <label class="label-form" for="publisher">Nhà xuất bản</label>
                <div class="group-form">
                    <input class="input-form" type="text" name="publisher" placeholder="Publisher" id="publisher" value="<?= htmlspecialchars($publisher ?? '') ?>" required>
                    <span style="color: red;"><?php echo $publisherError ?></span>
                </div>

                <label class="label-form" for="publishDay">Ngày xuất bản</label>
                <div class="group-form">
                    <input class="input-form" type="date" name="publishDay" placeholder="Publishing Day" id="publishDay" value="<?= htmlspecialchars($publishDay ?? '') ?>" required>
                    <span style="color: red;"><?php echo $publishDayError ?></span>
                </div>

                <label class="label-form" for="language">Ngôn ngữ</label>
                <div class="group-form">
                    <input class="input-form" type="text" name="language" placeholder="language" id="language" value="<?= htmlspecialchars($language ?? '') ?>" required>
                    <span style="color: red;"><?php echo $languageError ?></span>
                </div>

                <label class="label-form" for="sellPrice">Đơn giá bán</label>
                <div class="group-form">
                    <input class="input-form" type="text" name="sellPrice" placeholder="Selling Price" id="sellPrice" value="<?= htmlspecialchars($sellPrice ?? '') ?>" required>
                    <span style="color: red;"><?php echo $sellPriceError ?></span>
                </div>
                <button type="submit" name="submit_book" value="1">Thêm sách mới</button>
            </form>
            <?php if ($message): ?>
                <div class="alert" style="color:green;">
                <?= htmlspecialchars($message, ENT_QUOTES) ?></div>
            <?php endif; ?>
        </div>
    </body>
</html>