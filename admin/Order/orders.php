<?php
session_start();
include __DIR__ . '/../../connect.php';

if (!isset($_SESSION['loggedin'])){     
    header('Location: ../../loginFunction/login.php'); 
}

$result = $mysqli->query("SELECT * FROM quydinh ORDER BY NgayTao DESC LIMIT 1");
$latestRule = $result->fetch_assoc();

$result->free();
$result = $mysqli->query("SELECT MaSach, TenSach, GiaBan, SoLuongTon FROM sach");
$books = [];
while ($book = $result->fetch_assoc()) {
    $books[] = $book;
}
$result->free();

$result = $mysqli->query("SELECT * FROM hoadon");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý hóa đơn</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    <link rel="stylesheet" href="../../assets/general-style.css" type="text/css">
    <link rel="stylesheet" href="../../assets/orders-style.css" type="text/css">
</head>

<body>
    <div class="main-content">
        <div class="toolbar">
            <div class="toolbar-row">
                <div class="search-filter-group">
                    <div class="search-box">
                        <input type="text" id="timMaHD" name="mahd" placeholder="Tìm mã hóa đơn..." class="search-input" /> 
                        <button class="search-button">🔍</button>
                    </div>
                    <div class="date-range-group">
                        <input type="date" id="date-from" class="date-from" placeholder="Từ ngày">
                        <span style="margin: 0 4px;">-</span>
                        <input type="date" id="date-to" class="date-to" placeholder="Đến ngày">
                    </div>
                </div>
                <button class="add-button" onclick="createNewOrder()">
                    <img src="../../assets/plus.png" class="icon-add" alt="Add Icon" /> 
                    Thêm hóa đơn
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
                    <button class="sort-btn active" data-sort="id_hd">Mã HĐ</button>
                    <button class="sort-btn" data-sort="id_kh">Mã KH</button>
                    <div class="sort-dropdown">
                        <button class="sort-btn sort-dropdown-toggle" id="sortDateBtn">
                            <span class="label">Ngày lập</span>
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
        <!-- Bảng hóa đơn -->
        <table id="orderTable" class="table">
            <thead>
                <tr>
                    <th class="stt">STT</th>
                    <th class="id">Mã HĐ</th>
                    <th class="id">Mã KH</th>
                    <th>Ngày lập</th>
                    <th>Tổng tiền</th>
                    <th>Đã thanh toán</th>
                    <th>Còn lại</th>
                    <th class="action-buttons">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php $stt = 1; ?>
                <?php while($theOrder = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $stt++ ?></td>
                    <td class="id"><?= htmlspecialchars($theOrder['MaHD']) ?></td>
                    <td class="id"><?= htmlspecialchars($theOrder['MaKH']) ?></td>
                    <td>
                        <?php
                            $date = $theOrder['NgayLap'];
                            $d = new DateTime($date);
                            echo $d->format('d-m-Y');
                        ?>
                    </td>
                    <td><?= htmlspecialchars($theOrder['TongTien']) ?></td>
                    <td><?= htmlspecialchars($theOrder['TienTra']) ?></td>
                    <td><?= htmlspecialchars($theOrder['TienNo']) ?></td>
                    <td class="action-buttons">
                        <button class="view-btn" onclick="openOrderForm('<?= $theOrder['MaHD'] ?>', '<?= $theOrder['MaKH'] ?>')">Xem</button>
                        <!-- <button class="delete-btn" onclick="deleteRow(this)">Xóa</button> -->
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div class="toast" id="toast"></div>
    </div>

    <div id="orderFormOverlay" class="overlay">
        <div class="form-popup">
            <h2>Thông tin hóa đơn</h2>
            <form id="orderForm" name="orderForm" onsubmit="return false;" action="" method="post" novalidate>
                <label>Mã Hóa đơn:</label><input type="text" name="ma_hd" readonly required>

                <label>Mã Khách hàng:</label><input type="text" name="ma_kh" readonly oninput="findCustomer(this.value)">
                <span class="error" id="error_makh"></span>

                <label>Tên Khách hàng:</label><input type="text" name="ten_kh" readonly required>
                <span class="error" id="error_tenkh"></span>

                <div id="ngay_lap" style="display: none;">
                    <label>Ngày lập phiếu:</label>
                    <input type="date" name="ngay_lap" readonly>
                </div>

                <label>Thông tin sách đã mua:</label>
                <table id="booksOrderTable" style="border-collapse: collapse; width: 100%; text-align: center;">
                    <thead>
                        <tr>
                            <th>Mã sách</th>
                            <th>Tên sách</th>
                            <th>Số lượng</th>
                            <th>Đơn giá bán</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody id="book-rows"></tbody>
                </table>
                <span class="error" id="error_sach"></span>
                <button type="button" id="btnAdd" onclick="addBookRow()" style="display:none;" title="Thêm sách">
                    <img src="../../assets/plus.png" class="icon-add" alt="Add Icon" /> 
                </button>

                <label>Tổng tiền:</label><input type="text" name="tong_tien" oninput="updateDebt()" readonly required>
                <span class="error" id="error_tongtien"></span>

                <label>Số tiền đã thanh toán:</label><input type="text" name="tien_tra" oninput="updateDebt()" readonly required>
                <span class="error" id="error_tientra"></span>

                <label>Số tiền còn nợ:</label><input type="text" name="tien_no" readonly required>
                <span class="error" id="error_tienno"></span>

                <div class="form-buttons">
                    <button type="submit" class="btn-save" onclick="saveOrder()" style="display: none;">Lưu</button>
                    <!-- <button type="button" class="btn-edit" onclick="enableEditing()">Sửa</button> -->
                    <button type="button" class="btn-cancel" onclick="closeForm()">Đóng</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let editingIndex = -1;
        const latestRule = <?= json_encode($latestRule) ?>;

        // Mở form show thông tin chi tiết của hóa đơn
        function openOrderForm(maHD, maKH) {
            document.querySelectorAll(".error").forEach(el => el.textContent = "");

            fetch(`order_details.php?ma_hd=${maHD}`)
            .then(res => res.json())
            .then(data => {
                console.log("Fetched data:", data); 
                const form = document.forms["orderForm"];
                form.ma_hd.value = maHD;
                form.ma_kh.value = maKH;
                form.ten_kh.value = data.order.HoTen;
                form.ngay_lap.value = data.order.NgayLap;
                form.tien_tra.value = data.order.TienTra;
                form.tien_no.value = data.order.TienNo;
                form.tong_tien.value = data.order.TongTien;

                // Vì một hóa đơn có thể có nhiều sách nên sẽ có một bảng liệt kê các đầu sách được mua
                const tbody = document.querySelector("#booksOrderTable tbody");
                tbody.innerHTML = "";
                data.orderBooks.forEach(book => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${book.MaSach}</td>
                        <td>${book.TenSach}</td>
                        <td>${book.SoLuong}</td>
                        <td>${book.GiaBan}</td>
                        <td>${book.ThanhTien}</td>
                    `;
                    tbody.appendChild(row);
                });

                editingIndex = Array.from(document.querySelector('#orderTable tbody').rows)
                    .findIndex(row => row.cells[1].textContent === maHD);

                for (let input of form.elements) input.readOnly = true;

                document.getElementById("ngay_lap").style.display = "block";
                document.getElementById("btnAdd").style.display = "none";
                document.querySelector(".btn-save").style.display = "none";
                // document.querySelector(".btn-edit").style.display = "inline-block";
                document.getElementById("orderFormOverlay").classList.add("show");
            });
        }

        // Kiểm tra thông tin hợp lệ
        function validDateInputs(form) {
            let isValid = true;
            document.querySelectorAll(".error").forEach(el => el.textContent = "");

            const maHD = form.ma_hd.value;
            const maKH = form.ma_kh.value;
            const tenKH = form.ten_kh.value;
            const ngayLap = new Date().toISOString().split('T')[0];
            const tongTien = form.tong_tien.value;
            const tienTra = form.tien_tra.value;
            const tienNo = form.tien_no.value;

            const rows = document.querySelectorAll("#book-rows tr");
            if (rows.length === 0){
                document.getElementById("error_sach").textContent = "Bạn phải thêm ít nhất một đầu sách vào hóa đơn!";
                isValid = false;
            }
            rows.forEach((row, index) => {
                const maSachSelect = row.querySelector('[name="ma_sach[]"]');
                const selectedOption = maSachSelect.options[maSachSelect.selectedIndex];
                const tonKho = Number(selectedOption.dataset.ton)

                const tenSach = row.querySelector('[name="ten_sach[]"]').value;
                const soLuong = row.querySelector('[name="so_luong[]"]').value;
                const giaBan = row.querySelector('[name="gia_ban[]"]').value;
                const thanhTien = row.querySelector('[name="thanh_tien[]"]').value;

                if (!maSachSelect) {
                    document.getElementById("error_sach").textContent = `Vui lòng chọn mã sách cho dòng ${index + 1}`;
                    isValid = false;
                }
                if (!soLuong || soLuong <= 0) {
                    document.getElementById("error_sach").textContent = `Vui lòng chọn số lượng mua cho dòng ${index + 1}`;
                    isValid = false;
                } else if ((tonKho - soLuong) < latestRule.TonMinSauBan){
                    document.getElementById("error_sach").textContent = `Lượng tồn kho của sách dòng ${index + 1} là ${tonKho}. 
                        Vui lòng điều chỉnh lại số lượng mua để tối thiểu còn trong kho ${latestRule.TonMinSauBan} sau khi mua!`;
                    isValid = false;
                }
            });

            if(!maKH){
                document.getElementById("error_makh").textContent = `Vui lòng nhập mã khách hàng!`;
                isValid = false;
            }
            if(!tenKH){
                document.getElementById("error_tenkh").textContent = `Vui lòng nhập tên khách hàng!`;
                isValid = false;
            }

            if(!tienTra){
                document.getElementById("error_tientra").textContent = `Vui lòng nhập số tiền đã thanh toán!`;
                isValid = false;
            }
            return isValid;
        }
        
        // Hàm này tự động lấy tên khách hàng
        function findCustomer(maKH){
            if (!maKH) {
                document.forms["orderForm"].ten_kh.value = "";
                return;
            }
            fetch(`find_customer.php?ma_kh=${encodeURIComponent(maKH)}`)
            .then( res => res.text())
            .then (name => {
                if(name){
                document.forms["orderForm"].ten_kh.value = name;
                document.forms["orderForm"].ten_kh.readOnly = true;
                } else {
                document.forms["orderForm"].ten_kh.value = "";
                }
            })
            .catch(err => {
                console.error("Lỗi không lấy được tên khách hàng tương ứng.");
            });
        }

        //Thêm đầu sách vào hóa đơn
        function addBookRow() {
            const tbody = document.getElementById('book-rows');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <select name="ma_sach[]" required>
                        <option value="">- Chọn mã sách-</option>
                        <?php foreach ($books as $book): ?>
                        <option value="<?= $book['MaSach'] ?>"
                                data-name="<?= htmlspecialchars($book['TenSach']) ?>"
                                data-ton="<?= $book['SoLuongTon'] ?>"
                                data-price="<?= $book['GiaBan'] ?>">
                            <?= $book['MaSach'] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td><input type="text" name="ten_sach[]" required readonly></td>
                <td>
                    <input type="number" name="so_luong[]" min="1" required oninput="updateLineTotal(this)">
                    <span class="error" id="error_soluong"></span>
                </td>
                <td>
                    <input type="text" name="gia_ban[]" min="0" readonly required oninput="updateLineTotal(this)">
                    <span class="error" id="error_giaban"></span>
                </td>
                <td><input type="number" name="thanh_tien[]" readonly required"></td>
            `;
            const select = row.querySelector('select[name="ma_sach[]"]');
            const tenSachInput = row.querySelector('input[name="ten_sach[]"]');
            const giaBanInput = row.querySelector('input[name="gia_ban[]"]');

            select.addEventListener("change", function () {
                const selectedOption = this.options[this.selectedIndex];
                const bookName = selectedOption.getAttribute("data-name") || "";
                const bookPrice = selectedOption.getAttribute("data-price") || "";
                tenSachInput.value = bookName;
                giaBanInput.value = bookPrice;
            });
            tbody.appendChild(row);
            document.getElementById("btn")
        }

        // Mở form thêm hóa đơn mới
        function createNewOrder() {
            const form = document.forms['orderForm'];
            const table = document.getElementById("orderTable").getElementsByTagName("tbody")[0];
            const nextId = "HD" + String(table.rows.length + 1).padStart(3, '0');

            form.reset();
            form.ma_hd.value = nextId;
            document.getElementById("book-rows").innerHTML = "";

            for (let input of form.elements){
                if(input.name !== "ma_hd" && input.name !== "tong_tien" && input.name !== "tien_no"){
                    input.readOnly = false;
                }
            }
            document.getElementById("ngay_lap").style.display = "none";
            document.getElementById("btnAdd").style.display = "inline-block";
            document.querySelector(".btn-save").style.display = "inline-block";
            // document.querySelector(".btn-edit").style.display = "none";
            editingIndex = -1;
            document.getElementById("orderFormOverlay").classList.add("show");
        }

        // Hàm này sẽ tự động update input Thành tiền khi user thay đổi số lượng hoặc giá bán
        function updateLineTotal(input) {
            //tìm thuộc tính bị thay đổi
            const row = input.closest('tr');
            const quantity = parseInt(row.querySelector('[name="so_luong[]"]').value) || 0;
            const price = parseFloat(row.querySelector('[name="gia_ban[]"]').value) || 0;
            const lineTotal = quantity * price;
            row.querySelector('[name="thanh_tien[]"]').value = lineTotal.toFixed(2);
            updateTotal();
        }

        // Hàm này tự động update Tổng tiền của cả hóa đơn
        function updateTotal() {
            let total = 0;
            const thanhTien = document.querySelectorAll('[name="thanh_tien[]"]');
            thanhTien.forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            document.forms["orderForm"].tong_tien.value = total.toFixed(2);
            updateDebt();
        }

        // Hàm này tự động tính tiền nợ còn thiếu
        function updateDebt(){
            let debt = 0;
            const tongTien = document.forms["orderForm"].tong_tien.value;
            const tienTra = document.forms["orderForm"].tien_tra.value;
            debt = tongTien - tienTra;
            document.forms["orderForm"].tien_no.value = debt.toFixed(2);
        }

        // Button Lưu
        function saveOrder() {
            const form = document.forms['orderForm'];
            const table = document.getElementById("orderTable").getElementsByTagName("tbody")[0];
            if(!validDateInputs(form)) return;

            const maHD = form.ma_hd.value;
            const maKH = form.ma_kh.value;
            const tenKH = form.ten_kh.value;
            const ngayLap = new Date().toISOString().split('T')[0];
            const tongTien = form.tong_tien.value;
            const tienTra = form.tien_tra.value;
            const tienNo = form.tien_no.value;

            const maSach = Array.from(document.querySelectorAll('[name="ma_sach[]"]')).map(i => i.value);
            const soLuong = Array.from(document.querySelectorAll('[name="so_luong[]"]')).map(i => parseInt(i.value));
            const giaBan = Array.from(document.querySelectorAll('[name="gia_ban[]"]')).map(i => parseFloat(i.value));
            const thanhTien = Array.from(document.querySelectorAll('[name="thanh_tien[]"]')).map(i => parseFloat(i.value));

            const payload = {
                ma_hd: maHD,
                ma_kh: maKH,
                ten_kh: tenKH,
                ngay_lap: ngayLap,
                tong_tien: tongTien,
                tien_tra: tienTra,
                tien_no: tienNo,
                books: maSach.map((maSach, i) => ({
                ma_sach: maSach,
                so_luong: soLuong[i],
                gia_ban: giaBan[i],
                thanh_tien: thanhTien[i]
                }))
            };

            console.log("Sending payload to server:", payload);

            fetch('save_order.php', {
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
                } else if(response.trim() === "order_exists") {
                alert("Đã tồn tại hóa đơn. Vui lòng kiểm tra lại!");
                } else {
                alert("Lỗi: " + response);
                }
            })
            .catch(err => {
                console.error("Error:", err);
                alert("Có lỗi xảy ra khi gửi dữ liệu.");
            });
            closeForm();
            // if (editingIndex >= 0) {
            //   const row = table.rows[editingIndex];
            //   row.cells[0].textContent = maHD;
            //   row.cells[1].textContent = maKH;
            //   row.cells[2].textContent = ngayLap;
            //   row.cells[3].textContent = tongTien;
            //   row.cells[4].textContent = daThanhToan;
            //   row.cells[5].textContent = conLai;
            // } else {
            //   const row = table.insertRow();
            //   row.classList.add("new-row");
            //   row.innerHTML = `
            //     <td>${maHD}</td>
            //     <td>${maKH}</td>
            //     <td>${ngayLap}</td>
            //     <td>${tongTien}</td>
            //     <td>${daThanhToan}</td>
            //     <td>${conLai}</td>
            //     <td class="action-buttons">
            //       <button class="view-btn" onclick="openForm('${maHD}', '${maKH}', '${ngayLap}', '${tongTien}', '${daThanhToan}', '${conLai}')">Xem</button>
            //       <button class="delete-btn" onclick="deleteRow(this)">Xóa</button>
            //     </td>
            //   `;
            // }
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

        //Tìm kiếm 
        document.getElementById("timMaHD").addEventListener("input", renderOrderTable);

        function renderOrderTable() {
            const mahdFilter = document.getElementById("timMaHD").value.toLowerCase().trim();
            const dateFromInput = document.querySelector("#date-from");
            const dateToInput = document.querySelector("#date-to");
            const dateFrom = dateFromInput?.value;
            const dateTo = dateToInput?.value;

            let rows = getAllRows();

            rows.forEach(row => {
                const mahd = row.cells[1].textContent.toLowerCase();
                const ngayLapText = row.cells[3].textContent.trim(); // "18-06-2025"
                // Chuyển sang yyyy-mm-dd để so sánh
                const [day, month, year] = ngayLapText.split('-');
                const ngayLapISO = `${year}-${month}-${day}`;

                const matchMahd = mahd.includes(mahdFilter);
                let matchesDate = true;
                if (dateFrom || dateTo) {
                    if(dateFrom) {
                        matchesDate = matchesDate && (ngayLapISO >= dateFrom);
                    }
                    if(dateTo) {
                        matchesDate = matchesDate && (ngayLapISO <= dateTo);
                    }
                }

                if (matchMahd && matchesDate) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });

            // Sắp xếp
            let colIdx = 1;
            let compareFn = null;
            if (currentSort === "id_hd") colIdx = 1;
            if (currentSort === "id_kh") colIdx = 2;
            if (currentSort === "date-asc" || currentSort === "date-desc") colIdx = 3;

            if (currentSort === "id_hd" || currentSort === "id_kh") {
                compareFn = (a, b) => {
                    const valA = a.cells[colIdx].textContent.trim().toLowerCase();
                    const valB = b.cells[colIdx].textContent.trim().toLowerCase();
                    return valA.localeCompare(valB, 'vi');
                };
            }

            if (currentSort === "date-asc" || currentSort === "date-desc") {
                compareFn = (a, b) => {
                    // Lấy text ngày, ví dụ: "18-06-2025"
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
                const tbody = document.querySelector("#orderTable tbody");
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

            // Fix table borders after filtering and rendering
            fixTableBorders('orderTable');
        }

        document.addEventListener("DOMContentLoaded", () => {
            document.getElementById("timMaHD").addEventListener("input", () => {
                currentPage = 1;
                renderOrderTable();
            });

            document.querySelector("#date-from").addEventListener("change", () => {
                currentPage = 1;
                renderOrderTable();
            });
            document.querySelector("#date-to").addEventListener("change", () => {
                currentPage = 1;
                renderOrderTable();
            });

            // Xử lý dropdown menu cho ngày lập hóa đơn
            const sortDateBtn = document.getElementById('sortDateBtn');
            const sortDateMenu = document.getElementById('sortDateMenu');
            sortDateBtn.addEventListener('click', (e) => {
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
                    document.querySelector('#sortDateBtn .label').textContent = 'Ngày lập';

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
                    renderOrderTable();
                }
            });

            document.querySelector(".page-btn.next").addEventListener("click", () => {
                currentPage++;
                renderOrderTable();
            });

            renderOrderTable(); 
        });

        // Hàm đổi kiểu sắp xếp
        function handleSortChange(sortType) {
            currentSort = sortType;
            currentPage = 1;
            renderOrderTable();
        }

        // Hàm này đóng form
        function closeForm() {
            document.getElementById("orderFormOverlay").classList.remove("show");
        }

        document.getElementById("orderFormOverlay").addEventListener("click", e => {
            if (e.target === e.currentTarget) closeForm();
        });

        // Hàm này hiển thị thông báo 
        function showToast(message) {
            const toast = document.getElementById("toast");
            toast.textContent = message;
            toast.classList.add("show");
            setTimeout(() => toast.classList.remove("show"), 3000);
        }

        document.addEventListener("keydown", e => {
        if (e.key === "Escape") closeForm();
        });

        document.getElementById("orderFormOverlay").addEventListener("click", e => {
            if (e.target === e.currentTarget) closeForm();
        });
    </script>
</body>
</html>

<!-- function enableEditing() {
  const form = document.forms['orderForm'];
  for (let input of form.elements) {
    if (input.name !== "ma_hd") input.readOnly = false;
  }
  document.querySelector(".btn-save").style.display = "inline-block";
  document.querySelector(".btn-edit").style.display = "none";
} -->

<!-- function deleteRow(button) {
      if (confirm("Bạn có chắc muốn xóa hóa đơn này không?")) {
        const row = button.closest("tr");
        row.style.transition = "opacity 0.4s ease";
        row.style.opacity = 0;
        setTimeout(() => row.remove(), 400);
      }
    } -->
