
// // Giá trị mặc định
// const defaultSettings = {
//     minImport: 200,
//     maxStock: 500,
//     minStockAfterSale: 20,
//     maxStockToImport: 300,
//     minShifts: 15,
//     priceRate: "105%",
//     maxDebtNormal: 1000000,
//     maxDebtVip: 3000000,
// };

// // Lấy settings từ localStorage hoặc mặc định
// function getSettings() {
//     return JSON.parse(localStorage.getItem('settings') || 'null') || defaultSettings;
// }
// function setSettings(settings) {
//     localStorage.setItem('settings', JSON.stringify(settings));
// }

// // Hiển thị dữ liệu lên form
// function renderSettings() {
//     const s = getSettings();
//     document.getElementById('min-import').value = s.minImport;
//     document.getElementById('max-stock').value = s.maxStock;
//     document.getElementById('min-stock-after-sale').value = s.minStockAfterSale;
//     document.getElementById('max-stock-to-import').value = s.maxStockToImport;
//     document.getElementById('min-shifts').value = s.minShifts;
//     document.getElementById('price-rate').value = s.priceRate;
//     document.getElementById('max-debt-normal').value = s.maxDebtNormal;
//     document.getElementById('max-debt-vip').value = s.maxDebtVip
// }

// const form = document.getElementById('settings-form');
// const editBtn = document.getElementById('edit-btn');
// const saveBtn = document.getElementById('save-btn');
// const cancelBtn = document.getElementById('cancel-btn');
// const inputs = form.querySelectorAll('input[type="number"], input[type="text"]');
// const resetBtn = document.getElementById('reset-btn');

// let backup = {};

// editBtn.onclick = function() {
//     // Lưu lại giá trị cũ để có thể hủy
//     backup = {};
//     inputs.forEach(input => {
//         input.readOnly = false;
//         backup[input.id] = input.value;
//     });
//     editBtn.style.display = 'none';
//     saveBtn.style.display = '';
//     cancelBtn.style.display = '';
//     resetBtn.style.display = '';
// };

// cancelBtn.onclick = function() {
//     // Khôi phục giá trị cũ và khóa lại
//     inputs.forEach(input => {
//         input.value = backup[input.id];
//         input.readOnly = true;
//     });
//     editBtn.style.display = '';
//     saveBtn.style.display = 'none';
//     cancelBtn.style.display = 'none';
//     resetBtn.style.display = 'none';
// };

// form.onsubmit = function(e) {
//     e.preventDefault();
//     // Lưu dữ liệu và khóa lại
//     const settings = {
//         minImport: Number(document.getElementById('min-import').value),
//         maxStock: Number(document.getElementById('max-stock').value),
//         minStockAfterSale: Number(document.getElementById('min-stock-after-sale').value),
//         maxStockToImport: Number(document.getElementById('max-stock-to-import').value),
//         minShifts: Number(document.getElementById('min-shifts').value),
//         priceRate: document.getElementById('price-rate').value,
//         maxDebtNormal: Number(document.getElementById('max-debt-normal').value),
//         maxDebtVip: Number(document.getElementById('max-debt-vip').value)
//     };
//     setSettings(settings);
//     //showToast("Đã lưu thành công!");
//     inputs.forEach(input => input.readOnly = true);
//     editBtn.style.display = '';
//     saveBtn.style.display = 'none';
//     cancelBtn.style.display = 'none';
//     resetBtn.style.display = 'none';
// };

// resetBtn.onclick = function() {
//     // Đưa về mặc định
//     Object.keys(defaultSettings).forEach(key => {
//         const el = document.getElementById(key.replace(/[A-Z]/g, m => '-' + m.toLowerCase()));
//         if (el) el.value = defaultSettings[key];
//     });
// };
// // Khi load trang, đảm bảo các input readonly và nút đúng trạng thái
// window.onload = function() {
//     renderSettings();
//     inputs.forEach(input => input.readOnly = true);
//     editBtn.style.display = '';
//     saveBtn.style.display = 'none';
//     cancelBtn.style.display = 'none';
//     resetBtn.style.display = 'none';
// };

// function showToast(message) {
//     const toast = document.getElementById("toast");
//     toast.textContent = message;
//     toast.classList.add("show");

//     setTimeout(() => {
//         toast.classList.remove("show");
//     }, 3000);
// }