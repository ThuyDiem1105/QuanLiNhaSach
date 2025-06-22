<?php
session_start();
if (!isset($_SESSION['loggedin'])){     
    header('Location: ../../loginFunction/login.php'); 
}

include __DIR__ . '/../../connect.php';
// Đọc danh mục sách
$danhMucArr = [];
$result = $mysqli->query("SELECT MaKH, HoTen, SDT, DiaChi, Email, Loai, SoTienNo FROM khachhang");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Lưu toàn bộ thông tin của khách hàng theo mã khách
        $danhMucArr[$row['MaKH']] = [
            'HoTen' => $row['HoTen'],
            'SDT' => $row['SDT'],
            'DiaChi' => $row['DiaChi'],
            'Email' => $row['Email'],
            'Loai' => $row['Loai'],
            'SoTienNo' => $row['SoTienNo']
        ];
    }
}
$result->free();


$result = $mysqli->query("SELECT * FROM khachhang ORDER BY MaKH");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý khách hàng</title>
    <link rel="stylesheet" href="../../assets/general-style.css" />
    <script src="customers-script.js" defer></script>
    <style>
        .sort-dropdown-menu {
            display: none;
            position: absolute;
            left: 0;
            top: 110%;
            min-width: 150px;
            background: #fff;
            border: 1px solid #d0d0d0;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            z-index: 10;
            overflow: hidden;
        }

        #sortPriceBtn {
            min-width: 150px;   /* hoặc lớn hơn nếu bạn muốn */
            display: flex;
            align-items: center;
            justify-content: space-between; /* label trái, arrow phải */
            padding-right: 16px;
            padding-left: 16px;
        }

        #sortPriceBtn .label {
            flex: 1;
            text-align: left;
        }

        #sortPriceBtn .arrow {
            margin-left: 8px;
            font-size: 14px;
            flex-shrink: 0;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <h2 class="title">
            <img src="../../assets/sheet.png" class="title-icon">
            Quản lý khách hàng
        </h2>
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
                    <img src="../../assets/plus.png" class="icon-add" alt="Add Icon" /> 
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
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr 
                        data-address="<?php echo htmlspecialchars($row['DiaChi'], ENT_QUOTES); ?>" 
                        data-email="<?php echo htmlspecialchars($row['Email'], ENT_QUOTES); ?>"
                    >
                        <td class="stt"></td>
                        <td><?php echo htmlspecialchars($row['MaKH']); ?></td>
                        <td><?php echo htmlspecialchars($row['HoTen']); ?></td>
                        <td><?php echo htmlspecialchars($row['SDT']); ?></td>
                        <td><?php echo htmlspecialchars($row['Loai']); ?></td>
                        <td><?php echo htmlspecialchars($row['SoTienNo']); ?></td>
                        <td class="action-buttons">
                            <button class="view-btn" onclick="viewCustomer(
                                '<?= $row['MaKH']; ?>',
                                '<?= $row['HoTen']; ?>',
                                '<?= $row['SDT']; ?>',
                                '<?php echo htmlspecialchars($row['DiaChi'], ENT_QUOTES); ?>',
                                '<?php echo htmlspecialchars($row['Email'], ENT_QUOTES); ?>',
                                '<?= $row['Loai']; ?>',
                                '<?= $row['SoTienNo']; ?>'
                            )">Xem</button>
                            <button class="delete-btn" onclick="deleteRow(this)">Xóa</button>
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