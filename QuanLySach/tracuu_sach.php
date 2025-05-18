<?php
session_start();
if (!isset($_SESSION['account_loggedin'])) {
    header('Location: ../loginFunction/mainPage.php');
    exit;
}

// Database connection
$con = mysqli_connect('localhost', 'root', '', 'phplogin');
if (mysqli_connect_errno()) {
    die("Connection failed: " . mysqli_connect_error());
}
$con->set_charset("utf8mb4");

if (isset($_GET['load'])) {
    header('Content-Type: application/json; charset=utf-8');

    $result = $con->query('SELECT * FROM sach');
    $bookArr = [];
    while ($row = $result->fetch_assoc()) {
        $bookArr[] = $row;
    }

    $result = $con->query('SELECT * FROM dausach');
    $categoryArr = [];
    while ($row = $result->fetch_assoc()) {
        $categoryArr[] = $row;
    }

    $result = $con->query('SELECT * FROM theloai');
    $genreArr = [];
    while ($row = $result->fetch_assoc()) {
        $genreArr[] = $row;
    }

    echo json_encode([
        "sach" => $bookArr,
        "dausach" => $categoryArr,
        "theloai" => $genreArr
    ]);    
    $con->close();
    exit;
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>TRA CỨU SÁCH</title>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <style>
            .form-popup {
            display: none;
            position: fixed;
            bottom: 0;
            right: 15px;
            border: 3px solid #f1f1f1;
            z-index: 9;
            }

            /* Add styles to the form container */
            .form-container {
            max-width: 300px;
            padding: 10px;
            background-color: white;
            }
        </style>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    </head>
    <body>
        <nav>
            <a href="../adminHomePage.php">Về Trang chủ</a>
            <a href="quanly_sach.php">Về Quản lý sách</a>
        </nav>
        <h2>Danh sách quyển sách của nhà sách</h2>
        <table id="sachTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Mã sách</th>
                    <th>Mã đầu sách</th>
                    <th>Mã thể loại</th>
                    <th>Tên sách</th>
                    <th>Tác giả</th>
                    <th>Ngôn ngữ</th>
                    <th>Nhà xuất bản</th>
                    <th>Ngày xuất bản</th>
                    <th>Giá bán</th>
                    <th>Số lượng tồn</th>
                    <th></th>
                </tr>
            </thead>
        </table>

        <h2>Danh mục sách của nhà sách</h2>
        <button type="submit" id="addCategory">Thêm danh mục sách</button>
        <table id="dausachTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Mã danh mục</th>
                    <th>Tên danh mục</th>
                    <th></th>
                </tr>
            </thead>
        </table>

        <div class="form-popup" id="categoryModal">
            <form method="post" action="" class="form-container" id="addCategory-Form">
                <h1>Thêm danh mục sách</h1>
                <label class="label-form" for="category">Tên danh mục</label>
                <div class="group-form">
                    <input class="input-form" type="text" name="category" placeholder="Category" id="category" value="<?= htmlspecialchars($category?? '') ?>" required>
                </div>
                <button type="submit" name ="add_category">Thêm danh mục</button>
                <button type="button" id="close_category">Đóng</button>
            </form>
        </div>

        <div class="form-popup" id="genreModal">
            <form method="post" action="" class="form-container" id="addGenre-Form">
                <h1>Thêm thể loại</h1>
                <label class="label-form" for="category_id">Mã danh mục</label>
                <div class="group-form">
                    <input type="text" name="category_id" placeholder="Category Id" id="category_id" value="<?= htmlspecialchars($category_id?? '') ?>" readonly required>
                </div>
                <label class="label-form" for="genre">Tên thể loại</label>
                <div class="group-form">
                    <input class="input-form" type="text" name="genre" placeholder="Genre" id="genre" value="<?= htmlspecialchars($genre?? '') ?>" required>
                </div>
                <button type="submit" name="add_genre">Thêm thể loại</button>
                <button type="button" id="close_genre">Đóng</button>
            </form>
        </div>

        <h2>Danh sách thể loại của nhà sách</h2>
        <table id="theloaiTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Mã thể loại</th>
                    <th>Tên thể loại</th>
                    <th>Mã danh mục</th>
                </tr>
            </thead>
        </table>

        <script>
            function openForm() {
                document.getElementById("addCategory-Form").style.display = "block"; }
            function closeForm() {
                document.getElementById("addCategory-Form").style.display = "none"; }

            $(document).ready(function () {
                $.ajax({
                    url: 'tracuu_sach.php?load=true',
                    dataType: 'json',
                    success: function (response) {
                        //Bảng danh mục sách
                        $('#dausachTable').DataTable({
                            data: response.dausach,
                            "columns": [
                                { data: "MaDS"},
                                { data: "TenDauSach"},
                                {
                                    data: null,
                                    "orderable": false,
                                    "render": function (data, type, row) {
                                        return `
                                        <button class="addGenre-btn" data-category-id="${row.MaDS}" title="Thêm thể loại">➕</button>
                                        `;
                                    }
                                }
                            ],
                            "scrollY": "400px",
                            "scrollCollapse": true,
                            "paging": false,         
                            "info": false,
                        });

                        //Bảng thể loại
                        $('#theloaiTable').DataTable({
                            data: response.theloai,
                            "columns": [
                                { data: "MaTL"},
                                { data: "TenTheLoai"},
                                { data: "MaDS"}
                            ],
                            "scrollY": "400px",
                            "scrollCollapse": true,
                            "paging": false,         
                            "info": false,
                        });

                        //Bảng sách
                        $('#sachTable').DataTable({
                            data: response.sach,
                            "columns": [
                                { data: "MaSach" },
                                { data: "MaDS"},
                                { data: "MaTL" },
                                { data: "TenSach" },
                                { data: "TacGia" },
                                { data: "NgonNgu"},
                                { data: "NhaXuatBan"},
                                { data: "NgayXuatBan"},
                                { data: "GiaBan"},
                                { data: "SoLuongTon"},
                                {
                                    data: null,
                                    "orderable": false,
                                    "render": function (data, type, row) {
                                        return `
                                        <button class="edit-btn" data-book-id="${row.MaSach}" title="Sửa">✏️</button>
                                        <button class="delete-btn" data-book-id="${row.MaSach}" title="Xóa">🗑️</button>
                                        `;
                                    }
                                }
                            ],
                            "scrollY": "400px",
                            "scrollCollapse": true,
                            "paging": false,         
                            "info": false,
                        });
                    }
                });
            });

            //region Thêm danh mục sách (popup form)
            $('#addCategory').on('click', function() {
                $('#categoryModal').fadeIn();
            });
            $('#close_category').on('click', function () {
                $('#categoryModal').fadeOut();
            });
            $('#addCategory-Form').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serialize();   //get all form values
                $.ajax({
                    url: 'them_dausach.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function (res) {
                        if (res.success) {
                            alert('Thêm danh mục sách mới thành công!');
                            location.reload();
                        } else {
                            alert('Lỗi: ' + res.error);
                        }
                    },
                    error: function () {
                        alert('Lỗi AJAX. Không thể gửi yêu cầu thêm!');
                    }
                });
            });
            //endregion

            //region Thêm thể loại cho đầu sách
            $('#dausachTable').on('click', '.addGenre-btn', function () {
                const category_id = $(this).data('category-id');
                $('#category_id').val(category_id);
                $('#genreModal').fadeIn();
            });
            $('#close_genre').on('click', function () {
                $('#genreModal').fadeOut();
            });

            $('#addGenre-Form').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serialize();   //get all form values
                $.ajax({
                    url: 'them_theloai.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function (res) {
                        if (res.success) {
                            alert('Thêm thể loại mới cho danh mục sách thành công!');
                            location.reload();
                        } else {
                            alert('Lỗi: ' + res.error);
                        }
                    },
                    error: function () {
                        alert('Lỗi AJAX. Không thể gửi yêu cầu thêm!');
                    }
                });
            });
            //endregion

            //Sửa sách
            $('#sachTable').on('click', '.edit-btn', function () {
                const book_id = $(this).data('book-id');
                window.location.href = 'sua_sach.php?id=' + book_id;
            });

            //Xóa sách
            $('#sachTable').on('click', '.delete-btn', function() {
                const book_id = $(this).data('book-id');
                if (confirm('Bạn có chắc muốn xóa quyển sách này không?')) {
                    $.ajax({
                        url: 'xoa_sach.php',
                        type: 'POST',
                        data: {id: book_id},
                        dataType: 'json',
                        success: function (res) {
                            if (res.success) {
                                alert('Xóa sách thành công!');
                                location.reload();
                            } else {
                                alert("Xóa không thành công: " + res.error);
                            }
                        },
                        error: function () {
                            alert('Lỗi AJAX. Không thể gửi yêu cầu xóa.');
                        }
                    });
                }
            });
        </script>
    </body>
</html>
