function getLast6MonthsLabels() {
    const months = ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'];
    const labels = [];
    const today = new Date();
    let currentMonth = today.getMonth(); // 0-indexed (0 = Jan)

    for (let i = 5; i >= 0; i--) {
        let monthIndex = (currentMonth - i + 12) % 12;
        labels.push(months[monthIndex]);
    }

    return labels;
}

document.addEventListener("DOMContentLoaded", () => {
    const dashboard = document.getElementById("dashboard");
    if (dashboard) {
        dashboard.classList.remove("hidden");
    
        // Tạo biểu đồ
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: getLast6MonthsLabels(),
                datasets: [
                    {
                        label: 'IELTS 15',
                        backgroundColor: '#8b5cf6',
                        data: [12, 19, 7, 5, 6, 4, 9]
                    },
                    {
                        label: 'Tư Duy Nhanh và Chậm',
                        backgroundColor: '#ec4899',
                        data: [5, 12, 8, 6, 11, 13, 8]
                    },
                    {
                        label: 'Sách Giáo Khoa Toán 9',
                        backgroundColor: '#fbbf24',
                        data: [3, 6, 5, 7, 9, 10, 11]
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' }
                }
            }
        });
    } else {
      document.getElementById("no-access").classList.remove("hidden");
    }
});

const orders = [
    { date: "01/01/2045", code: "INV-0123", customer: "John Doe", status: "Đã giao" },
    { date: "02/01/2045", code: "INV-0124", customer: "Jane Smith", status: "Đang xử lý" },
    { date: "03/01/2045", code: "INV-0125", customer: "Alice", status: "Đã hủy" },
  ];
  
  const orderTable = document.getElementById("orderTable");
  orders.forEach(order => {
    orderTable.innerHTML += `
      <tr>
        <td class="px-4 py-2 border">${order.date}</td>
        <td class="px-4 py-2 border">${order.code}</td>
        <td class="px-4 py-2 border">${order.customer}</td>
        <td class="px-4 py-2 border">${order.status}</td>
      </tr>
    `;
  });
  
  