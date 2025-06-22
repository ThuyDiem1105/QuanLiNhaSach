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

document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.report-tab');
    const activeTabInput = document.getElementById('active-tab-input');
    const tonFilter = document.querySelector('.filter-ton');
    const congNoFilter = document.querySelector('.filter-congno');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault(); // Ngăn form tự động gửi khi chỉ bấm chuyển tab
            const type = this.dataset.type;

            // Cập nhật class 'active' trên các nút tab
            document.querySelectorAll('.report-tab').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            // Cập nhật giá trị của trường input ẩn
            if (activeTabInput) {
                activeTabInput.value = type;
            }

            // Hiển thị bộ lọc tương ứng và ẩn bộ lọc còn lại
            if (type === 'ton') {
                tonFilter.classList.remove('hidden');
                congNoFilter.classList.add('hidden');
            } else {
                tonFilter.classList.add('hidden');
                congNoFilter.classList.remove('hidden');
            }
        });
    });

    // Phần code này để thêm style cho input tháng khi có giá trị (có thể giữ lại)
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

    // Gắn sự kiện cho cả hai nút xuất Excel
    document.querySelectorAll('.export-btn').forEach(button => {
        button.addEventListener('click', function() {
            alert('Chức năng xuất Excel sẽ được bổ sung!');
        });
    });
});