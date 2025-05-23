<?php
/* session_start(); if (isset($_POST['account_loggedin'])){     header('Location: ../loginFunction/mainPage.php'); } */
include __DIR__ . '/../../database_connect.php';
$result = $mysqli->query("SELECT * FROM khachhang");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý khách hàng</title>
    <link rel="stylesheet" href="../../assets/customers-style.css" />
    <script src="../customers-script.js" defer></script>
</head>
<body>
    <div class="main-content">
        <div class="toolbar">
            <div class="toolbar-row">
                <div class="search-filter-group">
                    <div class="search-box">
                        <input type="text" placeholder="Tìm kiếm khách hàng..." class="search-input" />
                        <button class="search-button">🔍</button>
                    </div>
                    <select class="filter-select">
                        <option value="all">Tất cả</option>
                        <option value="Thường">Khách thường</option>
                        <option value="VIP">Khách VIP</option>
                    </select>
                </div>
                <button class="add-button" onclick="createNewCustomer()">+ Thêm khách hàng</button>
            </div>
        </div>
        <!-- Bảng khách hàng -->
        <table class="customer-table">
            <thead>
                <tr>
                    <th class="id">Mã khách hàng</th>
                    <th>Họ tên</th>
                    <th>Số điện thoại</th>
                    <th>Loại</th>
                    <th class="debt">Số tiền nợ</th>
                    <th class="actions">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>KH001</td>
                    <td>Nguyễn Văn A</td>
                    <td>0901234567</td>
                    <td>Thường</td>
                    <td>150.000</td>
                    <td class="action-buttons">
                        <button class="view-btn" onclick="viewCustomer('KH001')">Xem</button>
                        <button class="delete-btn" onclick="deleteRow(this)">Xóa</button>
                    </td>
                </tr>
                <tr>
                    <td>KH002</td>
                    <td>Trần Thị B</td>
                    <td>0912345678</td>
                    <td>VIP</td>
                    <td>0</td>
                    <td class="action-buttons">
                        <button class="view-btn" onclick="viewCustomer('KH002')">Xem</button>
                        <button class="delete-btn" onclick="deleteRow(this)">Xóa</button>
                    </td>
                </tr>
                <!-- Các dòng khác -->
            </tbody>
        </table>
    </div>
    
    <div class="toast" id="toast"></div>
</body>
</html>