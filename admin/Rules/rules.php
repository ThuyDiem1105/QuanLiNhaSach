<?php
session_start();
include __DIR__ . '/../../connect.php';

if (!isset($_SESSION['loggedin'])){     
    header('Location: ../../loginFunction/login.php'); 
}

$result = $mysqli->query("SELECT COUNT(*) as total FROM quydinh");
$row = $result->fetch_assoc();
$totalRules = $row['total'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cài đặt Quy định</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/rules-style.css">
    <style>
        .error {
            color: red;
            font-size: 16px;
            margin-top: 4px;
            display: block;
        } 
    </style>
</head>
<body>
    <section class="settings-section">
        <h2 class="settings-title">
            <img src="../../assets/settings-2.png" class="rule-icon" alt="Rule Icon" />
            Cài đặt Quy định
        </h2>
        <form class="settings-form" id="settings-form" autocomplete="off" novalidate>
            <div class="form-group">
                <label for="min-import">Số lượng nhập tối thiểu</label>
                <input type="number" id="min-import" name="min_import" min="1" required readonly>
                <span class="error" id="error_min_import"></span>

            </div>
            <div class="form-group">
                <label for="max-stock">Số lượng tồn kho tối đa</label>
                <input type="number" id="max-stock" name="max_stock" min="1" required readonly>
                <span class="error" id="error_max_stock"></span>

            </div>
            <div class="form-group">
                <label for="min-stock-after-sale">Số lượng tồn tối thiểu sau bán</label>
                <input type="number" id="min-stock-after-sale" name="min_stock_aftersale" min="0" required readonly>
                <span class="error" id="error_minstock_aftersale"></span>

            </div>
            <div class="form-group">
                <label for="max-stock-to-import">Số lượng tồn tối đa để được nhập thêm</label>
                <input type="number" id="max-stock-to-import" name="max_stock_toimport" min="1" required readonly>
                <span class="error" id="error_maxstock_import"></span>

            </div>
            <div class="form-group">
                <label for="min-shifts">Số ca đăng ký tối thiểu (ca/tuần)</label>
                <input type="number" id="min-shifts" name="min_shifts" min="1" required readonly>
                <span class="error" id="error_minshifts"></span>

            </div>
            <div class="form-group">
                <label for="price-rate">Tỉ lệ tính đơn giá bán (%)</label>
                <input type="number" id="price-rate" name="price_rate" min="101" required readonly>
                <span class="error" id="error_pricerate"></span>

            </div>
            <div class="form-group">
                <label for="max-debt-normal">Số tiền nợ tối đa (Khách thường, VND)</label>
                <input type="number" id="max-debt-normal" name="max_debt_normal" min="0" required readonly>
                <span class="error" id="error_maxdebt1"></span>

            </div>
            <div class="form-group">
                <label for="max-debt-vip">Số tiền nợ tối đa (Khách VIP, VND)</label>
                <input type="number" id="max-debt-vip" name="max_debt_vip" min="0" required readonly>
                <span class="error" id="error_maxdebt2"></span>
            </div>
            <button type="button" id="edit-btn">Thay đổi</button>
            <div class="form-actions">
                <button type="submit" id="save-btn" onclick="saveRule(event)">Lưu</button>
                <button type="button" id="cancel-btn">Hủy</button>
                <button type="button" id="reset-btn">Mặc định</button>
            </div>
        </form>
    </section>
    <div class="toast" id="toast"></div>

    <script>
        const totalRules = <?= $totalRules ?>;

        const form = document.getElementById('settings-form');
        const editBtn = document.getElementById('edit-btn');
        const saveBtn = document.getElementById('save-btn');
        const cancelBtn = document.getElementById('cancel-btn');
        const inputs = form.querySelectorAll('input[type="number"], input[type="text"]');
        const resetBtn = document.getElementById('reset-btn');

        let backup = {};

        // Giá trị mặc định
        const defaultSettings = {
            minImport: 200,
            maxStock: 500,
            minStockAfterSale: 20,
            maxStockToImport: 300,
            minShifts: 15,
            priceRate: 105,
            maxDebtNormal: 1000000,
            maxDebtVip: 3000000,
        };

        // Lấy settings từ localStorage hoặc mặc định
        function getSettings() {
            return JSON.parse(localStorage.getItem('settings') || 'null') || defaultSettings;
        }
        function setSettings(settings) {
            localStorage.setItem('settings', JSON.stringify(settings));
        }

        function checkValidateInput(form) {
            let isValid = true;
            document.querySelectorAll(".error").forEach(el => el.textContent = "");

            const nhapMin = Number(form.min_import.value);
            const tonMax = Number(form.max_stock.value);
            const tonMinSauBan = Number(form.min_stock_aftersale.value);
            const tonMaxDeNhap = Number(form.max_stock_toimport.value);
            const calamMin = Number(form.min_shifts.value);
            const tiLeBan = Number(form.price_rate.value);
            const noMaxThuong = Number(form.max_debt_normal.value);
            const noMaxVip = Number(form.max_debt_vip.value);

            
            // Số lượng nhập tối thiểu
            if (!nhapMin) {
                document.getElementById("error_min_import").textContent = "Vui lòng ghi số lượng nhập tối thiểu!";
                isValid = false;
            } else if(nhapMin <= 0){
                document.getElementById("error_min_import").textContent = "Số lượng nhập tối thiểu phải lớn hơn 0!";
                isValid = false;
            } else if (nhapMin > (tonMax - tonMaxDeNhap)){
                document.getElementById("error_min_import").textContent = "Số lượng nhập tối thiểu phải nhỏ hơn hoặc bằng số chênh lệch giữa lượng tồn kho tối đa và lượng tồn tối đa để nhập!";
                isValid = false;
            }

            // Lượng tồn kho tối đa 
            if (!tonMax) {
                document.getElementById("error_max_stock").textContent = "Vui lòng nhập lượng tồn kho tối đa!";
                isValid = false;
            } else if(tonMax <= 0){
                document.getElementById("error_max_stock").textContent = "Lượng tồn kho tối đa phải lớn hơn 0!";
                isValid = false;
            } else if(tonMax < tonMaxDeNhap){
                document.getElementById("error_max_stock").textContent = "Lượng tồn kho tối đa phải lớn hơn lượng tồn tối đa để nhập!";
                isValid = false;
            } else if(tonMax < nhapMin){
                document.getElementById("error_max_stock").textContent = "Lượng tồn kho tối đa phải lớn hơn số lượng nhập tối thiểu!";
                isValid = false;
            } else if(tonMax < tonMinSauBan){
                document.getElementById("error_max_stock").textContent = "Lượng tồn kho tối đa phải lớn hơn lượng tồn tối thiểu sau khi bán!";
                isValid = false;
            }

            // Số lượng tồn tối thiếu sau khi bán
            if (!tonMinSauBan) {
                document.getElementById("error_minstock_aftersale").textContent = "Vui lòng ghi số lượng tồn tối thiểu sau khi bán!";
                isValid = false;
            } else if(tonMinSauBan <= 0){
                document.getElementById("error_minstock_aftersale").textContent = "Số lượng tồn tối thiểu sau khi bán phải lớn hơn 0!";
                isValid = false;
            } else if(tonMinSauBan > tonMax){
                document.getElementById("error_minstock_aftersale").textContent = "Số lượng tồn tối thiểu sau khi bán phải nhỏ hơn lượng tồn kho tối đa!";
                isValid = false;
            } else if(tonMinSauBan > tonMaxDeNhap){
                document.getElementById("error_minstock_aftersale").textContent = "Số lượng tồn tối thiểu sau khi bán phải nhỏ hơn hoặc bằng lượng tồn tối đa để nhập!";
                isValid = false;
            }

            // Số lượng tồn tối đa để nhập
            if (!tonMaxDeNhap) {
                document.getElementById("error_maxstock_import").textContent = "Vui lòng nhập lượng tồn tối đa để có thể nhập!";
                isValid = false;
            } else if(tonMaxDeNhap <= 0){
                document.getElementById("error_maxstock_import").textContent = "Lượng tồn tối đa để có thể nhập phải lớn hơn 0!";
                isValid = false;
            } else if(tonMaxDeNhap > tonMax){
                document.getElementById("error_maxstock_import").textContent = "Lượng tồn tối đa để có thể nhập phải nhỏ hơn lượng tồn kho tối đa!";
                isValid = false;
            } else if( tonMaxDeNhap < tonMinSauBan){
                document.getElementById("error_maxstock_import").textContent = "Lượng tồn tối đa để có thể nhập phải lớn hơn hoặc bằng lượng tồn tối thiểu sau khi bán!";
                isValid = false;
            }

            //Số ca làm cần đăng ký tối thiểu cho mỗi tuần
            if (!calamMin) {
                document.getElementById("error_minshifts").textContent = "Vui lòng nhập số ca làm tối thiểu cần đăng ký trong một tuần!";
                isValid = false;
            } else if(calamMin >= 28){
                document.getElementById("error_minshifts").textContent = "Số ca đăng ký tối đa nên nhỏ hơn 28! Không nên hết mọi ca trong tuần.";
                isValid = false;
            } else if(calamMin <= 0){
                document.getElementById("error_minshifts").textContent = "Số ca đăng ký tối thiểu phải lớn hơn 0!";
                isValid = false;
            }

            if (!tiLeBan) {
                document.getElementById("error_pricerate").textContent = "Vui lòng nhập tỉ lệ tính đơn giá bán!";
                isValid = false;
            } else if(tiLeBan <= 100){
                document.getElementById("error_pricerate").textContent = "Tỉ lệ tính đơn giá bán phải tối thiểu lớn hơn 100%! Không bán giá nhập.";
                isValid = false;
            }

            if(!noMaxThuong){
                document.getElementById("error_maxdebt1").textContent = "Vui lòng nhập số nợ tối đa cho khách thường!";
                isValid = false;
            } else if(noMaxThuong <= 0){
                document.getElementById("error_maxdebt1").textContent = "Số nợ phải lớn hơn 0!";
                isValid = false;
            } else if(noMaxThuong >= noMaxVip){
                document.getElementById("error_maxdebt1").textContent = "Số nợ của khách thường không được nhiều hơn khách VIP!";
                isValid = false;
            }

            if(!noMaxVip){
                document.getElementById("error_maxdebt2").textContent = "Vui lòng nhập số nợ tối đa cho khách VIP!";
                isValid = false;
            } else if(noMaxVip <= 0){
                document.getElementById("error_maxdebt2").textContent = "Số nợ phải lớn hơn 0!";
                isValid = false;
            } else if(noMaxVip <= noMaxThuong){
                document.getElementById("error_maxdebt2").textContent = "Số nợ của khách VIP phải lớn hơn khách thường!";
                isValid = false;
            }
            
            return isValid;
        }

        function saveRule(e){   
            e.preventDefault();
            const form = document.getElementById('settings-form');
            const ruleId = "QD" + String(totalRules + 1).padStart(3, '0');

            const nhapMin = form.min_import.value;
            const tonMax = form.max_stock.value;
            const tonMinSauBan = form.min_stock_aftersale.value;
            const tonMaxDeNhap = form.max_stock_toimport.value;
            const calamMin = form.min_shifts.value;
            const tiLeBan = form.price_rate.value;
            const noMaxThuong = form.max_debt_normal.value;
            const noMaxVip = form.max_debt_vip.value;

            console.log("Form Values:");
            console.log("Rule ID:", ruleId);
            console.log("Số lượng nhập tối thiểu:", nhapMin);
            console.log("Tồn kho tối đa:", tonMax);
            console.log("Tồn sau bán:", tonMinSauBan);
            console.log("Tồn để nhập:", tonMaxDeNhap);
            console.log("Ca tối thiểu:", calamMin);
            console.log("Tỉ lệ bán:", tiLeBan);
            console.log("Nợ thường:", noMaxThuong);
            console.log("Nợ VIP:", noMaxVip);

            if (!checkValidateInput(form)) return;

             const confirmSave = confirm("Bạn có chắc chắn muốn lưu các quy định này? Bạn có thể bấm hủy để tiếp tục chỉnh sửa.");
             if (!confirmSave) return;

            const formData = new FormData(form);

            fetch(`save_rule.php?ma_qd=${ruleId}`, {
                method: "POST",
                body: formData,
            })
            .then(res => res.text())
            .then(response => {
                console.log("Raw response:", response);
                if(response.trim() === "OK") {

                    const settings = {
                        minImport: Number(document.getElementById('min-import').value),
                        maxStock: Number(document.getElementById('max-stock').value),
                        minStockAfterSale: Number(document.getElementById('min-stock-after-sale').value),
                        maxStockToImport: Number(document.getElementById('max-stock-to-import').value),
                        minShifts: Number(document.getElementById('min-shifts').value),
                        priceRate: Number(document.getElementById('price-rate').value),
                        maxDebtNormal: Number(document.getElementById('max-debt-normal').value),
                        maxDebtVip: Number(document.getElementById('max-debt-vip').value)
                    };
                    setSettings(settings);
                    
                    showToast("Cập nhật quy định thành công!");
                    setTimeout(() => {
                        location.reload();
                    }, 1000);

                } else {
                    alert("Lỗi: " + response);
                }
            })
            .catch(error => {
                console.error("Lỗi: ", error);
                alert("Lỗi khi gửi dữ liệu.");
            });
        }

        // Hiển thị dữ liệu lên form
        function renderSettings() {
            const s = getSettings();
            document.getElementById('min-import').value = s.minImport;
            document.getElementById('max-stock').value = s.maxStock;
            document.getElementById('min-stock-after-sale').value = s.minStockAfterSale;
            document.getElementById('max-stock-to-import').value = s.maxStockToImport;
            document.getElementById('min-shifts').value = s.minShifts;
            document.getElementById('price-rate').value = s.priceRate;
            document.getElementById('max-debt-normal').value = s.maxDebtNormal;
            document.getElementById('max-debt-vip').value = s.maxDebtVip
        }

        editBtn.onclick = function() {
            // Lưu lại giá trị cũ để khi bấm hủy thì mặc định giữ giá trị cũ
            backup = {};
            inputs.forEach(input => {
                input.readOnly = false;
                backup[input.id] = input.value;
            });
            editBtn.style.display = 'none';
            saveBtn.style.display = '';
            cancelBtn.style.display = '';
            resetBtn.style.display = '';
        };

        cancelBtn.onclick = function() {
            // Khôi phục giá trị cũ và khóa lại
            inputs.forEach(input => {
                input.value = backup[input.id];
                input.readOnly = true;
            });
            editBtn.style.display = '';
            saveBtn.style.display = 'none';
            cancelBtn.style.display = 'none';
            resetBtn.style.display = 'none';
        };

        resetBtn.onclick = function() {
            // Đưa về mặc định
            Object.keys(defaultSettings).forEach(key => {
                const el = document.getElementById(key.replace(/[A-Z]/g, m => '-' + m.toLowerCase()));
                if (el) el.value = defaultSettings[key];
            });
        };

        // Khi load trang, đảm bảo các input readonly và nút đúng trạng thái
        window.onload = function() {
            renderSettings();
            inputs.forEach(input => input.readOnly = true);
            editBtn.style.display = '';
            saveBtn.style.display = 'none';
            cancelBtn.style.display = 'none';
            resetBtn.style.display = 'none';
        };

        function showToast(message) {
            const toast = document.getElementById("toast");
            toast.textContent = message;
            toast.classList.add("show");

            setTimeout(() => {
                toast.classList.remove("show");
            }, 3000);
        }
    </script>
</body>
</html>
