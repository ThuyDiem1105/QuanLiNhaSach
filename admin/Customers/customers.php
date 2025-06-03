<?php
session_start();
include __DIR__ . '/../../connect.php';
// Đọc danh mục khách hàng từ cơ sở dữ liệu
$danhMucArr = [];
$result = $mysqli->query("SELECT MaKH, HoTen, SDT, DiaChi, Email, Loai, SoTienNo FROM khachhang ORDER BY MaKH ASC");
while ($row = $result->fetch_assoc()) {
    $row['SoTienNo'] = number_format($row['SoTienNo'], 0, ',', '.');
    $row['DiaChi'] = htmlspecialchars($row['DiaChi']);  
    $row['Email'] = htmlspecialchars($row['Email']);
    $row['SDT'] = htmlspecialchars($row['SDT']);
    $row['Loai'] = htmlspecialchars($row['Loai']);
    $row['HoTen'] = htmlspecialchars($row['HoTen']);
    $row['MaKH'] = htmlspecialchars($row['MaKH']);
    $danhMucArr[] = $row;

}
$result->free();

$result = $mysqli->query("SELECT * FROM sach");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý khách hàng</title>
    <link rel="stylesheet" href="../../assets/general-style.css" />
    <link rel="stylesheet" href="../../assets/customers-style.css" />
    <script src="customers-script.js" defer></script>
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
                    <th class="stt">STT</th>
                    <th class="id">Mã khách hàng</th>
                    <th>Họ tên</th>
                    <th>Số điện thoại</th>
                    <th>Loại</th>
                    <th class="debt">Số tiền nợ</th>
                    <th class="actions">Thao tác</th>
                </tr>
            </thead>
            <tbody>
<?php 
// Đảm bảo con trỏ dữ liệu ở đầu bảng
$result->data_seek(0);
$stt = 1;
while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $stt++ ?></td>
    <td><?= htmlspecialchars($row['MaKH'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['HoTen'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['SDT'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['DiaChi'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['Email'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['Loai'] ?? '') ?></td>
    <td><?= htmlspecialchars(number_format($row['SoTienNo'] ?? 0, 0, ',', '.')) ?> VNĐ</td>
    <td class="action-buttons">
        <button class="view-btn" onclick="openForm(
            '<?= htmlspecialchars($row['MaKH'] ?? '') ?>',
            '<?= htmlspecialchars($row['HoTen'] ?? '') ?>',
            '<?= htmlspecialchars($row['SDT'] ?? '') ?>',
            '<?= htmlspecialchars($row['DiaChi'] ?? '') ?>',
            '<?= htmlspecialchars($row['Email'] ?? '') ?>',
            '<?= htmlspecialchars($row['Loai'] ?? '') ?>',
            '<?= htmlspecialchars($row['SoTienNo'] ?? '') ?>'
        )">Xem</button>
        <button class="delete-btn" onclick="deleteCustomer('<?= htmlspecialchars($row['MaKH'] ?? '') ?>')">Xóa</button>
    </td>
</tr>
<?php endwhile; ?>
</tbody>
                </table>
    </div>
    <script src="customers-script.js" defer></script>
    <div class="toast" id="toast"></div>
</body>
</html>