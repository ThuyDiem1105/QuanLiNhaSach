<?php
/* session_start(); if (isset($_POST['account_loggedin'])){     header('Location: ../loginFunction/mainPage.php'); } */
include __DIR__ . '/../../database_connect.php';

$result = $mysqli->query("SELECT MaSach, TenSach FROM sach WHERE SoLuongTon < 300");
$bookIds = [];
while ($book = $result->fetch_assoc()) {
  $bookIds[$book['MaSach']] = $book['TenSach'];
}
$result->free();

$result = $mysqli->query("SELECT * FROM phieunhap");


?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý phiếu nhập</title>
  <link rel="stylesheet" href="../assets/style.css" />
  <style> 
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      background: linear-gradient(135deg, #f9fff6, #edfaf9, #d8ddd3);
      background-size: 400% 400%;
      animation: gradientShift 15s ease infinite;
    }

/*
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f8f9fa;
    }

    i[data-lucide] {
      width: 18px;
      height: 18px;
      color: inherit;
    } */

    .main-content {
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
      font-family: fontweb;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 6px;
      transition: all 0.25s ease;
    }

    .search-filter input:hover {
      border-color: #81c784;
      background-color: #f0fff4;
      box-shadow: 0 0 8px rgba(129, 199, 132, 0.3);
    }/*

    .search-filter input:hover {
  border-color: #81c784;
  background-color: #f0fff4;
  box-shadow: 0 0 8px rgba(129, 199, 132, 0.3);
  transition: all 0.25s ease;
}
    body {
      background: linear-gradient(135deg, #f4ffef, #e8fffd, #d2daca);
      background-size: 400% 400%;
      animation: gradientShift 15s ease infinite;
    } */ /*
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
    }*/
    .add-button {
      background-color: #c8ffe5;
      font-family: fontweb;
      color: #0d3c6b;
      padding: 8px 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 500;
      transition: all 0.2s ease;
    }

    .add-button:hover {
      transform: scale(1.05);
      filter: brightness(1.1);
    }

    button {
      position: relative;
      overflow: hidden;
    }

    .ripple {
      position: absolute;
      background: rgba(255, 255, 255, 0.6);
      border-radius: 50%;
      transform: scale(0);
      animation: rippleAnim 0.6s linear;
      width: 100px;
      height: 100px;
      pointer-events: none;
    }
    @keyframes rippleAnim {
      to {
        transform: scale(2.5);
        opacity: 0;
      }
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
      background-color: #c8ffe5;
      color: #0d3c6b;
    }

    tbody tr {
      transition: transform 0.2s ease, background-color 0.2s ease;
    }

    tbody tr:hover {
      transform: scale(1.01);
      background-color: #e8f5e9;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      cursor: pointer;
    }

    tr.new-row {
      animation: highlightNewRow 1.5s ease;
    }

    @keyframes highlightNewRow {
      from { background-color: #d6f0f1; }
      to { background-color: transparent; }
    }

    .action-buttons button {
      margin-right: 5px;
      padding: 6px 10px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-family: fontweb;
      transition: all 0.2s ease;
    }

    .action-buttons button:hover {
      transform: scale(1.05);
      filter: brightness(1.1);
    }

    .view-btn { background-color: #64b5f6; color: white; }
    .edit-btn { background-color: #ffb74d; color: white; }
    .delete-btn { background-color: #e57373; color: white; }

    /* Overlay */
    .overlay {
      position: fixed;
      top: 0; left: 0;
      width: 100vw; height: 100vh;
      background: rgba(0, 0, 0, 0.3);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 9999;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.4s ease;
      backdrop-filter: blur(6px);
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
      border: 1px solid #81c784;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
      position: relative;
      transform: translateY(-20px) scale(0.98);
      opacity: 0;
      transition: all 0.4s ease;
    }

    .overlay.show .form-popup {
      animation: popupIn 0.4s ease forwards;
    }

    @keyframes popupIn {
      0% { transform: scale(0.95); opacity: 0; }
      100% { transform: scale(1); opacity: 1; }
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

    .btn-save,
    .btn-cancel,
    .btn-edit {
      padding: 10px 20px;
      font-family: fontweb;
      font-size: 14px;
      border-radius: 8px;
      border: none;
      cursor: pointer;
      font-weight: bold;
      transition: all 0.2s ease;
    }

    .btn-save { background-color: #81c784; color: white; }
    .btn-cancel { background-color: #e57373; color: white; }
    .btn-edit { background-color: #ffb74d; color: white; }

    .btn-save:hover { background-color: #66bb6a; transform: scale(1.05); }
    .btn-cancel:hover { background-color: #ef5350; transform: scale(1.05); }
    .btn-edit:hover { background-color: #ffa726; transform: scale(1.05); }

    /* Toast */
    #toast {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background-color: #b3ffdc;
      color: #1c5083;
      padding: 12px 20px;
      border-radius: 8px;
      opacity: 0;
      transform: translateY(20px);
      z-index: 10000;
      font-size: 14px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
      transition: opacity 0.4s ease, transform 0.4s ease;
    }

    #toast.show {
      opacity: 1;
      transform: translateY(0);
    }

    .error {
      color: red;
      font-size: 16px;
      margin-top: 4px;
      display: block;
    }

    /*.action-buttons button {
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
      background: rgba(0, 0, 0, 0.3);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 9999;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.4s ease;
      backdrop-filter: blur(6px);
    }

    .overlay.show {
      opacity: 1;
      pointer-events: all;
    }
    .form-popup {
      transform: translateY(-20px) scale(0.98);
      opacity: 0;
      transition: all 0.4s ease;
      box-shadow: 0 20px 50px rgba(0,0,0,0.2);
      border: none;
    }
    .overlay.show .form-popup {
      transform: translateY(0) scale(1);
      opacity: 1;
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

    .btn-save, .btn-cancel, .btn-edit {
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

    .btn-edit {
      background-color: #ffb74d;
      color: white;
    }

    .btn-save:hover {
      background-color: #66bb6a;
    }

    .btn-cancel:hover {
      background-color: #ef5350;
    }

    .btn-edit:hover {
      background-color: #ffa726;
    }
    tbody tr:hover {
      background-color: #f1f8e9;
      transition: background-color 0.25s ease;
      cursor: pointer;
    }

    @keyframes fadeSlideIn {
      from {
        opacity: 0;
        transform: translateY(-30px) scale(0.95);
      }
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    .overlay.show .form-popup {
      animation: fadeSlideIn 0.5s ease forwards;
    }
    @keyframes highlightNewRow {
      from { background-color: #dcedc8; }
      to { background-color: transparent; }
    }

    tr.new-row {
      animation: highlightNewRow 1.5s ease;
    }

    .action-buttons button,
    .add-button,
    .btn-save,
    .btn-cancel,
    .btn-edit {
      transition: all 0.2s ease;
      transform: scale(1);
    }

    .action-buttons button:hover,
    .add-button:hover,
    .btn-save:hover,
    .btn-cancel:hover,
    .btn-edit:hover {
      transform: scale(1.05);
      filter: brightness(1.1);
    }
    
    @keyframes popupIn {
      0% { transform: scale(0.95); opacity: 0; }
      100% { transform: scale(1); opacity: 1; }
    }
    .overlay.show .form-popup {
      animation: popupIn 0.4s ease forwards;
    }
    
    


    #toast {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background-color: #4caf50;
  color: white;
  padding: 12px 20px;
  border-radius: 8px;
  opacity: 0;
  transition: opacity 0.4s ease, transform 0.4s ease;
  transform: translateY(20px);
  z-index: 10000;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  font-size: 14px;
}

#toast.show {
  opacity: 1;
  transform: translateY(0);
}

    */
  </style>
</head>
<body>
  <div class="main-content">
    <div class="header">
      <div class="search-filter">
        <input type="text" id="timMaPN" name="mapn" placeholder="Tìm mã phiếu...">
        <input type="date" id="timNgayNhap" name="ngaynhap" placeholder="Ngày nhập...">
      </div>
      <button class="add-button" onclick="createNewReceipt()">+ Thêm phiếu nhập</button>
    </div>
    <table id="receiptTable">
      <thead>
        <tr>
          <th>Mã phiếu</th>
          <th>Ngày lập phiếu</th>
          <th>Ngày nhập</th>
          <th>Tổng tiền</th>
          <th>Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($receipts = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($receipts['MaPN']) ?></td>
          <td><?= htmlspecialchars($receipts['NgayLapPhieu']) ?></td>
          <td><?= htmlspecialchars($receipts['NgayNhap']) ?></td>
          <td><?= htmlspecialchars($receipts['TongTien']) ?></td>
          <td class="action-buttons">
            <button class="view-btn" onclick="openReceiptForm('<?= $receipts['MaPN'] ?>')">Xem</button>
            <!-- <button class="view-btn" onclick="openReceiptForm(
              '<?= $receipts['MaPN'] ?>',
              '<?= $receipts['NgayLapPhieu'] ?>',
              '<?= $receipts['NgayNhap'] ?>',
              '<?= $receipts['TongTien'] ?>',
            )">Xem</button> -->
            <!-- <button class="delete-btn" onclick="deleteReceipt('<?= $row['MaPN'] ?>')">Xóa</button> -->
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    <div id="toast"></div>
  </div>

  <div id="receiptFormOverlay" class="overlay">
    <div class="form-popup">
      <h3>Chi tiết phiếu nhập</h3>
      <form id="receiptForm" name="receiptForm" onsubmit="return false;" action="" method="post" novalidate>
        <label>Mã phiếu:</label><input type="text" name="ma_pn" required readonly>

        <div id="ngay_lap" style="display: none;">
          <label>Ngày lập phiếu:</label>
          <input type="date" name="ngay_lap" readonly>
        </div>

        <label>Ngày nhập sách:</label><input type="date" name="ngay_nhap" required readonly>
        <span class="error" id="error_ngaynhap"></span>

        <label>Nhập thông tin đầu sách được nhập:</label>
        <table id="booksReceiptTable" style="border-collapse: collapse; width: 100%; text-align: center;">
          <thead>
            <tr>
              <th>Mã sách nhập</td>
              <th>Số lượng nhập</th>
              <th>Đơn giá nhập</th>
              <th>Thành tiền</th>
            </tr>
          </thead>
          <tbody id="book-rows">
          </tbody>
        </table>
        <span class="error" id="error_sach"></span>
        <button type="button" id="btnAdd" onclick="addBookRow()" style="display:none;" title="Thêm đầu sách">➕</button>

        <label>Tổng tiền phiếu nhập:</label><input type="text" name="tong_tien" required readonly>
        <span class="error" id="error_tongtien"></span>

        <div class="form-buttons">
          <!-- <button type="button" class="btn-edit" id="btnEdit" onclick="enableEdit()" style="display:none;">Sửa</button> -->
          <button type="submit" class="btn-save" id="btnSave" onclick="saveReceipt()" style="display:none;">Lưu</button>
          <button type="button" class="btn-cancel" onclick="closeForm()">Đóng</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    let editingIndex = -1;

    // Mở form thể hiện chi tiết thông tin phiếu nhập
    function openReceiptForm(maPN){
      fetch(`receipt_details.php?ma_pn=${maPN}`)
      .then(res => res.json())
      .then(data => {
        console.log("Fetched data:", data); 
        const form = document.forms["receiptForm"];
        form.ma_pn.value = maPN;
        form.ngay_lap.value = data.receipt.NgayLapPhieu;
        form.ngay_nhap.value = data.receipt.NgayNhap;
        form.tong_tien.value = data.receipt.TongTien;

        // Vì một phiếu nhập có thể có nhiều sách nên sẽ có một bảng liệt kê các đầu sách được nhập
        const tbody = document.querySelector("#booksReceiptTable tbody");
        tbody.innerHTML = "";
        data.receiptBooks.forEach(book => {
          const row = document.createElement("tr");
          row.innerHTML = `
            <td>${book.MaSach}</td>
            <td>${book.SoLuong}</td>
            <td>${book.DonGiaNhap}</td>
            <td>${book.ThanhTien}</td>
          `;
          tbody.appendChild(row);
        });

        toggleReadonly(true);
        document.getElementById("ngay_lap").style.display = "block";
        document.getElementById("btnAdd").style.display = "none";
        document.getElementById("btnAdd").style.display = "none";
        document.getElementById("btnSave").style.display = "none";
        document.getElementById("receiptFormOverlay").classList.add("show");
      });
    }
    // function openForm(maPhieu, maSach, soLuong, donGia, index = -1) {
    //   const form = document.forms.receiptForm;
    //   form.ma_phieu.value = maPhieu;
    //   form.ma_sach.value = maSach;
    //   form.so_luong.value = soLuong;
    //   form.don_gia.value = donGia;
    //   form.thanh_tien.value = soLuong * donGia;

    //   editingIndex = index;

    //   toggleReadonly(true);
    //   document.getElementById("btnSave").style.display = "none";
    //   document.getElementById("btnEdit").style.display = "inline-block";

    //   document.getElementById("receiptFormOverlay").classList.add("show");
    // }

    // function enableEdit() {
    //   toggleReadonly(false);
    //   document.getElementById("btnSave").style.display = "inline-block";
    //   document.getElementById("btnEdit").style.display = "none";
    // }

    function toggleReadonly(state) {
      const form = document.forms["receiptForm"];
      form.ma_pn.readOnly = true;
      form.ngay_lap.readOnly = state;
      form.ngay_nhap.readOnly = state;
      form.tong_tien.readOnly = true;
    }

    // Kiểm tra các ô thông tin
    function validateInputs(form){
      let isValid = true;
      document.querySelectorAll(".error").forEach(el => el.textContent = "");
      const maPN = form.ma_pn.value;
      const ngayLap = new Date().toISOString().split('T')[0];
      const ngayNhap = form.ngay_nhap.value;
      const tongTien = form.tong_tien.value;

      if(!ngayNhap){
        document.getElementById("error_ngaynhap").textContent = "Vui lòng chọn ngày nhập sách";
        isValid = false;
      }
      if(!tongTien){
        document.getElementById("error_tongtien").textContent = "Lỗi tính toán!";
        isValid = false;
      }

      const rows = document.querySelectorAll("#book-rows tr");
      if (rows.length === 0){
        document.getElementById("error_sach").textContent = "Bạn phải thêm ít nhất một đầu sách vào phiếu nhập!";
        isValid = false;
      }
      rows.forEach((row, index) => {
        const maSach = row.querySelector('[name="ma_sach[]"]').value;
        const soLuong = row.querySelector('[name="so_luong[]"]').value;
        const donGia = row.querySelector('[name="don_gia[]"]').value;
        const thanhTien = row.querySelector('[name="thanh_tien[]"]').value;

        if (!maSach) {
          document.getElementById("error_sach").textContent = `Vui lòng chọn mã sách cho dòng ${index + 1}`;
          isValid = false;
        }
        if (!soLuong) {
          document.getElementById("error_sach").textContent = `Vui lòng nhập số lượng cho dòng ${index + 1}`;
          isValid = false;
        } else if (soLuong < 150) {
          document.getElementById("error_sach").textContent = `Số lượng nhập tối thiểu cho dòng ${index + 1} phải là 150.`;
          isValid = false;
        }
        if (!donGia) {
          document.getElementById("error_sach").textContent = `Vui lòng nhập giá nhập hợp lệ cho dòng ${index + 1}`;
          isValid = false;
        }
        if(!thanhTien){
          document.getElementById("error_sach").textContent = "Lỗi tính toán!";
          isValid = fasle;
        }
      });

      return isValid;
    }

    //Thêm đầu sách vào phiếu
    function addBookRow() {
      const tbody = document.getElementById('book-rows');
      const row = document.createElement('tr');

      row.innerHTML = `
        <td>
          <select name="ma_sach[]" required>
            <option value="">- Chọn mã sách-</option>
            <?php foreach ($bookIds as $bookId => $bookName): ?>
              <option value="<?= $bookId ?>"><?= $bookId ?></option>
            <?php endforeach; ?>
          </select>
          <span class="error" id="error_masach"></span>
        </td>
        <td>
          <input type="number" name="so_luong[]" min="1" required oninput="updateLineTotal(this)">
          <span class="error" id="error_soluong"></span>
        </td>
        <td>
          <input type="text" name="don_gia[]" min="0" required oninput="updateLineTotal(this)">
          <span class="error" id="error_dongia"></span>
        </td>
        <td><input type="number" name="thanh_tien[]" readonly required"></td>
      `;

      tbody.appendChild(row);
      document.getElementById("btn")
    }

    // Hàm này sẽ tự động update input Thành tiền khi user thay đổi số lượng hoặc đơn giá nhập
    function updateLineTotal(input) {
      //tìm thuộc tính bị thay đổi
      const row = input.closest('tr');
      const quantity = parseInt(row.querySelector('[name="so_luong[]"]').value) || 0;
      const price = parseFloat(row.querySelector('[name="don_gia[]"]').value) || 0;
      const lineTotal = quantity * price;
      row.querySelector('[name="thanh_tien[]"]').value = lineTotal.toFixed(2);
      updateTotal();
    }

    // Hàm này tự động update Tổng tiền của cả phiếu nhập
    function updateTotal() {
      let total = 0;
      const thanhTien = document.querySelectorAll('[name="thanh_tien[]"]');
      thanhTien.forEach(input => {
        total += parseFloat(input.value) || 0;
      });
      document.forms["receiptForm"].tong_tien.value = total.toFixed(2);
    }

    // Mở form thêm phiếu nhập
    function createNewReceipt() {
      const form = document.forms["receiptForm"];
      const table = document.getElementById("receiptTable");
      const nextId = "PN" + String(table.rows.length).padStart(3, '0');

      form.reset();
      form.ma_pn.value = nextId;
      document.getElementById("book-rows").innerHTML = "";
      toggleReadonly(false);

      document.getElementById("btnSave").style.display = "inline-block";
      document.getElementById("ngay_lap").style.display = "none";
      document.getElementById("btnAdd").style.display = "inline-block";

      document.getElementById("receiptFormOverlay").classList.add("show");
      editingIndex = -1;
    }

    // Button Lưu khi thêm phiếu mới
    function saveReceipt() {
      const form = document.forms["receiptForm"];
      const table = document.getElementById("receiptTable").getElementsByTagName("tbody")[0];

      if(!validateInputs(form)) return;

      const maPN = form.ma_pn.value;
      const ngayLap = new Date().toISOString().split('T')[0];
      const ngayNhap = form.ngay_nhap.value;
      const tongTien = parseInt(form.tong_tien.value);

      const maSach = Array.from(document.querySelectorAll('[name="ma_sach[]"]')).map(i => i.value);
      const soLuong = Array.from(document.querySelectorAll('[name="so_luong[]"]')).map(i => parseInt(i.value));
      const donGia = Array.from(document.querySelectorAll('[name="don_gia[]"]')).map(i => parseFloat(i.value));
      const thanhTien = Array.from(document.querySelectorAll('[name="thanh_tien[]"]')).map(i => parseFloat(i.value));

      const payload = {
        ma_pn: maPN,
        ngay_lap: ngayLap,
        ngay_nhap: ngayNhap,
        tong_tien: tongTien,
        books: maSach.map((maSach, i) => ({
          ma_sach: maSach,
          so_luong: soLuong[i],
          don_gia: donGia[i],
          thanh_tien: thanhTien[i]
        }))
      };

      console.log("Sending payload to server:", payload);

      fetch('save_receipt.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
      })
      .then(res => res.text())
      .then(response => {
        console.log("Server response:", response);

        if (response.trim() === "OK") {
          showToast("Lưu thành công!");
          setTimeout(() => {
            location.reload();
          }, 1000);
        } else if(response.trim() === "receipt_exists") {
          alert("Đã tồn tại phiếu nhập. Vui lòng kiểm tra lại!");
        } else {
          alert("Lỗi: " + response);
        }
      })
      .catch(err => {
        console.error("Error:", err);
        alert("Có lỗi xảy ra khi gửi dữ liệu.");
      });
      closeForm();
      // if (editingIndex !== -1) {
      //   const row = table.rows[editingIndex];
      //   row.cells[0].innerText = maPhieu;
      //   row.cells[1].innerText = ngayNhap;
      //   row.cells[2].innerText = thanhTien;
      //   row.cells[3].innerHTML = `
      //     <button class="view-btn" onclick="openForm('${maPhieu}', '${form.ma_sach.value}', ${soLuong}, ${donGia}, ${editingIndex})">Xem</button>
      //     <button class="delete-btn" onclick="deleteRow(this)">Xóa</button>
      //   `;
      // } else {
      //   const row = table.insertRow();
      //   row.classList.add("new-row");
      //   const index = row.rowIndex - 1; 
      //   row.innerHTML = `
      //     <td>${maPhieu}</td>
      //     <td>${ngayNhap}</td>
      //     <td>${thanhTien}</td>
      //     <td class="action-buttons">
      //       <button class="view-btn" onclick="openForm('${maPhieu}', '${form.ma_sach.value}', ${soLuong}, ${donGia}, ${index})">Xem</button>
      //       <button class="delete-btn" onclick="deleteRow(this)">Xóa</button>
      //     </td>
      //   `;
      // }
    }

    //Tìm kiếm 
    document.getElementById("timMaPN").addEventListener("input", filterTable);
    document.getElementById("timNgayNhap").addEventListener("input", filterTable);

    function filterTable() {
      const mapnFilter = document.getElementById("timMaPN").value.toLowerCase();
      const ngaynhapFilter = document.getElementById("timNgayNhap").value.toLowerCase();

      const rows = document.querySelectorAll("#receiptTable tbody tr");

      rows.forEach(row => {
        const mapn = row.cells[0].textContent.toLowerCase();
        const ngaynhap = row.cells[2].textContent.toLowerCase();
        const matchMapn = mapn.includes(mapnFilter);
        const matchNgayNhap= ngaynhap.includes(ngaynhapFilter);

        if (matchMapn && matchNgayNhap) {
          row.style.display = "";
        } else {
          row.style.display = "none";
        }
      });
    }    


    function deleteRow(button) {
      if (confirm("Bạn có chắc muốn xóa phiếu nhập này không?")) {
        const row = button.closest("tr");
        row.style.transition = "opacity 0.4s ease";
        row.style.opacity = 0;
        setTimeout(() => row.remove(), 400);
      }
    }

    //region UI

    document.querySelectorAll('button').forEach(btn => {
      btn.addEventListener('click', function (e) {
        const circle = document.createElement('span');
        circle.classList.add('ripple');
        this.appendChild(circle);
        circle.style.left = `${e.offsetX}px`;
        circle.style.top = `${e.offsetY}px`;
        setTimeout(() => circle.remove(), 600);
      });
    });

    function closeForm() {
      document.getElementById("receiptFormOverlay").classList.remove("show");
    }

    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape") {
        closeForm();
      }
    });

    document.getElementById("receiptFormOverlay").addEventListener("click", function (e) {
      if (e.target === this) {
        closeForm();
      }
    });
    function showToast(message) {
      const toast = document.getElementById("toast");
      toast.textContent = message;
      toast.classList.add("show");

      setTimeout(() => {
        toast.classList.remove("show");
      }, 3000);
    }

    //endregion

    </script>
</body>
</html>
