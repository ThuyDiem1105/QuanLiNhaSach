// --- PHÂN TRANG, TÌM KIẾM, LỌC, SẮP XẾP ---

const PAGE_SIZE = 50;
let currentPage = 1;
let currentSort = "id"; // Mặc định sắp xếp theo mã KH

function getAllRows() {
    return Array.from(document.querySelectorAll(".table tbody tr"));
}

function renderTable() {
    // Lấy dữ liệu lọc
    const searchInput = document.querySelector(".search-input");
    const filterSelect = document.querySelector(".filter-select");
    const keyword = searchInput ? searchInput.value.toLowerCase() : "";
    const typeFilter = filterSelect ? filterSelect.value : "all";

    // Lấy tất cả dòng
    let rows = getAllRows();

    // LỌC
    rows = rows.filter(row => {
        const maKH = row.children[1]?.textContent.toLowerCase() || ""; // Mã KH
        const tenKH = row.children[2]?.textContent.toLowerCase() || ""; // Họ tên
        const loaiKH = row.children[4]?.textContent.toLowerCase() || ""; // Loại
        const matchName = tenKH.includes(keyword);
        const matchType = (typeFilter === "all" || loaiKH === typeFilter.toLowerCase());
        return matchName && matchType;
    });

    // SẮP XẾP
    rows.sort((a, b) => {
        if (currentSort === "id") {
            return a.children[1].textContent.localeCompare(b.children[1].textContent, undefined, {numeric: true});
        }
        if (currentSort === "name") {
            return a.children[2].textContent.localeCompare(b.children[2].textContent, 'vi');
        }
        if (currentSort === "debt-asc" || currentSort === "debt-desc") {
            const debtA = parseInt(a.children[5].textContent.replace(/\./g, "")) || 0;
            const debtB = parseInt(b.children[5].textContent.replace(/\./g, "")) || 0;
            return currentSort === "debt-asc" ? debtA - debtB : debtB - debtA;
        }
        return 0;
    });

    // GẮN LẠI các dòng vào tbody theo thứ tự mới
    const tbody = document.querySelector(".table tbody");
    getAllRows().forEach(row => row.style.display = "none"); // Ẩn tất cả trước
    rows.forEach(row => tbody.appendChild(row));

    // PHÂN TRANG
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
}

// Sự kiện phân trang
const prevBtn = document.querySelector(".page-btn.prev");
const nextBtn = document.querySelector(".page-btn.next");
if (prevBtn) prevBtn.addEventListener("click", () => {
    if (currentPage > 1) {
        currentPage--;
        renderTable();
    }
});
if (nextBtn) nextBtn.addEventListener("click", () => {
    const totalRows = getAllRows().filter(row => row.style.display !== "none").length;
    const totalPages = Math.max(1, Math.ceil(totalRows / PAGE_SIZE));
    if (currentPage < totalPages) {
        currentPage++;
        renderTable();
    }
});

// Sự kiện tìm kiếm và lọc
const searchInput = document.querySelector(".search-input");
const filterSelect = document.querySelector(".filter-select");
if (searchInput) searchInput.addEventListener("input", () => { currentPage = 1; renderTable(); });
if (filterSelect) filterSelect.addEventListener("change", () => { currentPage = 1; renderTable(); });

// SẮP XẾP
const sortPriceBtn = document.getElementById("sortPriceBtn");
const sortPriceMenu = document.getElementById("sortPriceMenu");
const sortDebtLabels = {
    "debt-asc": "Nợ: Tăng dần",
    "debt-desc": "Nợ: Giảm dần"
};
if (sortPriceBtn && sortPriceMenu) {
    sortPriceBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        sortPriceMenu.classList.toggle("show");
        sortPriceBtn.classList.toggle("sort-dropdown-active");
    });
    document.addEventListener("click", () => {
        sortPriceMenu.classList.remove("show");
        sortPriceBtn.classList.remove("sort-dropdown-active");
    });
    document.querySelectorAll(".sort-dropdown-item").forEach(item => {
        item.addEventListener("click", function(e) {
            document.querySelectorAll(".sort-btn").forEach(b => b.classList.remove("active"));
            document.querySelectorAll(".sort-dropdown-item").forEach(i => i.classList.remove("active"));
            this.classList.add("active");
            sortPriceBtn.classList.add("sort-dropdown-active");
            currentSort = this.getAttribute("data-sort");
            currentPage = 1;
            sortPriceBtn.querySelector(".label").textContent = sortDebtLabels[currentSort];
            sortPriceBtn.classList.add("active");
            renderTable();
            sortPriceMenu.classList.remove("show");
        });
    });
}
document.querySelectorAll(".sort-btn:not(.sort-dropdown-toggle)").forEach(btn => {
    btn.addEventListener("click", function() {
        document.querySelectorAll(".sort-btn").forEach(b => b.classList.remove("active"));
        document.querySelectorAll(".sort-dropdown-item").forEach(i => i.classList.remove("active"));
        this.classList.add("active");
        if (sortPriceBtn) sortPriceBtn.classList.remove("sort-dropdown-active");
        if (sortPriceBtn) sortPriceBtn.querySelector(".label").textContent = "Tiền nợ ";
        if (sortPriceBtn) sortPriceBtn.classList.remove("active");
        currentSort = this.getAttribute("data-sort");
        currentPage = 1;
        renderTable();
    });
});

// Hiển thị bảng ngay từ đầu
renderTable();