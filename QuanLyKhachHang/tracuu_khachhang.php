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
    $result = $con->query('SELECT * FROM khachhang');
    $dataArr = [];

    while ($row = $result->fetch_assoc()) {
        $dataArr[] = $row;
    }

    echo json_encode(["data" => $dataArr]);
    $con->close();
    exit;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>TRA CỨU KHÁCH HÀNG</title>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    </head>
    <body>
        <nav>
            <a href="../adminHomePage.php">Về Trang chủ</a>
            <a href="quanly_khachhang.php">Về Quản lý khách hàng</a>
        </nav>
        <h2>Danh sách khách hàng của nhà sách</h2>
        <table id="khachhangTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Mã khách hàng</th>
                    <th>Họ tên</th>
                    <th>Số điện thoại</th>
                    <th>Địa chỉ</th>
                    <th>Email</th>
                    <th>Số tiền nợ</th>
                    <th></th>
                </tr>
            </thead>
        </table>

        <script>
            $(document).ready(function () {
                $('#khachhangTable').DataTable({
                    "ajax": "tracuu_khachhang.php?load=true",
                    "columns": [
                        { "data": "MaKH" },
                        { "data": "HoTen" },
                        { "data": "SDT" },
                        { "data": "DiaChi" },
                        { "data": "Email" },
                        { "data": "SoTienNo" },
                        {
                            "data": null,
                            "orderable": false,
                            "render": function (data, type, row) {
                                return `
                                <button class="edit-btn" data-id="${row.MaKH}" title="Sửa">✏️</button>
                                <button class="delete-btn" data-id="${row.MaKH}" title="Xóa">🗑️</button>
                                `;
                            }
                        }
                    ],
                    "scrollY": "400px",
                    "scrollCollapse": true,
                    "paging": false,         
                    "info": false,    
                });

                $('#khachhangTable').on('click', '.edit-btn', function () {
                    const id = $(this).data('id');
                    window.location.href = 'sua_khachhang.php?id=' + id;
                });

                $('#khachhangTable').on('click', '.delete-btn', function() {
                    const id = $(this).data('id');
                    if (confirm('Bạn có chắc muốn xóa khách hàng này không?')) {
                        $.ajax({
                            url: 'xoa_khachhang.php',
                            type: 'POST',
                            data: {id: id},
                            dataType: 'json',
                            success: function (res) {
                                if (res.success) {
                                    alert('Xóa khách hàng thành công!');
                                    $('#khachhangTable').DataTable().ajax.reload(null, false); 
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
            });
        </script>
    </body>
</html>
