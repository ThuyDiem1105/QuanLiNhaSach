window.onload = function () {
    renderChartFromPHP('chart1', 'dashboard-sales-chart.php', 'Top 3 sách bán chạy 7 tháng gần nhất');
    renderChartFromPHP('chart2', 'dashboard-finance-chart.php', 'Doanh thu, hoàn tiền, chi phí 7 ngày gần nhất');
};

function getLast7Months() {
    const months = [];
    const now = new Date();

    for (let i = 6; i >= 0; i--) {
        const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
        const month = d.getMonth() + 1;
        const year = d.getFullYear();
        months.push(`${month < 10 ? '0' + month : month}/${year}`);
    }

    return months;
}

function renderChartFromPHP(canvasId, phpEndpoint, chartTitle) {
    fetch(phpEndpoint)
        .then(res => res.json())
        .then(json => {
            const ctx = document.getElementById(canvasId).getContext('2d');
            if (!json || !json.labels || !json.datasets) {
                ctx.font = '16px fontweb';
                ctx.fillText('Không có dữ liệu', 50, 100);
                return;
            }
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: json.labels,
                    datasets: json.datasets.map((ds, idx) => ({
                        ...ds,
                        backgroundColor: ['#0d3c6b', '#48749f', '#b0c2d4', '#e0e0e0'][idx % 4]
                    }))
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: "#000000",
                                font: { size: 12, family: "fontweb" },
                            }
                        },
                        title: {
                            display: true,
                            text: chartTitle,
                            color: "#000000",
                            font: { size: 18, family: "fontweb", weight: 'bold' }
                        }
                    },
                    scales: {
                        x: {
                            ticks: { color: '#0f172a', font: { size: 12, family: "fontweb" } },
                            grid: { color: '#000000' }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { color: '#0f172a', font: { size: 12, family: "fontweb" } },
                            grid: { color: '#000000' }
                        }
                    }
                }
            });
        })
        .catch(() => {
            const ctx = document.getElementById(canvasId).getContext('2d');
            ctx.font = '16px fontweb';
            ctx.fillText('Không có dữ liệu', 50, 100);
        });
}

// Hàm tạo danh sách 7 ngày gần nhất
function getLast7Days() {
    const days = [];
    const today = new Date();
    for (let i = 6; i >= 0; i--) {
        const d = new Date(today);
        d.setDate(today.getDate() - i);
        const day = ('0' + d.getDate()).slice(-2);
        const month = ('0' + (d.getMonth() + 1)).slice(-2);
        days.push(`${day}/${month}`);
    }
    return days;
}

// Dữ liệu mẫu (ngẫu nhiên, bạn có thể thay bằng dữ liệu thực tế từ server)
function generateRandomData(min, max) {
    return Array.from({ length: 7 }, () => Math.floor(Math.random() * (max - min + 1)) + min);
}

function renderFinanceChart() {
    const labels = getLast7Days();
    const profitData = generateRandomData(2000000, 5000000);
    const refundData = generateRandomData(50000, 500000);
    const expenseData = generateRandomData(900000, 2500000);

    const data = {
        labels: labels,
        datasets: [
            {
                label: 'Lợi nhuận',
                data: profitData,
                backgroundColor: '#0d3c6b'
            },
            {
                label: 'Hoàn tiền',
                data: refundData,
                backgroundColor: '#48749f'
            },
            {
                label: 'Chi phí',
                data: expenseData,
                backgroundColor: '#b0c2d4'
            }
        ]
    };

    const config = {
        type: 'bar',
        data: data,
        options: {
            maintainAspectRatio: false,
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
                    text: 'Thống kê doanh thu',
                    color: "#000000",
                    font: {
                        size: 18,
                        family: "fontweb",
                        weight: 'bold'
                    }
                },
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
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' đ';
                        },
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
    };

    new Chart(document.getElementById('financeChart'), config);
}