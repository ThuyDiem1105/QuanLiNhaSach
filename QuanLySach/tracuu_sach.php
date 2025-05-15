<?php
session_start();
if (!isset($_SESSION['account_loggedin'])) {
    header('Location: ../loginFunction/mainPage.php');
    exit;
}

if (isset($_GET['load'])) {
    // Return JSON only
    header('Content-Type: application/json; charset=utf-8');
    $con = mysqli_connect('localhost', 'root', '', 'phplogin');
    if (mysqli_connect_errno()) {
        die(json_encode(["error" => "Connection failed: " . mysqli_connect_error()]));
    }

    $con->set_charset("utf8mb4");
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

        <h3>Danh sách đầu sách của nhà sách</h2>
        <table id="dausachTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Mã đầu sách</th>
                    <th>Tên đầu sách</th>
                    <th></th>
                </tr>
            </thead>
        </table>

        <h3>Danh sách thể loại của nhà sách</h2>
        <table id="theloaiTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Mã thể loại</th>
                    <th>Tên thể loại</th>
                    <th>Mã đầu sách</th>
                    <th></th>
                </tr>
            </thead>
        </table>

        <script>
            $(document).ready(function () {
                $.ajax({
                    url: 'tracuu_sach.php?load=true',
                    dataType: 'json',
                    success: function (response) {
                        //Bảng đầu sách
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
                                        <button class="edit-btn" data-id="${row.MaDS}" title="Sửa">✏️</button>
                                        <button class="delete-btn" data-id="${row.MaDS}" title="Xóa">🗑️</button>
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
                                { data: "MaDS"},
                                {
                                    data: null,
                                    "orderable": false,
                                    "render": function (data, type, row) {
                                        return `
                                        <button class="edit-btn" data-id="${row.MaTL}" title="Sửa">✏️</button>
                                        <button class="delete-btn" data-id="${row.MaTL}" title="Xóa">🗑️</button>
                                        `;
                                    }
                                }
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
                                        <button class="edit-btn" data-id="${row.MaSach}" title="Sửa">✏️</button>
                                        <button class="delete-btn" data-id="${row.MaSach}" title="Xóa">🗑️</button>
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

            $('#sachTable').on('click', '.edit-btn', function () {
                const sach_id = $(this).data('id');
                window.location.href = 'sua_sach.php?id=' + sach_id;
            });

            $('#dausachTable').on('click', '.delete-btn', function () {
                const dausach_id = $(this).data('id');
                if (confirm('Bạn có chắc muốn xóa đầu sách này không? Nếu xóa đầu sách thì các thể loại và sách tương ứng của đầu sách cũng bị xóa theo.')) {
                    $.ajax({
                        url: 'xoa_dausach.php',
                        type: 'POST',
                        data: {iD: dausach_id},
                        dataType: 'json',
                        success: function (res) {
                            if (res.success) {
                                alert('Xóa đầu sách thành công!');
                                $('#dausachTable').DataTable().ajax.reload(null, false); 
                                $('#theloaiTable').DataTable().ajax.reload(null, false);
                                $('#sachTable').DataTable().ajax.reload(null, false);
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

            $('#theloaiTable').on('click', '.delete-btn', function () {
                const theloai_id = $(this).data('id');
                if (confirm('Bạn có chắc muốn xóa thể loại này không? Nếu xóa thì bạn cần thêm lại thể loại tương ứng cho những sách có thể loại này.')) {
                    $.ajax({
                        url: 'xoa_theloai.php',
                        type: 'POST',
                        data: {iD: theloai_id},
                        dataType: 'json',
                        success: function (res) {
                            if (res.success) {
                                alert('Xóa thể loại thành công!');
                                $('#theloaiTable').DataTable().ajax.reload(null, false); 
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

            $('#sachTable').on('click', '.delete-btn', function() {
                const id = $(this).data('id');
                if (confirm('Bạn có chắc muốn xóa quyển sách này không?')) {
                    $.ajax({
                        url: 'xoa_sach.php',
                        type: 'POST',
                        data: {id: id},
                        dataType: 'json',
                        success: function (res) {
                            if (res.success) {
                                alert('Xóa sách thành công!');
                                $('#sachTable').DataTable().ajax.reload(null, false); 
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
