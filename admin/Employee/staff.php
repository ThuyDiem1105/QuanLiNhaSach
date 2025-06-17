<?php
session_start();
include __DIR__ . '/../../connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'Admin'){     
    header('Location: ../../loginFunction/login.php'); 
}

$result = $mysqli->query("SELECT * FROM quydinh ORDER BY NgayTao DESC LIMIT 1");
$latestRule = $result->fetch_assoc();
$result->free();

$result = $mysqli->query("SELECT * FROM nhanvien");
$results = $mysqli->query("SELECT * FROM taikhoan");

$accountEmployeeIds = [];
while ($row = $results->fetch_assoc()) {
    $accountEmployeeIds[] = $row['MaNV'];
}

$results = $mysqli->query("SELECT * FROM taikhoan");

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý nhân viên</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    <link rel="stylesheet" href="../../assets/staff-style.css" type="text/css">
    <link rel="stylesheet" href="../../assets/general-style.css" type="text/css">
</head>

<body>
    <div class="main-content">
        <div class="tab-bar">
            <button class="tab-btn active" id="tab-employee" onclick="showTab('employee')">Quản lý nhân viên</button>
            <button class="tab-btn" id="tab-account" onclick="showTab('account')">Quản lý tài khoản</button>
        </div>
        <div id="employeeTabContent">
            <div class="toolbar">
                <div class="toolbar-row">
                    <div class="search-filter-group">
                        <div class="search-box">
                            <input type="text" id="searchName" name="ten" placeholder="Tìm kiếm theo tên..." class="search-input" />
                            <button class="search-button">🔍</button>
                        </div>
                        <div class="search-box">
                            <input type="text" id="searchPosition" name="chucvu" placeholder="Tìm kiếm theo chức vụ..." class="search-input" />
                            <button class="search-button">🔍</button>
                        </div>
                    </div>
                    <button class="add-button" onclick="createNewEmployee()">
                        <img src="../../assets/plus.png" class="icon-add" alt="Add Icon" /> 
                        Thêm nhân viên
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
                        <button class="sort-btn active" data-sort="id">Mã NV</button>
                        <button class="sort-btn" data-sort="name">Tên NV</button>
                    </div>
                </div>
                <span class="pagination">
                    <button class="page-btn prev">&lt;</button>
                    <span class="page-info">1/1</span>
                    <button class="page-btn next">&gt;</button>
                </span>
            </div>
            <!-- Bảng nhân viên -->
            <table id="employeeTable" class="table">
                <thead>
                    <tr>
                        <th class="stt">STT</th>
                        <th class="id">Mã NV</th>
                        <th>Họ tên</th>
                        <th>Chức vụ</th>
                        <th class="action-buttons">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $stt = 1; ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $stt++ ?></td>
                        <td class="id"><?= htmlspecialchars($row['MaNV']) ?></td>
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
                            <button class="create-account-btn" 
                                onclick="createNewAccount('<?= $row['MaNV'] ?>')" 
                                <?= in_array($row['MaNV'], $accountEmployeeIds) ? 'disabled style="opacity:0.6; cursor:not-allowed;" 
                                    title=\'Nhân viên đã có tài khoản\''  : '' ?>>Tạo tài khoản
                            </button>                        
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <div id="accountTabContent" style="display:none;">
            <div class="toolbar">
                <div class="toolbar-row">
                    <div class="search-filter-group">
                        <div class="search-box">
                            <input type="text" id="searchUsername" name="tendn" placeholder="Tìm kiếm theo tên tài khoản..." class="search-input" />
                            <button class="search-button">🔍</button>
                        </div>
                        <div class="search-box">
                            <input type="text" id="searchRole" name="quyen" placeholder="Tìm kiếm theo quyền..." class="search-input" />
                            <button class="search-button">🔍</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sort-pagination-bar">
                <span class="pagination">
                    <button class="page-btn prev">&lt;</button>
                    <span class="page-info">1/1</span>
                    <button class="page-btn next">&gt;</button>
                </span>
            </div>
            <!-- Bảng tài khoản -->
            <table id="accountTable" class="table">
                <thead>
                    <tr>
                        <th class="stt">STT</th>
                        <th class="id">Mã NV</th>
                        <th>Tên đăng nhập</th>
                        <th>Email</th>
                        <th>Quyền</th>
                        <th class="action-buttons">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $stt = 1; ?>
                    <?php while ($row = $results->fetch_assoc()): ?>
                    <tr>
                        <td class="stt"><?= $stt++ ?></td>
                        <td class="id"><?= htmlspecialchars($row['MaNV']) ?></td>
                        <td><?= htmlspecialchars($row['TenDN']) ?></td>
                        <td><?= htmlspecialchars($row['Email']) ?></td>
                        <td><?= htmlspecialchars($row['Quyen']) ?></td>
                        <td class="action-buttons">
                            <button class="view-btn" onclick="openAccountForm(
                            '<?= $row['MaNV'] ?>',
                            '<?= $row['TenDN'] ?>',
                            '<?= $row['Email'] ?>',
                            '<?= $row['Quyen'] ?>',
                            '<?= $row['MatKhau'] ?>'
                            )">Xem</button>
                            <button class="delete-btn" onclick="deleteAccount('<?= $row['MaNV'] ?>')">Xóa</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <div class="toast" id="toast"></div>
    </div>

    <div id="employeeFormOverlay" class="overlay">
        <div class="form-popup">
            <h2>Thông tin nhân viên</h2>
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
            <h2>Thông tin tài khoản</h2>
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
                    <option value="Manager">Quản lý</option>
                    <option value="Employee">Nhân viên</option>
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
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <script>
        //biến editingIndex dùng để lưu vị trí của dòng đang được click button Xem/Sửa/Xóa
        let editingIndex = -1;
        let editingAccountIndex = -1;
        let chucVuChoices, luongChoices, quyenChoices;
        let isEditing = false;
        const latestRule = <?= json_encode($latestRule) ?>;

        window.addEventListener("DOMContentLoaded", () => {
            //không sort mà để theo thứ tự đã initilize, ít nên ẩn tìm kiếm
            chucVuChoices = new Choices("#chuc_vu", { shouldSort: false, searchEnabled: false });
            luongChoices = new Choices("#luong", { shouldSort: false, searchEnabled: false });
            quyenChoices = new Choices("#quyen", { shouldSort: false, searchEnabled: false });
            chucVuChoices.disable();
            luongChoices.disable();
            quyenChoices.disable();
        });

        //region NHÂN VIÊN
        function openEmployeeForm(maNV, hoTen, ngaySinh, sdt, noiO, chucVu, caLam, luong) {      
            // Reset scroll position to top
            document.querySelector(".form-popup").scrollTop = 0;
            document.querySelectorAll(".error").forEach(el => el.textContent = "");

            const form = document.forms['employeeForm'];
            form.ma_nv.value = maNV;
            form.ho_ten.value = hoTen;
            form.ngay_sinh.value = ngaySinh;
            form.sdt.value = sdt;
            form.noi_o.value = noiO;

            // Cập nhật giá trị cho Choices.js
            // Đảm bảo chucVu và luong ở đây là các giá trị VAlUE của option, không phải text hiển thị
            chucVuChoices.setChoiceByValue(chucVu);
            luongChoices.setChoiceByValue(String(parseInt(luong))); // Đảm bảo truyền string cho setChoiceByValue

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
                .findIndex(row => row.cells[1].textContent === maNV);

            // hiện tại chỉ được phép xem, không được chỉnh sửa thông tin  
            for (let input of form.querySelectorAll("input")) input.readOnly = true;
            chucVuChoices.disable();
            luongChoices.disable();
            checkboxes.forEach(cb => cb.disabled = true);

            //đang ở chế độ xem nên ẩn nút Lưu
            document.querySelector(".btn-save").style.display = "none";
            document.querySelector(".btn-edit").style.display = "inline-block";
            document.getElementById("employeeFormOverlay").classList.add("show");
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
            bookFormOverlay.classList.add("show");
        }

        //Button Thêm nhân viên
        function createNewEmployee() {
            // Reset scroll position to top
            document.querySelector(".form-popup").scrollTop = 0;
            document.querySelectorAll(".error").forEach(el => el.textContent = "");

            const form = document.forms['employeeForm'];
            form.reset();

            const table = document.getElementById("employeeTable").getElementsByTagName("tbody")[0];
            const nextId = "NV" + String(table.rows.length + 1).padStart(3, '0');

            form.ma_nv.value = nextId; // Gán ID trước khi cài đặt readonly

            for (let input of form.querySelectorAll("input")) {
                if (input.name !== "ma_nv") input.readOnly = false;
            }
            document.querySelector(".btn-save").style.display = "inline-block";
            document.querySelector(".btn-edit").style.display = "none";

            editingIndex = -1;
            chucVuChoices.enable();
            luongChoices.enable();
            // Reset Choices.js selected values
            chucVuChoices.setChoiceByValue(''); // Đặt lại về option đầu tiên
            luongChoices.setChoiceByValue(''); // Đặt lại về option đầu tiên


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

            // Lấy các ca làm đã chọn
            const checkedShifts = Array.from(document.querySelectorAll('#shiftTable input[type=checkbox]:checked'));
            const caLamCheckedCount = checkedShifts.length;

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
            if (caLamCheckedCount < latestRule.SoCaMin) { // Kiểm tra số lượng ca làm đã chọn
                document.getElementById("error_ca_lam").textContent = `Vui lòng chọn ít nhất ${latestRule.SoCaMin} ca làm trong một tuần!`;
                isValid = false;
            }
            // Lương
            if (isNaN(luong) || luong <= 0) { // Kiểm tra nếu luong không hợp lệ
                document.getElementById("error_luong").textContent = "Vui lòng chọn mức lương phù hợp!";
                isValid = false;
            }
            return isValid;
        }

        //Button Lưu nhân viên
        function saveEmployee() {
            const form = document.forms['employeeForm'];
            const table = document.getElementById("employeeTable").getElementsByTagName("tbody")[0];

            if(!checkValidEmployeeForm(form)) return; // Thêm check form hợp lệ

            const maNV = form.ma_nv.value;
            const hoTen = form.ho_ten.value;
            const ngaySinh = form.ngay_sinh.value;
            const sdt = form.sdt.value;
            const noiO = form.noi_o.value;
            const chucVu = form.chuc_vu.value; // Lấy value trực tiếp từ select
            const luong = form.luong.value; // Lấy value trực tiếp từ select

            const checkedShifts = [];
            document.querySelectorAll('#shiftTable input[type=checkbox]:checked').forEach(cb => {
                checkedShifts.push(cb.value);
            });
            const caLam = checkedShifts.join(',');

            const formData = new FormData(form);
            formData.append("ca_lam", caLam);
            formData.append("chuc_vu", chucVu); // Đảm bảo gửi đúng value
            formData.append("luong", luong); // Đảm bảo gửi đúng value

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
            closeForm("employeeFormOverlay");
        }

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

        function fixTableBorders(tableId) {
            const table = document.getElementById(tableId);
            if (!table) return;
            const rows = Array.from(table.querySelectorAll('tbody tr'))
                .filter(row => row.style.display !== "none");
            // Đặt lại border-bottom cho tất cả các dòng hiển thị
            rows.forEach(row => row.querySelectorAll('td').forEach(td => td.style.borderBottom = "1px solid #0d3c6b"));
            // Bỏ border-bottom cho dòng cuối cùng hiển thị
            if (rows.length > 0) {
                rows[rows.length - 1].querySelectorAll('td').forEach(td => td.style.borderBottom = "none");
                // Hiện border-bottom cho th
                table.querySelectorAll('th').forEach(th => th.style.borderBottom = "1px solid #0d3c6b");
            }
            else {
                // Không có dòng nào hiển thị, ẩn border-bottom của th
                table.querySelectorAll('th').forEach(th => th.style.borderBottom = "none");
            }
        }

        const PAGE_SIZE = 50;
        let currentPage = 1;
        let currentSort = "id";

        function getAllRows() {
            return Array.from(document.querySelectorAll("#employeeTable tbody tr"));
        }

        //Tìm kiếm nhân viên
        document.getElementById("searchName").addEventListener("input", renderEmployeeTable);
        document.getElementById("searchPosition").addEventListener("input", renderEmployeeTable);

        function renderEmployeeTable() {
            const nameFilter = document.getElementById("searchName").value.toLowerCase().trim();
            const positionFilter = document.getElementById("searchPosition").value.toLowerCase().trim();

            let rows = getAllRows();

            // Sắp xếp toàn bộ các dòng (kể cả dòng đang ẩn)
            let colIdx = 1;
            let compareFn = null;
            if (currentSort === "id") colIdx = 1;
            if (currentSort === "name") colIdx = 2;
            if (currentSort === "id" || currentSort === "name") {
                compareFn = (a, b) => {
                    const valA = a.cells[colIdx].textContent.trim().toLowerCase();
                    const valB = b.cells[colIdx].textContent.trim().toLowerCase();
                    return valA.localeCompare(valB, 'vi');
                };
            }
            if (compareFn) {
                // Sắp xếp lại toàn bộ DOM
                rows.sort(compareFn);
                const tbody = document.querySelector("#employeeTable tbody");
                rows.forEach(row => tbody.appendChild(row));
            }

            // Lọc
            rows.forEach(row => {
                const name = row.cells[2].textContent.toLowerCase().trim();
                const position = row.cells[3].textContent.toLowerCase().trim();
                const matchName = name.includes(nameFilter);
                const matchPosition = position.includes(positionFilter);
                row.style.display = (matchName && matchPosition) ? "" : "none";
            });

            // Lấy lại các dòng còn hiển thị để phân trang
            let visibleRows = rows.filter(row => row.style.display !== "none");

            // Phân trang
            const totalRows = visibleRows.length;
            const totalPages = Math.max(1, Math.ceil(totalRows / PAGE_SIZE));
            if (currentPage > totalPages) currentPage = totalPages;
            const start = (currentPage - 1) * PAGE_SIZE;
            const end = start + PAGE_SIZE;

            // Ẩn tất cả dòng
            rows.forEach(row => row.style.display = "none");
            // Hiện dòng thuộc trang hiện tại
            visibleRows.slice(start, end).forEach(row => row.style.display = "");

            // Cập nhật phân trang
            document.querySelector(".page-info").textContent = `${currentPage}/${totalPages}`;
            document.querySelector(".page-btn.prev").disabled = currentPage === 1;
            document.querySelector(".page-btn.next").disabled = currentPage === totalPages;

            // Đánh lại số thứ tự STT cho các dòng đang hiển thị
            visibleRows.slice(start, end).forEach((row, idx) => {
                row.children[0].textContent = (start + idx + 1);
            });

            // Fix table borders after filtering and rendering
            fixTableBorders('employeeTable');
        }

        document.addEventListener("DOMContentLoaded", () => {
            document.getElementById("searchName").addEventListener("input", () => {
                currentPage = 1;
                renderEmployeeTable();
            });
            document.getElementById("searchPosition").addEventListener("input", () => {
                currentPage = 1;
                renderEmployeeTable();
            });

            // Xử lý khi click vào các nút sắp xếp thông thường
            document.querySelectorAll('.sort-btn[data-sort]').forEach(btn => {
                btn.addEventListener('click', function() {
                    // Xóa active class từ tất cả các nút sắp xếp
                    document.querySelectorAll('.sort-btn').forEach(b => b.classList.remove('active'));
                    // Thêm active class cho nút được click
                    this.classList.add('active');
                    handleSortChange(this.getAttribute('data-sort'));
                });
            });

            // Phân trang
            document.querySelector(".page-btn.prev").addEventListener("click", () => {
                if (currentPage > 1) {
                    currentPage--;
                    renderEmployeeTable();
                }
            });
            document.querySelector(".page-btn.next").addEventListener("click", () => {
                currentPage++;
                renderEmployeeTable();
            });

            renderEmployeeTable();
        });

        // Hàm đổi kiểu sắp xếp
        function handleSortChange(sortType) {
            currentSort = sortType;
            currentPage = 1;
            renderEmployeeTable();
        }

        function closeForm(formID) {
            document.getElementById(formID).classList.remove("show");
        }

        document.getElementById("employeeFormOverlay").addEventListener("click", e => {
            if (e.target === e.currentTarget) closeForm("employeeFormOverlay");
        });
        
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
        //endregion

        //region TÀI KHOẢN
        function openAccountForm(maNV, tenDN, email, quyen, matkhauhash) {
            // Reset scroll position to top
            document.querySelector(".form-popup").scrollTop = 0;
            document.querySelectorAll(".error").forEach(el => el.textContent = ""); // Xóa lỗi cũ
            
            const form = document.forms['accountForm'];
            form.tk_ma_nv.value = maNV;
            form.ten_dn.value = tenDN;
            form.email.value = email;
            form.quyen.value = quyen;
            quyenChoices.setChoiceByValue(quyen);
            form.matkhau.value = matkhauhash;
            form.xacnhan_mk.value = matkhauhash;

            //lấy vị trí dòng (nhân viên) được chọn để xem
            editingAccountIndex = Array.from(document.querySelector('#accountTable tbody').rows)
                .findIndex(row => row.cells[1].textContent === maNV);

            // hiện tại chỉ được phép xem, không được chỉnh sửa thông tin  
            for (let input of form.querySelectorAll("input")) input.readOnly = true;
            quyenChoices.disable();

            //đang ở chế độ xem nên ẩn nút Lưu
            form.querySelector(".btn-save").style.display = "none";
            form.querySelector(".btn-edit").style.display = "inline-block";
            document.getElementById("accountFormOverlay").classList.add("show");
            // accountFormOverlay.classList.add("show"); // Dòng này bị trùng lặp, có thể xóa
        };

        // Button Sửa tài khoản
        function enableEditingAccount() {
            const form = document.forms['accountForm'];
            for (let input of form.querySelectorAll("input")) {
                if (input.name !== "tk_ma_nv" && input.name !== "matkhau" && input.name !== "xacnhan_mk") input.readOnly = false;
            }
            quyenChoices.enable();
            isEditing = true;

            form.querySelector(".btn-save").style.display = "inline-block";
            form.querySelector(".btn-edit").style.display = "none";
        }

        //Button Thêm tài khoản
        function createNewAccount(maNV) {
            // Reset scroll position to top
            document.querySelector(".form-popup").scrollTop = 0;
            document.querySelectorAll(".error").forEach(el => el.textContent = ""); // Xóa lỗi cũ

            const form = document.forms['accountForm'];
            form.reset();
            const table = document.getElementById("accountTable").getElementsByTagName("tbody")[0];
            form.tk_ma_nv.value = maNV;
            for (let input of form.querySelectorAll("input")) {
                if (input.name !== "tk_ma_nv") input.readOnly = false;
            }
            form.querySelector(".btn-save").style.display = "inline-block";
            form.querySelector(".btn-edit").style.display = "none";

            editingIndex = -1; // Nên là editingAccountIndex = -1
            quyenChoices.enable();
            quyenChoices.setChoiceByValue(''); // Đặt lại về option đầu tiên
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
            if(!isEditing){
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

            if(!checkValidAccountForm(form)) return; // Thêm check form hợp lệ

            const maNV = form.tk_ma_nv.value.trim();
            const tenDN = form.ten_dn.value.trim();
            const email = form.email.value.trim();
            const matKhau = form.matkhau.value.trim();
            const xacnhanMK = form.xacnhan_mk.value.trim();
            const quyen = form.quyen.value;

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
            closeForm("accountFormOverlay");
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

        const PAGE_SIZE_ACCOUNT = 50;
        let currentPage_account = 1;
        let currentSort_account = "id";

        function getAllRowsAccount() {
            return Array.from(document.querySelectorAll("#accountTable tbody tr"));
        }

        document.getElementById("accountFormOverlay").addEventListener("click", e => {
            if (e.target === e.currentTarget) closeForm("accountFormOverlay");
        });

        //Tìm kiếm tài khoản
        document.getElementById("searchUsername").addEventListener("input", renderAccountTable);
        document.getElementById("searchRole").addEventListener("input", renderAccountTable);

        function renderAccountTable() {
            const tendnFilter = document.getElementById("searchUsername").value.toLowerCase();
            const quyenFilter = document.getElementById("searchRole").value.toLowerCase();

            const rows = getAllRowsAccount();

            rows.forEach(row => {
                const tendn = row.cells[2].textContent.toLowerCase();
                const quyen = row.cells[4].textContent.toLowerCase();

                const matchUsername = tendn.includes(tendnFilter);
                const matchRole = quyen.includes(quyenFilter);

                if (matchUsername && matchRole) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });

            let visibleRows = rows.filter(row => row.style.display !== "none");

            // Phân trang
            const totalRows = visibleRows.length;
            const totalPages = Math.max(1, Math.ceil(totalRows / PAGE_SIZE_ACCOUNT));
            if (currentPage_account > totalPages) currentPage_account = totalPages;
            const start = (currentPage_account - 1) * PAGE_SIZE_ACCOUNT;
            const end = start + PAGE_SIZE_ACCOUNT;
            
            // Ẩn tất cả dòng
            rows.forEach(row => row.style.display = "none");
            // Hiện dòng thuộc trang hiện tại
            visibleRows.slice(start, end).forEach(row => row.style.display = "");

            // Cập nhật phân trang
            document.querySelector(".page-info").textContent = `${currentPage_account}/${totalPages}`;
            document.querySelector(".page-btn.prev").disabled = currentPage_account === 1;
            document.querySelector(".page-btn.next").disabled = currentPage_account === totalPages;

            // Đánh lại số thứ tự STT cho các dòng đang hiển thị
            visibleRows.slice(start, end).forEach((row, idx) => {
                row.children[0].textContent = (start + idx + 1);
            });

            // Fix table borders after filtering and rendering
            fixTableBorders('accountTable');
        } 

        document.addEventListener("DOMContentLoaded", () => {
            document.getElementById("searchUsername").addEventListener("input", () => {
                currentPage_account = 1;
                renderAccountTable();
            });
            document.getElementById("searchRole").addEventListener("input", () => {
                currentPage_account = 1;
                renderAccountTable();
            });

            // Phân trang
            document.querySelector(".page-btn.prev").addEventListener("click", () => {
                if (currentPage_account > 1) {
                    currentPage_account--;
                    renderAccountTable();
                }
            });
            document.querySelector(".page-btn.next").addEventListener("click", () => {
                currentPage_account++;
                renderAccountTable();
            });

            renderAccountTable();
        });
        //endregion

        function showTab(tab) {
            const empTab = document.getElementById('employeeTabContent');
            const accTab = document.getElementById('accountTabContent');
            const btnEmp = document.getElementById('tab-employee');
            const btnAcc = document.getElementById('tab-account');
            if (tab === 'employee') {
                empTab.style.display = '';
                accTab.style.display = 'none';
                btnEmp.classList.add('active');
                btnAcc.classList.remove('active');
            } else {
                empTab.style.display = 'none';
                accTab.style.display = '';
                btnEmp.classList.remove('active');
                btnAcc.classList.add('active');
            }
        }
    </script>
</body>
</html>