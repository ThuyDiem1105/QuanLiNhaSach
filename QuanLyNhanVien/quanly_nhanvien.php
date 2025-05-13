<?php
/*
session_start();
if (isset($_POST['account_loggedin'])){
    header('Location: ../loginFunction/mainPage.php');
} */
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý nhân viên</title>
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }

  body {
    font-family: 'Segoe UI', sans-serif;
    background-color: #f8f9fa;
  }

  .sidebar {
    height: 100vh;
    width: 220px;
    background: linear-gradient(to bottom, #c8e6e0, #dbeaf1);
    color: #34495e;
    position: fixed;
    top: 0;
    left: 0;
    padding: 20px 0;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05);
  }

  .sidebar h2 {
    text-align: center;
    margin-bottom: 30px;
    font-size: 20px;
    color: #2c3e50;
  }

  .sidebar a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 24px;
    color: #2c3e50;
    text-decoration: none;
    transition: all 0.25s ease;
    border-radius: 8px;
    margin: 4px 16px;
    font-weight: 500;
  }

  .sidebar a.active {
    background-color: #b2dfdb;
    font-weight: bold;
    color: #004d40;
  }

  .sidebar a:hover {
    background-color: #aed9e0;
    color: #004d40;
    transform: translateX(4px);
  }

  i[data-lucide] {
    width: 18px;
    height: 18px;
    color: inherit;
  }

  .main-content {
    margin-left: 220px;
    padding: 20px;
  }

  .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }

  .search-filter {
    display: flex;
    gap: 10px;
  }

  .search-filter input {
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 6px;
  }

  .add-button {
    background-color: #81c784;
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.2s ease;
  }

  .add-button:hover {
    background-color: #66bb6a;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    background-color: white;
    border-radius: 6px;
    overflow: hidden;
  }

  th, td {
    border: 1px solid #ddd;
    padding: 12px;
    text-align: left;
  }

  th {
    background-color: #81c784;
    color: white;
  }

  .action-buttons button {
    margin-right: 5px;
    padding: 6px 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }

  .view-btn {
    background-color: #64b5f6;
    color: white;
  }

  .edit-btn {
    background-color: #ffb74d;
    color: white;
  }

  .delete-btn {
    background-color: #e57373;
    color: white;
  }

  .overlay {
    position: fixed;
    top: 0; left: 0;
    width: 100vw; height: 100vh;
    background: rgba(0, 0, 0, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
  }

  .overlay.show {
    opacity: 1;
    pointer-events: all;
  }

  .form-popup {
    background: #fff;
    padding: 32px 24px;
    width: 100%;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    position: relative;
    border: 1px solid #81c784;
  }

  .form-popup::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    height: 6px;
    width: 100%;
    background-color: #81c784;
    border-top-left-radius: 16px;
    border-top-right-radius: 16px;
  }

  .form-popup h3 {
    margin-top: 0;
    margin-bottom: 24px;
    color: #2c3e50;
    text-align: center;
    font-size: 22px;
  }

  .form-popup label {
    display: block;
    margin-top: 16px;
    font-weight: 600;
    color: #444;
    font-size: 14px;
  }

  .form-popup input {
    width: 100%;
    padding: 10px 12px;
    margin-top: 6px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 14px;
  }

  .form-popup input:focus {
    border-color: #81c784;
    outline: none;
    box-shadow: 0 0 6px rgba(129, 199, 132, 0.3);
  }

  .form-popup input[readonly] {
    background-color: #f4f4f4;
    border-color: #ddd;
    color: #666;
    cursor: not-allowed;
  }

  .form-buttons {
    display: flex;
    justify-content: flex-end;
    margin-top: 28px;
    gap: 12px;
  }

  .btn-save, .btn-cancel {
    padding: 10px 20px;
    font-size: 14px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-weight: bold;
  }

  .btn-save {
    background-color: #81c784;
    color: white;
  }

  .btn-cancel {
    background-color: #e57373;
    color: white;
  }

  .btn-save:hover {
    background-color: #66bb6a;
  }

  .btn-cancel:hover {
    background-color: #ef5350;
  }
</style>

</head>
<body>
  <div class="sidebar">
    <h2><i data-lucide="book-open"></i> NHÀ SÁCH</h2>
    <a href="#"><i data-lucide="home"></i> Trang chủ</a>
    <a href="#" class="active"><i data-lucide="user-round"></i> Nhân viên</a>
    <a href="#"><i data-lucide="package-open"></i> Phiếu nhập</a>
    <a href="#"><i data-lucide="book"></i> Sách</a>
    <a href="#"><i data-lucide="receipt-text"></i> Hóa đơn</a>
    <a href="#"><i data-lucide="users"></i> Khách hàng</a>
    <a href="#"><i data-lucide="gift"></i> Khuyến mãi</a>
    <a href="#"><i data-lucide="bar-chart-2"></i> Báo cáo</a>
    <a href="#"><i data-lucide="settings"></i> Quy định</a>
  </div>

  <div class="main-content">
    <div class="header">
      <div class="search-filter">
        <input type="text" placeholder="Tìm kiếm theo tên...">
        <input type="text" placeholder="Chức vụ...">
      </div>
      <button class="add-button" onclick="createNewEmployee()">+ Thêm nhân viên</button>
    </div>
    <table id="employeeTable">
      <thead>
        <tr>
          <th>Mã NV</th>
          <th>Họ tên</th>
          <th>Chức vụ</th>
          <th>Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>NV001</td>
          <td>Nguyễn Văn A</td>
          <td>Quản lý</td>
          <td class="action-buttons">
            <button class="view-btn" onclick="openForm('NV001', 'Nguyễn Văn A', '1995-01-01', '0901234567', 'Hà Nội', 'Quản lý', 'Sáng', '15000000')">Xem</button>
            <button class="delete-btn" onclick="deleteRow(this)">Xóa</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <div id="employeeFormOverlay" class="overlay">
    <div class="form-popup">
      <h3>Thông tin nhân viên</h3>
      <form id="employeeForm" onsubmit="return false;">
        <label>Mã NV:</label><input type="text" name="ma_nv" required readonly>
        <label>Họ tên:</label><input type="text" name="ho_ten" required>
        <label>Ngày sinh:</label><input type="date" name="ngay_sinh" required>
        <label>SĐT:</label><input type="text" name="sdt" required>
        <label>Nơi ở:</label><input type="text" name="noi_o">
        <label>Chức vụ:</label><input type="text" name="chuc_vu">
        <label>Ca làm:</label><input type="text" name="ca_lam">
        <label>Lương:</label><input type="number" name="luong">
        <div class="form-buttons">
          <button type="button" class="btn-save" onclick="saveEmployee()">Lưu</button>
          <button type="button" class="btn-cancel" onclick="closeForm()">Đóng</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    let editingIndex = -1;

    function openForm(maNV, hoTen, ngaySinh, sdt, noiO, chucVu, caLam, luong) {
      editingIndex = -1;
      const form = document.forms.employeeForm;
      form.ma_nv.value = maNV;
      form.ho_ten.value = hoTen;
      form.ngay_sinh.value = ngaySinh;
      form.sdt.value = sdt;
      form.noi_o.value = noiO;
      form.chuc_vu.value = chucVu;
      form.ca_lam.value = caLam;
      form.luong.value = luong;

      document.getElementById("employeeFormOverlay").classList.add("show");
    }

    function createNewEmployee() {
      const form = document.forms.employeeForm;
      const table = document.getElementById("employeeTable");
      const nextId = "NV" + String(table.rows.length).padStart(3, '0');

      form.reset();
      form.ma_nv.value = nextId;
      document.getElementById("employeeFormOverlay").classList.add("show");
      editingIndex = table.rows.length - 1;
    }

    function saveEmployee() {
      const form = document.forms.employeeForm;
      const table = document.getElementById("employeeTable").getElementsByTagName("tbody")[0];
      const maNV = form.ma_nv.value;
      const hoTen = form.ho_ten.value;
      const chucVu = form.chuc_vu.value;

      if (editingIndex >= 0) {
        const row = table.rows[editingIndex];
        row.cells[0].textContent = maNV;
        row.cells[1].textContent = hoTen;
        row.cells[2].textContent = chucVu;
      } else {
        const row = table.insertRow();
        row.innerHTML = `
          <td>${maNV}</td>
          <td>${hoTen}</td>
          <td>${chucVu}</td>
          <td class="action-buttons">
            <button class="view-btn" onclick="openForm('${maNV}', '${hoTen}', '${form.ngay_sinh.value}', '${form.sdt.value}', '${form.noi_o.value}', '${chucVu}', '${form.ca_lam.value}', '${form.luong.value}')">Xem</button>
            <button class="delete-btn" onclick="deleteRow(this)">Xóa</button>
          </td>
        `;
      }

      closeForm();
    }

    function deleteRow(button) {
      if (confirm("Bạn có chắc muốn xóa nhân viên này không?")) {
        const row = button.closest("tr");
        row.remove();
      }
    }

    function closeForm() {
      document.getElementById("employeeFormOverlay").classList.remove("show");
    }

    lucide.createIcons();
  </script>
</body>
</html>
