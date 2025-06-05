<?php
session_start();
include __DIR__ . '/../../connect.php';
// Đọc danh mục khách hàng từ cơ sở dữ liệu
$danhMucArr = [];
$result = $mysqli->query("SELECT MaKH, HoTen, SDT, DiaChi, Email, Loai, SoTienNo FROM khachhang ORDER BY MaKH ASC");
while ($row = $result->fetch_assoc()) {
    $danhMucArr[$row['MaKH']] = $row['HoTen'];
}
$result->free();

// Sửa ở đây: lấy lại danh sách khách hàng
$result = $mysqli->query("SELECT * FROM khachhang");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý khách hàng</title>
    <link rel="stylesheet" href="../../assets/general-style.css" />
    <link rel="stylesheet" href="../../assets/customers-style.css" />
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
                <button class="add-button" onclick="createNewCustomer()">
                    Thêm khách hàng
                </button>
            </div>
        </div>

        <div class="sort-pagination-bar">
            <div class="sort-bar">
                <div class="sort-title-group">
                    <span class="sort-icon">
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="none"><rect x="4" y="7" width="16" height="2" rx="1" fill="#393939"/><rect x="4" y="11" width="10" height="2" rx="1" fill="#393939"/><rect x="4" y="15" width="6" height="2" rx="1" fill="#393939"/></svg>
                    </span>
                    <span class="sort-label">Sắp xếp theo</span>
                </div>
                <div class="sort-tabs">
                    <button class="sort-btn active" data-sort="id">Mã KH</button>
                    <button class="sort-btn" data-sort="name">Tên KH</button>
                    <div class="sort-dropdown">
                        <button class="sort-btn sort-dropdown-toggle" id="sortPriceBtn">
                            <span class="label">Tiền nợ</span>
                            <span class="arrow">&#9660;</span>
                        </button>
                        <div class="sort-dropdown sort-dropdown-menu" id="sortPriceMenu">
                            <div class="sort-dropdown-item" data-sort="debt-asc">Nợ: Tăng dần</div>
                            <div class="sort-dropdown-item" data-sort="debt-desc">Nợ: Giảm dần</div>
                        </div>
                    </div>
                </div>
            </div>
            <span class="pagination">
                <button class="page-btn prev">&lt;</button>
                <span class="page-info">1/1</span>
                <button class="page-btn next">&gt;</button>
            </span>
        </div>
        <!-- Bảng khách hàng -->
        <table class="table">
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
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                <td><?= $row['MaKH'] ?></td>
                <td><?= $row['HoTen'] ?></td>
                <td><?= $row['SDT'] ?></td>
                <td><?= $row['Loai'] ?></td>
                <td><?= number_format($row['SoTienNo'], 0, ',', '.') ?> VNĐ</td>
                <td class="action-buttons">
                    <button class="view-btn" onclick="openForm(
                    '<?= $row['MaKH'] ?>',
                    '<?= $row['HoTen'] ?>',
                    '<?= $row['SDT'] ?>',
                    '<?= $row['DiaChi'] ?>',
                    '<?= $row['Email'] ?>',
                    '<?= $row['Loai'] ?>',
                    '<?= $row['SoTienNo'] ?>'
                    )">Xem</button>
                    <button class="delete-btn" onclick="deleteCustomer('<?= $row['MaKH'] ?>')">Xóa</button>
                </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
                </table>
    </div>
    <div id="customerFormOverlay" class="overlay">
        <div class="form-popup">
        <h3>Chi tiết khách hàng</h3>
        <form id="customerForm" onsubmit="return false;" action="" method="post" novalidate>
            <input type="hidden" id="form_mode" name="form_mode" value="new">

            <label>Mã khách hàng:</label><input type="text" name="ma_kh" required readonly>
            <span class="error" id="error_makh"></span>

            <label>Tên khách hàng:</label><input type="text" name="ten_kh" required readonly>
            <span class="error" id="error_tenkh"></span>

            <label>Số điện thoại:</label><input type="text" name="sdt" required readonly>
            <span class="error" id="error_sdt"></span>

            <label>Địa chỉ:</label><input type="text" name="diachi" required readonly>
            <span class="error" id="error_diachi"></span>

            <label>Email:</label><input type="email" name="email" required readonly>
            <span class="error" id="error_email"></span>

            <label>Loại khách hàng:</label>
            <select name="loai" required disabled>
                <option value="Thường">Khách thường</option>
                <option value="VIP">Khách VIP</option>
            </select>
            <span class="error" id="error_loai"></span>

            <label>Số tiền nợ:</label><input type="number" name="so_tien_no" required readonly>
            <span class="error" id="error_sotienno"></span>

            <div class="form-buttons">
            <button type="submit" class="btn-save" onclick="saveCustomer()" style="display: none;">Lưu</button>
            <button type="button" class="btn-edit" onclick="enableEditing()">Sửa</button>
            <button type="button" class="btn-cancel" onclick="closeForm()">Đóng</button>
            </div>
        </form>
        </div>
    </div>
    <div id="toast"></div>
    <script src="customer-script.js" defer></script>
</body>
</html>