<?php
session_start();
//kiểm tra xem đã đăng nhập chưa, nếu chưa thì quay về trang đăng nhập
if (!isset($_SESSION['loggedin'])){     
    header('Location: ../loginFunction/login.php'); 
}
//phân quyền admin hay employee
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?php if($role === 'Admin'): ?>
    <title>Admin Page</title>
    <?php endif; ?>
    <?php if($role !== 'Admin'): ?>
    <title>Employee Page</title>
    <?php endif; ?>
    <link rel="stylesheet" href="../assets/style.css" />
    <script src="script.js" defer></script>
</head>
<body>
    <div class="container">
        <aside>
            <div class="logo">
                <img src="../assets/logo.png" class="icon-logo" alt="Logo Icon" />
                Booktopia
            </div>
            <nav class="menu">
                <!-- quyền cả hai -->
                <a href="dashboard.html" id="dashboardBtn" class="sidebar-link active">
                    <img src="../assets/dashboard.png" id="dashboard-icon" class="icon" alt="Dashboard Icon" />
                    Bảng điều khiển
                </a>
                <a href="orders.html" id="ordersBtn" class="sidebar-link">
                    <img src="../assets/order.png" id="order-icon" class="icon" alt="Order Icon" />
                    Hóa đơn
                </a>
                <a href="Book/book.php" id="booksBtn" class="sidebar-link">
                    <img src="../assets/book.png" id="book-icon" class="icon" alt="Book Icon" />
                    Sách
                </a>
                <a href="Customers/customers.php" id="customersBtn" class="sidebar-link">
                    <img src="../assets/customer.png" id="customer-icon" class="icon" alt="Customer Icon" />
                    Khách hàng
                </a>
                <a href="Receipts/receipts.php" id="receiptsBtn" class="sidebar-link">
                    <img src="../assets/receipt.png" id="receipt-icon" class="icon" alt="Receipt Icon" />
                    Phiếu nhập
                </a>
                <a href="#" id="dealsBtn" class="sidebar-link">
                    <img src="../assets/deal.png" id="deal-icon" class="icon" alt="Deal Icon" />
                    Khuyến mãi
                </a>
                <a href="#" id="reportsBtn" class="sidebar-link">
                    <img src="../assets/report.png" id="report-icon" class="icon" alt="Report Icon" />
                    Báo cáo
                </a>

            <!-- quyền admin -->
            <?php if ($role === 'Admin'): ?>
                <a href="#" id="staffBtn" class="sidebar-link">
                    <img src="../assets/staff.png" id="staff-icon" class="icon" alt="Staff Icon" />
                    Nhân viên
                </a>
                <a href="#" id="rulesBtn" class="sidebar-link">
                    <img src="../assets/rule.png" id="rule-icon" class="icon" alt="Rule Icon" />
                    Quy định
                </a>
            <?php endif; ?>

            <!-- quyền employee -->
            <?php if ($role !== 'Admin'): ?>
                <a href="#" id="scheduleBtn" class="sidebar-link">
                    <img src="../assets/staff.png" id="staff-icon" class="icon" alt="Staff Icon" />
                    Lịch làm việc
                </a>
                <a href="#" id="profileBtn" class="sidebar-link">
                    <img src="../assets/rule.png" id="rule-icon" class="icon" alt="Rule Icon" />
                    Profile
                </a>
            <?php endif; ?>

                <a href="../loginFunction/logout.php" onclick="return confirm('Bạn có chắc muốn đăng xuất không?')" id="logoutBtn" class="sidebar-link"> <img src="../assets/logout.png" id="logout-icon" class="icon" alt="Logout Icon" />
                    Đăng xuất
                </a>
            </nav>
        </aside>

        <main class="main-content">
            <iframe id="contentFrame" src="dashboard.html" frameborder="0" style="width:100%; height:100vh;"></iframe>
        </main>
    </div>
</body>
</html>