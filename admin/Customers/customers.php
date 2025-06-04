<?php
session_start();
include __DIR__ . '/../../connect.php';

$danhMucArr = [];
$result = $mysqli->query("SELECT MaKH, HoTen FROM khachhang");
while ($row = $result->fetch_assoc()) {
    $danhMucArr[$row['MaKH']] = $row['HoTen'];
}
$result->free();

$result = $mysqli->query("SELECT * FROM khachhang");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý khách hàng</title>
    <link rel="stylesheet" href="../../assets/general-style.css" />
    <link rel="stylesheet" href="../../assets/customers-style.css" />
    <link rel="stylesheet" href="../../style.css" />
    <script src="customers-script.js" defer></script>
</head>
<body>
    <div class="main-content">
        <div class="toolbar">
            <div class="toolbar-row">
                <div class="search-filter-group">
                    <div class="search-box">
                        <input type="text" placeholder="Tìm kiếm khách hàng..." class="search-input" />
                        <button class="search-button">🔍</button>
                    </div>
                    <select class="filter-select">
                        <option value="all">Tất cả</option>
                        <option value="Thường">Khách thường</option>
                        <option value="VIP">Khách VIP</option>
                    </select>
                </div>
                <button class="add-button" onclick="createNewCustomer()">
                    Thêm khách hàng mới
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
                    <button class="sort-btn active" data-sort="id">Mã KH</button>
                    <button class="sort-btn" data-sort="name">Tên KH</button>
                    <div class="sort-dropdown">
                        <button class="sort-btn sort-dropdown-toggle" id="sortPriceBtn">
                            <span class="label">Tiền nợ</span>
                            <span class="arrow">&#9660;</span>
                        </button>
                        <div class="sort-dropdown sort-dropdown-menu" id="sortPriceMenu">
                            <div class="sort-dropdown-item" data-sort="debt-asc">Nợ: Tăng dần</div>
                            <div class="sort-dropdown-item" data-sort="debt-desc">Nợ: Giảm dần</div>
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

    <table class="table">
        <thead>
            <tr>
                <th class="id">Mã khách hàng</th>
                <th>Họ tên</th>
                <th>Số điện thoại</th>
                <th>Loại</th>
                <th class="debt">Số tiền nợ</th>
                <th class="actions">Thao tác</th>
            </tr>
        </thead>
        <tbody>
<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($row['MaKH']) ?></td>
    <td><?= htmlspecialchars($row['HoTen']) ?></td>
    <td><?= htmlspecialchars($row['SDT']) ?></td>
    <td><?= htmlspecialchars($row['Loai']) ?></td>
    <td><?= htmlspecialchars($row['SoTienNo']) ?></td>
    <td class="action-buttons">
        <button class="view-btn" onclick="openForm(
            '<?= $row['MaKH'] ?>',
            '<?= $row['HoTen'] ?>',
            '<?= $row['SDT'] ?>',
            '<?= $row['DiaChi'] ?>',
            '<?= $row['Email'] ?>',
            '<?= $row['Loai'] ?>',
            '<?= $row['SoTienNo'] ?>'
        )">Xem</button>
        <button class="delete-btn" onclick="deleteCustomer('<?= $row['MaKH'] ?>','<?= $row['HoTen'] ?>','<?= $row['SDT'] ?>','<?= $row['DiaChi'] ?>','<?= $row['Email'] ?>','<?= $row['Loai'] ?>','<?= $row['SoTienNo'] ?>')">Xóa</button>
    </td>
</tr>
<?php endwhile; ?>
</tbody>
    </table>
    <div id="toast"></div>
    </div>

    <div id="customerFormOverlay" class="overlay">
        <div class="form-popup">
        <h3>Chi tiết khách hàng</h3>
        <form id="customerForm" onsubmit="return false;" action="" method="post" novalidate>
            <input type="hidden" id="form_mode" name="form_mode" value="new">

            <label>Mã khách hàng:</label><input type="text" name="ma_kh" required readonly>
            <span class="error" id="error_makh"></span>

            <label>Tên khách hàng:</label><input type="text" name="ten_kh" required readonly>
            <span class="error" id="error_tenkh"></span>

            <label>Số điện thoại:</label><input type="text" name="sdt" required readonly>
            <span class="error" id="error_sdt"></span>

            <label>Địa chỉ:</label><input type="text" name="diachi" required readonly>
            <span class="error" id="error_diachi"></span>

            <label>Email:</label><input type="email" name="email" required readonly>
            <span class="error" id="error_email"></span>

            <label>Loại khách hàng:</label>
            <select name="loai" required disabled>
                <option value="Thường">Khách thường</option>
                <option value="VIP">Khách VIP</option>
            </select>
            <span class="error" id="error_loai"></span>

            <label>Số tiền nợ:</label><input type="number" name="so_tien_no" required readonly>
            <span class="error" id="error_sotienno"></span>

            <div class="form-buttons">
            <button type="submit" class="btn-save" onclick="saveCustomer()" style="display: none;">Lưu</button>
            <button type="button" class="btn-edit" onclick="enableEditing()">Sửa</button>
            <button type="button" class="btn-cancel" onclick="closeForm()">Đóng</button>
            </div>
        </form>
        </div>
    </div>
<script>
    let customerFormOverlay = document.getElementById('customerFormOverlay');
    let customerForm = document.getElementById('customerForm');
    let formModeInput = document.getElementById('form_mode');
    let customerFormContent = customerForm.querySelector('.form-popup');

    function openForm(maKH, hoTen, sdt, diachi, email, loai, soTienNo) {
        customerFormOverlay.classList.add('show');
        formModeInput.value = 'edit'; // Đặt chế độ edit để khi lưu sẽ cập nhật
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
        document.querySelector('.btn-save').style.display = 'none';
        document.querySelector('.btn-edit').style.display = 'inline-block';
        document.querySelector('.btn-cancel').style.display = 'inline-block';
    }

    function closeForm() {
        customerFormOverlay.classList.remove('show');
    }

    function enableEditing() {
        // Chỉ cho phép sửa các trường ngoại trừ mã khách hàng
        customerForm.querySelectorAll('input:not([name="ma_kh"])').forEach(input => input.removeAttribute('readonly'));
        customerForm.querySelector('select[name="loai"]').removeAttribute('disabled');
        document.querySelector('.btn-save').style.display = 'inline-block';
        document.querySelector('.btn-edit').style.display = 'none';
    }

    

    function saveCustomer() {
        const form = document.forms['customerForm'];
        const formData = new FormData(form);
        // Giữ nguyên form_mode (new hoặc edit) để backend biết là thêm mới hay cập nhật
        fetch('save_customers.php', {
            method: 'POST',
            body: formData,
        })
        .then(res => res.text())
        .then(response => {
            if (response.trim() === 'OK') {
                showToast('Lưu thông tin thành công!');
                setTimeout(() => { location.reload(); }, 1000);
            } else if (response.trim() === 'Khách hàng đã tồn tại.') {
                alert('Khách hàng đã tồn tại!');
            } else {
                alert('Lỗi: ' + response);
            }
        })
        .catch(error => {
            console.error('Lỗi: ', error);
            alert('Lỗi khi gửi dữ liệu.');
        });
        closeForm();
    }

    function createNewCustomer() {
        const customerFormOverlay = document.getElementById('customerFormOverlay');
        const formModeInput = document.getElementById('form_mode');
        const form = document.getElementById('customerForm');

        if (!customerFormOverlay || !formModeInput || !form) {
            console.error('Một trong các phần tử không tồn tại.');
            return;
        }

        form.reset();

        // Tìm mã khách hàng tiếp theo
        const table = document.querySelector('.table tbody');
        let nextId = 1;
        const existingIds = Array.from(table ? table.rows : []).map(row => row.cells[0].textContent.trim());
        while (existingIds.includes("KH" + String(nextId).padStart(3, '0'))) {
            nextId++;
        }
        form.ma_kh.value = "KH" + String(nextId).padStart(3, '0');
        formModeInput.value = 'new'; // Đặt chế độ thêm mới
        customerFormOverlay.classList.add('show');

        // Reset các trường
        form.ten_kh.value = '';
        form.sdt.value = '';
        form.diachi.value = '';
        form.email.value = '';
        form.loai.value = 'Thường';
        form.so_tien_no.value = '0';

        // Cho phép nhập tất cả các trường (trừ mã KH readonly)
        form.querySelectorAll('input, textarea').forEach(input => input.removeAttribute('readonly'));
        form.querySelector('select[name="loai"]').removeAttribute('disabled');
        form.querySelector('input[name="ma_kh"]').setAttribute('readonly', true);

        document.querySelector('.btn-save').style.display = 'inline-block';
        document.querySelector('.btn-edit').style.display = 'none';
        document.querySelector('.btn-cancel').style.display = 'inline-block';
    }

    function deleteCustomer(maKH) {
      if (confirm("Bạn có chắc chắn muốn xóa khách hàng này?")) {
        fetch('./delete_customers.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({ ma_kh: maKH })
        })
        .then(response => response.text())
        .then(data => {
          if (data === 'OK') {
            alert('Xóa khách hàng thành công.');
            location.reload();
          } else {
            alert('Lỗi khi xóa khách hàng: ' + data);
          }
        });
      }
    }

    function showToast(message) {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    document.addEventListener("keydown", e => {
        if (e.key === "Escape") {
            closeForm();
        }
    });

    document.getElementById("customerFormOverlay").addEventListener("click", e => {
        if (e.target === e.currentTarget) closeForm();
    });

    document.querySelectorAll('button').foreach(btn => {
        btn.addEventListener('click', e => {
            const circle = document.createElement('span');
            circle.classList.add('ripple');
            circle.style.left = `${e.offsetX}px`;
            circle.style.top = `${e.offsetY}px`;
            btn.appendChild(circle);
            setTimeout(() => {
                circle.remove();
            }, 600);
        });
    });


</script>
</body>
</html>


