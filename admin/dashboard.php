<?php
session_start();

// Giả lập kiểm tra đăng nhập (demo)
if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = 'Admin';  // Mặc định tạm Admin
}

// Lấy trang đang chọn
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Hàm tải nội dung trang
function load_page($page) {
    switch ($page) {
        case 'sanpham':
            return "Quản lý Sản phẩm";
        case 'hoadon':
            return "Quản lý Hóa đơn";
        case 'khachhang':
            return "Quản lý Khách hàng";
        case 'nhanvien':
            return "Quản lý Nhân viên";
        case 'phieunhap':
            return "Phiếu nhập sản phẩm";
        case 'khuyenmai':
            return "Khuyến mãi";
        case 'baocao':
            return "Báo cáo";
        case 'thaydoi':
            return "Thay đổi quy định";
        default:
            return "Trang quản trị";
    }
}

// Xử lý đăng xuất
if ($page == 'logout') {
    session_destroy();
    echo json_encode(["message" => "Bạn đã đăng xuất!"]);
    exit;
}

// Xử lý nội dung của trang
$response = [
    'user' => htmlspecialchars($_SESSION['user']),
    'content' => load_page($page)
];

echo json_encode($response); // Trả về dữ liệu dưới dạng JSON
?>
