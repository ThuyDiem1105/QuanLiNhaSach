<?php
session_start();
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

include __DIR__ . '/../../connect.php';

// Handle AJAX request for finding a customer
if (isset($_GET['action']) && $_GET['action'] == 'find_customer') {
    header('Content-Type: application/json');
    $response = ['error' => 'Không tìm thấy khách hàng.'];
    if (isset($_GET['ma_kh'])) {
        $ma_kh = $_GET['ma_kh'];
        $stmt = $mysqli->prepare("SELECT HoTen, SDT, SoTienNo FROM khachhang WHERE MaKH = ?");
        if ($stmt) {
            $stmt->bind_param("s", $ma_kh);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($customer = $result->fetch_assoc()) {
                $response = $customer;
            }
            $stmt->close();
        } else {
            $response['error'] = 'Lỗi truy vấn: ' . $mysqli->error;
        }
    }
    echo json_encode($response);
    exit;
}

if (!isset($_SESSION['loggedin'])) {
    header('Location: ../../loginFunction/login.php');
    exit;
}

// Fetch all receipt collections
$collections_result = $mysqli->query(
    "SELECT pt.MaPT, pt.MaKH, kh.HoTen, pt.NgayThu, pt.SoTienThu 
     FROM phieuthutien pt
     JOIN khachhang kh ON pt.MaKH = kh.MaKH
     ORDER BY pt.NgayThu DESC, pt.MaPT DESC"
);

if (!$collections_result) {
    die("Lỗi truy vấn: " . $mysqli->error);
}

// Fetch all customers for the dropdown
$customers_result = $mysqli->query("SELECT MaKH, HoTen, SoTienNo FROM khachhang WHERE SoTienNo > 0 ORDER BY HoTen");
if (!$customers_result) {
    die("Lỗi truy vấn khách hàng: " . $mysqli->error);
}

$customers = [];
while ($customer = $customers_result->fetch_assoc()) {
    $customers[] = $customer;
}
$customerDebtsJson = json_encode(array_column($customers, 'SoTienNo', 'MaKH'));
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Phiếu Thu Tiền</title>
    <link rel="stylesheet" href="../../assets/general-style.css" type="text/css">
    <link rel="stylesheet" href="../../assets/receipt_collection-style.css" type="text/css">
</head>
<body>
    <div class="main-content">
        <h2 class="title">
            <img src="../../assets/sheet.png" class="title-icon" alt="Receipt Collection Icon">
            Quản lý Phiếu thu tiền
        </h2>
        <div class="toolbar">
            <div class="toolbar-row">
                <div class="search-filter-group">
                    <div class="search-box">
                        <input type="text" id="timMaPT" name="mapn" placeholder="Tìm mã phiếu..." class="search-input">
                        <button class="search-button">🔍</button>
                    </div>
                    <div class="date-range-group">
                        <input type="date" id="date-from" class="date-from" placeholder="Từ ngày">
                        <span style="margin: 0 4px;">-</span>
                        <input type="date" id="date-to" class="date-to" placeholder="Đến ngày">
                    </div>
                </div>
                <button class="add-button" onclick="openAddForm()">
                    <img src="../../assets/plus.png" class="icon-add" alt="Add Icon" /> 
                    Thêm Phiếu thu
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
                    <button class="sort-btn active" data-sort="id_pt">Mã phiếu</button>
                    <button class="sort-btn" data-sort="id_kh">Mã KH</button>
                    <div class="sort-dropdown">
                        <button class="sort-btn sort-dropdown-toggle" id="sortDateBtn">
                            <span class="label">Ngày thu</span>
                            <span class="arrow">&#9660;</span>
                        </button>
                        <div class="sort-dropdown-menu" id="sortDateMenu">
                            <div class="sort-dropdown-item" data-sort="date-desc">Mới nhất</div>
                            <div class="sort-dropdown-item" data-sort="date-asc">Cũ nhất</div>    
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
        <!-- Bảng phiếu thu -->
        <div class="table-wrapper">
            <table id="collectionTable" class="table">
                <thead>
                    <tr>
                        <th class="stt">STT</th>
                        <th class="id">Mã phiếu</th>
                        <th class="id">Mã KH</th>
                        <th>Họ tên</th>
                        <th>Ngày thu</th>
                        <th>Số tiền thu</th>
                        <th class="action-buttons">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $stt = 1; ?>
                    <?php if ($collections_result->num_rows > 0): ?>
                        <?php while ($row = $collections_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $stt++ ?></td>
                            <td class="id"><?= htmlspecialchars($row['MaPT']) ?></td>
                            <td class="id"><?= htmlspecialchars($row['MaKH']) ?></td>
                            <td><?= htmlspecialchars($row['HoTen']) ?></td>
                            <td><?= htmlspecialchars(date('d-m-Y', strtotime($row['NgayThu']))) ?></td>
                            <td><?= htmlspecialchars(number_format($row['SoTienThu'], 0, ',', '.')) ?> VNĐ</td>
                            <td class="action-buttons">
                                <button class="view-btn" onclick="openViewForm('<?= $row['MaPT'] ?>')">Xem</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">Chưa có phiếu thu nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="toast" id="toast"></div>
    </div>

    <div id="collectionFormOverlay" class="overlay">
        <div class="form-popup">
            <h2 id="formTitle"></h2>
            <form id="collectionForm" onsubmit="return saveCollection(event);" novalidate>
                <div class="form-group" id="ma_pt_group">
                    <label>Mã phiếu thu:</label>
                    <input type="text" name="ma_pt" readonly>
                </div>
                <div class="form-group">
                    <label>Mã khách hàng:</label>
                    <input type="text" name="ma_kh" required oninput="findCustomerDebounce(this)">
                    <span class="error" id="error_ma_kh"></span>
                </div>
                <div class="form-group">
                    <label>Họ tên:</label>
                    <input type="text" name="ho_ten" readonly>
                </div>
                <div class="form-group">
                    <label>Số điện thoại:</label>
                    <input type="text" name="sdt" readonly>
                </div>
                <div class="form-group">
                    <label>Số tiền nợ:</label>
                    <input type="text" name="so_tien_no" readonly style="font-weight: bold; color: #c0392b;">
                </div>
                <div class="form-group">
                    <label>Ngày thu:</label>
                    <input type="date" name="ngay_thu" required>
                </div>
                <div class="form-group">
                    <label>Số tiền thu:</label>
                    <input type="number" name="so_tien_thu" min="1" required>
                    <span class="error" id="error_so_tien_thu"></span>
                </div>
                <div class="form-buttons">
                    <button type="submit" id="btn-save" class="btn-save">Lưu</button>
                    <button type="button" class="btn-cancel" onclick="closeForm()">Đóng</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function fixTableBorders() {
            const rows = Array.from(document.querySelectorAll('.table tbody tr'))
                .filter(row => row.style.display !== "none");
            // Đặt lại border-bottom cho tất cả các dòng hiển thị
            rows.forEach(row => row.querySelectorAll('td').forEach(td => td.style.borderBottom = "1px solid #0d3c6b"));
            // Bỏ border-bottom cho dòng cuối cùng hiển thị
            if (rows.length > 0) {
                rows[rows.length - 1].querySelectorAll('td').forEach(td => td.style.borderBottom = "none");
                // Hiện border-bottom cho th
                document.querySelectorAll('.table th').forEach(th => th.style.borderBottom = "1px solid #0d3c6b");
            }
            else {
                // Không có dòng nào hiển thị, ẩn border-bottom của th
                document.querySelectorAll('.table th').forEach(th => th.style.borderBottom = "none");
            }
        }

        const PAGE_SIZE = 50;
        let currentPage = 1;
        let currentSort = "id_pt";

        function getAllRows() {
            return Array.from(document.querySelectorAll(".table tbody tr"));
        }

        //region Tìm kiếm 
        function renderCollectionTable() {
            const searchInput = document.getElementById("timMaPT");
            const dateFromInput = document.querySelector("#date-from");
            const dateToInput = document.querySelector("#date-to");
            const dateFrom = dateFromInput?.value;
            const dateTo = dateToInput?.value;

            let rows = getAllRows();

            rows.forEach(row => {
                const maPT = row.cells[1].textContent.toLowerCase();
                const maKH = row.cells[2].textContent.toLowerCase();
                const hoTen = row.cells[3].textContent.toLowerCase();
                const ngayThuText = row.cells[4].textContent.trim();
                const ngayThuISO = toISODate(ngayThuText);

                const matchMaPT = maPT.includes(searchInput.value) || maKH.includes(searchInput.value) || hoTen.includes(searchInput.value);
                let matchesDate = true;
                if (dateFrom || dateTo) {
                    if(dateFrom) {
                        matchesDate = matchesDate && (ngayThuISO >= dateFrom);
                    }
                    if(dateTo) {
                        matchesDate = matchesDate && (ngayThuISO <= dateTo);
                    }
                }

                if (matchMaPT && matchesDate) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });

            // Sắp xếp
            let colIdx = 1;
            let compareFn = null;
            if (currentSort === "id_pt") colIdx = 1;
            if (currentSort === "id_kh") colIdx = 2;
            if (currentSort === "date-asc" || currentSort === "date-desc") colIdx = 4;

            if (currentSort === "id_pt" || currentSort === "id_kh") {
                compareFn = (a, b) => {
                    const valA = a.cells[colIdx].textContent.trim().toLowerCase();
                    const valB = b.cells[colIdx].textContent.trim().toLowerCase();
                    return valA.localeCompare(valB, 'vi');
                };
            }

            if (currentSort === "date-asc" || currentSort === "date-desc") {
                compareFn = (a, b) => {
                    const parseDMY = (str) => {
                        const [day, month, year] = str.split('-');
                        return new Date(`${year}-${month}-${day}`);
                    };
                    const valA = parseDMY(a.cells[colIdx].textContent.trim());
                    const valB = parseDMY(b.cells[colIdx].textContent.trim());
                    return currentSort === "date-asc" ? valA - valB : valB - valA;
                };
            }

            if (compareFn) {
                rows.sort(compareFn);
                const tbody = document.querySelector("#collectionTable tbody");
                rows.forEach(row => tbody.appendChild(row));
            }

            let visibleRows = rows.filter(row => row.style.display !== "none");

            // Phân trang
            const totalRows = visibleRows.length;
            const totalPages = Math.max(1, Math.ceil(totalRows / PAGE_SIZE));
            if (currentPage > totalPages) currentPage = totalPages;
            const start = (currentPage - 1) * PAGE_SIZE;
            const end = start + PAGE_SIZE;

            // Ẩn tất cả dòng
            rows.forEach(row => row.style.display = "none");

            // Hiện dòng thuộc trang hiện tại
            visibleRows.slice(start, end).forEach(row => row.style.display = "");

            // Cập nhật số trang
            document.querySelector(".page-info").textContent = `${currentPage}/${totalPages}`;
            document.querySelector(".page-btn.prev").disabled = currentPage === 1;
            document.querySelector(".page-btn.next").disabled = currentPage === totalPages;

            // Đánh lại số thứ tự STT cho các dòng đang hiển thị
            visibleRows.slice(start, end).forEach((row, idx) => {
                row.children[0].textContent = (start + idx + 1);
            });

            fixTableBorders();
        }

        document.addEventListener("DOMContentLoaded", () => {
            // Thêm event listeners cho tìm kiếm
            document.getElementById("timMaPT").addEventListener("input", () => {
                currentPage = 1;
                renderCollectionTable();
            });
            
            document.querySelector("#date-from").addEventListener("change", () => {
                currentPage = 1;
                renderCollectionTable();
            });
            document.querySelector("#date-to").addEventListener("change", () => {
                currentPage = 1;
                renderCollectionTable();
            });

            // Xử lý dropdown menu cho ngày thu
            const sortDateBtn = document.getElementById('sortDateBtn');
            const sortDateMenu = document.getElementById('sortDateMenu');
            sortDateBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                sortDateMenu.style.display = sortDateMenu.style.display === 'block' ? 'none' : 'block';
            });

            // Xử lý khi click vào các nút sắp xếp thông thường
            document.querySelectorAll('.sort-btn[data-sort]').forEach(btn => {
                btn.addEventListener('click', function() {
                    // Xóa active class từ tất cả các nút sắp xếp
                    document.querySelectorAll('.sort-btn').forEach(b => b.classList.remove('active'));
                    // Thêm active class cho nút được click
                    this.classList.add('active');
                    // Reset text của các nút dropdown về mặc định
                    document.querySelector('#sortDateBtn .label').textContent = 'Ngày thu';

                    handleSortChange(this.getAttribute('data-sort'));
                });
            });

            // Xử lý khi chọn tùy chọn sắp xếp
            document.querySelectorAll('.sort-dropdown-item').forEach(item => {
                item.addEventListener('click', function() {
                    const sortType = this.getAttribute('data-sort');
                    const parentMenu = this.closest('.sort-dropdown-menu');
                    const parentBtn = parentMenu.previousElementSibling;
                    const labelSpan = parentBtn.querySelector('.label');
                    
                    // Xóa active class từ tất cả các nút sắp xếp
                    document.querySelectorAll('.sort-btn').forEach(btn => btn.classList.remove('active'));
                    
                    // Thêm active class cho nút được chọn
                    parentBtn.classList.add('active');
                    
                    // Cập nhật text trên nút
                    labelSpan.textContent = this.textContent;
                    sortDateMenu.style.display = 'none';

                    handleSortChange(sortType);
                });
            });

            // Đóng dropdown khi click ra ngoài
            document.addEventListener('click', () => {
                sortDateMenu.style.display = 'none';
            });

            // Ngăn chặn sự kiện click trong menu   
            sortDateMenu.addEventListener('click', (e) => e.stopPropagation());

            // Phân trang
            document.querySelector(".page-btn.prev").addEventListener("click", () => {
                if (currentPage > 1) {
                    currentPage--;
                    renderCollectionTable();
                }
            });
            
            document.querySelector(".page-btn.next").addEventListener("click", () => {
                currentPage++;
                renderCollectionTable();
            });

            // Thêm event listener cho overlay form
            document.getElementById("collectionFormOverlay").addEventListener("click", e => {
                if (e.target === e.currentTarget) closeForm();
            });

            renderCollectionTable();
        });

        //region Hàm đổi kiểu sắp xếp
        function handleSortChange(sortType) {
            currentSort = sortType;
            currentPage = 1;
            renderCollectionTable();
        }
        //endregion

        function toISODate(dmy) {
            if (!dmy) return "";
            let [day, month, year] = dmy.split('-');
            if (!day || !month || !year) return "";
            // Đảm bảo luôn có 2 chữ số
            if (day.length === 1) day = '0' + day;
            if (month.length === 1) month = '0' + month;
            return `${year}-${month}-${day}`;
        }

        let findCustomerTimeout;

        function setFormMode(mode) {
            const form = document.forms['collectionForm'];
            const title = document.getElementById('formTitle');
            
            document.getElementById('ma_pt_group').style.display = (mode === 'view') ? 'block' : 'none';
            document.getElementById('btn-save').style.display = (mode === 'add') ? 'block' : 'none';
            
            // Reset all fields and errors
            form.reset();
            document.querySelectorAll('.error').forEach(el => el.textContent = '');

            if (mode === 'view') {
                title.innerText = 'Chi Tiết Phiếu Thu';
                Object.values(form.elements).forEach(el => {
                    if (el.type !== 'button') el.setAttribute('readonly', true);
                });
            } else { // add mode
                title.innerText = 'Tạo Phiếu Thu Mới';
                Object.values(form.elements).forEach(el => {
                    if (el.type !== 'button') el.removeAttribute('readonly');
                });
                // These fields are always readonly as they are auto-filled
                form.ho_ten.setAttribute('readonly', true);
                form.sdt.setAttribute('readonly', true);
                form.so_tien_no.setAttribute('readonly', true);
                form.ngay_thu.value = new Date().toISOString().slice(0, 10);
            }
        }
        
        function openViewForm(maPT) {
            setFormMode('view');
            fetch(`receipt_collection_details.php?ma_pt=${maPT}`)
                .then(res => res.json())
                .then(data => {
                    if (data.error) {
                        showToast(data.error, true);
                        return;
                    }
                    const form = document.forms["collectionForm"];
                    form.ma_pt.value = data.MaPT;
                    form.ma_kh.value = data.MaKH;
                    form.ho_ten.value = data.HoTen;
                    form.sdt.value = data.SDT;
                    form.ngay_thu.value = data.NgayThu;
                    form.so_tien_no.value = new Intl.NumberFormat('vi-VN').format(data.SoTienNo) + ' VNĐ';
                    form.so_tien_thu.value = data.SoTienThu;
                    
                    document.getElementById('collectionFormOverlay').classList.add('show');
                    // Reset scroll position to top
                    document.querySelector(".form-popup").scrollTop = 0;
                }).catch(err => showToast('Lỗi tải dữ liệu.', true));
        }

        function openAddForm() {
            setFormMode('add');
            document.getElementById('collectionFormOverlay').classList.add('show');
            // Reset scroll position to top
            document.querySelector(".form-popup").scrollTop = 0;
        }

        function findCustomerDebounce(input) {
            clearTimeout(findCustomerTimeout);
            findCustomerTimeout = setTimeout(() => findCustomer(input.value), 500);
        }

        function findCustomer(maKH) {
            const form = document.forms['collectionForm'];
            if (!maKH) {
                form.ho_ten.value = '';
                form.sdt.value = '';
                form.so_tien_no.value = '';
                return;
            }

            fetch(`receipt_collection.php?action=find_customer&ma_kh=${maKH}`)
                .then(res => res.json())
                .then(data => {
                    if (data.error) {
                        form.ho_ten.value = 'Không tìm thấy';
                        form.sdt.value = '';
                        form.so_tien_no.value = '';
                        document.getElementById('error_ma_kh').textContent = data.error;
                    } else {
                        form.ho_ten.value = data.HoTen;
                        form.sdt.value = data.SDT;
                        form.so_tien_no.value = new Intl.NumberFormat('vi-VN').format(data.SoTienNo) + ' VNĐ';
                        form.so_tien_thu.max = data.SoTienNo;
                        document.getElementById('error_ma_kh').textContent = '';
                    }
                }).catch(err => showToast('Lỗi tìm khách hàng.', true));
        }

        function closeForm() {
            document.getElementById('collectionFormOverlay').classList.remove('show');
        }
        
        function validateForm() {
            const form = document.forms['collectionForm'];
            const maKH = form.ma_kh.value;
            const collectionAmount = parseInt(form.so_tien_thu.value, 10);
            const debtString = form.so_tien_no.value;
            const customerDebt = parseInt(debtString.replace(/[^0-9]/g, ''), 10);
            
            document.getElementById('error_ma_kh').textContent = '';
            document.getElementById('error_so_tien_thu').textContent = '';
            
            if (!maKH) {
                document.getElementById('error_ma_kh').textContent = 'Vui lòng nhập mã khách hàng.';
                return false;
            }
            if (!debtString || form.ho_ten.value === 'Không tìm thấy') {
                document.getElementById('error_ma_kh').textContent = 'Mã khách hàng không hợp lệ.';
                return false;
            }
            if (isNaN(collectionAmount) || collectionAmount <= 0) {
                document.getElementById('error_so_tien_thu').textContent = 'Vui lòng nhập số tiền thu hợp lệ.';
                return false;
            }
            if (collectionAmount > customerDebt) {
                document.getElementById('error_so_tien_thu').textContent = 'Số tiền thu không được vượt quá số nợ.';
                return false;
            }
            return true;
        }

        function saveCollection(event) {
            event.preventDefault();
            if (!validateForm()) return;

            const form = document.getElementById('collectionForm');
            const formData = new FormData(form);

            fetch('save_receipt_collection.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === 'OK') {
                    showToast('Lưu phiếu thu thành công!');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(data, true);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Có lỗi xảy ra, vui lòng thử lại.', true);
            });
        }

        function showToast(message, isError = false) {
            const toast = document.getElementById("toast");
            toast.textContent = message;
            toast.className = "toast show";
            if (isError) {
                toast.classList.add("error-toast");
            }
            setTimeout(() => { toast.className = toast.className.replace("show", ""); }, 3000);
        }
    </script>
</body>
</html> 