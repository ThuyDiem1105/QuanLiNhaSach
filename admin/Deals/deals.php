<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý khuyến mãi</title>
    <link rel="stylesheet" href="../../assets/general-style.css" />
    <link rel="stylesheet" href="../../assets/deals-style.css" />
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
                    <img src="../assets/plus.png" class="icon-add" alt="Add Icon" /> 
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
                    <th>Thời gian áp dụng</th>
                    <th>Trạng thái</th>
                    <th class="actions">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="stt"></td>
                    <td>KM001</td>
                    <td>Giảm 10% toàn bộ sách</td>
                    <td>01/06/2025 - 15/06/2025</td>
                    <td>Đang áp dụng</td>
                    <td class="action-buttons">
                        <button class="view-btn" onclick="viewDeal('KM001')">Xem</button>
                        <button class="delete-btn" onclick="deleteDeal(this)">Xóa</button>
                    </td>
                </tr>
                <tr>
                    <td class="stt"></td>
                    <td>KM002</td>
                    <td>Mua 2 tặng 1</td>
                    <td>01/05/2025 - 10/05/2025</td>
                    <td>Hết hạn</td>
                    <td class="action-buttons">
                        <button class="view-btn" onclick="viewDeal('KM002')">Xem</button>
                        <button class="delete-btn" onclick="deleteDeal(this)">Xóa</button>
                    </td>
                </tr>
                <!-- Các dòng khác -->
            </tbody>
        </table>
    </div>
    <div class="toast" id="toast"></div>
</body>
</html>