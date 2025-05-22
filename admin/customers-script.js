// Hàm xử lý border-bottom cho dòng cuối cùng đang hiển thị
function fixTableBorders() {
    const rows = Array.from(document.querySelectorAll('.customer-table tbody tr'))
        .filter(row => row.style.display !== "none");
    // Đặt lại border-bottom cho tất cả các dòng hiển thị
    rows.forEach(row => row.querySelectorAll('td').forEach(td => td.style.borderBottom = "1px solid #0d3c6b"));
    // Bỏ border-bottom cho dòng cuối cùng hiển thị
    if (rows.length > 0) {
        rows[rows.length - 1].querySelectorAll('td').forEach(td => td.style.borderBottom = "none");
        // Hiện border-bottom cho th
        document.querySelectorAll('.customer-table th').forEach(th => th.style.borderBottom = "1px solid #0d3c6b");
    }
    else {
        // Không có dòng nào hiển thị, ẩn border-bottom của th
        document.querySelectorAll('.customer-table th').forEach(th => th.style.borderBottom = "none");
    }
}

// Thêm vào đầu file hoặc trước các hàm sử dụng
function isValidEmail(email) {
    // Regex kiểm tra email cơ bản
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function isValidPhone(phone) {
    return /^[0-9]{9,11}$/.test(phone); // Ví dụ: chỉ chấp nhận số từ 9–11 chữ số
}

function viewCustomer(customerId) {
    const row = [...document.querySelectorAll(".customer-table tbody tr")]
        .find(tr => tr.children[0].textContent === customerId);

    if (!row) return;

    const id = row.children[0].textContent;
    const name = row.children[1].textContent;
    const phone = row.children[2].textContent;
    const type = row.children[3].textContent;
    const debtRaw = row.children[4].textContent;
    
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
    overlay.className = "customer-detail-overlay";

    overlay.innerHTML = `
        <div class="customer-detail-form">
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
            
            row.children[1].textContent = nameInput.value;
            row.children[2].textContent = phoneInput.value;
            row.children[3].textContent = typeSelect.value;
            row.children[4].textContent = parsedDebt.toLocaleString("vi-VN");
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
}

function createNewCustomer() {
    const form = document.createElement("div");
    form.className = "customer-detail-overlay";
    form.innerHTML = `
        <div class="customer-add-form">
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

    form.querySelector(".cancel-btn").addEventListener("click", () => {
        form.remove(); // Đóng popup khi nhấn Hủy
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

        const table = document.querySelector(".customer-table tbody");
        const newId = generateCustomerId();
        const newRow = document.createElement("tr");

        // Lưu địa chỉ và email vào thuộc tính data- của tr
        newRow.setAttribute("data-address", address);
        newRow.setAttribute("data-email", email);

        newRow.innerHTML = `
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
        fixTableBorders(); // <-- Thêm dòng này ngay sau khi thêm dòng mới
        form.remove();

        // Hiển thị thông báo đã lưu
        showToast("Đã thêm thành công!");
    });
}

function generateCustomerId() {
    const rows = document.querySelectorAll(".customer-table tbody tr");
    let max = 0;
    rows.forEach(row => {
        const id = row.children[0].textContent;
        const num = parseInt(id.replace("KH", ""));
        if (!isNaN(num) && num > max) max = num;
    });
    const next = max + 1;
    return `KH${next.toString().padStart(3, "0")}`;
}

document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.querySelector(".search-input");
    const filterSelect = document.querySelector(".filter-select");
    const tableBody = document.querySelector(".customer-table tbody");

    function filterCustomers() {
        const keyword = searchInput.value.toLowerCase();
        const typeFilter = filterSelect.value;

        const rows = tableBody.querySelectorAll("tr");
        rows.forEach(row => {
            const cells = [
                row.children[0]?.textContent.toLowerCase() || "",
                row.children[1]?.textContent.toLowerCase() || "",
                row.children[2]?.textContent.toLowerCase() || "",
                row.children[3]?.textContent.toLowerCase() || "",
            ];
            const matchesKeyword = cells.some(text => text.includes(keyword));
            const matchesType = (typeFilter === "all" || cells[2] === typeFilter.toLowerCase());

            if (matchesKeyword && matchesType) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
        fixTableBorders();
    }

    searchInput.addEventListener("input", filterCustomers);
    filterSelect.addEventListener("change", filterCustomers);

    // Gọi fixTableBorders khi trang vừa load
    fixTableBorders();
});

function showToast(message) {
    const toast = document.getElementById("toast");
    toast.textContent = message;
    toast.classList.add("show");

    setTimeout(() => {
        toast.classList.remove("show");
    }, 3000);
}