document.addEventListener('DOMContentLoaded', () => {
    const tonTabBtn = document.querySelector('.report-tab[data-type="ton"]');
    const congnoTabBtn = document.querySelector('.report-tab[data-type="congno"]');

    const tonFilter = document.querySelector('.filter-ton');
    const congnoFilter = document.querySelector('.filter-congno');

    const tonReport = document.querySelector('.report-ton');
    const congnoReport = document.querySelector('.report-congno');

    const exportBtns = document.querySelectorAll('.export-btn');
    const filterBtns = document.querySelectorAll('.filter-btn');

    let currentTab = localStorage.getItem('active_report_tab') || 'ton';

    function switchTab(type) {
        currentTab = type;
        localStorage.setItem('active_report_tab', type);

        document.querySelectorAll('.report-tab').forEach(btn => btn.classList.remove('active'));
        document.querySelector(`.report-tab[data-type="${type}"]`).classList.add('active');

        if (type === 'ton') {
            tonFilter.style.display = '';
            congnoFilter.style.display = 'none';
            tonReport.classList.remove('hidden');
            congnoReport.classList.add('hidden');
        } else {
            tonFilter.style.display = 'none';
            congnoFilter.style.display = '';
            tonReport.classList.add('hidden');
            congnoReport.classList.remove('hidden');
        }
    }

    // Gắn sự kiện click cho tab
    tonTabBtn.addEventListener('click', () => switchTab('ton'));
    congnoTabBtn.addEventListener('click', () => switchTab('congno'));

    // Nút xem báo cáo
    filterBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            if (btn.closest('.filter-group').classList.contains('filter-ton')) {
                tonReport.classList.remove('hidden');
                congnoReport.classList.add('hidden');
                switchTab('ton');
            } else {
                congnoReport.classList.remove('hidden');
                tonReport.classList.add('hidden');
                switchTab('congno');
            }
        });
    });

    // Nút xuất Excel (demo)
    exportBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            alert('Chức năng xuất Excel đang được phát triển.');
            // Sau này có thể thay bằng:
            // window.location.href = '/export-excel?type=' + currentTab + '&month=' + ...
        });
    });

    // Giao diện khởi tạo theo tab cũ
    switchTab(currentTab);
});
