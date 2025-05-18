<?php
session_start();
if (!isset($_SESSION['account_loggedin'])){
    header("Location: ../loginFunction/mainPage.php");
}

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $book_id = $_POST['id'];
} else {
    $book_id = $_GET['id'] ?? '';
}

$category = $genre = $name = $author = $publisher = $publishDay = $language = $sellPrice = $currentQuantity = '';
$nameError = $categoryError = $genreError = $authorError = $publisherError = $publishDayError = $languageError = $sellPriceError = $currentQuantityError = '';
$selectedCategory = $selectedGenre = '';
$selectedSub = [];

$con = new mysqli('localhost','root','','phplogin');
if ($con->connect_errno) {
  die("DB failed: ".$con->connect_error);
}

//đọc đầu sách
$categories = [];
$result = $con->query("SELECT MaDS, TenDauSach FROM dausach");
while ($row = $result->fetch_assoc()) {
    $categories[$row['MaDS']] = $row['TenDauSach'];
}
$result->free();

//đọc thể loại
$subGenres = [];
$result = $con->query("SELECT TenTheLoai, MaDS FROM theloai");
while ($row = $result->fetch_assoc()) {
    $id = $row['MaDS'];
    $subGenres[$id][] = $row['TenTheLoai']; 
}
$result->free();

if (isset($_GET['load'])) {
    header('Content-Type: application/json; charset=utf-8');
    if ($stmt = $con->prepare('SELECT MaDS, MaTL, TenSach, TacGia, NhaXuatBan, NgayXuatBan, NgonNgu, GiaBan, SoLuongTon FROM sach WHERE MaSach = ?')) {
        $stmt->bind_param('i', $book_id);
        $stmt->execute();
        $stmt->bind_result($category_id, $genre_id, $name, $author, $publisher, $publishDay, $language, $sellPrice, $currentQuantity);

        $stmt_ds = $con->prepare('SELECT TenDauSach FROM dausach WHERE MaDS = ?');
        $stmt_ds->bind_param('i', $category_id);
        $stmt_ds->execute();
        $stmt_ds->bind_result($category);
        $stmt_ds->close();

        $stmt_tl = $con->prepare('SELECT TenTheLoai FROM theloai WHERE MaTL = ?');
        $stmt_tl->bind_param('i', $genre_id);
        $stmt_tl->execute();
        $stmt_tl->bind_result($genre);
        $stmt_tl->close();

        if ($stmt->fetch()) {
            echo json_encode([
                "success" => true,
                "data" => [
                    "DauSach" => $category,
                    "TheLoai" => $genre,
                    "TenSach" => $name, 
                    "TacGia" => $author,
                    "NhaXuatBan" =>$publisher,
                    "NgayXuatBan" =>$publishDay,
                    "NgonNgu" => $language,
                    "GiaBan" => $sellPrice,
                    "SoLuongTon" => $currentQuantity
                ]
            ]);
        } else {
            echo json_encode(["success" => false, "error" => "Không tìm thấy sách"]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "error" => "Lỗi truy vấn"]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $book_id) {

    $stmt = $con->prepare('SELECT MaDS, MaTL, TenSach, TacGia, NhaXuatBan, NgayXuatBan, NgonNgu, GiaBan, SoLuongTon FROM sach WHERE MaSach = ?');
    $stmt->bind_param('i',$book_id);
    $stmt->execute();
    $stmt->bind_result($category_id, $genre_id, $name, $author, $publisher, $publishDay, $language, $sellPrice, $currentQuantity);
    $stmt->fetch();
    $stmt->close();

    $stmt_ds = $con->prepare('SELECT TenDauSach FROM dausach WHERE MaDS = ?');
    $stmt_ds->bind_param('i', $category_id);
    $stmt_ds->execute();
    $stmt_ds->bind_result($selectedCategory);
    $stmt_ds->fetch();
    $stmt_ds->close();

    $stmt_tl = $con->prepare('SELECT TenTheLoai FROM theloai WHERE MaTL = ?');
    $stmt_tl->bind_param('i', $genre_id);
    $stmt_tl->execute();
    $stmt_tl->bind_result($selectedGenre);
    $stmt_tl->fetch();
    $stmt_tl->close();

    $subgenres = $subGenres[$category_id];
}

if ($_SERVER['REQUEST_METHOD']==='POST' && (isset($_POST['submit_book']) || 
        isset($_POST['submit_category']) || isset($_POST['submit_genre']))){
    $book_id = $_POST['id'] ?? '';
    $name = $_POST['name'] ?? '';
    $author = $_POST['author'] ?? '';
    $publisher = $_POST['publisher'] ?? '';
    $publishDay = $_POST['publishDay'] ?? '';
    $language = $_POST['language'] ?? '';
    $sellPrice = $_POST['sellPrice'] ?? '';
    $currentQuantity = $_POST['currentQuantity'] ?? '';

    $category_id = $_POST['category'] ?? '';
    $selectedGenre = $_POST['genre'] ?? '';
    $subgenres = $subGenres[$category_id] ?? [];

    if(empty($name)){
        $nameError = "<br />Vui lòng nhập đầy đủ tên sách!";
    }
    if(empty($selectedGenre)){
        $genreError = "<br />Vui lòng chọn thể loại của đầu sách!";
    }
    if(empty($category_id)){
        $categoryError = "<br />Vui lòng chọn đầu sách tương ứng!";
    }
    if(empty($author)){
        $authorError = "<br />Vui lòng nhập đầy đủ tên tác giả!";
    }
    if(empty($publisher)){
        $publisherError = "<br />Vui lòng nhập đầy đủ nhà xuất bản!";
    }
    if(empty($publishDay)){
        $publishDayError = "<br /> Vui lòng chọn ngày xuất bản!";
    } elseif (!strtotime($publishDay)) {
        $publishDayError = "<br /> Ngày không hợp lệ!";
    }

    if(empty($language)){
        $languageError = "<br /> Vui lòng chọn ngôn ngữ của sách!";
    }
    if(empty($sellPrice)){
        $sellPriceError = "<br /> Vui lòng nhập giá bán của sách!";
    } elseif (!is_numeric($sellPrice)) {
        $sellPriceError = "<br /> Giá bán phải là số!";
    }
    if(empty($currentQuantity)){
        $currentQuantityError = '<br /> Vui lòng nhập số lượng tồn của sách!';
    }

    if (!$nameError && !$genreError && !$categoryError && !$authorError && !$publisherError 
            && !$publishDayError && !$languageError  && !$sellPriceError && !$currentQuantityError){

        //lấy mã đầu sách và mã thể loại tương ứng 
        $stmt = $con->prepare('SELECT MaTL FROM theloai WHERE TenTheLoai = ? AND MaDS = ?');
        $stmt->bind_param('si', $selectedGenre, $category_id);
        $stmt->execute();
        $stmt->bind_result($genre_id);
        if (!$stmt->fetch()){
            die("Đầu sách không có thể loại này.");
        }
        $stmt->close();

        if ($stmt = $con->prepare('UPDATE sach SET MaDS = ?, MaTL = ?, TenSach = ?, TacGia = ?, NhaXuatBan = ?, NgayXuatBan = ?, NgonNgu = ?, GiaBan = ?, SoLuongTon = ? WHERE MaSach = ?')) {
            $stmt->bind_param('iisssssdii', $category_id, $genre_id, $name, $author, $publisher, $publishDay, $language, $sellPrice, $currentQuantity, $book_id);
            $stmt->execute();            
            $stmt->close();
            echo <<<HTML
                <script>
                    alert("Cập nhật thông tin sách thành công!");
                    window.location.href = "tracuu_sach.php"; 
                </script>
                HTML;
            exit;
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
        <title>CẬP NHẬT THÔNG TIN SÁCH</title>    
    </head>
    <body>
        <nav>
            <a href="../adminHomePage.php">Về Trang chủ</a>
            <a href="quanly_sach.php">Về Quản lý sách</a>
        </nav>        
        <div class="container">
            <h1>Cập nhật thông tin sách</h1>
            <form action="" method="post" class="editBook-form" novalidate>
                <input type="hidden" name="form_action" id="form_action" value="">
                <label class="label-form" for="id">Mã sách</label>
                <div class="group-form">
                    <input class="input-form" type="text" name="id" placeholder="Id" id="id" value="<?= htmlspecialchars($book_id?? '') ?>" readonly required>
                </div>

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
                            <option value="<?= $key ?>"<?= strval($key) === strval($category_id) ? 'selected' : '' ?>> <?= $label ?></option>
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
                        <option value="<?= $option ?>"<?= strval($option) === strval($selectedGenre) ? 'selected' : '' ?>><?= $option ?></option>
                    <?php endforeach; ?>
                    </select>
                    <span style="color: red;"><?php echo $genreError ?></span>
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

                <label class="label-form" for="currentQuantity">Số lượng tồn</label>
                <div class="group-form">
                    <input class="input-form" type="text" name="currentQuantity" placeholder="Current Quantity" id="currentQuantity" value="<?= htmlspecialchars($currentQuantity ?? '') ?>" required>
                    <span style="color: red;"><?php echo $currentQuantityError ?></span>
                </div>
                <button type="submit" name="submit_book" value="1">Cập nhật sách</button>
            </form>
        </div>
    </body>
</html>