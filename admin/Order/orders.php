<?php
session_start();
include __DIR__ . '/../../connect.php';
if (!isset($_SESSION['loggedin']) && $_SESSION['role'] === 'Admin'){     
    header('Location: ../../loginFunction/login.php'); 
}

$result = $mysqli->query("SELECT * FROM quydinh ORDER BY NgayTao DESC LIMIT 1");
$latestRule = $result->fetch_assoc();

$result->free();
$result = $mysqli->query("SELECT MaSach, TenSach, GiaBan, SoLuongTon FROM sach");
$books = [];
while ($book = $result->fetch_assoc()) {
  $books[] = $book;
}
$result->free();

$result = $mysqli->query("SELECT * FROM hoadon");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý hóa đơn</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
  <link rel="stylesheet" href="../../assets/style.css" type="text/css">
  <style>
    /* Reset */
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

    /* Layout */
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
    }

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

    /* Table */
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
      font-size: 14px;
      margin-top: 4px;
      display: block;
    }
  </style>
</head>

<body>
  <div class="main-content">
    <div class="header">
      <div class="search-filter">
        <input type="text" id="timMaHD" name="mahd" placeholder="Tìm mã hóa đơn...">
      </div>
      <button class="add-button" onclick="createNewOrder()">+ Thêm hóa đơn</button>
    </div>

    <table id="orderTable">
      <thead>
        <tr>
          <th>Mã HĐ</th>
          <th>Mã KH</th>
          <th>Ngày lập</th>
          <th>Tổng tiền</th>
          <th>Đã thanh toán</th>
          <th>Còn lại</th>
          <th>Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <?php while($theOrder = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($theOrder['MaHD']) ?></td>
          <td><?= htmlspecialchars($theOrder['MaKH']) ?></td>
          <td><?= htmlspecialchars($theOrder['NgayLap']) ?></td>
          <td><?= htmlspecialchars($theOrder['TongTien']) ?></td>
          <td><?= htmlspecialchars($theOrder['TienTra']) ?></td>
          <td><?= htmlspecialchars($theOrder['TienNo']) ?></td>
          <td class="action-buttons">
            <button class="view-btn" onclick="openOrderForm('<?= $theOrder['MaHD'] ?>', '<?= $theOrder['MaKH'] ?>')">Xem</button>
            <!-- <button class="delete-btn" onclick="deleteRow(this)">Xóa</button> -->
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    <div id="toast"></div>
  </div>

  <div id="orderFormOverlay" class="overlay">
    <div class="form-popup">
      <h3>Thông tin hóa đơn</h3>
      <form id="orderForm" name="orderForm" onsubmit="return false;" action="" method="post" novalidate>
        <label>Mã Hóa đơn:</label><input type="text" name="ma_hd" readonly required>

        <label>Mã Khách hàng:</label><input type="text" name="ma_kh" readonly oninput="findCustomer(this.value)">
        <span class="error" id="error_makh"></span>

        <label>Tên Khách hàng:</label><input type="text" name="ten_kh" readonly required>
        <span class="error" id="error_tenkh"></span>

        <div id="ngay_lap" style="display: none;">
          <label>Ngày lập phiếu:</label>
          <input type="date" name="ngay_lap" readonly>
        </div>

        <label>Thông tin sách đã mua:</label>
        <table id="booksOrderTable" style="border-collapse: collapse; width: 100%; text-align: center;">
          <thead>
            <tr>
              <th>Mã sách</th>
              <th>Tên sách</th>
              <th>Số lượng</th>
              <th>Đơn giá bán</th>
              <th>Thành tiền</th>
            </tr>
          </thead>
          <tbody id="book-rows"></tbody>
        </table>
        <span class="error" id="error_sach"></span>
        <button type="button" id="btnAdd" onclick="addBookRow()" style="display:none;" title="Thêm sách">➕</button>

        <label>Tổng tiền:</label><input type="text" name="tong_tien" oninput="updateDebt()" readonly required>
        <span class="error" id="error_tongtien"></span>

        <label>Số tiền đã thanh toán:</label><input type="text" name="tien_tra" oninput="updateDebt()" readonly required>
        <span class="error" id="error_tientra"></span>

        <label>Số tiền còn nợ:</label><input type="text" name="tien_no" readonly required>
        <span class="error" id="error_tienno"></span>

        <div class="form-buttons">
          <button type="submit" class="btn-save" onclick="saveOrder()" style="display: none;">Lưu</button>
          <!-- <button type="button" class="btn-edit" onclick="enableEditing()">Sửa</button> -->
          <button type="button" class="btn-cancel" onclick="closeForm()">Đóng</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    let editingIndex = -1;
    const latestRule = <?= json_encode($latestRule) ?>;

    // Mở form show thông tin chi tiết của hóa đơn
    function openOrderForm(maHD, maKH) {
      document.querySelectorAll(".error").forEach(el => el.textContent = "");

      fetch(`order_details.php?ma_hd=${maHD}`)
      .then(res => res.json())
      .then(data => {
        console.log("Fetched data:", data); 
        const form = document.forms["orderForm"];
        form.ma_hd.value = maHD;
        form.ma_kh.value = maKH;
        form.ten_kh.value = data.order.TenKH;
        form.ngay_lap.value = data.order.NgayLap;
        form.tien_tra.value = data.order.TienTra;
        form.tien_no.value = data.order.TienNo;
        form.tong_tien.value = data.order.TongTien;

        // Vì một hóa đơn có thể có nhiều sách nên sẽ có một bảng liệt kê các đầu sách được mua
        const tbody = document.querySelector("#booksOrderTable tbody");
        tbody.innerHTML = "";
        data.orderBooks.forEach(book => {
          const row = document.createElement("tr");
          row.innerHTML = `
            <td>${book.MaSach}</td>
            <td>${book.TenSach}</td>
            <td>${book.SoLuong}</td>
            <td>${book.GiaBan}</td>
            <td>${book.ThanhTien}</td>
          `;
          tbody.appendChild(row);
        });

        editingIndex = Array.from(document.querySelector('#orderTable tbody').rows)
        .findIndex(row => row.cells[0].textContent === maHD);

        for (let input of form.elements) input.readOnly = true;

        document.getElementById("ngay_lap").style.display = "block";
        document.getElementById("btnAdd").style.display = "none";
        document.querySelector(".btn-save").style.display = "none";
        // document.querySelector(".btn-edit").style.display = "inline-block";
        document.getElementById("orderFormOverlay").classList.add("show");
      });
    }

    // Kiểm tra thông tin hợp lệ
    function validateInputs(form){
      let isValid = true;
      document.querySelectorAll(".error").forEach(el => el.textContent = "");

      const maHD = form.ma_hd.value;
      const maKH = form.ma_kh.value;
      const tenKH = form.ten_kh.value;
      const ngayLap = new Date().toISOString().split('T')[0];
      const tongTien = form.tong_tien.value;
      const tienTra = form.tien_tra.value;
      const tienNo = form.tien_no.value;

      const rows = document.querySelectorAll("#book-rows tr");
      if (rows.length === 0){
        document.getElementById("error_sach").textContent = "Bạn phải thêm ít nhất một đầu sách vào hóa đơn!";
        isValid = false;
      }
      rows.forEach((row, index) => {
        const maSachSelect = row.querySelector('[name="ma_sach[]"]');
        const selectedOption = maSachSelect.options[maSachSelect.selectedIndex];
        const tonKho = Number(selectedOption.dataset.ton)

        const tenSach = row.querySelector('[name="ten_sach[]"]').value;
        const soLuong = row.querySelector('[name="so_luong[]"]').value;
        const giaBan = row.querySelector('[name="gia_ban[]"]').value;
        const thanhTien = row.querySelector('[name="thanh_tien[]"]').value;

        if (!maSachSelect) {
          document.getElementById("error_sach").textContent = `Vui lòng chọn mã sách cho dòng ${index + 1}`;
          isValid = false;
        }
        if (!soLuong || soLuong <= 0) {
          document.getElementById("error_sach").textContent = `Vui lòng chọn số lượng mua cho dòng ${index + 1}`;
          isValid = false;
        } else if ((tonKho - soLuong) < latestRule.TonMinSauBan){
          document.getElementById("error_sach").textContent = `Lượng tồn kho của sách dòng ${index + 1} là ${tonKho}. 
            Vui lòng điều chỉnh lại số lượng mua để tối thiểu còn trong kho ${latestRule.TonMinSauBan} sau khi mua!`;
          isValid = false;
        }
      });

      if(!maKH){
        document.getElementById("error_makh").textContent = `Vui lòng nhập mã khách hàng!`;
        isValid = false;
      }
      if(!tenKH){
        document.getElementById("error_tenkh").textContent = `Vui lòng nhập tên khách hàng!`;
        isValid = false;
      }

      if(!tienTra){
        document.getElementById("error_tientra").textContent = `Vui lòng nhập số tiền đã thanh toán!`;
        isValid = false;
      }
      return isValid;
    }
    
    // Hàm này tự động lấy tên khách hàng
    function findCustomer(maKH){
      if (!maKH) {
        document.forms["orderForm"].ten_kh.value = "";
        return;
      }
      fetch(`find_customer.php?ma_kh=${encodeURIComponent(maKH)}`)
      .then( res => res.text())
      .then (name => {
        if(name){
          document.forms["orderForm"].ten_kh.value = name;
          document.forms["orderForm"].ten_kh.readOnly = true;
        } else {
          document.forms["orderForm"].ten_kh.value = "";
        }
      })
      .catch(err => {
        console.error("Lỗi không lấy được tên khách hàng tương ứng.");
      });
    }

    //Thêm đầu sách vào hóa đơn
    function addBookRow() {
      const tbody = document.getElementById('book-rows');
      const row = document.createElement('tr');
      row.innerHTML = `
        <td>
          <select name="ma_sach[]" required>
            <option value="">- Chọn mã sách-</option>
            <?php foreach ($books as $book): ?>
              <option value="<?= $book['MaSach'] ?>"
                      data-name="<?= htmlspecialchars($book['TenSach']) ?>"
                      data-ton="<?= $book['SoLuongTon'] ?>"
                      data-price="<?= $book['GiaBan'] ?>">
                <?= $book['MaSach'] ?>
              </option>
            <?php endforeach; ?>
          </select>
        </td>
        <td><input type="text" name="ten_sach[]" required readonly></td>
        <td>
          <input type="number" name="so_luong[]" min="1" required oninput="updateLineTotal(this)">
          <span class="error" id="error_soluong"></span>
        </td>
        <td>
          <input type="text" name="gia_ban[]" min="0" readonly required oninput="updateLineTotal(this)">
          <span class="error" id="error_giaban"></span>
        </td>
        <td><input type="number" name="thanh_tien[]" readonly required"></td>
      `;
      const select = row.querySelector('select[name="ma_sach[]"]');
      const tenSachInput = row.querySelector('input[name="ten_sach[]"]');
      const giaBanInput = row.querySelector('input[name="gia_ban[]"]');

      select.addEventListener("change", function () {
        const selectedOption = this.options[this.selectedIndex];
        const bookName = selectedOption.getAttribute("data-name") || "";
        const bookPrice = selectedOption.getAttribute("data-price") || "";
        tenSachInput.value = bookName;
        giaBanInput.value = bookPrice;
      });
      tbody.appendChild(row);
      document.getElementById("btn")
    }

    // Mở form thêm hóa đơn mới
    function createNewOrder() {
      const form = document.forms['orderForm'];
      const table = document.getElementById("orderTable").getElementsByTagName("tbody")[0];
      const nextId = "HD" + String(table.rows.length + 1).padStart(3, '0');

      form.reset();
      form.ma_hd.value = nextId;
      document.getElementById("book-rows").innerHTML = "";

      for (let input of form.elements){
        if(input.name !== "ma_hd" && input.name !== "tong_tien" && input.name !== "tien_no"){
          input.readOnly = false;
        }
      }
      document.getElementById("ngay_lap").style.display = "none";
      document.getElementById("btnAdd").style.display = "inline-block";
      document.querySelector(".btn-save").style.display = "inline-block";
      // document.querySelector(".btn-edit").style.display = "none";
      editingIndex = -1;
      document.getElementById("orderFormOverlay").classList.add("show");
    }

    // Hàm này sẽ tự động update input Thành tiền khi user thay đổi số lượng hoặc giá bán
    function updateLineTotal(input) {
      //tìm thuộc tính bị thay đổi
      const row = input.closest('tr');
      const quantity = parseInt(row.querySelector('[name="so_luong[]"]').value) || 0;
      const price = parseFloat(row.querySelector('[name="gia_ban[]"]').value) || 0;
      const lineTotal = quantity * price;
      row.querySelector('[name="thanh_tien[]"]').value = lineTotal.toFixed(2);
      updateTotal();
    }

    // Hàm này tự động update Tổng tiền của cả hóa đơn
    function updateTotal() {
      let total = 0;
      const thanhTien = document.querySelectorAll('[name="thanh_tien[]"]');
      thanhTien.forEach(input => {
        total += parseFloat(input.value) || 0;
      });
      document.forms["orderForm"].tong_tien.value = total.toFixed(2);
      updateDebt();
    }

    // Hàm này tự động tính tiền nợ còn thiếu
    function updateDebt(){
      let debt = 0;
      const tongTien = document.forms["orderForm"].tong_tien.value;
      const tienTra = document.forms["orderForm"].tien_tra.value;
      debt = tongTien - tienTra;
      document.forms["orderForm"].tien_no.value = debt.toFixed(2);
    }

    // Button Lưu
    function saveOrder() {
      const form = document.forms['orderForm'];
      const table = document.getElementById("orderTable").getElementsByTagName("tbody")[0];
      if(!validateInputs(form)) return;

      const maHD = form.ma_hd.value;
      const maKH = form.ma_kh.value;
      const tenKH = form.ten_kh.value;
      const ngayLap = new Date().toISOString().split('T')[0];
      const tongTien = form.tong_tien.value;
      const tienTra = form.tien_tra.value;
      const tienNo = form.tien_no.value;

      const maSach = Array.from(document.querySelectorAll('[name="ma_sach[]"]')).map(i => i.value);
      const soLuong = Array.from(document.querySelectorAll('[name="so_luong[]"]')).map(i => parseInt(i.value));
      const giaBan = Array.from(document.querySelectorAll('[name="gia_ban[]"]')).map(i => parseFloat(i.value));
      const thanhTien = Array.from(document.querySelectorAll('[name="thanh_tien[]"]')).map(i => parseFloat(i.value));

      const payload = {
        ma_hd: maHD,
        ma_kh: maKH,
        ten_kh: tenKH,
        ngay_lap: ngayLap,
        tong_tien: tongTien,
        tien_tra: tienTra,
        tien_no: tienNo,
        books: maSach.map((maSach, i) => ({
          ma_sach: maSach,
          so_luong: soLuong[i],
          gia_ban: giaBan[i],
          thanh_tien: thanhTien[i]
        }))
      };

      console.log("Sending payload to server:", payload);

      fetch('save_order.php', {
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
        } else if(response.trim() === "order_exists") {
          alert("Đã tồn tại hóa đơn. Vui lòng kiểm tra lại!");
        } else {
          alert("Lỗi: " + response);
        }
      })
      .catch(err => {
        console.error("Error:", err);
        alert("Có lỗi xảy ra khi gửi dữ liệu.");
      });
      closeForm();
      // if (editingIndex >= 0) {
      //   const row = table.rows[editingIndex];
      //   row.cells[0].textContent = maHD;
      //   row.cells[1].textContent = maKH;
      //   row.cells[2].textContent = ngayLap;
      //   row.cells[3].textContent = tongTien;
      //   row.cells[4].textContent = daThanhToan;
      //   row.cells[5].textContent = conLai;
      // } else {
      //   const row = table.insertRow();
      //   row.classList.add("new-row");
      //   row.innerHTML = `
      //     <td>${maHD}</td>
      //     <td>${maKH}</td>
      //     <td>${ngayLap}</td>
      //     <td>${tongTien}</td>
      //     <td>${daThanhToan}</td>
      //     <td>${conLai}</td>
      //     <td class="action-buttons">
      //       <button class="view-btn" onclick="openForm('${maHD}', '${maKH}', '${ngayLap}', '${tongTien}', '${daThanhToan}', '${conLai}')">Xem</button>
      //       <button class="delete-btn" onclick="deleteRow(this)">Xóa</button>
      //     </td>
      //   `;
      // }
    }

    //Tìm kiếm 
    document.getElementById("timMaHD").addEventListener("input", filterTable);

    function filterTable() {
      const mahdFilter = document.getElementById("timMaHD").value.toLowerCase();
      const rows = document.querySelectorAll("#orderTable tbody tr");

      rows.forEach(row => {
        const mahd = row.cells[0].textContent.toLowerCase();
        const matchMahd = mahd.includes(mahdFilter);

        if (matchMahd) {
          row.style.display = "";
        } else {
          row.style.display = "none";
        }
      });
    }    

    // Hàm này đóng form
    function closeForm() {
      document.getElementById("orderFormOverlay").classList.remove("show");
    }

    // Hàm này hiển thị thông báo 
    function showToast(message) {
      const toast = document.getElementById("toast");
      toast.textContent = message;
      toast.classList.add("show");
      setTimeout(() => toast.classList.remove("show"), 3000);
    }

    document.addEventListener("keydown", e => {
      if (e.key === "Escape") closeForm();
    });

    document.getElementById("orderFormOverlay").addEventListener("click", e => {
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
  </script>
</body>
</html>

<!-- function enableEditing() {
  const form = document.forms['orderForm'];
  for (let input of form.elements) {
    if (input.name !== "ma_hd") input.readOnly = false;
  }
  document.querySelector(".btn-save").style.display = "inline-block";
  document.querySelector(".btn-edit").style.display = "none";
} -->

<!-- function deleteRow(button) {
      if (confirm("Bạn có chắc muốn xóa hóa đơn này không?")) {
        const row = button.closest("tr");
        row.style.transition = "opacity 0.4s ease";
        row.style.opacity = 0;
        setTimeout(() => row.remove(), 400);
      }
    } -->
