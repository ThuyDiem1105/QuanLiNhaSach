<?php
session_start();
include __DIR__ . '/../../connect.php';

//nếu chưa đăng nhập thì cho quay về trang login
if (!isset($_SESSION['loggedin'])){     
    header('Location: ../../loginFunction/login.php'); 
}

//gắn quyền xem thử đây là admin hay employee
$role = $_SESSION['role'];

$result = $mysqli->query("SELECT * FROM quydinh ORDER BY NgayTao DESC LIMIT 1");
$latestRule = $result->fetch_assoc();
$slTonRule = $latestRule['TonMaxDeNhap'];

$result->free();
$result = $mysqli->query("SELECT MaSach, TenSach, SoLuongTon FROM sach WHERE SoLuongTon <= $slTonRule");
$bookList = [];
while ($book = $result->fetch_assoc()) {
  //$bookList[$book['MaSach']] = $book['TenSach'];
  $bookList[] = $book;
}
$result->free();

$result = $mysqli->query("SELECT * FROM phieunhap");

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý phiếu nhập</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    <link rel="stylesheet" href="../../assets/general-style.css" type="text/css">
    <link rel="stylesheet" href="../../assets/receipts-style.css" type="text/css">
</head>
<body>
    <div class="main-content">
        <h2 class="title">
            <img src="../../assets/sheet.png" class="title-icon">
            Quản lý phiếu nhập
        </h2>
        <div class="toolbar">
            <div class="toolbar-row">
                <div class="search-filter-group">
                    <div class="search-box">
                        <input type="text" id="timMaPN" name="mapn" placeholder="Tìm mã phiếu..." class="search-input">
                        <button class="search-button">🔍</button>
                    </div>
                    <div class="date-range-group">
                        <input type="date" id="date-from" class="date-from" placeholder="Từ ngày">
                        <span style="margin: 0 4px;">-</span>
                        <input type="date" id="date-to" class="date-to" placeholder="Đến ngày">
                    </div>
                </div>
                <!-- chỉ có Admin mới được thêm phiếu nhập -->
                <?php if($role === 'Admin'): ?>
                <button class="add-button" onclick="createNewReceipt()">
                    <img src="../../assets/plus.png" class="icon-add" alt="Add Icon" /> 
                    Thêm phiếu nhập
                </button>
                <?php endif; ?>
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
                    <button class="sort-btn active" data-sort="id">Mã phiếu</button>
                    <div class="sort-dropdown">
                        <button class="sort-btn sort-dropdown-toggle" id="sortDateBtn">
                            <span class="label">Ngày nhập</span>
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
        <!-- Bảng phiếu nhập -->
        <table id="receiptTable" class="table">
            <thead>
                <tr>
                    <th class="stt">STT</th>
                    <th class="id">Mã phiếu</th>
                    <th>Ngày lập phiếu</th>
                    <th>Ngày nhập</th>
                    <th>Tổng tiền</th>
                    <th class="action-buttons">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php $stt = 1; ?>
                <?php while ($receipts = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $stt++ ?></td>
                    <td class="id"><?= htmlspecialchars($receipts['MaPN']) ?></td>
                    <td>
                        <?php
                            $date = $receipts['NgayLapPhieu'];
                            $d = new DateTime($date);
                            echo $d->format('d-m-Y');
                        ?>
                    </td>
                    <td>
                        <?php
                            $date = $receipts['NgayNhap'];
                            $d = new DateTime($date);
                            echo $d->format('d-m-Y');
                        ?>
                    </td>
                    <td><?= htmlspecialchars($receipts['TongTien']) ?></td>
                    <td class="action-buttons">
                        <button class="view-btn" onclick="openReceiptForm('<?= $receipts['MaPN'] ?>')">Xem</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div class="toast" id="toast"></div>
    </div>

    <div id="receiptFormOverlay" class="overlay">
        <div class="form-popup">
            <h2>Chi tiết phiếu nhập</h2>
            <form id="receiptForm" name="receiptForm" onsubmit="return false;" action="" method="post" novalidate>
                <label>Mã phiếu:</label><input type="text" name="ma_pn" required readonly>

                <div id="ngay_lap" style="display: none;">
                    <label>Ngày lập phiếu:</label>
                    <input type="date" name="ngay_lap" readonly>
                </div>

                <label>Ngày nhập sách:</label><input type="date" name="ngay_nhap" required readonly>
                <span class="error" id="error_ngaynhap"></span>

                <label>Thông tin đầu sách được nhập:</label>
                <table id="booksReceiptTable" style="border-collapse: collapse; width: 100%; text-align: center;">
                    <thead>
                        <tr>
                            <th>Mã sách</th>
                            <th>Số lượng</th>
                            <th>Đơn giá nhập</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody id="book-rows">
                    </tbody>
                </table>
                <span class="error" id="error_sach"></span>
                <button type="button" id="btnAdd" onclick="addBookRow()" style="display:none;" title="Thêm đầu sách">
                    <img src="../../assets/plus.png" class="icon-add" alt="Add Icon" /> 
                </button>

                <label>Tổng tiền phiếu nhập:</label><input type="text" name="tong_tien" required readonly>
                <span class="error" id="error_tongtien"></span>

                <div class="form-buttons">
                    <button type="submit" class="btn-save" id="btnSave" onclick="saveReceipt()" style="display:none;">Lưu</button>
                    <button type="button" class="btn-cancel" onclick="closeForm()">Đóng</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let editingIndex = -1;
        const latestRule = <?= json_encode($latestRule) ?>;

        // Mở form thể hiện chi tiết thông tin phiếu nhập
        function openReceiptForm(maPN){
            fetch(`receipt_details.php?ma_pn=${maPN}`)
            .then(res => res.json())
            .then(data => {
                console.log("Fetched data:", data); 
                const form = document.forms["receiptForm"];
                form.ma_pn.value = maPN;
                form.ngay_lap.value = data.receipt.NgayLapPhieu;
                form.ngay_nhap.value = data.receipt.NgayNhap;
                form.tong_tien.value = data.receipt.TongTien;

                // Vì một phiếu nhập có thể có nhiều sách nên sẽ có một bảng liệt kê các đầu sách được nhập
                const tbody = document.querySelector("#booksReceiptTable tbody");
                tbody.innerHTML = "";
                data.receiptBooks.forEach(book => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${book.MaSach}</td>
                        <td>${book.SoLuong}</td>
                        <td>${book.DonGiaNhap}</td>
                        <td>${book.ThanhTien}</td>
                    `;
                    tbody.appendChild(row);
                });

                toggleReadonly(true);
                document.getElementById("ngay_lap").style.display = "block";
                document.getElementById("btnAdd").style.display = "none";
                document.getElementById("btnSave").style.display = "none";
                document.getElementById("receiptFormOverlay").classList.add("show");
            });
        }

        function toggleReadonly(state) {
            const form = document.forms["receiptForm"];
            form.ma_pn.readOnly = true;
            form.ngay_lap.readOnly = state;
            form.ngay_nhap.readOnly = state;
            form.tong_tien.readOnly = true;
        }

        // Kiểm tra các ô thông tin
        function validateInputs(form){
            let isValid = true;
            document.querySelectorAll(".error").forEach(el => el.textContent = "");
            const maPN = form.ma_pn.value;
            const ngayLap = new Date().toISOString().split('T')[0];
            const ngayNhap = form.ngay_nhap.value;
            const tongTien = form.tong_tien.value;

            if(!ngayNhap){
                document.getElementById("error_ngaynhap").textContent = "Vui lòng chọn ngày nhập sách";
                isValid = false;
            }
            if(!tongTien){
                document.getElementById("error_tongtien").textContent = "Lỗi tính toán!";
                isValid = false;
            }

            const rows = document.querySelectorAll("#book-rows tr");
            if (rows.length === 0){
                document.getElementById("error_sach").textContent = "Bạn phải thêm ít nhất một đầu sách vào phiếu nhập!";
                isValid = false;
            }
            rows.forEach((row, index) => {
                const maSachSelect = row.querySelector('[name="ma_sach[]"]');
                const selectedOption = maSachSelect.options[maSachSelect.selectedIndex];
                const tonKho = Number(selectedOption.dataset.ton)

                const soLuong = row.querySelector('[name="so_luong[]"]').value;
                const donGia = row.querySelector('[name="don_gia[]"]').value;
                const thanhTien = row.querySelector('[name="thanh_tien[]"]').value;

                if (!maSachSelect) {
                    document.getElementById("error_sach").textContent = `Vui lòng chọn mã sách cho dòng ${index + 1}`;
                    isValid = false;
                }
                if (!soLuong || soLuong == 0) {
                    document.getElementById("error_sach").textContent = `Vui lòng nhập số lượng cho dòng ${index + 1}`;
                    isValid = false;
                } else if (soLuong < latestRule.SLNhapMin) {
                    document.getElementById("error_sach").textContent = `Số lượng nhập tối thiểu cho dòng ${index + 1} phải là ${latestRule.SLNhapMin}.`;
                    isValid = false;
                } else if(soLuong > (latestRule.TonKhoMax - tonKho)){
                    document.getElementById("error_sach").textContent = `Số lượng nhập tối đa cho dòng ${index + 1} phải là ${latestRule.TonKhoMax - tonKho}.`;
                    isValid = false;
                }

                if (!donGia || donGia == 0) {
                    document.getElementById("error_sach").textContent = `Vui lòng nhập giá nhập hợp lệ cho dòng ${index + 1}`;
                    isValid = false;
                }
                if(!thanhTien){
                    document.getElementById("error_sach").textContent = "Lỗi tính toán!";
                    isValid = fasle;
                }
            });

            return isValid;
        }

        //Thêm đầu sách vào phiếu
        function addBookRow() {
            const tbody = document.getElementById('book-rows');
            const row = document.createElement('tr');

            row.innerHTML = `
                <td>
                    <select name="ma_sach[]" id="book-select" required>
                        <option value="">- Chọn mã sách-</option>
                        <?php foreach ($bookList as $book): ?>
                        <option value="<?= $book['MaSach'] ?>" 
                            data-ton="<?= $book['SoLuongTon'] ?>"><?= $book['MaSach'] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="error" id="error_masach"></span>
                </td>
                <td>
                    <input type="number" name="so_luong[]" min="1" required oninput="updateLineTotal(this)">
                    <span class="error" id="error_soluong"></span>
                </td>
                <td>
                    <input type="text" name="don_gia[]" min="0" required oninput="updateLineTotal(this)">
                    <span class="error" id="error_dongia"></span>
                </td>
                <td><input type="number" name="thanh_tien[]" readonly required"></td>
            `;

            tbody.appendChild(row);
            document.getElementById("btn")
        }

        // Hàm này sẽ tự động update input Thành tiền khi user thay đổi số lượng hoặc đơn giá nhập
        function updateLineTotal(input) {
            //tìm thuộc tính bị thay đổi
            const row = input.closest('tr');
            const quantity = parseInt(row.querySelector('[name="so_luong[]"]').value) || 0;
            const price = parseFloat(row.querySelector('[name="don_gia[]"]').value) || 0;
            const lineTotal = quantity * price;
            row.querySelector('[name="thanh_tien[]"]').value = lineTotal.toFixed(2);
            updateTotal();
        }

        // Hàm này tự động update Tổng tiền của cả phiếu nhập
        function updateTotal() {
            let total = 0;
            const thanhTien = document.querySelectorAll('[name="thanh_tien[]"]');
            thanhTien.forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            document.forms["receiptForm"].tong_tien.value = total.toFixed(2);
        }

        // Mở form thêm phiếu nhập
        function createNewReceipt() {
            const form = document.forms["receiptForm"];
            const table = document.getElementById("receiptTable");
            const nextId = "PN" + String(table.rows.length).padStart(3, '0');

            form.reset();
            form.ma_pn.value = nextId;
            document.getElementById("book-rows").innerHTML = "";
            toggleReadonly(false);

            document.getElementById("btnSave").style.display = "inline-block";
            document.getElementById("ngay_lap").style.display = "none";
            document.getElementById("btnAdd").style.display = "inline-block";

            document.getElementById("receiptFormOverlay").classList.add("show");
            editingIndex = -1;
        }

        // Button Lưu khi thêm phiếu mới
        function saveReceipt() {
            const form = document.forms["receiptForm"];
            const table = document.getElementById("receiptTable").getElementsByTagName("tbody")[0];

            if(!validateInputs(form)) return;

            const maPN = form.ma_pn.value;
            const ngayLap = new Date().toISOString().split('T')[0];
            const ngayNhap = form.ngay_nhap.value;
            const tongTien = parseInt(form.tong_tien.value);

            const maSach = Array.from(document.querySelectorAll('[name="ma_sach[]"]')).map(i => i.value);
            const soLuong = Array.from(document.querySelectorAll('[name="so_luong[]"]')).map(i => parseInt(i.value));
            const donGia = Array.from(document.querySelectorAll('[name="don_gia[]"]')).map(i => parseFloat(i.value));
            const thanhTien = Array.from(document.querySelectorAll('[name="thanh_tien[]"]')).map(i => parseFloat(i.value));

            const payload = {
                ma_pn: maPN,
                ngay_lap: ngayLap,
                ngay_nhap: ngayNhap,
                tong_tien: tongTien,
                books: maSach.map((maSach, i) => ({
                    ma_sach: maSach,
                    so_luong: soLuong[i],
                    don_gia: donGia[i],
                    thanh_tien: thanhTien[i]
                }))
            };

            console.log("Sending payload to server:", payload);

            fetch(`save_receipt.php?tile_ban=${latestRule.TiLeBan}`, {
                method: 'POST',
                headers: {
                'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.text())
            .then(response => {
                console.log("Server response:", response);

                if (response.trim() === "OK") {
                    showToast("Lưu thành công!");
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else if(response.trim() === "receipt_exists") {
                    alert("Đã tồn tại phiếu nhập. Vui lòng kiểm tra lại!");
                } else {
                    alert("Lỗi: " + response);
                }
            })
            .catch(err => {
                console.error("Error:", err);
                alert("Có lỗi xảy ra khi gửi dữ liệu.");
            });
            closeForm();
        }

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
        let currentSort = "id";

        function getAllRows() {
            return Array.from(document.querySelectorAll(".table tbody tr"));
        }

        //region Tìm kiếm 
        document.getElementById("timMaPN").addEventListener("input", renderReceiptTable);
        document.getElementById("date-from").addEventListener("change", renderReceiptTable);
        document.getElementById("date-to").addEventListener("change", renderReceiptTable);

        function renderReceiptTable() {
            const mapnFilter = document.getElementById("timMaPN").value.toLowerCase();
            const dateFromInput = document.querySelector("#date-from");
            const dateToInput = document.querySelector("#date-to");
            const dateFrom = dateFromInput?.value;
            const dateTo = dateToInput?.value;

            let rows = getAllRows();

            rows.forEach(row => {
                const mapn = row.cells[1].textContent.toLowerCase();
                const ngaynhapText = row.cells[3].textContent.trim();
                const ngayNhapISO = toISODate(ngaynhapText);

                const matchMapn = mapn.includes(mapnFilter);
                let matchesDate = true;
                if (dateFrom || dateTo) {
                    if(dateFrom) {
                        matchesDate = matchesDate && (ngayNhapISO >= dateFrom);
                    }
                    if(dateTo) {
                        matchesDate = matchesDate && (ngayNhapISO <= dateTo);
                    }
                }

                if (matchMapn && matchesDate) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });

            // Sắp xếp
            let colIdx = 1;
            let compareFn = null;
            if (currentSort === "id") colIdx = 1;
            if (currentSort === "date-asc" || currentSort === "date-desc") colIdx = 3;
            
            if (currentSort === "id") {
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
                const tbody = document.querySelector("#receiptTable tbody");
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

            // Cập nhật phân trang
            document.querySelector(".page-info").textContent = `${currentPage}/${totalPages}`;
            document.querySelector(".page-btn.prev").disabled = currentPage === 1;
            document.querySelector(".page-btn.next").disabled = currentPage === totalPages;

            // Đánh lại số thứ tự STT cho các dòng đang hiển thị
            visibleRows.slice(start, end).forEach((row, idx) => {
                row.children[0].textContent = (start + idx + 1);
            });
            
            fixTableBorders('receiptTable');
        }
        //endregion

        document.addEventListener("DOMContentLoaded", () => {
            document.getElementById("timMaPN").addEventListener("input", () => {
                currentPage = 1;
                renderReceiptTable();
            });
            
            document.querySelector("#date-from").addEventListener("change", () => {
                currentPage = 1;
                renderReceiptTable();
            });
            document.querySelector("#date-to").addEventListener("change", () => {
                currentPage = 1;
                renderReceiptTable();
            });

            // Xử lý dropdown menu cho ngày nhập sách
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
                    document.querySelector('#sortDateBtn .label').textContent = 'Ngày nhập';

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
                    renderReceiptTable();
                }
            });

            document.querySelector(".page-btn.next").addEventListener("click", () => {
                currentPage++;
                renderReceiptTable();
            });

            renderReceiptTable();
        });

        // Hàm đổi kiểu sắp xếp
        function handleSortChange(sortType) {
            currentSort = sortType;
            currentPage = 1;
            renderReceiptTable();
        }

        function closeForm() {
            document.getElementById("receiptFormOverlay").classList.remove("show");
        }

        document.addEventListener("keydown", e => {
            if (e.key === "Escape") closeForm();
        });

        document.getElementById("receiptFormOverlay").addEventListener("click", e => {
            if (e.target === e.currentTarget) closeForm();
        });

        function showToast(message) {
            const toast = document.getElementById("toast");
            toast.textContent = message;
            toast.classList.add("show");

            setTimeout(() => {
                toast.classList.remove("show");
            }, 3000);
        }

        function toISODate(dmy) {
            if (!dmy) return "";
            let [day, month, year] = dmy.split('-');
            if (!day || !month || !year) return "";
            // Đảm bảo luôn có 2 chữ số
            if (day.length === 1) day = '0' + day;
            if (month.length === 1) month = '0' + month;
            return `${year}-${month}-${day}`;
        }

    </script>
</body>
</html>
