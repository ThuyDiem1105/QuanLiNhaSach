// Chuyển tab báo cáo
let tonViewed = false;
let congnoViewed = false;

document.querySelectorAll('.report-tab').forEach(btn => {
    btn.addEventListener('click', function() {
        // Active tab
        document.querySelectorAll('.report-tab').forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        // Ẩn/hiện bộ lọc đúng tab
        if (this.dataset.type === 'ton') {
            document.querySelector('.filter-ton').style.display = '';
            document.querySelector('.filter-congno').style.display = 'none';
            // CHỈ hiện báo cáo nếu đã chọn tháng và nhấn "Xem báo cáo"
            if (tonViewed) {
                document.querySelector('.report-ton').classList.remove('hidden');
            } else {
                document.querySelector('.report-ton').classList.add('hidden');
            }
            document.querySelector('.report-congno').classList.add('hidden');
        } else {
            document.querySelector('.filter-ton').style.display = 'none';
            document.querySelector('.filter-congno').style.display = '';
            // Hiện báo cáo nếu đã chọn và nhấn "Xem báo cáo"
            if (congnoViewed) {
                document.querySelector('.report-congno').classList.remove('hidden');
            } else {
                document.querySelector('.report-congno').classList.add('hidden');
            }
            document.querySelector('.report-ton').classList.add('hidden');
        }
    });
});

function formatMonth(ym) {
    if (!ym) return '';
    const [year, month] = ym.split('-');
    return `${month}/${year}`;
}

// Xem báo cáo cho từng tab
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const type = this.dataset.type;
        const monthInput = document.getElementById('report-month-' + type);
        if (!monthInput.value) {
            document.querySelector('.report-' + type).classList.add('hidden');
            alert('Vui lòng chọn tháng!');
            return;
        }
        document.querySelector('.report-' + type).classList.remove('hidden');
        // Đánh dấu đã xem báo cáo
        if (type === 'ton') tonViewed = true;
        if (type === 'congno') congnoViewed = true;
        // Cập nhật tiêu đề động
        const title = document.getElementById('report-' + type + '-title');
        if (title) {
            if (type === 'ton') {
                title.textContent = `Báo cáo kho tháng ${formatMonth(monthInput.value)}`;
            } else {
                title.textContent = `Báo cáo công nợ tháng ${formatMonth(monthInput.value)}`;
            }
        }
    });
});

['report-month-ton', 'report-month-congno'].forEach(id => {
    const input = document.getElementById(id);
    if (input) {
        input.addEventListener('input', function() {
            if (this.value) {
                this.classList.add('has-value');
            } else {
                this.classList.remove('has-value');
            }
        });
    }
});

// Xuất Excel (demo)
document.querySelector('.export-btn').addEventListener('click', function() {
    alert('Chức năng xuất Excel sẽ được bổ sung!');
});