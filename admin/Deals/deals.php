<?php
session_start();
include __DIR__ . '/../../connect.php';

$danhMucArr = [];
$result = $mysqli->query("SELECT MaKM, TenKM FROM khuyenmai");
while ($row = $result->fetch_assoc()) {
    $danhMucArr[$row['MaKM']] = $row['TenKM'];
}
$result->free();

$result = $mysqli->query("SELECT * FROM khuyenmai");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý khuyến mãi</title>
  <link rel="stylesheet" href="../../style.css" type="text/css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
  <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
</head>
<body>
  <div class="main-content">
    <div class="header">
      <div class="search-filter">
        <input type="text" id="searchTenKM" name="ten_km" placeholder="Tìm theo tên...">
        <input type="text" id="searchNgayBatDau" name="ngay_bat_dau" placeholder="Tìm theo ngày bắt đầu...">
      </div>
      <button class="add-button" onclick="createNewDeal()">+ Thêm khuyến mãi mới</button>
    </div>

    <table id="bookTable">
      <thead>
        <tr>
          <th>Mã khuyến mãi</th>
          <th>Tên khuyến mãi</th>
          <th>Ngày bắt đầu</th>
          <th>Ngày kết thúc</th>
          <th>Điều kiện áp dụng</th>
          <th>Trạng thái</th>
          <th>Chi tiết</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <?php
            $ngayBatDau = date('Y-m-d', strtotime($row['NgayBatDau']));
            $ngayKetThuc = date('Y-m-d', strtotime($row['NgayKetThuc']));
            $trangThai = $row['TrangThai'] ? 'Đang áp dụng' : 'Ngừng áp dụng';
        ?>
        <tr>
          <td><?= htmlspecialchars($row['MaKM']) ?></td>
          <td><?= htmlspecialchars($row['TenKM']) ?></td>
          <td><?= htmlspecialchars(date('d/m/Y', strtotime($row['NgayBatDau']))) ?></td>
          <td><?= htmlspecialchars(date('d/m/Y', strtotime($row['NgayKetThuc']))) ?></td>
          <td><?= htmlspecialchars($row['DieuKienApDung']) ?></td>
          <td><?= htmlspecialchars($trangThai) ?></td>
          <td class="action-buttons">
            <button class="view-btn" onclick="openForm(
              '<?= $row['MaKM'] ?>',
              '<?= $row['TenKM'] ?>',
              '<?= $ngayBatDau ?>',
              '<?= $ngayKetThuc ?>',
              '<?= $row['DieuKienApDung'] ?>',
              '<?= $trangThai ?>'
            )">Xem</button>
            <button class="delete-btn" onclick="deleteKM('<?= $row['MaKM'] ?>')">Xóa</button>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    <div id="toast"></div>
  </div>

  <div id="kmFormOverlay" class="overlay">
    <div class="form-popup">
      <h3>Chi tiết khuyến mãi</h3>
      <form id="kmForm" onsubmit="return false;" action="" method="post" novalidate>
        <input type="hidden" id="form_mode" name="form_mode" value="new">

        <label>Mã khuyến mãi:</label><input type="text" name="ma_km" required readonly>
        <span class="error" id="error_makm"></span>

        <label>Tên khuyến mãi:</label><input type="text" name="ten_km" required readonly>
        <span class="error" id="error_tenkm"></span>

        <label>Ngày bắt đầu:</label><input type="date" name="ngay_bat_dau" required readonly>
        <span class="error" id="error_ngaybatdau"></span>

        <label>Ngày kết thúc:</label><input type="date" name="ngay_ket_thuc" required readonly>
        <span class="error" id="error_ngayketthuc"></span>

        <label>Điều kiện áp dụng:</label><textarea name="dieu_kien_ap_dung" required readonly></textarea>
        <span class="error" id="error_dieukienapdung"></span>

        <label>Trạng thái:</label>
        <select name="trang_thai" required disabled>
          <option value="1">Đang áp dụng</option>
          <option value="0">Ngừng áp dụng</option>
        </select>
        <span class="error" id="error_trangthai"></span>

        <div class="form-buttons">
          <button type="submit" class="btn-save" onclick="saveKM()" style="display: none;">Lưu</button>
          <button type="button" class="btn-edit" onclick="enableEditing()">Sửa</button>
          <button type="button" class="btn-cancel" onclick="closeForm()">Đóng</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    let dealFormOverlay = document.getElementById('kmFormOverlay');
    let dealForm = document.getElementById('kmForm');
    let formModeInput = document.getElementById('form_mode');

    function openForm(maKM, TenKM, NgayBatDau, NgayKetThuc, DieuKienApDung, TrangThai) {
      console.log('openForm called with:', { maKM, TenKM, NgayBatDau, NgayKetThuc, DieuKienApDung, TrangThai });
      dealFormOverlay.classList.add('show');
      formModeInput.value = 'edit';
      dealForm.querySelector('input[name="ma_km"]').value = maKM || '';
      dealForm.querySelector('input[name="ten_km"]').value = TenKM || '';
      dealForm.querySelector('input[name="ngay_bat_dau"]').value = NgayBatDau || '';
      dealForm.querySelector('input[name="ngay_ket_thuc"]').value = NgayKetThuc || '';
      dealForm.querySelector('textarea[name="dieu_kien_ap_dung"]').value = DieuKienApDung || '';
      dealForm.querySelector('select[name="trang_thai"]').value = TrangThai === 'Đang áp dụng' ? '1' : '0';

      // Set all fields to readonly/disabled (view mode)
      dealForm.querySelectorAll('input, textarea').forEach(input => input.setAttribute('readonly', true));
      dealForm.querySelector('select[name="trang_thai"]').setAttribute('disabled', true);

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
      dealFormOverlay.classList.remove('show'); // Remove the 'show' class
    }

    function enableEditing() {
      // Chỉ cho phép sửa các trường ngoại trừ mã khuyến mãi
      dealForm.querySelectorAll('input:not([name="ma_km"]), textarea').forEach(input => input.removeAttribute('readonly'));
      dealForm.querySelector('select[name="trang_thai"]').removeAttribute('disabled');
      document.querySelector('.btn-save').style.display = 'inline-block';
      document.querySelector('.btn-edit').style.display = 'none';
    }

    // Button Lưu khuyến mãi
    function saveKM() {
      const form = document.forms['kmForm'];
      const formData = new FormData(form);
      fetch('save_deals.php', {
        method: 'POST',
        body: formData,
      })
      .then(res => res.text())
      .then(response => {
        if (response.trim() === 'OK') {
          showToast('Lưu thông tin thành công!');
          setTimeout(() => { location.reload(); }, 1000);
        } else if (response.trim() === 'deal_exists') {
          alert('Khuyến mãi đã tồn tại! Bạn có thể cập nhật lại thông tin khuyến mãi.');
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

    //Button Thêm khuyến mãi mới
    function createNewDeal() {
  const dealFormOverlay = document.getElementById('kmFormOverlay');
  const formModeInput = document.getElementById('form_mode');
  const form = document.getElementById('kmForm');

  if (!dealFormOverlay || !formModeInput || !form) {
    console.error('Form elements not found!');
    return;
  }

  form.reset();

  // Sinh mã khuyến mãi mới tự động
  const table = document.getElementById("bookTable").getElementsByTagName("tbody")[0];
  let nextId = 1;
  const existingIds = Array.from(table.rows).map(row => row.cells[0].textContent.trim());
  while (existingIds.includes("KM" + String(nextId).padStart(3, '0'))) {
    nextId++;
  }
  form.ma_km.value = "KM" + String(nextId).padStart(3, '0');

  // Hiển thị form và thiết lập chế độ
  dealFormOverlay.classList.add('show');
  formModeInput.value = 'new';

  // Reset các trường
  form.ten_km.value = '';
  form.ngay_bat_dau.value = '';
  form.ngay_ket_thuc.value = '';
  form.dieu_kien_ap_dung.value = '';
  form.trang_thai.value = '1';

  // Cho phép nhập tất cả các trường (trừ mã khuyến mãi readonly)
  form.querySelectorAll('input, textarea').forEach(input => input.removeAttribute('readonly'));
  form.querySelector('input[name="ma_km"]').setAttribute('readonly', true);
  form.querySelector('select[name="trang_thai"]').removeAttribute('disabled');

  // Hiện nút Lưu & Đóng, ẩn Sửa
  document.querySelector('.btn-save').style.display = 'inline-block';
  document.querySelector('.btn-edit').style.display = 'none';
  document.querySelector('.btn-cancel').style.display = 'inline-block';
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

    document.getElementById("dealFormOverlay").addEventListener("click", e => {
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


    function deleteDeal(maKM) {
      if (confirm("Bạn có chắc chắn muốn xóa khuyến mãi này?")) {
        fetch('./delete_deals.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({ ma_km: maKM })
        })
        .then(response => response.text())
        .then(data => {
          if (data === 'OK') {
            alert('Xóa khuyến mãi thành công.');
            location.reload();
          } else {
            alert('Lỗi khi xóa khuyến mãi: ' + data);
          }
        });
      }
    }
  </script>
</body>
</html>
