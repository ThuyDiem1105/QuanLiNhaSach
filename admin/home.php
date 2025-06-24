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
    <?php if($role === 'Employee'): ?>
    <title>Employee Page</title>
    <?php endif; ?>
    <?php if($role === 'Manager'): ?>
    <title>Manager Page</title>
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
                <a href="Order/orders.php" id="ordersBtn" class="sidebar-link">
                    <img src="../assets/order.png" id="order-icon" class="icon" alt="Order Icon" />
                    Hóa đơn
                </a>
                <a href="Books/books.php" id="booksBtn" class="sidebar-link">
                    <img src="../assets/book.png" id="book-icon" class="icon" alt="Book Icon" />
                    Sách
                </a>
                <a href="Customers/customers.php" id="customersBtn" class="sidebar-link">
                    <img src="../assets/customer.png" id="customer-icon" class="icon" alt="Customer Icon" />
                    Khách hàng
                </a>
                <a href="ReceiptCollection/receipt_collection.php" id="receiptCollectionBtn" class="sidebar-link">
                    <img src="../assets/receipt_collection.png" id="receipt-collection-icon" class="icon" alt="Receipt Collection Icon" />
                    Phiếu thu
                </a>
                <a href="Receipt/receipts.php" id="receiptsBtn" class="sidebar-link">
                    <img src="../assets/receipt.png" id="receipt-icon" class="icon" alt="Receipt Icon" />
                    Phiếu nhập
                </a>
                <a href="Deals/deals.php" id="dealsBtn" class="sidebar-link">
                    <img src="../assets/deal.png" id="deal-icon" class="icon" alt="Deal Icon" />
                    Khuyến mãi
                </a>

            <!-- quyền admin và manager-->
            <?php if ($role !== 'Employee'): ?>
                <a href="Employee/staff.php" id="staffBtn" class="sidebar-link">
                    <img src="../assets/staff.png" id="staff-icon" class="icon" alt="Staff Icon" />
                    Nhân viên
                </a>
                <a href="Report/report.php" id="reportsBtn" class="sidebar-link">
                    <img src="../assets/report.png" id="report-icon" class="icon" alt="Report Icon" />
                    Báo cáo
                </a>
                <a href="Rules/rules.php" id="rulesBtn" class="sidebar-link">
                    <img src="../assets/rule.png" id="rule-icon" class="icon" alt="Rule Icon" />
                    Quy định
                </a>
            <?php endif; ?>

            <!-- quyền employee -->
            <?php if ($role === 'Employee'): ?>
                <a href="../employee/schedule.php" id="scheduleBtn" class="sidebar-link">
                    <img src="../assets/schedule.png" id="schedule-icon" class="icon" alt="Schedule Icon" />
                    Lịch làm việc
                </a>
                <a href="../employee/profile.php" id="profileBtn" class="sidebar-link">
                    <img src="../assets/profile.png" id="profile-icon" class="icon" alt="Profile Icon" />
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