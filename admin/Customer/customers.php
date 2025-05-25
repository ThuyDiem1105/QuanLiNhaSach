<?php
session_start();
include __DIR__ . '/../../connect.php';
// Đọc danh mục sách
$danhMucArr = [];
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
    <div class="form-popup">
      <h3>Chi tiết khách hàng</h3>
      <form id="customerForm" onsubmit="return false;" action="" method="post" novalidate>
        <input type="hidden" id="form_mode" name="form_mode" value="new">

        <label>Mã khách hàng:</label><input type="text" name="ma_kh" required readonly>
        <span class="error" id="error_makh"></span>

        <label>Họ tên:</label><input type="text" name="ho_ten" required readonly>
        <span class="error" id="error_hoten"></span>

        <label>Số điện thoại:</label><input type="text" name="sdt" required readonly>
        <span class="error" id="error_sdt"></span>

        <label>Loại:</label><input type="text" name="loai" required readonly>
        <select name="loai" id="loai" required>
          <option value="Thường">Thường</option>
          <option value="VIP">VIP</option>
          <option value="Thân thiết">Thân thiết</option>
        </select>
        <span class="error" id="error_loai"></span>

        <label>Số tiền nợ:</label><input type="text" name="so_tien_no" required readonly>
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

    function openForm(maKH, hoTen, sdt, loai, soTienNo) {
      console.log('openForm called with:', { maKH, hoTen, sdt, loai, soTienNo }); // Debug log
      customerFormOverlay.classList.add('show'); // Add the 'show' class
      customerForm.reset();
      formModeInput.value = 'edit';
      customerForm.querySelector('input[name="ma_kh"]').value = maKH;
      customerForm.querySelector('input[name="ho_ten"]').value = hoTen;
      customerForm.querySelector('input[name="sdt"]').value = sdt;
      customerForm.querySelector('select[name="loai"]').value = loai;
      customerForm.querySelector('input[name="so_tien_no"]').value = soTienNo;
      enableEditing();
    }

    function closeForm() {
      customerFormOverlay.classList.remove('show'); // Remove the 'show' class
    }

    function enableEditing() {
      const inputs = customerForm.querySelectorAll('input, select');
      inputs.forEach(input => input.removeAttribute('readonly'));
      document.querySelector('.btn-save').style.display = 'inline-block';
      document.querySelector('.btn-edit').style.display = 'none';
    }

    function createNewCustomer() {
      openForm('', '', '', 'Thường', '');
      formModeInput.value = 'new';
    }

    function saveCustomer() {
      const formData = new FormData(customerForm);
      fetch('./save_customer.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.text())
      .then(data => {
        if (data === "OK") {
          showToast("Lưu thành công!", "success");
          location.reload();
        } else if (data === "book_exists") {
          showToast("Khách hàng đã tồn tại!", "error");
        } else {
          showToast("Lỗi: " + data, "error");
        }
      })
      .catch(error => showToast("Lỗi kết nối: " + error.message, "error"));
    }

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

    function createNewCustomer() {
      window.location.href = 'add_customer.php';
    }
  </script>
</body>
</html>
