<?php
session_start();
include __DIR__ . '/../../connect.php';

$danhMucArr = [];
$result = $mysqli->query("SELECT MaKM, TenKM FROM khuyenmai");
while ($row = $result->fetch_assoc()) {
    $danhMucArr[$row['MaKM']] = $row['TenKM'];
}
$result->free();
$result = $mysqli->query("SELECT * FROM khuyenmai");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý khuyến mãi</title>
    <link rel="stylesheet" href="../../assets/general-style.css" />
    <style>
        .sort-dropdown-menu {
            display: none;
            position: absolute;
            left: 0;
            top: 110%;
            min-width: 200px;
            background: #fff;
            border: 1px solid #d0d0d0;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            z-index: 10;
            overflow: hidden;
        }

        #sortTimeBtn {
            min-width: 200px;   /* hoặc lớn hơn nếu bạn muốn */
            display: flex;
            align-items: center;
            justify-content: space-between; /* label trái, arrow phải */
            padding-right: 16px;
            padding-left: 16px;
        }

        #sortTimeBtn .label {
            flex: 1;
            text-align: left;
        }

        #sortTimeBtn .arrow {
            margin-left: 8px;
            font-size: 14px;
            flex-shrink: 0;
        }

        .date-range-group {
            display: flex;
            align-items: center;
            gap: 4px;
            margin-left: 12px;
        }

        .date-range-group input[type="date"] {
            padding: 4px 8px;
            border: 1px solid #bdbdbd;
            border-radius: 5px;
            font-size: 14px;
        }
    </style>
    <script src="deals-script.js" defer></script>
</head>
<body>
    <div class="main-content">
        <div class="toolbar">
            <div class="toolbar-row">
                <div class="search-filter-group">
                    <div class="search-box">
                        <input type="text" placeholder="Tìm kiếm khuyến mãi..." class="search-input" />
                        <button class="search-button">🔍</button>
                    </div>
                    <select class="filter-select">
                        <option value="all">Tất cả</option>
                        <option value="active">Đang áp dụng</option>
                        <option value="expired">Hết hạn</option>
                    </select>
                    <div class="date-range-group">
                        <input type="date" id="date-from" class="date-from" placeholder="Từ ngày">
                        <span style="margin: 0 4px;">-</span>
                        <input type="date" id="date-to" class="date-to" placeholder="Đến ngày">
                    </div>
                </div>
                <button class="add-button" onclick="createNewDeal()">
                    <img src="../../assets/plus.png" class="icon-add" alt="Add Icon" /> 
                    Thêm khuyến mãi
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
                    <button class="sort-btn active" data-sort="id">Mã KM</button>
                    <button class="sort-btn" data-sort="name">Tên KM</button>
                    <div class="sort-dropdown">
                        <button class="sort-btn time sort-dropdown-toggle" id="sortTimeBtn">
                            <span class="label">Thời gian</span>
                            <span class="arrow">&#9660;</span>
                        </button>
                        <div class="sort-dropdown time sort-dropdown-menu" id="sortTimeMenu">
                            <div class="sort-dropdown-item" data-sort="time-desc">Thời gian: Mới nhất</div>
                            <div class="sort-dropdown-item" data-sort="time-asc">Thời gian: Cũ nhất</div>
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
        <!-- Bảng khuyến mãi -->
        <table class="table">
            <thead>
                <tr>
                    <th class="stt">STT</th>
                    <th class="id">Mã khuyến mãi</th>
                    <th>Tên khuyến mãi</th>
                    <th>Ngày diễn ra</th>
                    <th>Trạng thái</th>
                    <th class="actions">Thao tác</th>
                </tr>
            </thead>
            
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <?php
                $ngayBatDau = date('d/m/Y', strtotime($row['NgayBatDau']));
                $ngayKetThuc = date('d/m/Y', strtotime($row['NgayKetThuc']));
            ?>
            <tr data-condition="<?= htmlspecialchars($row['DieuKienApDung']) ?>">
            <td class="stt"></td>
            <td><?= htmlspecialchars($row['MaKM']) ?></td>
            <td><?= htmlspecialchars($row['TenKM']) ?></td>
            <td><?= $ngayBatDau ?> - <?= $ngayKetThuc ?></td>
            <td class="action-buttons">
                <button class="view-btn" onclick="viewDeal('<?= $row['MaKM'] ?>')">Xem</button>
                <button class="delete-btn" onclick="deleteDeal('<?= $row['MaKM'] ?>')">Xóa</button>
            </td>
            </tr>
            <?php endwhile; ?>
            </tbody>
    </table>
    <div class="toast" id="toast"></div>
  </div>
</body>
</html>
