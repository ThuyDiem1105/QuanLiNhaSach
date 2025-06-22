<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include __DIR__ . '/../../connect.php';

if (!isset($_SESSION['loggedin'])) {
    header('Location: ../../loginFunction/login.php');
    exit;
}

// Fetch all receipt collections
$collections_result = $mysqli->query(
    "SELECT pt.MaPT, pt.MaKH, kh.HoTen, pt.NgayThu, pt.SoTienThu 
     FROM phieuthutien pt
     JOIN khachhang kh ON pt.MaKH = kh.MaKH
     ORDER BY pt.NgayThu DESC, pt.MaPT DESC"
);

if (!$collections_result) {
    die("Lỗi truy vấn: " . $mysqli->error);
}

// Fetch all customers for the dropdown
$customers_result = $mysqli->query("SELECT MaKH, HoTen, SoTienNo FROM khachhang WHERE SoTienNo > 0 ORDER BY HoTen");
if (!$customers_result) {
    die("Lỗi truy vấn khách hàng: " . $mysqli->error);
}

$customers = [];
while ($customer = $customers_result->fetch_assoc()) {
    $customers[] = $customer;
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Phiếu Thu Tiền</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    <link rel="stylesheet" href="../../assets/staff-style.css" type="text/css">
    <link rel="stylesheet" href="../../assets/general-style.css" type="text/css">
    <style>
        .table-wrapper {
            overflow-x: auto;
        }
        .form-popup .choices__inner {
            background-color: white;
        }
        .form-popup .choices__list--dropdown {
            background-color: white;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <h1>Quản lý Phiếu Thu Tiền</h1>
        <div class="toolbar">
            <div class="toolbar-row">
                <button class="add-button" onclick="openCollectionForm()">
                    <img src="../../assets/plus.png" class="icon-add" alt="Add Icon" /> 
                    Thêm Phiếu thu
                </button>
            </div>
        </div>

        <div class="table-wrapper">
            <table id="collectionTable" class="table">
                <thead>
                    <tr>
                        <th>Mã Phiếu Thu</th>
                        <th>Mã Khách Hàng</th>
                        <th>Họ Tên Khách Hàng</th>
                        <th>Ngày Thu</th>
                        <th>Số Tiền Thu</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($collections_result->num_rows > 0): ?>
                        <?php while ($row = $collections_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['MaPT']) ?></td>
                            <td><?= htmlspecialchars($row['MaKH']) ?></td>
                            <td><?= htmlspecialchars($row['HoTen']) ?></td>
                            <td><?= htmlspecialchars(date('d-m-Y', strtotime($row['NgayThu']))) ?></td>
                            <td><?= htmlspecialchars(number_format($row['SoTienThu'], 0, ',', '.')) ?> VNĐ</td>
                            <td class="action-buttons">
                                <button class="delete-btn" onclick="deleteCollection('<?= $row['MaPT'] ?>')">Xóa</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">Chưa có phiếu thu nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="collectionFormOverlay" class="overlay">
        <div class="form-popup">
            <h2 id="formTitle">Tạo Phiếu Thu Mới</h2>
            <form id="collectionForm" onsubmit="return saveCollection(event);" novalidate>
                <input type="hidden" name="ma_phieu_thu" id="ma_phieu_thu">
                
                <label for="ma_kh">Khách hàng:</label>
                <select name="ma_kh" id="ma_kh" required>
                    <option value="">-- Chọn khách hàng --</option>
                    <?php foreach ($customers as $customer): ?>
                        <option value="<?= htmlspecialchars($customer['MaKH']) ?>"><?= htmlspecialchars($customer['HoTen']) ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="error" id="error_ma_kh"></span>
                
                <label>Số tiền nợ hiện tại:</label>
                <input type="text" name="so_tien_no" id="so_tien_no" readonly style="font-weight: bold; color: #c0392b;">
                
                <label for="ngay_thu">Ngày thu tiền:</label>
                <input type="date" name="ngay_thu" id="ngay_thu" value="<?= date('Y-m-d') ?>" required>
                <span class="error" id="error_ngay_thu"></span>

                <label for="so_tien_thu">Số tiền thu:</label>
                <input type="number" name="so_tien_thu" id="so_tien_thu" min="1" required>
                <span class="error" id="error_so_tien_thu"></span>

                <div class="form-buttons">
                    <button type="submit" class="btn-save">Lưu</button>
                    <button type="button" class="btn-cancel" onclick="closeForm()">Đóng</button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="toast" id="toast"></div>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <script>
        let customerChoices;
        let customerDebts = {};
        <?php foreach ($customers as $customer) {
            echo "customerDebts['{$customer['MaKH']}'] = {$customer['SoTienNo']};\n";
        } ?>

        document.addEventListener('DOMContentLoaded', function() {
            customerChoices = new Choices('#ma_kh', {
                searchEnabled: true,
                itemSelectText: 'Nhấn để chọn',
            });

            document.getElementById('ma_kh').addEventListener('change', function(event) {
                const customerId = event.detail.value;
                const debtInput = document.getElementById('so_tien_no');
                const collectionInput = document.getElementById('so_tien_thu');
                
                if (customerId && customerDebts[customerId]) {
                    const debt = customerDebts[customerId];
                    debtInput.value = new Intl.NumberFormat('vi-VN').format(debt) + ' VNĐ';
                    collectionInput.max = debt;
                } else {
                    debtInput.value = '';
                    collectionInput.max = null;
                }
            });
        });

        function openCollectionForm() {
            document.getElementById('collectionForm').reset();
            customerChoices.setChoiceByValue('');
            document.getElementById('so_tien_no').value = '';
            document.getElementById('ngay_thu').value = new Date().toISOString().slice(0, 10);
            document.getElementById('formTitle').innerText = 'Tạo Phiếu Thu Mới';
            document.querySelectorAll('.error').forEach(el => el.textContent = '');
            document.getElementById('collectionFormOverlay').classList.add('show');
        }

        function closeForm() {
            document.getElementById('collectionFormOverlay').classList.remove('show');
        }
        
        function validateForm() {
            let isValid = true;
            document.querySelectorAll('.error').forEach(el => el.textContent = '');

            const customerId = document.getElementById('ma_kh').value;
            const collectionAmount = parseInt(document.getElementById('so_tien_thu').value, 10);
            const customerDebt = customerId ? parseInt(customerDebts[customerId], 10) : 0;

            if (!customerId) {
                document.getElementById('error_ma_kh').textContent = 'Vui lòng chọn khách hàng.';
                isValid = false;
            }
            if (isNaN(collectionAmount) || collectionAmount <= 0) {
                document.getElementById('error_so_tien_thu').textContent = 'Vui lòng nhập số tiền thu hợp lệ.';
                isValid = false;
            } else if (collectionAmount > customerDebt) {
                document.getElementById('error_so_tien_thu').textContent = 'Số tiền thu không được vượt quá số tiền nợ.';
                isValid = false;
            }

            return isValid;
        }

        function saveCollection(event) {
            event.preventDefault();
            if (!validateForm()) return;

            const form = document.getElementById('collectionForm');
            const formData = new FormData(form);

            fetch('save_receipt_collection.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === 'OK') {
                    showToast('Lưu phiếu thu thành công!');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(data, true);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Có lỗi xảy ra, vui lòng thử lại.', true);
            });
        }

        function deleteCollection(id) {
            if (confirm('Bạn có chắc muốn xóa phiếu thu này không? Hành động này sẽ hoàn lại tiền cho khách hàng.')) {
                fetch('delete_receipt_collection.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'ma_phieu_thu=' + id
                })
                .then(response => response.text())
                .then(data => {
                    if (data.trim() === 'OK') {
                        showToast('Xóa phiếu thu thành công!');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast(data, true);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Có lỗi xảy ra, vui lòng thử lại.', true);
                });
            }
        }

        function showToast(message, isError = false) {
            const toast = document.getElementById("toast");
            toast.textContent = message;
            toast.className = "toast show";
            if (isError) {
                toast.classList.add("error-toast");
            }
            setTimeout(() => { toast.className = toast.className.replace("show", ""); }, 3000);
        }

        document.getElementById("collectionFormOverlay").addEventListener("click", e => {
            if (e.target === e.currentTarget) closeForm();
        });
    </script>
</body>
</html> 