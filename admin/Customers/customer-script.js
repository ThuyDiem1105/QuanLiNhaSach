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

function saveCustomer() {
    const form = document.getElementById("customerForm"); // Sửa lại đúng form
    const formData = new FormData(form);

    const customerData = {
        ma_kh: formData.get("ma_kh"),
        ten_kh: formData.get("ten_kh"),
        sdt: formData.get("sdt"),
        diachi: formData.get("diachi"),
        email: formData.get("email"),
        loai: formData.get("loai"),
        so_tien_no: formData.get("so_tien_no"),
        form_mode: formData.get("form_mode")
    };

    fetch("save_customers.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(customerData)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showToast("Lưu khách hàng thành công");
            closeForm();
            location.reload(); // hoặc cập nhật lại danh sách khách hàng
        } else {
            showToast("Lỗi: " + result.message);
            // KHÔNG đóng form khi lỗi
        }
    })
    .catch(error => {
        console.error("Lỗi khi gửi:", error);
        showToast("Lỗi kết nối với máy chủ");
        // KHÔNG đóng form khi lỗi
    });
}

    function closeForm() {
        document.getElementById('customerFormOverlay').classList.remove('show');
    }

    function enableEditing() {
        // Chỉ cho phép sửa các trường ngoại trừ mã khách hàng
        customerForm.querySelectorAll('input:not([name="ma_kh"])').forEach(input => input.removeAttribute('readonly'));
        customerForm.querySelector('select[name="loai"]').removeAttribute('disabled');
        document.querySelector('.btn-save').style.display = 'inline-block';
        document.querySelector('.btn-edit').style.display = 'none';
    }

    

// Hiển thị popup readonly khi nhấn button Xem
function openForm(maKH, hoTen, sdt, diachi, email, loai, soTienNo) {
    // Lấy các phần tử form
    const overlay = document.getElementById('customerFormOverlay');
    const customerForm = document.getElementById('customerForm');
    const formModeInput = document.getElementById('form_mode');
    overlay.classList.add('show');
    formModeInput.value = 'view'; // Đặt chế độ view để khi lưu sẽ không cập nhật
    customerForm.querySelector('input[name="ma_kh"]').value = maKH;
    customerForm.querySelector('input[name="ten_kh"]').value = hoTen;
    customerForm.querySelector('input[name="sdt"]').value = sdt;
    customerForm.querySelector('input[name="diachi"]').value = diachi;
    customerForm.querySelector('input[name="email"]').value = email;
    customerForm.querySelector('select[name="loai"]').value = loai;
    customerForm.querySelector('input[name="so_tien_no"]').value = soTienNo;

    // Đặt tất cả các trường về readonly/disabled (chế độ chỉ xem)
    customerForm.querySelectorAll('input, textarea').forEach(input => input.setAttribute('readonly', true));
    customerForm.querySelector('select[name="loai"]').setAttribute('disabled', true);

    // Hiện nút Sửa, Ẩn nút Lưu
    customerForm.querySelector('.btn-save').style.display = 'none';
    customerForm.querySelector('.btn-edit').style.display = 'inline-block';
    customerForm.querySelector('.btn-cancel').style.display = 'inline-block';

    window._formJustOpened = true; // Đánh dấu vừa mở form
}

window.openForm = openForm;

// Hiển thị popup ở chế độ thêm mới khách hàng
function createNewCustomer() {
    const overlay = document.getElementById('customerFormOverlay');
    const customerForm = document.getElementById('customerForm');
    const formModeInput = document.getElementById('form_mode');
    overlay.classList.add('show');
    formModeInput.value = 'add'; // Chế độ thêm mới
    // Reset các trường về rỗng
    customerForm.querySelector('input[name="ma_kh"]').value = '';
    customerForm.querySelector('input[name="ten_kh"]').value = '';
    customerForm.querySelector('input[name="sdt"]').value = '';
    customerForm.querySelector('input[name="diachi"]').value = '';
    customerForm.querySelector('input[name="email"]').value = '';
    customerForm.querySelector('select[name="loai"]').value = '';
    customerForm.querySelector('input[name="so_tien_no"]').value = '';
    // Cho phép nhập tất cả các trường
    customerForm.querySelectorAll('input, textarea').forEach(input => input.removeAttribute('readonly'));
    customerForm.querySelector('select[name="loai"]').removeAttribute('disabled');
    // Hiện nút Lưu, Ẩn nút Sửa
    customerForm.querySelector('.btn-save').style.display = 'inline-block';
    customerForm.querySelector('.btn-edit').style.display = 'none';
    customerForm.querySelector('.btn-cancel').style.display = 'inline-block';

    window._formJustOpened = true; // Đánh dấu vừa mở form
}
window.createNewCustomer = createNewCustomer;

// Ngăn form submit ngoài ý muốn khi chỉ xem hoặc vừa mở form
window.addEventListener('DOMContentLoaded', function() {
    const customerFormEl = document.getElementById('customerForm');
    if (customerFormEl) {
        customerFormEl.addEventListener('submit', function(e) {
            // Nếu vừa mở form, chặn submit đầu tiên
            if (window._formJustOpened) {
                e.preventDefault();
                window._formJustOpened = false;
                console.log('Chặn submit ngoài ý muốn khi vừa mở form');
                return;
            }
            const mode = document.getElementById('form_mode').value;
            if (mode !== 'edit' && mode !== 'add') {
                e.preventDefault();
                console.log('Form submit bị chặn vì đang ở chế độ view');
            }
        });
        // Đảm bảo các nút không phải submit
        customerFormEl.querySelectorAll('.btn-edit, .btn-cancel').forEach(btn => {
            btn.type = 'button';
        });
        // Đảm bảo chỉ nút Lưu là submit
        const btnSave = customerFormEl.querySelector('.btn-save');
        if (btnSave) btnSave.type = 'submit';
    }
});