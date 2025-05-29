<?php
session_start();
include __DIR__ . '/../../connect.php';

$danhMucArr = [];
// Lấy danh sách khách hàng từ cơ sở dữ liệu
$result = $mysqli->query("SELECT MaKH, HoTen, SDT, Loai, SoTienNo FROM khachhang");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Lưu toàn bộ thông tin của khách hàng theo mã khách
        $danhMucArr[$row['MaKH']] = [
            'HoTen' => $row['HoTen'],
            'SDT' => $row['SDT'],
            'Loai' => $row['Loai'],
            'SoTienNo' => $row['SoTienNo']
        ];
    }
}
$result->free();


$result = $mysqli->query("SELECT * FROM sach");
?>


<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý sách</title>
  <link rel="stylesheet" href="../../style.css" type="text/css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
  <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
</head>
<body>

  <div class="main-content">
    <div class="header">
      <div class="search-filter">
        <input type="text" id="searchTenKH" name="ten_khachhang" placeholder="Tìm theo tên...">
        <input type="text" id="searchLoaiKH" name="loai_khachhang" placeholder="Tìm theo loại khách hàng...">
        <input type="text" id="searchSDT" name="sdt" placeholder="Tìm theo số điện thoại...">
      </div>
      <button class="add-button" onclick="createNewCustomer()">+ Thêm khách hàng</button>
    </div>

  <table id="customerTable">
    <thead>
      <tr>
        <th>Mã khách hàng</th>
        <th>Họ tên</th>
        <th>Số điện thoại</th>
        <th>Loại</th>
        <th>Số tiền nợ</th>
        <th>Thao tác</th>
      </tr>
    </thead>
    <tbody id="customerTableBody">
      <?php foreach ($danhMucArr as $maKH => $khachHang): ?>
        <tr>
          <td><?php echo htmlspecialchars($maKH); ?></td>
          <td><?php echo htmlspecialchars($khachHang['HoTen']); ?></td>
          <td><?php echo htmlspecialchars($khachHang['SDT']); ?></td>
          <td><?php echo htmlspecialchars($khachHang['Loai']); ?></td>
          <td><?php echo htmlspecialchars($khachHang['SoTienNo']); ?></td>
          <td class="action-buttons">
            <button class="view-btn" onclick="openForm(
              '<?= $maKH ?>',
              '<?= $khachHang['HoTen'] ?>',
              '<?= $khachHang['SDT'] ?>',
              '<?= $khachHang['Loai'] ?>',
              '<?= $khachHang['SoTienNo'] ?>'
            )">Xem</button>
            <button class="delete-btn" onclick="deleteCustomer('<?= $maKH ?>')">Xóa</button>

          </td>

        </tr>
      <?php endforeach; ?>
    </tbody>
    </table>
    <div id="toast"></div>
  </div>

  <div id="customerFormOverlay" class="overlay">
    <form id="customerForm" class="form-popup" onsubmit="return false;">
      <h2>Thêm Khách Hàng Mới</h2>
      <label for="ma_kh">Mã Khách Hàng:</label>
      <input type="text" name="ma_kh" id="ma_kh" required>

      <label for="ho_ten">Họ Tên:</label>
      <input type="text" name="ho_ten" id="ho_ten" required>

      <label for="sdt">Số Điện Thoại:</label>
      <input type="text" name="sdt" id="sdt" required>

      <label for="loai">Loại Khách Hàng:</label>
      <select name="loai" id="loai" required>
        <option value="Thường">Thường</option>
        <option value="VIP">VIP</option>
      </select>

      <label for="so_tien_no">Số Tiền Nợ:</label>
      <input type="number" name="so_tien_no" id="so_tien_no" required>

      <input type="hidden" name="form_mode" id="form_mode" value="new">

      <div class="form-buttons">
          <button type="submit" class="btn-save" onclick="saveCustomer()" style="display: none;">Lưu</button>
          <button type="button" class="btn-cancel" onclick="closeForm()">Đóng</button>
        </div>
      </form>
  </div>
  <script>
    let customerFormOverlay = document.getElementById('customerFormOverlay');
    let customerForm = document.getElementById('customerForm');
    let formModeInput = document.getElementById('form_mode');

 function openForm(maKH, hoTen, sdt, loai, soTienNo) {
  customerFormOverlay.classList.add('show');
  formModeInput.value = 'edit';
  customerForm.reset();
  customerForm.querySelector('input[name="ma_kh"]').value = maKH || '';
  customerForm.querySelector('input[name="ho_ten"]').value = hoTen || '';
  customerForm.querySelector('input[name="sdt"]').value = sdt || '';
  customerForm.querySelector('select[name="loai"]').value = loai || 'Thường';
  customerForm.querySelector('input[name="so_tien_no"]').value = soTienNo || '';

  // Set all fields to readonly/disabled (view mode)
  customerForm.querySelectorAll('input').forEach(input => input.setAttribute('readonly', true));
  customerForm.querySelector('select[name="loai"]').setAttribute('disabled', true);

  // Adjust buttons
  const saveButton = document.querySelector('.btn-save');
  const cancelButton = document.querySelector('.btn-cancel');
  let editButton = document.querySelector('.btn-edit');

  if (!editButton) {
    editButton = document.createElement('button');
    editButton.type = 'button';
    editButton.className = 'btn-edit';
    editButton.textContent = 'Sửa';
    editButton.onclick = enableEditing;
    document.querySelector('.form-buttons').insertBefore(editButton, cancelButton);
  }

  saveButton.style.display = 'none';
  editButton.style.display = 'inline-block';
  cancelButton.style.display = 'inline-block';
}

function closeForm() {
  customerFormOverlay.classList.remove('show'); // Remove the 'show' class
}

function enableEditing() {
  // Chỉ cho phép sửa các trường ngoại trừ mã khách hàng
  customerForm.querySelectorAll('input:not([name="ma_kh"])').forEach(input => input.removeAttribute('readonly'));
  customerForm.querySelector('select[name="loai"]').removeAttribute('disabled');
  document.querySelector('.btn-save').style.display = 'inline-block';
  document.querySelector('.btn-edit').style.display = 'none';
}

// Button Lưu khách hàng
function saveCustomer() {
  const form = document.forms['customerForm'];
  const formData = new FormData(form);
  fetch('save_customer.php', {
    method: 'POST',
    body: formData,
  })
  .then(res => res.text())
  .then(response => {
    if (response.trim() === 'OK') {
      showToast('Lưu thông tin thành công!');
      setTimeout(() => { location.reload(); }, 1000);
    } else if (response.trim() === 'customer_exists') {
      alert('Khách hàng đã tồn tại! Bạn có thể cập nhật lại thông tin khách hàng.');
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

    //Button Thêm khách hàng mới
    function createNewCustomer() {
  const customerFormOverlay = document.getElementById('customerFormOverlay');
  const formModeInput = document.getElementById('form_mode');
  const form = document.getElementById('customerForm');

  if (!customerFormOverlay || !formModeInput || !form) {
    console.error('Form elements not found!');
    return;
  }

  form.reset();

  const table = document.getElementById("customerTable").getElementsByTagName("tbody")[0];
  let nextId = 1;
  const existingIds = Array.from(table.rows).map(row => row.cells[0].textContent.trim());

  while (existingIds.includes("KH" + String(nextId).padStart(3, '0'))) {
    nextId++;
  }

  form.ma_kh.value = "KH" + String(nextId).padStart(3, '0');

  // Hiển thị form và thiết lập chế độ
  customerFormOverlay.classList.add('show');
  formModeInput.value = 'new';

  // Reset các trường
  document.getElementById('ho_ten').value = '';
  document.getElementById('sdt').value = '';
  document.getElementById('loai').value = 'Thường';
  document.getElementById('so_tien_no').value = '';

  // Hiện nút Lưu & Đóng (nếu đã ẩn trước đó)
  document.querySelector('.btn-save').style.display = 'inline-block';
  document.querySelector('.btn-close').style.display = 'inline-block';
}



    //Hiển thị tin nhắn thông báo
    function showToast(message) {
      const toast = document.getElementById("toast");
      toast.textContent = message;
      toast.classList.add("show");
      setTimeout(() => toast.classList.remove("show"), 3000);
    }

    document.addEventListener("keydown", e => {
      if (e.key === "Escape") closeForm();
    });

    document.getElementById("customerFormOverlay").addEventListener("click", e => {
      if (e.target === e.currentTarget) closeForm();
    });

    
    document.querySelectorAll('button').forEach(btn => {
      btn.addEventListener('click', e => {
        const circle = document.createElement('span');
        circle.classList.add('ripple');
        circle.style.left = `${e.offsetX}px`;
        circle.style.top = `${e.offsetY}px`;
        btn.appendChild(circle);
        setTimeout(() => circle.remove(), 600);
      });
    });

  
    function deleteCustomer(maKH) {
      if (confirm("Bạn có chắc chắn muốn xóa khách hàng này?")) {
        fetch('./delete_customer.php', {
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
  </script>
</body>
</html>
