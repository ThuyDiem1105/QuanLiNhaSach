document.addEventListener('DOMContentLoaded', function () {
    const links = document.querySelectorAll('.sidebar-link');
    const frame = document.getElementById('contentFrame');
  
    const pageMap = {
      dashboardBtn: 'dashboard.html',
      ordersBtn: 'orders.html',
      booksBtn: 'books.html',
      customersBtn: 'customers.html',
      staffBtn: 'staff.html',
      receiptsBtn: 'receipts.html',
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
  const ctx = document.getElementById('salesChart');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7'],
      datasets: [
        {
          label: 'IELTS 15',
          data: [12, 19, 7, 5, 8, 6, 9],
          backgroundColor: '#0d3c6b'
        },
        {
          label: 'Tư duy nhanh và chậm',
          data: [5, 12, 8, 6, 10, 7, 11],
          backgroundColor: '#48749f'
        },
        {
          label: 'Sách giáo khoa toán 9',
          data: [4, 7, 5, 3, 6, 4, 8],
          backgroundColor: '#b0c2d4'
        }
      ]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'top',
          labels: {
            color: "#000000",
            font: {
              size: 12,
              family: "fontweb"
            },
          }
        },
        title: {
          display: true,
          text: 'Top 3 sản phẩm bán chạy',
          color: "#000000",
          font: {
            size: 18,
            family: "fontweb",
            weight: 'bold'
          }
        }
      },
      scales: {
        x: {
          ticks: {
            color: '#0f172a', 
            font: {
              size: 12,
              family: "fontweb"
            },     
          },
          grid: {
            color: '#000000'             // màu lưới trục X
          }
        },
        y: {
          ticks: {
            color: '#0f172a',
            font: {
              size: 12,
              family: "fontweb"
            },
          },
          grid: {
            color: '#000000'             // màu lưới trục Y
          }
        }
      }
    }
  });
  