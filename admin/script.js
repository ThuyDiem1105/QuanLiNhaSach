document.addEventListener('DOMContentLoaded', function () {
    const links = document.querySelectorAll('.sidebar-link');
    const frame = document.getElementById('contentFrame');

    const pageMap = {
        dashboardBtn: 'dashboard.html',
        ordersBtn: 'Order/orders.php',
        booksBtn: 'Books/books.php',
        customersBtn: 'Customers/customers.php',
        receiptCollectionBtn: 'ReceiptCollection/receipt_collection.php',
        staffBtn: 'Employee/staff.php',
        receiptsBtn: 'Receipt/receipts.php',
        dealsBtn: 'Deals/deals.php',
        reportsBtn: 'Report/report.php',
        rulesBtn: 'Rules/rules.php',
        scheduleBtn: '../employee/schedule.php',
        profileBtn: '../employee/profile.php'
    };

    links.forEach(link => {
        link.addEventListener('click', (e) => {
            if (link.id === 'logoutBtn') {
                return; 
            }
            e.preventDefault();

            links.forEach(item => item.classList.remove('active'));
            link.classList.add('active');

            const page = pageMap[link.id];
            if (page) {
                frame.src = page;
            }
        });
    });
});