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
    $result = $con->query('SELECT * FROM taikhoan');
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
        <title>TRA CỨU TÀI KHOẢN</title>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    </head>
    <body>
        <nav>
            <a href="../adminHomePage.php">Về Trang chủ</a>
            <a href="quanly_nhanvien.php">Về Quản lý nhân viên</a>
        </nav>
        <h2>Danh sách tài khoản nhân viên</h2>
        <table id="taikhoanTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Mã nhân viên</th>
                    <th>Tên đăng nhập</th>
                    <th>Mật khẩu</th>
                    <th>Quyền</th>
                    <th>Email</th>
                    <th></th>
                </tr>
            </thead>
        </table>

        <script>
            $(document).ready(function () {
                $('#taikhoanTable').DataTable({
                    "ajax": "tracuu_taikhoan.php?load=true",
                    "columns": [
                        { "data": "MaNV" },
                        { "data": "TenDN"},
                        { "data": "RawMatKhau" },
                        { "data": "Quyen" },
                        { "data": "Email" },
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

                $('#taikhoanTable').on('click', '.edit-btn', function () {
                    const id = $(this).data('id');
                    window.location.href = 'sua_taikhoan.php?id=' + id;
                });

                $('#taikhoanTable').on('click', '.delete-btn', function() {
                    const id = $(this).data('id');
                    if (confirm('Bạn có chắc muốn xóa tài khoản này không? Sau khi xóa bạn cần phải thêm tài khoản nếu nhân viên còn làm việc, ' 
                        + 'ngược lại nếu nhân viên không còn làm thì chỉ cần xóa nhân viên, tài khoản tương ứng sẽ được xóa.')) {
                        $.ajax({
                            url: 'xoa_taikhoan.php',
                            type: 'POST',
                            data: {id: id},
                            dataType: 'json',
                            success: function (res) {
                                if (res.success) {
                                    alert('Xóa tài khoản thành công!');
                                    $('#taikhoanTable').DataTable().ajax.reload(null, false); 
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
