document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.report-tab');
    const activeTabInput = document.getElementById('active-tab-input');
    const tonFilter = document.querySelector('.filter-ton');
    const congNoFilter = document.querySelector('.filter-congno');
    
    // --- Tab Switching Logic ---
    tabButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault(); 
            const type = this.dataset.type;

            document.querySelectorAll('.report-tab').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            if (activeTabInput) {
                activeTabInput.value = type;
            }

            if (type === 'ton') {
                tonFilter.classList.remove('hidden');
                congNoFilter.classList.add('hidden');
            } else {
                tonFilter.classList.add('hidden');
                congNoFilter.classList.remove('hidden');
            }
            // Update export links on tab switch
            updateExportLinks();
        });
    });

    // --- Export Link Logic ---
    const monthTonInput = document.getElementById('report-month-ton');
    const monthCongNoInput = document.getElementById('report-month-congno');
    const exportTonBtn = document.getElementById('export-ton-btn');
    const exportCongNoBtn = document.getElementById('export-congno-btn');

    function updateExportLinks() {
        // Update Inventory Export Link
        const monthTon = monthTonInput.value;
        if (monthTon) {
            exportTonBtn.href = `export_excel.php?report_type=ton&month=${monthTon}`;
            exportTonBtn.classList.remove('disabled');
        } else {
            exportTonBtn.href = '#';
            exportTonBtn.classList.add('disabled');
        }

        // Update Debt Export Link
        const monthCongNo = monthCongNoInput.value;
        if (monthCongNo) {
            exportCongNoBtn.href = `export_excel.php?report_type=congno&month=${monthCongNo}`;
            exportCongNoBtn.classList.remove('disabled');
        } else {
            exportCongNoBtn.href = '#';
            exportCongNoBtn.classList.add('disabled');
        }
    }

    // Add event listeners to update links when date changes
    monthTonInput.addEventListener('change', updateExportLinks);
    monthCongNoInput.addEventListener('change', updateExportLinks);

    // Initial call to set links on page load
    updateExportLinks();
});