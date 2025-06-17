<?php
session_start();
//kiểm tra xem đã đăng nhập chưa, nếu chưa thì quay về trang đăng nhập
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] === 'Admin'){     
    header('Location: ../../loginFunction/login.php'); 
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Employee Page</title>
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
                <a href="schedule.php" id="scheduleBtn" class="sidebar-link">
                    <img src="../assets/report.png" id="report-icon" class="icon" alt="Report Icon" />
                    Lịch làm việc
                </a>
                <a href="profile.php" id="profileBtn" class="sidebar-link">
                    <img src="../assets/rule.png" id="rule-icon" class="icon" alt="Rule Icon" />
                    Profile
                </a>
                <a href="../loginFunction/logout.php" onclick="return confirm('Bạn có chắc muốn đăng xuất?')" id="logoutBtn" class="sidebar-link"> <img src="../assets/logout.png" id="logout-icon" class="icon" alt="Logout Icon" />
                    Đăng xuất
                </a>
            </nav>
        </aside>

        <main class="main-content">
            <iframe id="contentFrame" src="profile.php" frameborder="0" style="width:100%; height:100vh;"></iframe>
        </main>
    </div>
</body>
</html>