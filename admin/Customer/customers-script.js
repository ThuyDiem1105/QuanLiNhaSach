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

function openCustomerForm(maKH, tenKH, sdt, diaChi, email, loaiKH, tienNo) {
    const row = [...document.querySelectorAll(".table tbody tr")]
        .find(tr => tr.children[0].textContent === maKH);

    if (!row) return;

    const id = row.children[1].textContent;
    const name = row.children[2].textContent;
    const phone = row.children[3].textContent;
    const type = row.children[4].textContent;
    const debtRaw = row.children[5].textContent;
    
    // Lấy thông tin chi tiết từ nguồn dữ liệu khác nếu có, hoặc lưu trữ thêm thông tin khi tạo mới/sửa
    // Ở đây demo sẽ lấy tạm, bạn có thể mở rộng thêm nếu cần

    // Nếu bạn lưu thông tin chi tiết ở nơi khác, hãy lấy ra ở đây
    // Ví dụ: const customer = customers.find(c => c.id === id);

    // Nếu không có, có thể hiển thị trống hoặc thông báo
    const address = row.getAttribute('data-address') || "";
    const email = row.getAttribute('data-email') || "";

    // Xóa dấu chấm trong số tiền để parseInt đúng
    const debt = parseInt(debtRaw.replace(/\./g, "")) || 0;

    const overlay = document.createElement("div");
    overlay.className = "detail-overlay";

    overlay.innerHTML = `
        <div class="detail-form">
            <h2>Thông tin khách hàng</h2>
            <div class="form-group">
                <label for="id">Mã KH</label>
                <input type="text" id="id" value="${id}" disabled>
            </div>
            <div class="form-group">
                <label for="name">Họ tên</label>
                <input type="text" id="name" value="${name}" disabled>
            </div>
            <div class="form-group">
                <label for="address">Địa chỉ</label>
                <input type="text" id="address" value="${address}" disabled>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" value="${email}" disabled>
            </div>
            <div class="form-group">
                <label for="phone">Điện thoại</label>
                <input type="text" id="phone" value="${phone}" disabled>
            </div>
            <div class="form-group">
                <label for="type">Loại</label>
                <select id="type" disabled>
                    <option value="Thường" ${type === 'Thường' ? 'selected' : ''}>Thường</option>
                    <option value="VIP" ${type === 'VIP' ? 'selected' : ''}>VIP</option>
                </select>
            </div>
            <div class="form-group">
                <label for="debt">Số tiền nợ</label>
                <input type="number" id="debt" value="${debt}" disabled>
            </div>
            <div class="form-actions">
                <button class="edit-btn">Sửa</button>
                <button class="cancel-btn">Đóng</button>
            </div>
        </div>
    `;

    document.body.appendChild(overlay);

    const nameInput = overlay.querySelector("#name");
    const addressInput = overlay.querySelector("#address");
    const emailInput = overlay.querySelector("#email");
    const phoneInput = overlay.querySelector("#phone");
    const typeSelect = overlay.querySelector("#type");
    const debtInput = overlay.querySelector("#debt");

    const editBtn = overlay.querySelector(".edit-btn");
    let isEditing = false; // Trạng thái chỉnh sửa
    let originalData = {
        name, address, email, phone, type, debt
    }; // Dữ liệu gốc để so sánh

    editBtn.addEventListener("click", () => {
        const isDisabled = nameInput.disabled;
        nameInput.disabled = addressInput.disabled = emailInput.disabled = phoneInput.disabled = typeSelect.disabled = debtInput.disabled = !isDisabled;
        
        if (isDisabled) {
            // Bắt đầu chỉnh sửa
            editBtn.textContent = "Lưu";
            editBtn.classList.add("save-mode");
            isEditing = true;
        } else {
            // Nhấn lưu
            const keepEditingMode = () => {
                nameInput.disabled = addressInput.disabled = emailInput.disabled = phoneInput.disabled = typeSelect.disabled = debtInput.disabled = false;
                editBtn.textContent = "Lưu";
                editBtn.classList.add("save-mode");
                isEditing = true;
            };

            if (!nameInput.value || !addressInput.value || !emailInput.value || !phoneInput.value || !typeSelect.value) {
                alert("Vui lòng điền đầy đủ thông tin.");
                keepEditingMode();
                return;
            }
            if (!isValidEmail(emailInput.value)) {
                alert("Email không hợp lệ!");
                keepEditingMode();
                return;
            }
            if (!isValidPhone(phoneInput.value)) {
                alert("Số điện thoại không hợp lệ!");
                keepEditingMode();
                return;
            }
            const debtRaw = debtInput.value.trim();
            // Kiểm tra người dùng có nhập chuỗi không hợp lệ
            if (!/^\d+$/.test(debtRaw)) {
                alert("Vui lòng nhập số tiền nợ!");
                keepEditingMode();
                return;
            }
            const parsedDebt = Number(debtRaw);
            if (!Number.isInteger(parsedDebt) || parsedDebt < 0) {
                alert("Vui lòng nhập số tiền nợ!");
                keepEditingMode();
                return;
            }
            
            row.children[2].textContent = nameInput.value;
            row.children[3].textContent = phoneInput.value;
            row.children[4].textContent = typeSelect.value;
            row.children[5].textContent = parsedDebt.toLocaleString("vi-VN");
            row.setAttribute('data-address', addressInput.value);
            row.setAttribute('data-email', emailInput.value);

            // Vô hiệu hóa các trường nhập liệu
            nameInput.disabled = addressInput.disabled = emailInput.disabled = phoneInput.disabled = typeSelect.disabled = debtInput.disabled = true;
            
            // Đặt lại nút sửa
            editBtn.textContent = "Sửa";
            editBtn.classList.remove("save-mode");
            isEditing = false;

            // Cập nhật dữ liệu gốc
            originalData = {
                name: nameInput.value,
                address: addressInput.value,
                email: emailInput.value,
                phone: phoneInput.value,
                type: typeSelect.value,
                debt: debtInput.value
            };

            renderTable(); // Gọi lại renderTable để cập nhật phân trang
            // Hiển thị thông báo đã lưu
            showToast("Đã lưu thành công!");
            overlay.remove(); // <-- Đóng popup sau khi lưu
        }
    });

    overlay.querySelector(".cancel-btn").addEventListener("click", () => {
        const currentData = {
            name: nameInput.value,
            address: addressInput.value,
            email: emailInput.value,
            phone: phoneInput.value,
            type: typeSelect.value,
            debt: debtInput.value
        };
        // Kiểm tra nếu đang chỉnh sửa và dữ liệu thay đổi
        const dataChanged = JSON.stringify(currentData) !== JSON.stringify(originalData);

        if (isEditing && dataChanged) {
            const confirmExit = confirm("Bạn có muốn thoát khi chưa lưu không?");
            if (!confirmExit) return;
        }

        document.body.removeChild(overlay);
    });
}

function deleteRow(button) {
    const row = button.closest("tr");
    if (confirm("Bạn có chắc muốn xóa khách hàng này?")) {
        row.remove();
    }
    renderTable(); // Gọi lại renderTable để cập nhật phân trang
}

function createNewCustomer() {
    const form = document.createElement("div");
    form.className = "detail-overlay";
    form.innerHTML = `
        <div class="add-form">
            <h2>Thêm khách hàng</h2>
            <div class="form-group">
                <label for="new-name">Họ tên</label>
                <input type="text" id="new-name">
            </div>
            <div class="form-group">
                <label for="new-address">Địa chỉ</label>
                <input type="text" id="new-address">
            </div>
            <div class="form-group">
                <label for="new-email">Email</label>
                <input type="email" id="new-email">
            </div>
            <div class="form-group">
                <label for="new-phone">Điện thoại</label>
                <input type="text" id="new-phone">
            </div>
            <div class="form-group">
                <label for="new-type">Loại</label>
                <select id="new-type">
                    <option value="">Chọn</option>
                    <option value="Thường">Thường</option>
                    <option value="VIP">VIP</option>
                </select>
            </div>
            <div class="form-group">
                <label for="new-debt">Số tiền nợ</label>
                <input type="number" id="new-debt" min="0" value="0">
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
        address: "",
        email: "",
        phone: "",
        type: "",
        debt: "0"
    };

    form.querySelector(".cancel-btn").addEventListener("click", () => {
        // Lấy dữ liệu hiện tại
        const currentData = {
            name: form.querySelector("#new-name").value.trim(),
            address: form.querySelector("#new-address").value.trim(),
            email: form.querySelector("#new-email").value.trim(),
            phone: form.querySelector("#new-phone").value.trim(),
            type: form.querySelector("#new-type").value,
            debt: form.querySelector("#new-debt").value.trim() || "0"
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
        let address = form.querySelector("#new-address").value.trim();
        let email = form.querySelector("#new-email").value.trim();
        let phone = form.querySelector("#new-phone").value.trim();
        let type = form.querySelector("#new-type").value;
        let debt = form.querySelector("#new-debt").value.trim();

        if (!name || !address || !email || !phone || !type) {
            alert("Vui lòng điền đầy đủ thông tin.");
            return;
        }
        if (!isValidEmail(email)) {
            alert("Email không hợp lệ!");
            return;
        }
        if (!isValidPhone(phone)) {
            alert("Số điện thoại không hợp lệ!");
            return;
        }

        // Kiểm tra người dùng có nhập chuỗi không hợp lệ
        if (!/^\d+$/.test(debt)) {
            alert("Vui lòng nhập số tiền nợ!");
            return;
        }
        const parsedDebt = Number(debt);
        if (!Number.isInteger(parsedDebt) || parsedDebt < 0) {
            alert("Vui lòng nhập số tiền nợ!");
            return;
        }

        const table = document.querySelector(".table tbody");
        const newId = generateCustomerId();
        const newRow = document.createElement("tr");

        // Lưu địa chỉ và email vào thuộc tính data- của tr
        newRow.setAttribute("data-address", address);
        newRow.setAttribute("data-email", email);

        newRow.innerHTML = `
            <td></td> <!-- STT sẽ được JS cập nhật -->
            <td>${newId}</td>
            <td>${name}</td>
            <td>${phone}</td>
            <td>${type}</td>
            <td>${Number(debt).toLocaleString("vi-VN")}</td>
            <td class="action-buttons">
                <button class="view-btn" onclick="viewCustomer('${newId}')">Xem</button>
                <button class="delete-btn" onclick="deleteRow(this)">Xóa</button>
            </td>
        `;

        table.appendChild(newRow);
        renderTable();
        form.remove();

        // Hiển thị thông báo đã lưu
        showToast("Đã thêm thành công!");
    });
}

function generateCustomerId() {
    const rows = document.querySelectorAll(".table tbody tr");
    const usedNumbers = [];
    rows.forEach(row => {
        const id = row.children[1].textContent;
        const num = parseInt(id.replace("KH", ""));
        if (!isNaN(num)) usedNumbers.push(num);;
    });
    // Tìm số nhỏ nhất chưa dùng
    let next = 1;
    while (usedNumbers.includes(next)) {
        next++;
    }
    return `KH${next.toString().padStart(3, "0")}`;
}

document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.querySelector(".search-input");
    const filterSelect = document.querySelector(".filter-select");
    const tableBody = document.querySelector(".table tbody");

    function filterCustomers() {
        currentPage = 1; // Reset về trang đầu khi lọc
        renderTable(); // Gọi lại renderTable để cập nhật phân trang
    }

    searchInput.addEventListener("input", filterCustomers);
    filterSelect.addEventListener("change", filterCustomers);

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
let currentSort = "id"; // mặc định sắp xếp theo mã KH

function getAllRows() {
    return Array.from(document.querySelectorAll(".table tbody tr"));
}

function renderTable() {
    // Lấy dữ liệu lọc
    const searchInput = document.querySelector(".search-input");
    const filterSelect = document.querySelector(".filter-select");
    const keyword = searchInput ? searchInput.value.toLowerCase() : "";
    const typeFilter = filterSelect ? filterSelect.value : "all";

    // Lấy tất cả dòng (không ẩn)
    let rows = getAllRows();

    // LỌC
    rows = rows.filter(row => {
        const cells = [
            row.children[1]?.textContent.toLowerCase() || "", // Mã KH
            row.children[2]?.textContent.toLowerCase() || "", // Họ tên
            row.children[3]?.textContent.toLowerCase() || "", // Số điện thoại
            row.children[4]?.textContent.toLowerCase() || "", // Loại
        ];
        const matchesKeyword = cells.some(text => text.includes(keyword));
        const matchesType = (typeFilter === "all" || cells[3] === typeFilter.toLowerCase());
        return matchesKeyword && matchesType;
    });

    // Sắp xếp
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

// Dropdown cho sắp xếp Nợ
const sortPriceBtn = document.getElementById("sortPriceBtn");
const sortPriceMenu = document.getElementById("sortPriceMenu");

// Gán nhãn cho từng lựa chọn
const sortDebtLabels = {
    "debt-asc": "Nợ: Tăng dần",
    "debt-desc": "Nợ: Giảm dần"
};

sortPriceBtn.addEventListener("click", (e) => {
    e.stopPropagation();
    sortPriceMenu.classList.toggle("show");
    sortPriceBtn.classList.toggle("sort-dropdown-active");
});

// Ẩn dropdown khi click ra ngoài
document.addEventListener("click", () => {
    sortPriceMenu.classList.remove("show");
    sortPriceBtn.classList.remove("sort-dropdown-active");
});

// Chọn sắp xếp theo nợ
document.querySelectorAll(".sort-dropdown-item").forEach(item => {
    item.addEventListener("click", function(e) {
        document.querySelectorAll(".sort-btn").forEach(b => b.classList.remove("active"));
        document.querySelectorAll(".sort-dropdown-item").forEach(i => i.classList.remove("active"));
        this.classList.add("active");
        sortPriceBtn.classList.add("sort-dropdown-active");
        currentSort = this.getAttribute("data-sort");
        currentPage = 1;
        // Đổi nhãn nút chính
        sortPriceBtn.querySelector(".label").textContent = sortDebtLabels[currentSort];
        sortPriceBtn.classList.add("active");
        renderTable();
        sortPriceMenu.classList.remove("show");
    });
});

// Đổi lại sự kiện cho các nút sort-btn thường (trừ sortPriceBtn)
document.querySelectorAll(".sort-btn:not(.sort-dropdown-toggle)").forEach(btn => {
    btn.addEventListener("click", function() {
        document.querySelectorAll(".sort-btn").forEach(b => b.classList.remove("active"));
        document.querySelectorAll(".sort-dropdown-item").forEach(i => i.classList.remove("active"));
        this.classList.add("active");
        sortPriceBtn.classList.remove("sort-dropdown-active");
        // Đặt lại nhãn nút "Nợ" về mặc định
        sortPriceBtn.querySelector(".label").textContent = "Tiền nợ ";
        sortPriceBtn.classList.remove("active");
        currentSort = this.getAttribute("data-sort");
        currentPage = 1;
        renderTable();
    });
});