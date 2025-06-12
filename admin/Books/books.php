<?php
session_start();
include __DIR__ . '/../../connect.php';
// Đọc danh mục sách
$danhMucArr = [];
$result = $mysqli->query("SELECT MaDMS, TenDanhMuc FROM danhmucsach");
while ($row = $result->fetch_assoc()) {
    $danhMucArr[$row['MaDMS']] = $row['TenDanhMuc'];
}
$result->free();

// Đọc thể loại
$theLoaiArr = [];
$result = $mysqli->query("SELECT MaTL, TenTheLoai FROM theloai");
while ($row = $result->fetch_assoc()) {
    $theLoaiArr[$row['MaTL']] = $row['TenTheLoai'];
}
$result->free();

$result = $mysqli->query("SELECT * FROM sach");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý sách</title>
    <link rel="stylesheet" href="../../assets/general-style.css" type="text/css">
    <link rel="stylesheet" href="../../assets/books-style.css" type="text/css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
</head>
<body>
    <div class="main-content">
        <div class="toolbar">
            <div class="toolbar-row">
                <div class="search-filter-group">
                    <div class="search-box">
                        <input type="text" id="searchTensach" name="ten_sach" placeholder="Tìm kiếm sách..." class="search-input" />
                        <button class="search-button">🔍</button>
                    </div>
                    <select id="searchDanhmuc" class="filter-select">
                        <option value="">Tất cả danh mục</option>
                        <?php foreach ($danhMucArr as $ma_danhmuc => $ten_danhmuc): ?>
                            <option value="<?= $ma_danhmuc ?>"><?= $ten_danhmuc ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select id="searchTheloai" class="filter-select">
                        <option value="">Tất cả thể loại</option>
                        <?php foreach ($theLoaiArr as $ma_theloai => $ten_theloai): ?>
                            <option value="<?= $ma_theloai ?>"><?= $ten_theloai ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button class="add-button" onclick="createNewBook()">
                    <img src="../../assets/plus.png" class="icon-add" alt="Add Icon" /> 
                    Thêm sách mới
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
                    <button class="sort-btn active" data-sort="id">Mã sách</button>
                    <button class="sort-btn" data-sort="name">Tên sách</button>
                    <div class="sort-dropdown">
                        <button class="sort-btn sort-dropdown-toggle" id="sortPriceBtn">
                            <span class="label">Giá bán</span>
                            <span class="arrow">&#9660;</span>
                        </button>
                        <div class="sort-dropdown-menu" id="sortPriceMenu">
                            <div class="sort-dropdown-item" data-sort="price-asc">Giá: Tăng dần</div>
                            <div class="sort-dropdown-item" data-sort="price-desc">Giá: Giảm dần</div>
                        </div>
                    </div>
                    <div class="sort-dropdown">
                        <button class="sort-btn sort-dropdown-toggle" id="sortStockBtn">
                            <span class="label">Lượng tồn</span>
                            <span class="arrow">&#9660;</span>
                        </button>
                        <div class="sort-dropdown-menu" id="sortStockMenu">
                            <div class="sort-dropdown-item" data-sort="stock-asc">Lượng tồn: Tăng dần</div>
                            <div class="sort-dropdown-item" data-sort="stock-desc">Lượng tồn: Giảm dần</div>
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
        <!-- Bảng sách -->
        <table id="bookTable" class="table">
            <thead>
                <tr>
                    <th class="stt">STT</th>
                    <th class="id">Mã sách</th>
                    <th>Tên sách</th>
                    <th>Danh mục</th>
                    <th>Thể loại</th>
                    <th>Tác giả</th>
                    <th>Số lượng tồn</th>
                    <th>Giá bán</th>
                    <th class="actions">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php $stt = 1; ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                $tlArr = explode(',', $row['TheLoai']);
                $tenTheloaiArr = [];
                foreach ($tlArr as $theloai){
                    $tenTheloaiArr[] = $theLoaiArr[$theloai];
                }
                ?>
                <tr>
                    <td><?= $stt++ ?></td>
                    <td><?= htmlspecialchars($row['MaSach']) ?></td>
                    <td><?= htmlspecialchars($row['TenSach']) ?></td>
                    <td data-madm="<?= htmlspecialchars($row['MaDMS']) ?>">
                        <?= htmlspecialchars($danhMucArr[$row['MaDMS']]) ?>
                    </td>
                    <td data-matl="<?= htmlspecialchars($row['TheLoai']) ?>">
                        <?= htmlspecialchars(implode(', ', $tenTheloaiArr)) ?>
                    </td>
                    <td><?= htmlspecialchars($row['TacGia']) ?></td>
                    <td><?= htmlspecialchars($row['SoLuongTon']) ?></td>
                    <td><?= htmlspecialchars($row['GiaBan']) ?></td>
                    <td class="action-buttons">
                        <button class="view-btn" onclick="openForm(
                        '<?= $row['MaSach'] ?>',
                        '<?= $row['TenSach'] ?>',
                        '<?= $row['MaDMS'] ?>',
                        '<?= $row['TheLoai'] ?>',
                        '<?= $row['TacGia'] ?>',
                        '<?= $row['NhaXuatBan'] ?>',
                        '<?= $row['NgayXuatBan'] ?>',
                        '<?= $row['NgonNgu'] ?>',
                        '<?= $row['SoLuongTon'] ?>',
                        '<?= $row['GiaBan'] ?>'
                        )">Xem</button>
                        <button class="delete-btn" onclick="deleteBook('<?= $row['MaSach'] ?>')">Xóa</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div class="toast" id="toast"></div>
    </div>
    <div id="bookFormOverlay" class="overlay">
        <div class="form-popup">
            <h2>Chi tiết sách</h2>
            <form id="bookForm" onsubmit="return false;" action="" method="post" novalidate>
                <input type="hidden" id="form_mode" name="form_mode" value="view">

                <label>Mã sách:</label><input type="text" name="ma_sach" required readonly>
                <span class="error" id="error_masach"></span>

                <label>Tên sách:</label><input type="text" name="ten_sach" required readonly>
                <span class="error" id="error_tensach"></span>
                
                <label>Danh mục sách:</label>
                <select id="danh_muc" name="danh_muc" required readonly>
                    <option value="">-- Chọn danh mục sách --</option>
                    <?php foreach ($danhMucArr as $ma_danhmuc => $ten_danhmuc): ?>
                        <option value="<?= $ma_danhmuc ?>"><?= $ten_danhmuc ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="error" id="error_danhmuc"></span>

                <label>Thể loại tương ứng:</label>
                <select id="the_loai" name="the_loai[]" multiple required readonly>
                    <option value="">-- Chọn thể loại tương ứng --</option>
                    <?php foreach ($theLoaiArr as $ma_theloai => $ten_theloai): ?>
                    <option value="<?= $ma_theloai ?>"><?= $ten_theloai ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="error" id="error_theloai"></span>

                <label>Tác giả:</label>
                <input type="text" name="tac_gia" required readonly>
                <span class="error" id="error_tacgia"></span>

                <label>Ngôn ngữ:</label><input type="text" name="ngon_ngu" required readonly>
                <span class="error" id="error_ngonngu"></span>
                
                <label>Nhà xuất bản:</label><input type="text" name="nxb" required readonly>
                <span class="error" id="error_nxb"></span>
                
                <label>Ngày xuất bản:</label><input type="date" name="ngay_xb" required readonly>
                <span class="error" id="error_ngayxb"></span>
                
                <label>Số lượng tồn:</label><input type="number" name="sl_ton" required readonly>
                <span class="error" id="error_slton"></span>

                <label>Đơn giá bán:</label><input type="text" name="gia_ban" required readonly>
                <span class="error" id="error_giaban"></span>
                
                <div class="form-buttons">
                <button type="submit" class="btn-save" onclick="saveBook()" style="display: none;">Lưu</button>
                <button type="button" class="btn-edit" onclick="enableEditing()">Sửa</button>
                <button type="button" class="btn-cancel" onclick="closeForm()">Đóng</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
        let editingIndex = -1;
        let danhmucChoices, theloaiChoices;

        // Khởi tạo danh sách các danh mục và thể loại
        window.addEventListener("DOMContentLoaded", () => {
            //không sort mà để theo thứ tự đã initilize, ít nên ẩn tìm kiếm
            danhmucChoices = new Choices("#danh_muc", { shouldSort: false, searchEnabled: true });
            theloaiChoices = new Choices("#the_loai", { removeItemButton: true, shouldSort: false, searchEnabled: true });
            danhmucChoices.disable();
            theloaiChoices.disable();
        });

        // Button Xem chi tiết sách
        function openForm(maSach, tenSach, danhMuc, theLoaiStr, tacGia, nhaXB, ngayXB, ngonNgu, soluongTon, giaBan) {
            document.getElementById("bookFormOverlay").classList.add("show");
            // Reset scroll position to top
            document.querySelector(".form-popup").scrollTop = 0;
            document.querySelectorAll(".error").forEach(el => el.textContent = "");
            document.getElementById("form_mode").value = "view";

            const form = document.forms['bookForm'];
            form.ma_sach.value = maSach;
            form.ten_sach.value = tenSach;
            form.tac_gia.value = tacGia;
            form.ngon_ngu.value = ngonNgu;
            form.nxb.value = nhaXB;
            form.ngay_xb.value = ngayXB;
            form.sl_ton.value = soluongTon;
            form.gia_ban.value = parseInt(giaBan);

            form.danh_muc.value = danhMuc;
            danhmucChoices.setChoiceByValue(danhMuc);

            if(theLoaiStr){
                const theLoaiArr = theLoaiStr.split(',').map(s => s.trim());
                theloaiChoices.removeActiveItems();
                theLoaiArr.forEach(val => { theloaiChoices.setChoiceByValue(val); });
            }

            //lấy vị trí dòng (sách) được chọn để xem
            editingIndex = Array.from(document.querySelector('#bookTable tbody').rows)
                .findIndex(row => row.cells[0].textContent === maSach);
            
            // hiện tại chỉ được phép xem, không được chỉnh sửa thông tin  
            for (let input of form.querySelectorAll("input")) input.readOnly = true;
            danhmucChoices.disable();
            theloaiChoices.disable();

            //đang ở chế độ xem nên ẩn nút Lưu
            document.querySelector(".btn-save").style.display = "none";
            document.querySelector(".btn-edit").style.display = "inline-block";
            bookFormOverlay.classList.add("show");
        }

        // Button Sửa
        function enableEditing() {
            const form = document.forms['bookForm'];
            document.getElementById("form_mode").value = "edit";

            for (let input of form.querySelectorAll("input")) {
                if (input.name !== "ma_sach" && input.name !== "ten_sach") input.readOnly = false;
            }
            danhmucChoices.enable();
            theloaiChoices.enable();

            const maSach = form.ma_sach.value;

            document.querySelector(".btn-save").style.display = "inline-block";
            document.querySelector(".btn-edit").style.display = "none";
        }

        // Kiểm tra thông tin form
        function checkValidFormValues(form) {
            let isValid = true;
            document.querySelectorAll(".error").forEach(el => el.textContent = "");

            const maSach = form.ma_sach.value.trim();
            const tenSach = form.ten_sach.value.trim();

            const danhMuc = form.danh_muc.value;
            const theLoai = Array.from(form.the_loai.selectedOptions).map(o => o.value);

            const tacGia = form.tac_gia.value.trim();
            const nhaXB = form.nxb.value.trim();
            const ngayXB = form.ngay_xb.value;
            const ngonNgu = form.ngon_ngu.value.trim();
            const soluongTon = form.sl_ton.value;
            const giaBan = parseInt(form.gia_ban.value);

            // Tên sách
            if (!tenSach) {
                document.getElementById("error_tensach").textContent = "Tên sách không được để trống!";
                isValid = false;
            }
            // Ngày xuất bản
            if (!ngayXB) {
                document.getElementById("error_ngayxb").textContent = "Vui lòng chọn ngày xuất bản hợp lệ!";
                isValid = false;
            } else if (new Date(ngayXB) >= new Date()) {
                document.getElementById("error_ngayxb").textContent = "Ngày xuất bản phải trước ngày hiện tại!";
                isValid = false;
            }
            // Tác giả
            if (!tacGia) {
                document.getElementById("error_tacgia").textContent = "Tác giả không được để trống!";
                isValid = false;
            }
            //Ngôn ngữ
            if (!ngonNgu) {
                document.getElementById("error_ngonngu").textContent = "Ngôn ngữ không được để trống!";
                isValid = false;
            }
            //Nhà xuất bản
            if (!nhaXB) {
                document.getElementById("error_nxb").textContent = "Nhà xuất bản không được để trống!";
                isValid = false;
            }
            // Danh mục sách
            if (!danhMuc) {
                document.getElementById("error_danhmuc").textContent = "Danh mục sách không được để trống!";
                isValid = false;
            }
            // Thể loại
            if (!theLoai) {
                document.getElementById("error_theloai").textContent = "Vui lòng chọn ít nhất một thể loại cho sách!";
                isValid = false;
            }
            if (!giaBan) {
                document.getElementById("error_giaban").textContent = "Vui lòng thêm giá bán cho sách!";
                isValid = false;
            }
            return isValid;
        }

        //Button Lưu sách
        function saveBook() {
            const form = document.forms['bookForm'];
            const table = document.getElementById("bookTable").getElementsByTagName("tbody")[0];
            if(!checkValidFormValues(form)) return;

            const formMode = document.getElementById("form_mode").value;
            const maSach = form.ma_sach.value.trim();
            const tenSach = form.ten_sach.value.trim();

            const danhMuc = form.danh_muc.value;
            const theLoaiArr = Array.from(form.the_loai.selectedOptions).map(o => o.value);
            const theLoaiStr = theLoaiArr.join(',');

            const tacGia = form.tac_gia.value.trim();
            const nhaXB = form.nxb.value.trim();
            const ngayXB = form.ngay_xb.value;
            const ngonNgu = form.ngon_ngu.value.trim();
            const soluongTon = form.sl_ton.value;
            const giaBan = parseInt(form.gia_ban.value);

            const formData = new FormData(form);    
            formData.append("form_mode", formMode);
            formData.append("danh_muc", danhMuc);
            formData.append("gia_ban", giaBan);
            formData.append("the_loai", theLoaiStr);
            fetch('save_book.php', {
                method: "POST",
                body: formData,
            })
            .then(res => res.text())
            .then(response => {
                console.log("Raw response:", response);
                if(response.trim() === "OK") {
                showToast("Đã lưu thành công!");
                setTimeout(() => {
                    location.reload();
                }, 1000);
                } else if (response.trim() === "book_exists") {
                alert("Sách đã tồn tại! Bạn có thể cập nhật lại thông tin sách.");
                } else {
                alert("Lỗi: " + response);
                }
            })
            .catch(error => {
                console.error("Lỗi: ", error);
                alert("Lỗi khi gửi dữ liệu.");
            });
            closeForm();
        }

        //Button Thêm sách mới
        function createNewBook() {
            const form = document.forms['bookForm'];
            document.getElementById("form_mode").value = "new";

            form.reset();
            const table = document.getElementById("bookTable").getElementsByTagName("tbody")[0];
            const nextId = "SACH" + String(table.rows.length + 1).padStart(3, '0');
            form.ma_sach.value = nextId;
            for (let input of form.querySelectorAll("input")) {
                if (input.name !== "ma_sach" && input.name !== "sl_ton") input.readOnly = false;
            }
            document.querySelector(".btn-save").style.display = "inline-block";
            document.querySelector(".btn-edit").style.display = "none";

            editingIndex = -1;
            danhmucChoices.enable();
            theloaiChoices.enable();
            form.danh_muc.selectedIndex = 0;
            theloaiChoices.removeActiveItems();

            document.getElementById("bookFormOverlay").classList.add("show");
        }

        document.addEventListener("keydown", e => {
            if (e.key === "Escape") closeForm();
        });

        document.getElementById("bookFormOverlay").addEventListener("click", e => {
            if (e.target === e.currentTarget) closeForm();
        });

        // Button Xóa sách
        function deleteBook(maSach) {
            if (confirm("Bạn có chắc muốn xóa sách này không?")) {
                fetch('delete_book.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'ma_sach=' + encodeURIComponent(maSach),
                })
                .then(res => res.text())
                .then(response => {
                if(response.trim() === "OK") {
                    showToast("Xóa thành công!");
                    setTimeout(() => {
                    location.reload();
                    }, 1000);
                } else {
                    alert("Lỗi: " + response);
                }
                })
                .catch(error => {
                console.error("Lỗi: ", error);
                alert("Lỗi khi xóa sách.");
                });
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
        document.getElementById("searchTensach").addEventListener("input", renderBookTable);
        document.getElementById("searchDanhmuc").addEventListener("change", renderBookTable);
        document.getElementById("searchTheloai").addEventListener("change", renderBookTable);

        function renderBookTable() {
            const tensachFilter = document.getElementById("searchTensach").value.toLowerCase();
            const danhmucFilter = document.getElementById("searchDanhmuc").value;
            const theloaiFilter = document.getElementById("searchTheloai").value;

            let rows = getAllRows();

            // Lọc
            rows.forEach(row => {
                const tensach = row.cells[2].textContent.toLowerCase();
                const tacgia = row.cells[5].textContent.toLowerCase();
                const madm = row.cells[3].getAttribute('data-madm');
                const matl = row.cells[4].getAttribute('data-matl');
                const matchTensachOrTacgia = tensach.includes(tensachFilter) || tacgia.includes(tensachFilter);
                const matchDanhmuc = !danhmucFilter || madm === danhmucFilter;
                const matchTheloai = !theloaiFilter || (matl && matl.split(',').includes(theloaiFilter));
                row.style.display = (matchDanhmuc && matchTensachOrTacgia && matchTheloai) ? "" : "none";
            });

            function closeForm() {
                document.getElementById("bookFormOverlay").classList.remove("show");
            }

            document.getElementById("bookFormOverlay").addEventListener("click", e => {
                if (e.target === e.currentTarget) closeForm("bookFormOverlay");
            });

            // Lấy lại các dòng còn hiển thị để sắp xếp và phân trang
            let visibleRows = rows.filter(row => row.style.display !== "none");

            // Sắp xếp
            let colIdx = 1;
            let compareFn = null;
            if (currentSort === "id") colIdx = 1;
            if (currentSort === "name") colIdx = 2;
            if (currentSort === "price-asc" || currentSort === "price-desc") colIdx = 7;
            if (currentSort === "stock-asc" || currentSort === "stock-desc") colIdx = 6;

            if (currentSort === "id" || currentSort === "name") {
                compareFn = (a, b) => {
                    const valA = a.cells[colIdx].textContent.trim().toLowerCase();
                    const valB = b.cells[colIdx].textContent.trim().toLowerCase();
                    return valA.localeCompare(valB, 'vi');
                };
            }
            if (currentSort === "price-asc" || currentSort === "price-desc") {
                compareFn = (a, b) => {
                    const valA = parseFloat(a.cells[colIdx].textContent.replace(/,/g, '')) || 0;
                    const valB = parseFloat(b.cells[colIdx].textContent.replace(/,/g, '')) || 0;
                    return currentSort === "price-asc" ? valA - valB : valB - valA;
                };
            }
            if (currentSort === "stock-asc" || currentSort === "stock-desc") {
                compareFn = (a, b) => {
                    const valA = parseInt(a.cells[colIdx].textContent) || 0;
                    const valB = parseInt(b.cells[colIdx].textContent) || 0;
                    return currentSort === "stock-asc" ? valA - valB : valB - valA;
                };
            }
            if (compareFn) {
                visibleRows.sort(compareFn);
            }

            // Gắn lại các dòng vào tbody theo thứ tự mới
            const tbody = document.querySelector(".table tbody");
            visibleRows.forEach(row => tbody.appendChild(row));

            // Phân trang
            const totalRows = visibleRows.length;
            const totalPages = Math.max(1, Math.ceil(totalRows / PAGE_SIZE));
            if (currentPage > totalPages) currentPage = totalPages;
            const start = (currentPage - 1) * PAGE_SIZE;
            const end = start + PAGE_SIZE;

            // Ẩn tất cả dòng
            visibleRows.forEach(row => row.style.display = "none");
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
            fixTableBorders();
        }

        document.addEventListener("DOMContentLoaded", () => {
            document.getElementById("searchTensach").addEventListener("input", () => {
                currentPage = 1;
                renderBookTable();
            });
            document.getElementById("searchDanhmuc").addEventListener("change", () => {
                currentPage = 1;
                renderBookTable();
            });
            document.getElementById("searchTheloai").addEventListener("change", () => {
                currentPage = 1;
                renderBookTable();
            });

            // Xử lý dropdown menu cho giá bán
            const sortPriceBtn = document.getElementById('sortPriceBtn');
            const sortPriceMenu = document.getElementById('sortPriceMenu');
            sortPriceBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                sortPriceMenu.style.display = sortPriceMenu.style.display === 'block' ? 'none' : 'block';
                sortStockMenu.style.display = 'none';
            });

            // Xử lý dropdown menu cho lượng tồn
            const sortStockBtn = document.getElementById('sortStockBtn');
            const sortStockMenu = document.getElementById('sortStockMenu');
            sortStockBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                sortStockMenu.style.display = sortStockMenu.style.display === 'block' ? 'none' : 'block';
                sortPriceMenu.style.display = 'none';
            });

            // Xử lý khi click vào các nút sắp xếp thông thường
            document.querySelectorAll('.sort-btn[data-sort]').forEach(btn => {
                btn.addEventListener('click', function() {
                    // Xóa active class từ tất cả các nút sắp xếp
                    document.querySelectorAll('.sort-btn').forEach(b => b.classList.remove('active'));
                    // Thêm active class cho nút được click
                    this.classList.add('active');
                    
                    // Reset text của các nút dropdown về mặc định
                    document.querySelector('#sortPriceBtn .label').textContent = 'Giá bán';
                    document.querySelector('#sortStockBtn .label').textContent = 'Lượng tồn';
                    
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
                    if (sortType.includes('price')) {
                        labelSpan.textContent = this.textContent;
                        sortPriceMenu.style.display = 'none';
                        // Reset text của nút Lượng tồn về mặc định
                        document.querySelector('#sortStockBtn .label').textContent = 'Lượng tồn';
                    } else if (sortType.includes('stock')) {
                        labelSpan.textContent = this.textContent;
                        sortStockMenu.style.display = 'none';
                        // Reset text của nút Giá bán về mặc định
                        document.querySelector('#sortPriceBtn .label').textContent = 'Giá bán';
                    }
                    
                    handleSortChange(sortType);
                });
            });

            // Đóng dropdown khi click ra ngoài
            document.addEventListener('click', () => {
                sortPriceMenu.style.display = 'none';
                sortStockMenu.style.display = 'none';
            });

            // Ngăn chặn sự kiện click trong menu
            sortPriceMenu.addEventListener('click', (e) => e.stopPropagation());
            sortStockMenu.addEventListener('click', (e) => e.stopPropagation());

            // Phân trang
            document.querySelector(".page-btn.prev").addEventListener("click", () => {
                if (currentPage > 1) {
                    currentPage--;
                    renderBookTable();
                }
            });
            document.querySelector(".page-btn.next").addEventListener("click", () => {
                currentPage++;
                renderBookTable();
            });

            renderBookTable();
        });

        // Hàm đổi kiểu sắp xếp
        function handleSortChange(sortType) {
            currentSort = sortType;
            currentPage = 1;
            renderBookTable();
        }
        function closeForm() {
            document.getElementById("bookFormOverlay").classList.remove("show");
        }

        document.getElementById("bookFormOverlay").addEventListener("click", e => {
        if (e.target === e.currentTarget) closeForm("bookFormOverlay");
        });
        function showToast(message) {
            const toast = document.getElementById("toast");
            toast.textContent = message;
            toast.classList.add("show");

            setTimeout(() => {
                toast.classList.remove("show");
            }, 3000);
        }
    </script>
</body>
</html>