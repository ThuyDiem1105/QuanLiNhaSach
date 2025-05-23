document.addEventListener('DOMContentLoaded', function () {
  const links = document.querySelectorAll('.sidebar-link');
  const frame = document.getElementById('contentFrame');

  const pageMap = {
    dashboardBtn: 'dashboard.html',
    ordersBtn: 'orders.html',
    booksBtn: 'Books/books.php',
    customersBtn: 'Customer/customers.php',
    staffBtn: 'Employee/staff.php',
    receiptsBtn: 'Receipt/receipts.html',
    dealsBtn: 'deals.html',
    reportsBtn: 'reports.html',
    rulesBtn: 'rules.html',
    logoutBtn: 'logout.html'
  };

  links.forEach(link => {
    link.addEventListener('click', (e) => {
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