// Hàm xử lý border-bottom cho dòng cuối cùng đang hiển thị
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

function viewDeal(dealId) {
    const row = [...document.querySelectorAll(".table tbody tr")]
        .find(tr => tr.children[1] && tr.children[1].textContent.trim() === dealId.trim());
    if (!row) return;

    const id = row.children[1].textContent;
    const name = row.children[2].textContent;
    const timeRange = row.children[3].textContent.trim();
    const [startDate, endDate] = timeRange.split('-').map(s => s.trim());
    const condition = row.getAttribute("data-condition") || "";

    const overlay = document.createElement("div");
    overlay.className = "detail-overlay";
    overlay.innerHTML = `
        <div class="detail-form">
            <h2>Thông tin khuyến mãi</h2>
            <div class="form-group">
                <label for="deal-id">Mã khuyến mãi</label>
                <input type="text" id="deal-id" value="${id}" disabled>
            </div>
            <div class="form-group">
                <label for="deal-name">Tên khuyến mãi</label>
                <input type="text" id="deal-name" value="${name}" disabled>
            </div>
            <div class="form-group">
                <label for="deal-condition">Điều kiện áp dụng</label>
                <textarea id="deal-condition" disabled>${condition}</textarea>
            </div>
            <div class="form-group">
                <label for="deal-start">Ngày bắt đầu</label>
                <input type="date" id="deal-start" value="${formatDateForInput(startDate)}" disabled>
            </div>
            <div class="form-group">
                <label for="deal-end">Ngày kết thúc</label>
                <input type="date" id="deal-end" value="${formatDateForInput(endDate)}" disabled>
            </div>
            <div class="form-actions">
                <button class="edit-btn">Sửa</button>
                <button class="cancel-btn">Đóng</button>
            </div>
        </div>
    `;
    document.body.appendChild(overlay);

    const nameInput = overlay.querySelector("#deal-name");
    const conditionInput = overlay.querySelector("#deal-condition");
    const startInput = overlay.querySelector("#deal-start");
    const endInput = overlay.querySelector("#deal-end");
    const editBtn = overlay.querySelector(".edit-btn");

    let isEditing = false;
    let originalData = {
        name: nameInput.value,
        condition: conditionInput.value,
        start: startInput.value,
        end: endInput.value
    };

    editBtn.addEventListener("click", () => {
        const isDisabled = nameInput.disabled;
        nameInput.disabled = conditionInput.disabled = startInput.disabled = endInput.disabled = !isDisabled;
        if (isDisabled) {
            editBtn.textContent = "Lưu";
            editBtn.classList.add("save-mode");
            isEditing = true;
        } else {
            // Validate
            if (!nameInput.value || !conditionInput.value || !startInput.value || !endInput.value) {
                alert("Vui lòng điền đầy đủ thông tin.");
                nameInput.disabled = conditionInput.disabled = startInput.disabled = endInput.disabled = false;
                editBtn.textContent = "Lưu";
                editBtn.classList.add("save-mode");
                isEditing = true;
                return;
            }
            // Gửi AJAX cập nhật DB
            fetch('save_deals.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    form_mode: 'edit',
                    ma_km: id,
                    ten_km: nameInput.value,
                    dieu_kien_ap_dung: conditionInput.value,
                    ngay_bat_dau: startInput.value,
                    ngay_ket_thuc: endInput.value
                })
            })
            .then(res => res.text())
            .then(response => {
                if (response.trim() === 'OK') {
                    showToast('Cập nhật khuyến mãi thành công!');
                    // Cập nhật lại bảng
                    row.children[2].textContent = nameInput.value;
                    row.children[3].textContent = [formatDateForDisplay(startInput.value), formatDateForDisplay(endInput.value)].join(' - ');
                    row.setAttribute('data-condition', conditionInput.value);
                    nameInput.disabled = conditionInput.disabled = startInput.disabled = endInput.disabled = true;
                    editBtn.textContent = "Sửa";
                    editBtn.classList.remove("save-mode");
                    isEditing = false;
                    originalData = {
                        name: nameInput.value,
                        condition: conditionInput.value,
                        start: startInput.value,
                        end: endInput.value
                    };
                    renderTable();
                    overlay.remove();
                } else {
                    alert('Lỗi khi cập nhật khuyến mãi: ' + response);
                    nameInput.disabled = conditionInput.disabled = startInput.disabled = endInput.disabled = false;
                    editBtn.textContent = "Lưu";
                    editBtn.classList.add("save-mode");
                    isEditing = true;
                }
            })
            .catch(() => {
                alert('Lỗi kết nối máy chủ!');
                nameInput.disabled = conditionInput.disabled = startInput.disabled = endInput.disabled = false;
                editBtn.textContent = "Lưu";
                editBtn.classList.add("save-mode");
                isEditing = true;
            });
        }
    });

    overlay.querySelector(".cancel-btn").addEventListener("click", () => {
        const currentData = {
            name: nameInput.value,
            condition: conditionInput.value,
            start: startInput.value,
            end: endInput.value
        };
        const dataChanged = JSON.stringify(currentData) !== JSON.stringify(originalData);
        if (isEditing && dataChanged) {
            const confirmExit = confirm("Bạn có muốn thoát khi chưa lưu không?");
            if (!confirmExit) return;
        }
        document.body.removeChild(overlay);
    });
}

// Helper: chuyển dd/mm/yyyy thành yyyy-mm-dd cho input type=date
function formatDateForInput(dateStr) {
    if (!dateStr) return "";
    const [d, m, y] = dateStr.split("/");
    return `${y}-${m.padStart(2, '0')}-${d.padStart(2, '0')}`;
}
// Helper: chuyển yyyy-mm-dd thành dd/mm/yyyy
function formatDateForDisplay(dateStr) {
    if (!dateStr) return "";
    const [y, m, d] = dateStr.split("-");
    return `${d}/${m}/${y}`;
}


function deleteDeal(button) {
    const row = button.closest("tr");
    const id = row.children[1].textContent;
    if (confirm("Bạn có chắc muốn xóa khuyến mãi này?")) {
        fetch('delete_deals.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ ma_kh: id })
        })
        .then(res => res.text())
        .then(response => {
            if (response.trim() === 'OK') {
                showToast('Xóa khuyến mãi thành công!');
                row.remove();
                renderTable();
            } else {
                alert('Lỗi khi xóa khuyến mãi: ' + response);
            }
        })
        .catch(() => alert('Lỗi kết nối máy chủ!'));
    }
}


function createNewDeal() {
    const form = document.createElement("div");
    form.className = "detail-overlay";
    form.innerHTML = `
        <div class="add-form">
            <h2>Thêm khuyến mãi</h2>
            <div class="form-group">
                <label>Tên khuyến mãi</label>
                <input type="text" id="new-name">
            </div>
            <div class="form-group">
                <label>Điều kiện áp dụng</label>
                <textarea id="new-condition"></textarea>
            </div>
            <div class="form-group">
                <label>Thời gian áp dụng</label>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <input type="date" id="new-start">
                    <span>-</span>
                    <input type="date" id="new-end">
                </div>
            </div>
            <div class="form-group">
                <label>Trạng thái</label>
                <select id="new-status">
                    <option value="">Chọn</option>
                    <option value="active">Đang áp dụng</option>
                    <option value="expired">Hết hạn</option>
                </select>
            </div>
            <div class="form-actions">
                <button class="save-btn">Lưu</button>
                <button class="cancel-btn">Hủy</button>
            </div>
        </div>
    `;
    document.body.appendChild(form);

    // Lưu dữ liệu mặc định ban đầu
    const defaultData = {
        name: "",
        condition: "",
        start: "",
        end: "",
        status: ""
    };

    form.querySelector(".cancel-btn").addEventListener("click", () => {
        // Lấy dữ liệu hiện tại
        const currentData = {
            name: form.querySelector("#new-name").value.trim(),
            condition: form.querySelector("#new-condition").value.trim(),
            start: form.querySelector("#new-start").value,
            end: form.querySelector("#new-end").value,
            status: form.querySelector("#new-status").value
        };
        // Kiểm tra nếu có thay đổi so với mặc định
        const dataChanged = JSON.stringify(currentData) !== JSON.stringify(defaultData);

        if (dataChanged) {
            if (!confirm("Bạn có muốn thoát khi chưa lưu không?")) return;
        }
        form.remove();
    });

    form.querySelector(".save-btn").addEventListener("click", () => {
        let name = form.querySelector("#new-name").value.trim();
        let condition = form.querySelector("#new-condition").value.trim();
        let start = form.querySelector("#new-start").value;
        let end = form.querySelector("#new-end").value;
        let status = form.querySelector("#new-status").value;

        if (!name || !condition || !start || !end || !status) {
            alert("Vui lòng điền đầy đủ thông tin.");
            return;
        }

        const table = document.querySelector(".table tbody");
        const newId = generateDealId();
        const newRow = document.createElement("tr");

        // Lưu địa chỉ và email vào thuộc tính data- của tr
        newRow.setAttribute("data-condition", condition);

        const statusMap = {
            "active": "Đang áp dụng",
            "expired": "Hết hạn"
        };
        const statusText = statusMap[status] || "";

        newRow.innerHTML = `
            <td></td> <!-- STT sẽ được JS cập nhật -->
            <td>${newId}</td>
            <td>${name}</td>
            <td>${[start, end].map(date => date.split('-').reverse().join('/')).join(' - ')}</td>
            <td>${statusText}</td>
            <td>
                <button class="view-btn" onclick="viewDeal('${newId}')">Xem</button>
                <button class="delete-btn" onclick="deleteDeal(this)">Xóa</button>
            </td>
        `;

        table.appendChild(newRow);
        renderTable();
        form.remove();

        // Hiển thị thông báo đã lưu
        showToast("Đã thêm thành công!");
    });
}

function generateDealId() {
    const rows = document.querySelectorAll(".table tbody tr");
    const usedNumbers = [];
    rows.forEach(row => {
        const id = row.children[1].textContent;
        const num = parseInt(id.replace("KM", ""));
        if (!isNaN(num)) usedNumbers.push(num);;
    });
    // Tìm số nhỏ nhất chưa dùng
    let next = 1;
    while (usedNumbers.includes(next)) {
        next++;
    }
    return `KM${next.toString().padStart(3, "0")}`;
}

document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.querySelector(".search-input");
    const filterSelect = document.querySelector(".filter-select");
    const tableBody = document.querySelector(".table tbody");

    function filterDeals() {
        currentPage = 1; // Reset về trang đầu khi lọc
        renderTable(); // Gọi lại renderTable để cập nhật phân trang
    }

    searchInput.addEventListener("input", filterDeals);
    filterSelect.addEventListener("change", filterDeals);
    document.querySelector("#date-from").addEventListener("change", filterDeals);
    document.querySelector("#date-to").addEventListener("change", filterDeals);

    renderTable(); // Thêm dòng này để bảng hiển thị đúng ngay từ đầu
});

function showToast(message) {
    const toast = document.getElementById("toast");
    toast.textContent = message;
    toast.classList.add("show");

    setTimeout(() => {
        toast.classList.remove("show");
    }, 3000);
}

const PAGE_SIZE = 50;
let currentPage = 1;
let currentSort = "id"; // mặc định sắp xếp theo mã KM

function getAllRows() {
    return Array.from(document.querySelectorAll(".table tbody tr"));
}

function renderTable() {
    // Lấy dữ liệu lọc
    const searchInput = document.querySelector(".search-input");
    const filterSelect = document.querySelector(".filter-select");
    const keyword = searchInput ? searchInput.value.toLowerCase() : "";
    const typeFilter = filterSelect ? filterSelect.value : "all";

    const dateFromInput = document.querySelector("#date-from");
    const dateToInput = document.querySelector("#date-to");
    const dateFrom = dateFromInput?.value;
    const dateTo = dateToInput?.value;

    // Lấy tất cả dòng (không ẩn)
    let rows = getAllRows();

    // LỌC
    rows = rows.filter(row => {
        const id = row.children[1]?.textContent.toLowerCase() || "";
        const name = row.children[2]?.textContent.toLowerCase() || "";
        const status = row.children[4]?.textContent.toLowerCase() || "";
        const timeRange = row.children[3]?.textContent.trim();

        // Lọc theo keyword
        const matchesKeyword = id.includes(keyword) || name.includes(keyword);

        // Lọc theo trạng thái (filterSelect): chỉ lọc cột trạng thái
        const statusMap = {
            "active": "đang áp dụng",
            "expired": "hết hạn"
        };
        const matchesType = (typeFilter === "all" || status === (statusMap[typeFilter] || typeFilter).toLowerCase());

        // Lọc theo thời gian nếu có nhập ngày
        let matchesDate = true;
        if (dateFrom || dateTo) {
            const [startStr, endStr] = timeRange.split("-").map(s => s.trim());
            const [d1, m1, y1] = startStr.split("/");
            const [d2, m2, y2] = endStr.split("/");
            const startDate = new Date(`${y1}-${m1}-${d1}`);
            const endDate = new Date(`${y2}-${m2}-${d2}`);

            if (dateFrom) {
                matchesDate = matchesDate && (endDate >= new Date(dateFrom));
            }
            if (dateTo) {
                matchesDate = matchesDate && (startDate <= new Date(dateTo));
            }
        }

        return matchesKeyword && matchesType && matchesDate;;
    });

    // Sắp xếp
    rows.sort((a, b) => {
        if (currentSort === "id") {
            return a.children[1].textContent.localeCompare(b.children[1].textContent, undefined, {numeric: true});
        }
        if (currentSort === "name") {
            return a.children[2].textContent.localeCompare(b.children[2].textContent, 'vi');
        }
        if (currentSort === "time-asc") {
            // Cũ nhất lên đầu (tăng dần)
            const timeA = a.children[3].textContent.trim().split(" - ").map(date => {
                const [d, m, y] = date.split("/");
                return new Date(`${y}-${m}-${d}`);
            });
            const timeB = b.children[3].textContent.trim().split(" - ").map(date => {
                const [d, m, y] = date.split("/");
                return new Date(`${y}-${m}-${d}`);
            });
            return timeA[0] - timeB[0];
        }
        if (currentSort === "time-desc") {
            // Mới nhất lên đầu (giảm dần)
            const timeA = a.children[3].textContent.trim().split(" - ").map(date => {
                const [d, m, y] = date.split("/");
                return new Date(`${y}-${m}-${d}`);
            });
            const timeB = b.children[3].textContent.trim().split(" - ").map(date => {
                const [d, m, y] = date.split("/");
                return new Date(`${y}-${m}-${d}`);
            });
            return timeB[0] - timeA[0];
        }
        return 0;
    });

    // GẮN LẠI các dòng vào tbody theo thứ tự mới
    const tbody = document.querySelector(".table tbody");
    getAllRows().forEach(row => row.style.display = "none"); // Ẩn tất cả trước
    rows.forEach(row => tbody.appendChild(row));

    // Phân trang
    const totalRows = rows.length;
    const totalPages = Math.max(1, Math.ceil(totalRows / PAGE_SIZE));
    if (currentPage > totalPages) currentPage = totalPages;
    const start = (currentPage - 1) * PAGE_SIZE;
    const end = start + PAGE_SIZE;

    // Ẩn tất cả dòng
    rows.forEach(row => row.style.display = "none");
    // Hiện dòng thuộc trang hiện tại
    rows.slice(start, end).forEach(row => row.style.display = "");

    // Cập nhật phân trang
    document.querySelector(".page-info").textContent = `${currentPage}/${totalPages}`;
    document.querySelector(".page-btn.prev").disabled = currentPage === 1;
    document.querySelector(".page-btn.next").disabled = currentPage === totalPages;

    // Đánh lại số thứ tự STT cho các dòng đang hiển thị
    rows.slice(start, end).forEach((row, idx) => {
        row.children[0].textContent = (start + idx + 1);
    });
    fixTableBorders();
}

// Sự kiện phân trang
document.querySelector(".page-btn.prev").addEventListener("click", () => {
    if (currentPage > 1) {
        currentPage--;
        renderTable();
    }
});
document.querySelector(".page-btn.next").addEventListener("click", () => {
    const totalRows = getAllRows().length;
    const totalPages = Math.max(1, Math.ceil(totalRows / PAGE_SIZE));
    if (currentPage < totalPages) {
        currentPage++;
        renderTable();
    }
});

// Dropdown cho sắp xếp Thời gian
const sortTimeBtn = document.querySelector(".sort-btn.time");
const sortTimeMenu = document.querySelector(".sort-dropdown.time");

// Gán nhãn cho từng lựa chọn trong dropdown sắp xếp thời gian
const sortTimeLabels = {
    "time-asc": "Thời gian: Cũ nhất",
    "time-desc": "Thời gian: Mới nhất"
};

// Thêm sự kiện click cho nút sắp xếp thời gian
if (sortTimeBtn && sortTimeMenu) {
    sortTimeBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        sortTimeMenu.classList.toggle("show");
        sortTimeBtn.classList.toggle("sort-dropdown-active");
    });
}

// Chọn sắp xếp theo thời gian
if (sortTimeMenu) {
    sortTimeMenu.querySelectorAll(".sort-dropdown-item").forEach(item => {
        item.addEventListener("click", function(e) {
            e.stopPropagation();
            document.querySelectorAll(".sort-btn").forEach(b => b.classList.remove("active"));
            document.querySelectorAll(".sort-dropdown-item").forEach(i => i.classList.remove("active"));
            this.classList.add("active");
            sortTimeBtn.classList.add("sort-dropdown-active");
            currentSort = this.getAttribute("data-sort");
            currentPage = 1; // Reset về trang đầu khi thay đổi sắp xếp
            // Cập nhật nút sắp xếp thời gian
            sortTimeBtn.querySelector(".label").textContent = sortTimeLabels[currentSort];
            sortTimeBtn.classList.add("active");
            renderTable();
            sortTimeMenu.classList.remove("show");
        });
    });
}
// Đổi lại sự kiện cho các nút sort-btn thường (trừ sortTimeBtn)
document.querySelectorAll(".sort-btn:not(.sort-dropdown-toggle)").forEach(btn => {
    btn.addEventListener("click", function() {
        document.querySelectorAll(".sort-btn").forEach(b => b.classList.remove("active"));
        document.querySelectorAll(".sort-dropdown-item").forEach(i => i.classList.remove("active"));
        this.classList.add("active");
        sortTimeBtn.classList.remove("sort-dropdown-active");
        // Đặt lại nhãn nút sắp xếp thời gian
        sortTimeBtn.querySelector(".label").textContent = "Thời gian";
        sortTimeBtn.classList.remove("active");
        currentSort = this.getAttribute("data-sort");
        currentPage = 1; // Reset về trang đầu khi thay đổi sắp xếp
        renderTable();
    });
});