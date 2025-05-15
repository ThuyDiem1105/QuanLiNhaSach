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
    $result = $con->query('SELECT * FROM phieunhapsach');
    $bookTicketArr = [];
    while ($row = $result->fetch_assoc()) {
        $bookTicketArr[] = $row;
    }

    echo json_encode(["data" => $bookTicketArr]);   

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
        <h2>Danh sách phiếu nhập sách</h2>
        <table id="phieunhapsachTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Mã phiếu</th>
                    <th>Mã sách</th>
                    <th>Ngày lập phiếu</th>
                    <th>Ngày nhập</th>
                    <th>Nguồn nhập</th>
                    <th>Số lượng nhập</th>
                    <th>Đơn giá nhập</th>
                    <th>Thành tiền</th>
                    <th></th>
                </tr>
            </thead>
        </table>

        <script>
            $(document).ready(function () {
                $('#phieunhapsachTable').DataTable({
                    "ajax": "tracuu_phieunhap.php?load=true",
                    "columns": [
                        { "data": "MaPhieu" },
                        { "data": "MaSach" },
                        { "data": "NgayLapPhieu" },
                        { "data": "NgayNhap" },
                        { "data": "NguonNhap" },
                        { "data": "SoLuong" },
                        { "data": "DonGiaNhap" },
                        { "data": "ThanhTien" },
                        {
                            "data": null,
                            "orderable": false,
                            "render": function (data, type, row) {
                                return `
                                <button class="delete-btn" data-id="${row.MaPhieu}" title="Xóa">🗑️</button>
                                `;
                            }
                        }
                    ],
                    "scrollY": "400px",
                    "scrollCollapse": true,
                    "paging": false,         
                    "info": false,    
                });

                $('#phieunhapsachTable').on('click', '.delete-btn', function() {
                    const id = $(this).data('id');
                    if (confirm('Bạn có chắc muốn xóa phiếu nhập này không?')) {
                        $.ajax({
                            url: 'xoa_phieunhap.php',
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
