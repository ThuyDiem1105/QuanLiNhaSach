<?php
session_start();
include __DIR__ . '/../../connect.php';

// Đọc danh mục sách
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
      <button class="add-button" onclick="createNew()">+ Thêm khuyến mãi mới</button>
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
    function createNew() {
      document.getElementById('kmForm').reset();
      document.getElementById('form_mode').value = 'new';
      document.querySelector('.btn-save').style.display = 'inline-block';
      document.querySelector('.btn-edit').style.display = 'none';
      document.querySelector('.form-popup h3').textContent = 'Thêm khuyến mãi mới';
      openForm();
    }

    function openForm(maKM, tenKM, ngayBatDau, ngayKetThuc, dieuKienApDung, trangThai) {
      const form = document.getElementById('kmForm');
      form.ma_km.value = maKM || '';
      form.ten_km.value = tenKM || '';
      form.ngay_bat_dau.value = ngayBatDau || '';
      form.ngay_ket_thuc.value = ngayKetThuc || '';
      form.dieu_kien_ap_dung.value = dieuKienApDung || '';
      form.trang_thai.value = trangThai === 'Đang áp dụng' ? '1' : '0';

      document.querySelector('.btn-save').style.display = maKM ? 'none' : 'inline-block';
      document.querySelector('.btn-edit').style.display = maKM ? 'inline-block' : 'none';
      document.querySelector('.form-popup h3').textContent = maKM ? 'Chi tiết khuyến mãi' : 'Thêm khuyến mãi mới';

      document.getElementById('kmFormOverlay').style.display = 'block';
    }

    function closeForm() {
      document.getElementById('kmFormOverlay').style.display = 'none';
    }

    function enableEditing() {
      const inputs = document.querySelectorAll('#kmForm input, #kmForm textarea');
      inputs.forEach(input => input.removeAttribute('readonly'));
      document.querySelector('#kmForm select').removeAttribute('disabled');
      document.querySelector('.btn-save').style.display = 'inline-block';
    }

    function saveKM() {
      const formData = new FormData(document.getElementById('kmForm'));

      fetch('save_km.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.text())
      .then(data => {
        if (data === "OK") {
          showToast("Lưu thành công!", "success");
          location.reload();
        } else {
          showToast(data, "error");
        }
      });
    }

    function deleteKM(maKM) {
      if (confirm("Bạn có chắc chắn muốn xóa khuyến mãi này?")) {
        fetch('delete_km.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'ma_km=' + encodeURIComponent(maKM)
        })
        .then(response => response.text())
        .then(data => {
          if (data === 'OK') {
            showToast("Xóa thành công!", "success");
            location.reload();
          } else {
            showToast(data, "error");
          }
        });
      }
    }

    function showToast(message, type) {
      const toast = document.getElementById('toast');
      toast.textContent = message;
      toast.className = type;
      toast.style.display = 'block';
      setTimeout(() => {
        toast.style.display = 'none';
      }, 3000);
    }
  </script>
</body>
</html>
<script>

    document.querySelectorAll('button').forEach(btn => {
      btn.addEventListener('click', function (e) {
        // Xóa hiệu ứng ripple cũ nếu có
        const oldRipple = this.querySelector('.ripple');
        if (oldRipple) oldRipple.remove();
        // Tạo hiệu ứng ripple mới
        const circle = document.createElement('span');
        circle.classList.add('ripple');
        this.appendChild(circle);
        circle.style.left = `${e.offsetX}px`;
        circle.style.top = `${e.offsetY}px`;
        setTimeout(() => circle.remove(), 600);
      });
    });
</script>
