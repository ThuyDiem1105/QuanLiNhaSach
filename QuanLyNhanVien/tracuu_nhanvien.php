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
    $result = $con->query('SELECT * FROM nhanvien');
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
        <title>TRA CỨU NHÂN VIÊN</title>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    </head>
    <body>
        <nav>
            <a href="../homePage.php">Về Trang chủ</a>
            <a href="quanly_nhanvien.php">Về Quản lý nhân viên</a>
        </nav>
        <h2>Danh sách nhân viên nhà sách</h2>
        <table id="nhanvienTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Mã nhân viên</th>
                    <th>Họ tên</th>
                    <th>Ngày sinh</th>
                    <th>Số điện thoại</th>
                    <th>Nơi ở</th>
                    <th>Chức vụ</th>
                    <th>Ca làm</th>
                    <th>Lương</th>
                    <th></th>
                </tr>
            </thead>
        </table>

        <script>
            $(document).ready(function () {
                $('#nhanvienTable').DataTable({
                    "ajax": "tracuu_nhanvien.php?load=true",
                    "columns": [
                        { "data": "MaNV" },
                        { "data": "HoTen" },
                        { "data": "NgaySinh" },
                        { "data": "SDT" },
                        { "data": "NoiO" },
                        { "data": "ChucVu" },
                        { "data": "CaLam" },
                        { "data": "Luong" },
                        {
                            "data": null,
                            "orderable": false,
                            "render": function (data, type, row) {
                                return `
                                <button class="edit-btn" data-id="${row.MaNV}" title="Sửa">✏️</button>
                                <button class="delete-btn" data-id="${row.MaNV}" title="Xóa">🗑️</button>
                                `;
                            }
                        }
                    ],
                    "scrollY": "400px",
                    "scrollCollapse": true,
                    "paging": false,         
                    "info": false,    
                });

                $('#nhanvienTable').on('click', '.edit-btn', function () {
                    const id = $(this).data('id');
                    window.location.href = 'sua_nhanvien.php?id=' + id;
                });

                $('#nhanvienTable').on('click', '.delete-btn', function() {
                    const id = $(this).data('id');
                    if (confirm('Bạn có chắc muốn xóa nhân viên này không? Xóa nhân viên sẽ xóa luôn tài khoản và lịch làm việc của nhân viên bị xóa.')) {
                        // $.post('xoa_nhanvien.php', {id: id}, function (response) {
                        //     if (response.success) {
                        //         $('#nhanvienTable').DataTable().ajax.reload(null, false);
                        //     }
                        // }, 'json');
                        $.ajax({
                            url: 'xoa_nhanvien.php',
                            type: 'POST',
                            data: {id: id},
                            dataType: 'json',
                            success: function (res) {
                                if (res.success) {
                                    alert('Xóa nhân viên thành công!');
                                    $('#nhanvienTable').DataTable().ajax.reload(null, false); 
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
