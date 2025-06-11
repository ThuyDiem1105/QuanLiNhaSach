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
    if (!row) {
        alert("Không tìm thấy khuyến mãi với mã: " + dealId);
        return;
    }

    const name = row.children[2].textContent;
    const timeRange = row.children[3].textContent;
    const condition = row.getAttribute("data-condition") || "";
    let start = '', end = '';
    if (timeRange.includes("-")) {
        [start, end] = timeRange.split("-").map(s => s.trim());
    }

    const overlay = document.createElement("div");
    overlay.className = "detail-overlay";
    overlay.innerHTML = `
        <div class="detail-form">
            <h2>Thông tin khuyến mãi</h2>
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
                <input type="text" id="deal-start" value="${start}" disabled>
            </div>
            <div class="form-group">
                <label for="deal-end">Ngày kết thúc</label>
                <input type="text" id="deal-end" value="${end}" disabled>
            </div>
            <div class="form-actions">
                <butotn class="edit-btn">Sửa</button>
                <button class="cancel-btn">Đóng</button>
            </div>
        </div>
    `;
    document.body.appendChild(overlay);
    overlay.querySelector(".cancel-btn").addEventListener("click", () => {
        document.body.removeChild(overlay);
    });
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
                <label for="new-name">Tên khuyến mãi</label>
                <input type="text" id="new-name">
            </div>
            <div class="form-group">
                <label for="new-condition">Điều kiện áp dụng</label>
                <textarea id="new-condition"></textarea>
            </div>
            <div class="form-group">
                <label>Ngày bắt đầu</label>
                <input type="date" id="new-start">
            </div>
            <div class="form-group">
                <label>Ngày kết thúc</label>
                <input type="date" id="new-end">
            </div>
            <div class="form-actions">
                <button class="save-btn">Lưu</button>
                <button class="cancel-btn">Hủy</button>
            </div>
        </div>
    `;
    document.body.appendChild(form);

    const defaultData = {
        name: "",
        condition: "",
        start: "",
        end: ""
    };

    form.querySelector(".cancel-btn").addEventListener("click", () => {
        const currentData = {
            name: form.querySelector("#new-name").value.trim(),
            condition: form.querySelector("#new-condition").value.trim(),
            start: form.querySelector("#new-start").value,
            end: form.querySelector("#new-end").value
        };
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
        if (!name || !condition || !start || !end) {
            alert("Vui lòng điền đầy đủ thông tin.");
            return;
        }
        // Gửi AJAX lưu vào database
        fetch('save_deals.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                form_mode: 'new',
                ten_km: name,
                dieu_kien_ap_dung: condition,
                ngay_bat_dau: start,
                ngay_ket_thuc: end
            })
        })
        .then(res => res.text())
        .then(response => {
            if (response.trim() === 'OK') {
                showToast("Đã thêm thành công!");
                form.remove();
                location.reload();
            } else {
                alert('Lỗi khi thêm khuyến mãi: ' + response);
            }
        })
        .catch(() => alert('Lỗi kết nối máy chủ!'));
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