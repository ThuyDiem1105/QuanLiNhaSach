<?php
session_start();
include __DIR__ . '/../../connect.php';
$result = $mysqli->query("SELECT * FROM nhanvien");
$results = $mysqli->query("SELECT * FROM taikhoan");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý nhân viên</title>
  <link rel="stylesheet" href="../assets/style.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

  <!-- #region STYLE -->
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

  .choices__item--choice.is-highlighted {
    background-color: #84ffc6 !important; 
    color: #0d3c6b !important;
  }
  .choices__inner {
    min-height: 36px !important;
    font-size: 14px;
    padding: 4px 8px !important;
  }
  .choices__list--dropdown .choices__item {
    font-size: 14px;
    padding: 6px 10px;
  }
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

    tr.linked-highlight {
    /* These properties should mimic your tbody tr:hover styles */
    /* For example, if your hover styles are: */
    transform: scale(1.01);
    background-color: #e8f5e9;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    /* Add any other properties from your :hover that you want to apply */
  }
    .error {
    color: red;
    font-size: 16px;
    margin-top: 4px;
    display: block;
  } 
  </style>
  <!-- #endregion -->
</head>
<body>
  <div class="main-content">
    <div class="header">
      <div class="search-filter">
        <input type="text" id="searchName" name="ten" placeholder="Tìm kiếm theo tên...">
        <input type="text" id="searchPosition" name="chucvu" placeholder="Tìm kiếm theo chức vụ...">
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
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['MaNV']) ?></td>
          <td><?= htmlspecialchars($row['HoTen']) ?></td>
          <td><?= htmlspecialchars($row['ChucVu']) ?></td>
          <td class="action-buttons">
            <button class="view-btn" onclick="openEmployeeForm(
              '<?= $row['MaNV'] ?>',
              '<?= $row['HoTen'] ?>',
              '<?= $row['NgaySinh'] ?>',
              '<?= $row['SDT'] ?>',
              '<?= $row['NoiO'] ?>',
              '<?= $row['ChucVu'] ?>',
              '<?= $row['CaLam'] ?>',
              '<?= $row['Luong'] ?>'
            )">Xem</button>
            <button class="delete-btn" onclick="deleteEmployee('<?= $row['MaNV'] ?>')">Xóa</button>
            <button class="create-account-btn" onclick="createNewAccount('<?= $row['MaNV'] ?>')">Tạo tài khoản</button>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <h3 style="margin-top: 40px; margin-bottom: 20px; color: #2c3e50;">Danh sách tài khoản</h3>
    <div class="header">
      <div class="search-filter">
        <input type="text" id="searchUsername" name="tendn" placeholder="Tìm kiếm theo tên tài khoản...">
        <input type="text" id="searchRole" name="quyen" placeholder="Tìm kiếm theo quyền...">
      </div>
    </div>
    <table id="accountTable">
        <thead>
            <tr>
                <th>Mã NV</th>
                <th>Tên đăng nhập</th>
                <th>Email</th>
                <th>Quyền</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $results->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['MaNV']) ?></td>
          <td><?= htmlspecialchars($row['TenDN']) ?></td>
          <td><?= htmlspecialchars($row['Email']) ?></td>
          <td><?= htmlspecialchars($row['Quyen']) ?></td>
          <td class="action-buttons">
            <button class="view-btn" onclick="openAccountForm(
              '<?= $row['MaNV'] ?>',
              '<?= $row['TenDN'] ?>',
              '<?= $row['Email'] ?>',
              '<?= $row['Quyen'] ?>',
              '<?= $row['MatKhau'] ?>',
              '<?= $row['MatKhauGoc'] ?>'
            )">Xem</button>
            <button class="delete-btn" onclick="deleteAccount('<?= $row['MaNV'] ?>')">Xóa</button>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <div id="toast">Lưu thành công!</div>
  </div>

  <div id="employeeFormOverlay" class="overlay">
    <div class="form-popup">
      <h3>Thông tin nhân viên</h3>
      <form id="employeeForm" onsubmit="return false;" action="" method="post" novalidate>
        <label>Mã NV:</label><input type="text" name="ma_nv" required readonly>
        <span class="error" id="error_ma_nv"></span>
        <label>Họ tên:</label><input type="text" name="ho_ten" required readonly>
        <span class="error" id="error_ho_ten"></span>
        <label>Ngày sinh:</label><input type="date" name="ngay_sinh" required readonly>
        <span class="error" id="error_ngay_sinh"></span>
        <label>SĐT:</label><input type="text" name="sdt" required readonly>
        <span class="error" id="error_sdt"></span>
        <label>Nơi ở:</label><input type="text" name="noi_o" readonly required>
        <span class="error" id="error_noi_o"></span>

        <label for="chuc_vu">Chức vụ:</label>
        <select name="chuc_vu" id="chuc_vu">
          <option value="">-Chọn chức vụ</option>
          <option value="Bán hàng">Bán hàng</option>
          <option value="Thu ngân">Thu ngân</option>
          <option value="Marketing và chăm sóc khách hàng">Marketing và chăm sóc khách hàng</option>
        </select>
        <span class="error" id="error_chuc_vu"></span>

        <label for="luong">Lương:</label>
        <select name="luong" id="luong">
          <option value="">-Chọn mức lương tương ứng</option>
          <option value="25000">Lương hạng D: 25k/giờ</option>
          <option value="35000">Lương hạng C: 35k/giờ</option>
          <option value="50000">Lương hạng B: 50k/giờ</option>
          <option value="65000">Lương hạng A: 65k/giờ</option>
        </select>
        <span class="error" id="error_luong"></span>

        <label>Ca làm (Chọn ca cho từng thứ trong tuần):</label>
        <table id="shiftTable" style="border-collapse: collapse; width: 100%; text-align: center;">
          <thead>
              <tr>
                  <th>Ngày</th>
                  <th>Ca 1 (sáng) từ 7:00-10:30</th>
                  <th>Ca 2 (trưa) từ 10:30-14:00</th>
                  <th>Ca 3 (chiều) từ 14:00-17:30</th>
                  <th>Ca 4 (tối) từ 17:30-21:00</th>
              </tr>
          </thead>
            <tbody>
            <?php
            $days = ['Mon'=>'Thứ 2','Tue'=>'Thứ 3','Wed'=>'Thứ 4','Thu'=>'Thứ 5','Fri'=>'Thứ 6','Sat'=>'Thứ 7','Sun'=>'Chủ nhật'];
            foreach ($days as $key => $label): ?>
            <tr>
                <td><?= $label ?></td>
                <?php for ($i = 1; $i <= 4; $i++): ?>
                <td style="text-align:center">
                <input
                type="checkbox"
                name="shifts[]"
                value="<?= "{$key}-ca{$i}" ?>"
                <?= in_array("{$key}-ca{$i}", $_POST['shifts'] ?? [], true) ? 'checked' : '' ?>>
                </td>
                <?php endfor; ?> 
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <span class="error" id="error_ca_lam"></span>

        <div class="form-buttons">
          <button type="submit" class="btn-save" onclick="saveEmployee()" style="display: none;">Lưu</button>
          <button type="button" class="btn-edit" onclick="enableEditingEmployee()">Sửa</button>
          <button type="button" class="btn-cancel" onclick="closeForm('employeeFormOverlay')">Đóng</button>
        </div>
      </form>
    </div>
  </div>

  <div id="accountFormOverlay" class="overlay">
    <div class="form-popup">
      <h3>Thông tin tài khoản nhân viên</h3>
      <form id="accountForm" onsubmit="return false;" action="" method="post" novalidate>
          <label>Mã Nhân viên:</label><input type="text" name="tk_ma_nv" required readonly>

          <label>Tên đăng nhập:</label><input type="text" name="ten_dn" required>
          <span class="error" id="error_tendn"></span>

          <label>Mật khẩu:</label><input type="password" name="matkhau" required>
          <span class="error" id="error_matkhau"></span>

          <label>Xác nhận mật khẩu:</label><input type="password" name="xacnhan_mk" required>
          <span class="error" id="error_xacnhanmk"></span>

          <label>Địa chỉ Email:</label><input type="email" name="email" required>
          <span class="error" id="error_email"></span>

          <label for="quyen">Quyền:</label>
          <select name="quyen" id="quyen">
              <option value="">-Chọn quyền hợp lệ-</option>
              <option value="Quản trị viên">Quản trị viên</option>
              <option value="Quản lý">Quản lý</option>
              <option value="Nhân viên">Nhân viên</option>
          </select>
          <span class="error" id="error_quyen"></span>

          <div class="form-buttons">
            <button type="submit" class="btn-save" onclick="saveAccount()" style="display: none;">Lưu</button>
            <button type="button" class="btn-edit" onclick="enableEditingAccount()">Sửa</button>
            <button type="button" class="btn-cancel" onclick="closeForm('accountFormOverlay')">Đóng</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    //biến editingIndex dùng để lưu vị trí của dòng đang được click button Xem/Sửa/Xóa
    let editingIndex = -1;
    let editingAccountIndex = -1;
    let chucVuChoices, luongChoices, quyenChoices;

    window.addEventListener("DOMContentLoaded", () => {
      //không sort mà để theo thứ tự đã initilize, ít nên ẩn tìm kiếm
      chucVuChoices = new Choices("#chuc_vu", { shouldSort: false, searchEnabled: false });
      luongChoices = new Choices("#luong", { shouldSort: false, searchEnabled: false });
      quyenChoices = new Choices("#quyen", { shouldSort: false, searchEnabled: false });
      chucVuChoices.disable();
      luongChoices.disable();
      quyenChoices.disable();

      // Add event listeners for row highlighting
      setupRowHighlighting();
    });

    //region NHÂN VIÊN
    function openEmployeeForm(maNV, hoTen, ngaySinh, sdt, noiO, chucVu, caLam, luong) {
      const form = document.forms['employeeForm'];
      form.ma_nv.value = maNV;
      form.ho_ten.value = hoTen;
      form.ngay_sinh.value = ngaySinh;
      form.sdt.value = sdt;
      form.noi_o.value = noiO;

      form.chuc_vu.value = chucVu;
      chucVuChoices.setChoiceByValue(chucVu);
      //vì lương được lưu ở dạng thập phân trong database nên cần parseInt
      form.luong.value = parseInt(luong);
      luongChoices.setChoiceByValue(String(parseInt(luong))); 

      //tất cả checkbox ca làm đều trống khi load
      const checkboxes = document.querySelectorAll('#shiftTable input[type=checkbox]');
      checkboxes.forEach(cb => cb.checked = false);
      //nếu nhân viên đã có lịch làm việc
      if (caLam) {
        //ca làm được lưu trữ dưới dạng mảng: [Mon-ca1, Tue-ca2] -> ca 1 thứ 2 và ca 2 thứ 3
        const shifts = caLam.split(',');
        shifts.forEach(shift => {
          const trimmedShift = shift.trim();
          const cb = document.querySelector(`#shiftTable input[type="checkbox"][value="${trimmedShift}"]`);
          if (cb) cb.checked = true;
        });
      }

      //lấy vị trí dòng (nhân viên) được chọn để xem
      editingIndex = Array.from(document.querySelector('#employeeTable tbody').rows)
        .findIndex(row => row.cells[0].textContent === maNV);
      
      // hiện tại chỉ được phép xem, không được chỉnh sửa thông tin  
      for (let input of form.querySelectorAll("input")) input.readOnly = true;
      chucVuChoices.disable();
      luongChoices.disable();
      checkboxes.forEach(cb => cb.disabled = true);

      //đang ở chế độ xem nên ẩn nút Lưu
      document.querySelector(".btn-save").style.display = "none";
      document.querySelector(".btn-edit").style.display = "inline-block";
      document.getElementById("employeeFormOverlay").classList.add("show");
      employeeFormOverlay.classList.add("show");
    };

    // Button Sửa nhân viên
    function enableEditingEmployee() {
      const form = document.forms['employeeForm'];
      for (let input of form.querySelectorAll("input")) {
        if (input.name !== "ma_nv") input.readOnly = false;
      }
      chucVuChoices.enable();
      luongChoices.enable();

      const maNV = form.ma_nv.value;
      //reset lại tất cả checkbox trước
      const checkboxes = document.querySelectorAll('#shiftTable input[type=checkbox]');
      checkboxes.forEach(cb => cb.disabled = false);
      //disable những checkbox tương ứng với các ca làm đã được chọn
      fetch(`unavailableShifts.php?ma_nv=${encodeURIComponent(maNV)}`)
        .then(res => res.json())
        .then(unavailable => {
          checkboxes.forEach(cb => {
            if (unavailable.includes(cb.value)) {
              cb.disabled = true;
            }
          });
        });

      document.querySelector(".btn-save").style.display = "inline-block";
      document.querySelector(".btn-edit").style.display = "none";
    }

    //Button Thêm nhân viên
    function createNewEmployee() {
      const form = document.forms['employeeForm'];
      form.reset();
      const table = document.getElementById("employeeTable").getElementsByTagName("tbody")[0];
      const nextId = "NV" + String(table.rows.length + 1).padStart(3, '0');

      form.reset();
      form.ma_nv.value = nextId;
      for (let input of form.querySelectorAll("input")) {
        if (input.name !== "ma_nv") input.readOnly = false;
      }
      document.querySelector(".btn-save").style.display = "inline-block";
      document.querySelector(".btn-edit").style.display = "none";

      editingIndex = -1;
      chucVuChoices.enable();
      luongChoices.enable();
      form.chuc_vu.selectedIndex = 0;
      form.luong.selectedIndex = 0;


      const checkboxes = document.querySelectorAll('#shiftTable input[type=checkbox]');
      checkboxes.forEach(cb => {cb.disabled = false; cb.checked = false;});
      // lấy các ca làm việc đã tồn tại để disable checkboxes tương ứng
      fetch('unavailableShifts.php')
        .then(res => res.json())
        .then(unavailable => {
          checkboxes.forEach(cb => {
            if (unavailable.includes(cb.value)) {
              cb.disabled = true;
            }
          });
        });

      document.getElementById("employeeFormOverlay").classList.add("show");
    }

    // Kiểm tra thông tin form
    function checkValidEmployeeForm(form) {
      let isValid = true;
      document.querySelectorAll(".error").forEach(el => el.textContent = "");

      const maNV = form.ma_nv.value.trim();
      const hoTen = form.ho_ten.value.trim();
      const ngaySinh = form.ngay_sinh.value;
      const sdt = form.sdt.value.trim();
      const noiO = form.noi_o.value.trim();
      const chucVu = form.chuc_vu.value.trim();
      const caLam = Array.from(document.querySelectorAll('#shiftTable input[type=checkbox]')).some(cb => cb.checked);
      const caLamArr = Array.from(document.querySelectorAll('#shiftTable input[type=checkbox]')).map(cb => cb.value);
      var count = 0;
      for(var i = 0; i < caLamArr.length; ++i) count++;
      const luong = parseInt(form.luong.value);
      // Họ tên
      if (!hoTen) {
        document.getElementById("error_ho_ten").textContent = "Họ tên không được để trống!";
        isValid = false;
      }
      // Ngày sinh (must be in the past)
      if (!ngaySinh) {
        document.getElementById("error_ngay_sinh").textContent = "Vui lòng chọn ngày sinh hợp lệ!";
        isValid = false;
      } else if (new Date(ngaySinh) >= new Date()) {
        document.getElementById("error_ngay_sinh").textContent = "Ngày sinh phải trước ngày hiện tại!";
        isValid = false;
      }
      // SĐT (10 digits only)
      if (!sdt) {
        document.getElementById("error_sdt").textContent = "SĐT không được để trống!";
        isValid = false;
      } else if (!/^(?:09|05|03|07|08)[0-9]{8}$/.test(sdt)) {
        document.getElementById("error_sdt").textContent = "Số điện thoại phải bắt đầu với 09/03/05/07/08 và gồm 8 chữ số theo sau!";
        isValid = false;
      }
      // Nơi ở
      if (!noiO) {
        document.getElementById("error_noi_o").textContent = "Nơi ở không được để trống!";
        isValid = false;
      }
      // Chức vụ
      if (!chucVu) {
        document.getElementById("error_chuc_vu").textContent = "Chức vụ không được để trống!";
        isValid = false;
      }
      // Ca làm
      if (!caLam || count <= 3) {
        document.getElementById("error_ca_lam").textContent = "Vui lòng chọn ít nhất 4 ca làm trong một tuần!";
        isValid = false;
      }
      // Lương
      if (!luong) {
        document.getElementById("error_luong").textContent = "Vui lòng chọn mức lương phù hợp!";
        isValid = false;
      }
      return isValid;
    }

    //Button Lưu nhân viên
    function saveEmployee() {
      const form = document.forms['employeeForm'];
      const table = document.getElementById("employeeTable").getElementsByTagName("tbody")[0];

      const maNV = form.ma_nv.value;
      const hoTen = form.ho_ten.value;
      const ngaySinh = form.ngay_sinh.value;
      const sdt = form.sdt.value;
      const noiO = form.noi_o.value;
      const chucVu = form.chuc_vu.value;
      const luong = form.luong.value;

      if(!checkValidEmployeeForm(form)) return;

      const checkedShifts = [];
      document.querySelectorAll('#shiftTable input[type=checkbox]:checked').forEach(cb => {
        checkedShifts.push(cb.value);
      });
      const caLam = checkedShifts.join(',');

      const formData = new FormData(form);
      formData.append("ca_lam", caLam);
      formData.append("chuc_vu", chucVu);
      formData.append("luong", luong);
      fetch('save_employee.php', {
        method: "POST",
        body: formData,
      })
      .then(res => res.text())
      .then(response => {
        console.log("Raw response:", response);
        if(response.trim() === "OK") {
          showToast("Cập nhật thông tin thành công!");
          setTimeout(() => {
            location.reload();
          }, 1000);
        closeForm("employeeFormOverlay");
        setupRowHighlighting();

        } else if (response.trim() === "sdt_exists") {
          document.getElementById("error_sdt").textContent = "SĐT đã tồn tại! Vui lòng nhập số khác.";
        } else {
          alert("Lỗi: " + response);
        }
      })
      .catch(error => {
        console.error("Lỗi: ", error);
        alert("Lỗi khi gửi dữ liệu.");
      });
      // if (editingIndex >= 0) {
      //   const row = table.rows[editingIndex];
      //   row.cells[0].textContent = maNV;
      //   row.cells[1].textContent = hoTen;
      //   row.cells[2].textContent = chucVu;
      // } else {
      //   const row = table.insertRow();
      //   row.classList.add("new-row");
      //   row.innerHTML = `
      //     <td>${maNV}</td>
      //     <td>${hoTen}</td>
      //     <td>${chucVu}</td>
      //     <td class="action-buttons">
      //       <button class="view-btn" onclick="openForm('${maNV}', '${hoTen}', '${ngaySinh}', '${sdt}', '${noiO}', '${chucVu}', '${caLam}', '${luong}')">Xem</button>
      //       <button class="delete-btn" onclick="deleteRow(this)">Xóa</button>
      //     </td>
      //   `;
      // }
    }

    //what is this FE guys???? currently i dont use this tho
    // function deleteRow(button) {
    //   if (confirm("Bạn có chắc muốn xóa nhân viên này không?")) {
    //     const row = button.closest("tr");
    //     row.style.transition = "opacity 0.4s ease";
    //     row.style.opacity = 0;
    //     setTimeout(() => row.remove(), 400);
    //   }
    // }

    // Button Xóa nhân viên
    function deleteEmployee(maNV) {
      if (confirm("Bạn có chắc muốn xóa nhân viên này không? Xóa nhân viên sẽ xóa luôn tài khoản nhân viên và lịch làm việc của nhân viên đó.")) {
        fetch('delete_employee.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: 'ma_nv=' + encodeURIComponent(maNV),
        })
        .then(res => res.text())
        .then(response => {
          if(response.trim() === "OK") {
            showToast("Xóa nhân viên thành công!");
            setTimeout(() => {
            location.reload();
            }, 1000);
          } else {
            alert("Lỗi: " + response);
          }
        })
        .catch(error => {
          console.error("Lỗi: ", error);
          alert("Lỗi khi xóa nhân viên.");
        });
      }
    }

    //Tìm kiếm 
    document.getElementById("searchName").addEventListener("input", filterTable);
    document.getElementById("searchPosition").addEventListener("input", filterTable);

    function filterTable() {
      const nameFilter = document.getElementById("searchName").value.toLowerCase();
      const positionFilter = document.getElementById("searchPosition").value.toLowerCase();

      const rows = document.querySelectorAll("#employeeTable tbody tr");

      rows.forEach(row => {
        const name = row.cells[1].textContent.toLowerCase();
        const position = row.cells[2].textContent.toLowerCase();

        const matchName = name.includes(nameFilter);
        const matchPosition = position.includes(positionFilter);

        if (matchName && matchPosition) {
          row.style.display = "";
        } else {
          row.style.display = "none";
        }
      });
    }    
    //endregion

    //region HÀM BỔ TRỢ UI
    function closeForm(formID) {
      document.getElementById(formID).classList.remove("show");
    }
    //Hiển thị tin nhắn thông báo
    function showToast(message, isError = false) {
        const toast = document.getElementById("toast");
        toast.textContent = message;

        toast.classList.remove('show', 'error-toast');

        if (isError) {
            toast.classList.add('error-toast');
        } else {
            toast.classList.remove('error-toast');
        }

        toast.classList.add("show");
        setTimeout(() => {
            toast.classList.remove("show");
            toast.classList.remove('error-toast');
        }, 3000);
    }

    document.getElementById("employeeFormOverlay").addEventListener("click", e => {
      if (e.target === e.currentTarget) closeForm("employeeFormOverlay");
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

    function setupRowHighlighting() {
        const employeeTableRows = document.querySelectorAll('#employeeTable tbody tr');
        const accountTableRows = document.querySelectorAll('#accountTable tbody tr');

        employeeTableRows.forEach(row => {
            row.classList.remove('linked-highlight');
            row.onmouseenter = null;
            row.onmouseleave = null;
        });
        accountTableRows.forEach(row => {
            row.classList.remove('linked-highlight');
            row.onmouseenter = null;
            row.onmouseleave = null;
        });

        employeeTableRows.forEach(empRow => {
            const empId = empRow.cells[0].textContent;

            empRow.onmouseenter = () => {
                accountTableRows.forEach(accRow => {
                    if (accRow.cells[0].textContent === empId) {
                        accRow.classList.add('linked-highlight');
                    }
                });
            };

            empRow.onmouseleave = () => {
                accountTableRows.forEach(accRow => {
                    if (accRow.cells[0].textContent === empId) {
                        accRow.classList.remove('linked-highlight');
                    }
                });
            };
        });

        accountTableRows.forEach(accRow => {
            const accEmpId = accRow.cells[0].textContent;

            accRow.onmouseenter = () => {
                employeeTableRows.forEach(empRow => {
                    if (empRow.cells[0].textContent === accEmpId) {
                        empRow.classList.add('linked-highlight');
                    }
                });
            };

            accRow.onmouseleave = () => {
                employeeTableRows.forEach(empRow => {
                    if (empRow.cells[0].textContent === accEmpId) {
                        empRow.classList.remove('linked-highlight');
                    }
                });
            };
        });
    }
    //endregion

    //region TÀI KHOẢN
    function openAccountForm(maNV, tenDN, email, quyen, matkhauhash, matkhaugoc) {
      const form = document.forms['accountForm'];
      form.tk_ma_nv.value = maNV;
      form.ten_dn.value = tenDN;
      form.email.value = email;
      form.quyen.value = quyen;
      quyenChoices.setChoiceByValue(quyen);
      form.matkhau.value = matkhaugoc;
      form.xacnhan_mk.value = matkhaugoc;

      //lấy vị trí dòng (nhân viên) được chọn để xem
      editingAccountIndex = Array.from(document.querySelector('#accountTable tbody').rows)
        .findIndex(row => row.cells[0].textContent === maNV);
      
      // hiện tại chỉ được phép xem, không được chỉnh sửa thông tin  
      for (let input of form.querySelectorAll("input")) input.readOnly = true;
      quyenChoices.disable();

      //đang ở chế độ xem nên ẩn nút Lưu
      form.querySelector(".btn-save").style.display = "none";
      form.querySelector(".btn-edit").style.display = "inline-block";
      document.getElementById("accountFormOverlay").classList.add("show");
      accountFormOverlay.classList.add("show");
    };

    // Button Sửa tài khoản
    function enableEditingAccount() {
      const form = document.forms['accountForm'];
      for (let input of form.querySelectorAll("input")) {
        if (input.name !== "tk_ma_nv" && input.name !== "matkhau" && input.name !== "xacnhan_mk") input.readOnly = false;
      }
      quyenChoices.enable();

      form.querySelector(".btn-save").style.display = "inline-block";
      form.querySelector(".btn-edit").style.display = "none";
    }

    //Button Thêm tài khoản
    function createNewAccount(maNV) {
      const form = document.forms['accountForm'];
      form.reset();
      const table = document.getElementById("accountTable").getElementsByTagName("tbody")[0];
      form.tk_ma_nv.value = maNV;
      for (let input of form.querySelectorAll("input")) {
        if (input.name !== "tk_ma_nv") input.readOnly = false;
      }
      form.querySelector(".btn-save").style.display = "inline-block";
      form.querySelector(".btn-edit").style.display = "none";

      editingIndex = -1;
      quyenChoices.enable();
      form.quyen.selectedIndex = 0;
      document.getElementById("accountFormOverlay").classList.add("show");
    }

    // Kiểm tra thông tin form
    function checkValidAccountForm(form) {
      let isValid = true;
      document.querySelectorAll(".error").forEach(el => el.textContent = "");

      const maNV = form.tk_ma_nv.value.trim();
      const tenDN = form.ten_dn.value.trim();
      const email = form.email.value.trim();
      const matKhau = form.matkhau.value.trim();
      const xacnhanMK = form.xacnhan_mk.value.trim();
      const quyen = form.quyen.value;

      // Tên đăng nhập
      if (!tenDN) {
        document.getElementById("error_tendn").textContent = "Tên đăng nhập không được để trống!";
        isValid = false;
      }
      // Email
      const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!email) {
        document.getElementById("error_email").textContent = "Vui lòng nhập đầy đủ địa chỉ email!";
        isValid = false;
      } else if (!regex.test(email)) {
        document.getElementById("error_email").textContent = "Vui lòng nhập địa chỉ email hợp lệ!";
        isValid = false;
      }
      const regexx = /^(?=.{8,20}$)(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).*$/;
      // Mật khẩu
      if(!matKhau){
        document.getElementById("error_matkhau").textContent = "Vui lòng nhập mật khẩu!";
        isValid = false;
      } else if (!regexx.test(matKhau)) {
        document.getElementById("error_matkhau").textContent = "Mật khẩu phải chứa ít nhất một kí tự thường, một kí tự hoa, một số và một kí tự đặc biệt. Có độ dài từ 8-20 kí tự.";
        isValid = false;
      }
      // Xác nhận mật khẩu
      if(!xacnhanMK || xacnhanMK !== matKhau){
        document.getElementById("error_xacnhanmk").textContent = "Mật khẩu phải khớp với mật khẩu đã nhập ở trên!";
        isValid = false;
      }
      // Quyền
      if (!quyen) {
        document.getElementById("error_quyen").textContent = "Vui lòng phân quyền!";
        isValid = false;
      }
      return isValid;
    }

    //Button Lưu tài khoản
    function saveAccount() {
      const form = document.forms['accountForm'];
      const table = document.getElementById("accountTable").getElementsByTagName("tbody")[0];

      const maNV = form.tk_ma_nv.value.trim();
      const tenDN = form.ten_dn.value.trim();
      const email = form.email.value.trim();
      const matKhau = form.matkhau.value.trim();
      const xacnhanMK = form.xacnhan_mk.value.trim();
      const quyen = form.quyen.value;
      
      if(!checkValidAccountForm(form)) return;

      const formData = new FormData(form);
      formData.append("quyen", quyen);
      fetch('save_account.php', {
        method: "POST",
        body: formData,
      })
      .then(res => res.text())
      .then(response => {
        console.log("Raw response:", response);
        if(response.trim() === "OK") {
          showToast("Lưu thông tin thành công!");
          setTimeout(() => {
            location.reload();
          }, 1000);
          closeForm("accountFormOverlay");
          setupRowHighlighting();

        } else if (response.trim() === "tenDN_exists") {
          document.getElementById("error_tendn").textContent = "Tên đăng nhập đã tồn tại. Vui lòng nhập tên khác!";
        } else if (response.trim() === "email_exists") {
          document.getElementById("error_email").textContent = "Email đã tồn tại. Vui lòng nhập email khác!";
        } else {
          alert("Lỗi: " + response);
        }
      })
      .catch(error => {
        console.error("Lỗi: ", error);
        alert("Lỗi khi gửi dữ liệu.");
      });
      // if (editingIndex >= 0) {
      //   const row = table.rows[editingIndex];
      //   row.cells[0].textContent = maNV;
      //   row.cells[1].textContent = hoTen;
      //   row.cells[2].textContent = chucVu;
      // } else {
      //   const row = table.insertRow();
      //   row.classList.add("new-row");
      //   row.innerHTML = `
      //     <td>${maNV}</td>
      //     <td>${hoTen}</td>
      //     <td>${chucVu}</td>
      //     <td class="action-buttons">
      //       <button class="view-btn" onclick="openForm('${maNV}', '${hoTen}', '${ngaySinh}', '${sdt}', '${noiO}', '${chucVu}', '${caLam}', '${luong}')">Xem</button>
      //       <button class="delete-btn" onclick="deleteRow(this)">Xóa</button>
      //     </td>
      //   `;
      // }
    }

    // Button Xóa tài khoản
    function deleteAccount(maNV) {
      if (confirm("Bạn có chắc muốn xóa tài khoản này không?")) {
        fetch('delete_account.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: 'ma_nv=' + encodeURIComponent(maNV),
        })
        .then(res => res.text())
        .then(response => {
          if(response.trim() === "OK") {
            showToast("Xóa tài khoản thành công!");
            setTimeout(() => {
            location.reload();
            }, 1000);
          } else {
            alert("Lỗi: " + response);
          }
        })
        .catch(error => {
          console.error("Lỗi: ", error);
          alert("Lỗi khi xóa tài khoản.");
        });
      }
    }

    document.getElementById("accountFormOverlay").addEventListener("click", e => {
      if (e.target === e.currentTarget) closeForm("accountFormOverlay");
    });

    //Tìm kiếm 
    document.getElementById("searchUsername").addEventListener("input", filterTable);
    document.getElementById("searchRole").addEventListener("input", filterTable);

    function filterTable() {
      const tendnFilter = document.getElementById("searchUsername").value.toLowerCase();
      const quyenFilter = document.getElementById("searchRole").value.toLowerCase();

      const rows = document.querySelectorAll("#accountTable tbody tr");

      rows.forEach(row => {
        const tendn = row.cells[1].textContent.toLowerCase();
        const quyen = row.cells[3].textContent.toLowerCase();

        const matchUsername = tendn.includes(tendnFilter);
        const matchRole = quyen.includes(quyenFilter);

        if (matchUsername && matchRole) {
          row.style.display = "";
        } else {
          row.style.display = "none";
        }
      });
    }    
    //endregion
  </script>
</body>
</html>