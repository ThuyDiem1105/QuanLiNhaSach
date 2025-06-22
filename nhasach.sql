-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 22, 2025 at 05:26 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nhasach`
--

-- --------------------------------------------------------

--
-- Table structure for table `calam`
--

CREATE TABLE `calam` (
  `MaCa` varchar(10) NOT NULL,
  `Thu` varchar(10) NOT NULL,
  `LoaiCa` varchar(10) NOT NULL,
  `BatDau` time DEFAULT NULL,
  `KetThuc` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `calam`
--

INSERT INTO `calam` (`MaCa`, `Thu`, `LoaiCa`, `BatDau`, `KetThuc`) VALUES
('Fri-ca1', 'Thứ 6', 'Ca 1', '07:00:00', '10:30:00'),
('Fri-ca2', 'Thứ 6', 'Ca 2', '10:30:00', '14:00:00'),
('Fri-ca3', 'Thứ 6', 'Ca 3', '14:00:00', '17:30:00'),
('Fri-ca4', 'Thứ 6', 'Ca 4', '17:30:00', '21:00:00'),
('Mon-ca1', 'Thứ 2', 'Ca 1', '07:00:00', '10:30:00'),
('Mon-ca2', 'Thứ 2', 'Ca 2', '10:30:00', '14:00:00'),
('Mon-ca3', 'Thứ 2', 'Ca 3', '14:00:00', '17:30:00'),
('Mon-ca4', 'Thứ 2', 'Ca 4', '17:30:00', '21:00:00'),
('Sat-ca1', 'Thứ 7', 'Ca 1', '07:00:00', '10:30:00'),
('Sat-ca2', 'Thứ 7', 'Ca 2', '10:30:00', '14:00:00'),
('Sat-ca3', 'Thứ 7', 'Ca 3', '14:00:00', '17:30:00'),
('Sat-ca4', 'Thứ 7', 'Ca 4', '17:30:00', '21:00:00'),
('Sun-ca1', 'Chủ nhật', 'Ca 1', '07:00:00', '10:30:00'),
('Sun-ca2', 'Chủ nhật', 'Ca 2', '10:30:00', '14:00:00'),
('Sun-ca3', 'Chủ nhật', 'Ca 3', '14:00:00', '17:30:00'),
('Sun-ca4', 'Chủ nhật', 'Ca 4', '17:30:00', '21:00:00'),
('Thu-ca1', 'Thứ 5', 'Ca 1', '07:00:00', '10:30:00'),
('Thu-ca2', 'Thứ 5', 'Ca 2', '10:30:00', '14:00:00'),
('Thu-ca3', 'Thứ 5', 'Ca 3', '14:00:00', '17:30:00'),
('Thu-ca4', 'Thứ 5', 'Ca 4', '17:30:00', '21:00:00'),
('Tue-ca1', 'Thứ 3', 'Ca 1', '07:00:00', '10:30:00'),
('Tue-ca2', 'Thứ 3', 'Ca 2', '10:30:00', '14:00:00'),
('Tue-ca3', 'Thứ 3', 'Ca 3', '14:00:00', '17:30:00'),
('Tue-ca4', 'Thứ 3', 'Ca 4', '17:30:00', '21:00:00'),
('Wed-ca1', 'Thứ 4', 'Ca 1', '07:00:00', '10:30:00'),
('Wed-ca2', 'Thứ 4', 'Ca 2', '10:30:00', '14:00:00'),
('Wed-ca3', 'Thứ 4', 'Ca 3', '14:00:00', '17:30:00'),
('Wed-ca4', 'Thứ 4', 'Ca 4', '17:30:00', '21:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `chitiet_hoadon`
--

CREATE TABLE `chitiet_hoadon` (
  `MaCTHD` int(11) NOT NULL,
  `MaHD` varchar(10) NOT NULL,
  `MaSach` varchar(10) NOT NULL,
  `SoLuong` int(11) NOT NULL,
  `GiaBan` decimal(20,2) NOT NULL,
  `ThanhTien` decimal(20,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chitiet_hoadon`
--

INSERT INTO `chitiet_hoadon` (`MaCTHD`, `MaHD`, `MaSach`, `SoLuong`, `GiaBan`, `ThanhTien`) VALUES
(1, 'HD001', 'SACH001', 1, 90000.00, 90000.00),
(2, 'HD001', 'SACH002', 1, 180000.00, 180000.00),
(3, 'HD002', 'SACH001', 1, 90000.00, 90000.00),
(4, 'HD002', 'SACH002', 1, 180000.00, 180000.00),
(5, 'HD003', 'SACH001', 1, 90000.00, 90000.00),
(6, 'HD003', 'SACH002', 1, 180000.00, 180000.00),
(7, 'HD004', 'SACH001', 50, 99000.00, 4950000.00),
(8, 'HD005', 'SACH001', 3, 90000.00, 270000.00);

-- --------------------------------------------------------

--
-- Table structure for table `chitiet_phieunhap`
--

CREATE TABLE `chitiet_phieunhap` (
  `MaCTPN` int(11) NOT NULL,
  `MaPN` varchar(10) NOT NULL,
  `MaSach` varchar(10) NOT NULL,
  `SoLuong` int(11) NOT NULL,
  `DonGiaNhap` decimal(12,2) NOT NULL,
  `ThanhTien` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chitiet_phieunhap`
--

INSERT INTO `chitiet_phieunhap` (`MaCTPN`, `MaPN`, `MaSach`, `SoLuong`, `DonGiaNhap`, `ThanhTien`) VALUES
(1, 'PN001', 'SACH001', 150, 50.00, 7500000.00),
(2, 'PN001', 'SACH002', 200, 49000.00, 9800000.00),
(6, 'PN002', 'SACH001', 150, 88000.00, 13200000.00),
(7, 'PN002', 'SACH002', 150, 120000.00, 18000000.00),
(8, 'PN003', 'SACH003', 200, 80000.00, 16000000.00),
(9, 'PN004', 'SACH003', 200, 0.00, 0.00),
(10, 'PN004', 'SACH004', 200, 50000.00, 10000000.00),
(11, 'PN005', 'SACH001', 52, 80000.00, 4160000.00);

-- --------------------------------------------------------

--
-- Table structure for table `danhmucsach`
--

CREATE TABLE `danhmucsach` (
  `MaDMS` varchar(10) NOT NULL,
  `TenDanhMuc` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `danhmucsach`
--

INSERT INTO `danhmucsach` (`MaDMS`, `TenDanhMuc`) VALUES
('DM001', 'Tham khảo'),
('DM002', 'Giáo trình'),
('DM003', 'Truyện tranh'),
('DM004', 'Tiểu thuyết'),
('DM005', 'Khoa học tự nhiên'),
('DM006', 'Kinh doanh - tài chính'),
('DM007', 'Tâm lý'),
('DM008', 'Tôn giáo - Tâm linh'),
('DM009', 'Văn học nghệ thuật'),
('DM010', 'Thiếu nhi'),
('DM011', 'Phát triển bản thân'),
('DM012', 'Công nghệ - Kỹ thuật');

-- --------------------------------------------------------

--
-- Table structure for table `hoadon`
--

CREATE TABLE `hoadon` (
  `MaHD` varchar(10) NOT NULL,
  `MaKH` varchar(10) NOT NULL,
  `NgayLap` date NOT NULL DEFAULT curdate(),
  `TongTien` decimal(20,2) NOT NULL,
  `TienTra` decimal(20,2) NOT NULL,
  `TienNo` decimal(20,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hoadon`
--

INSERT INTO `hoadon` (`MaHD`, `MaKH`, `NgayLap`, `TongTien`, `TienTra`, `TienNo`) VALUES
('HD001', 'KH001', '2025-05-26', 350000.00, 250000.00, 100000.00),
('HD002', 'KH001', '2025-05-27', 270000.00, 200000.00, 70000.00),
('HD003', 'KH001', '2025-05-29', 270000.00, 200000.00, 70000.00),
('HD004', 'KH001', '2025-06-16', 4950000.00, 4950000.00, 0.00),
('HD005', 'KH002', '2025-06-22', 270000.00, 100000.00, 170000.00);

-- --------------------------------------------------------

--
-- Table structure for table `khachhang`
--

CREATE TABLE `khachhang` (
  `MaKH` varchar(10) NOT NULL,
  `HoTen` varchar(100) NOT NULL,
  `SDT` varchar(15) NOT NULL,
  `DiaChi` varchar(50) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Loai` enum('Thường','VIP') NOT NULL DEFAULT 'Thường',
  `SoTienNo` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `khachhang`
--

INSERT INTO `khachhang` (`MaKH`, `HoTen`, `SDT`, `DiaChi`, `Email`, `Loai`, `SoTienNo`) VALUES
('KH001', 'Nguyễn Minh A', '0901234567', 'TP.HCM', 'nguyenminha@gmail.com', 'Thường', 240000),
('KH002', 'Nguyễn Bảo Châu', '0886038804', 'Phú Yên', 'bchoune@gmail.com', 'VIP', 170000),
('KH003', 'Đậu Thị Diệu Anh', '0816810784', 'Quảng Bình', 'dieuanhxinh@gmail.com', 'VIP', 0),
('KH004', 'Hồ Thanh Tùng', '0906538235', 'Quảng Nam', 'hothanhtung235@gmail.com', 'VIP', 0);

-- --------------------------------------------------------

--
-- Table structure for table `khuyenmai`
--

CREATE TABLE `khuyenmai` (
  `MaKM` varchar(10) NOT NULL,
  `TenKM` varchar(100) DEFAULT NULL,
  `NgayBatDau` date DEFAULT NULL,
  `NgayKetThuc` date DEFAULT NULL,
  `DieuKienApDung` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `khuyenmai`
--

INSERT INTO `khuyenmai` (`MaKM`, `TenKM`, `NgayBatDau`, `NgayKetThuc`, `DieuKienApDung`) VALUES
('KM001', 'Giảm giá 10% sách mới', '2025-06-01', '2025-06-17', 'Áp dụng cho sách mới xuất bản'),
('KM002', 'Mua 2 tặng 1 sách giáo khoa', '2025-07-01', '2025-07-10', 'Áp dụng cho sách giáo khoa'),
('KM003', 'Giảm giá 20% cho khách hàng mới', '2025-06-19', '2025-06-30', 'Khách hàng mua hàng lần đầu tại nhà sách');

-- --------------------------------------------------------

--
-- Table structure for table `lichlamviec`
--

CREATE TABLE `lichlamviec` (
  `MaNV` varchar(10) NOT NULL,
  `MaCa` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lichlamviec`
--

INSERT INTO `lichlamviec` (`MaNV`, `MaCa`) VALUES
('NV001', 'Mon-ca1'),
('NV001', 'Thu-ca4'),
('NV001', 'Tue-ca2'),
('NV001', 'Wed-ca3'),
('NV002', 'Fri-ca1'),
('NV002', 'Thu-ca1'),
('NV002', 'Tue-ca1'),
('NV002', 'Wed-ca1'),
('NV003', 'Fri-ca2'),
('NV003', 'Sat-ca1'),
('NV003', 'Sun-ca1'),
('NV003', 'Thu-ca3'),
('NV003', 'Wed-ca4');

-- --------------------------------------------------------

--
-- Table structure for table `nhanvien`
--

CREATE TABLE `nhanvien` (
  `MaNV` varchar(10) NOT NULL,
  `HoTen` varchar(255) NOT NULL,
  `NgaySinh` date NOT NULL,
  `SDT` varchar(20) NOT NULL,
  `NoiO` varchar(255) NOT NULL,
  `ChucVu` varchar(255) NOT NULL,
  `CaLam` varchar(255) NOT NULL,
  `Luong` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nhanvien`
--

INSERT INTO `nhanvien` (`MaNV`, `HoTen`, `NgaySinh`, `SDT`, `NoiO`, `ChucVu`, `CaLam`, `Luong`) VALUES
('NV001', 'Nguyễn Văn A', '2004-03-12', '0912345678', 'Hà Nội', 'Bán hàng', 'Mon-ca1,Tue-ca2,Wed-ca3,Thu-ca4', 25000.00),
('NV002', 'Nguyễn Văn B', '2004-01-31', '0312345678', 'Thành Phố Hồ Chí Minh', 'Thu ngân', 'Tue-ca1,Wed-ca1,Thu-ca1,Fri-ca1', 50000.00),
('NV003', 'Nguyễn Thị C', '2001-01-01', '0712345678', 'Quảng Ngãi', 'Marketing và chăm sóc khách hàng', 'Wed-ca4,Thu-ca3,Fri-ca2,Sat-ca1,Sun-ca1', 65000.00);

-- --------------------------------------------------------

--
-- Table structure for table `phieunhap`
--

CREATE TABLE `phieunhap` (
  `MaPN` varchar(10) NOT NULL,
  `NgayLapPhieu` date NOT NULL DEFAULT curdate(),
  `NgayNhap` date NOT NULL,
  `TongTien` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `phieuthutien`
--

CREATE TABLE `phieuthutien` (
  `MaPT` varchar(10) NOT NULL,
  `MaKH` varchar(10) NOT NULL,
  `NgayThu` date NOT NULL DEFAULT curdate(),
  `SoTienThu` decimal(20,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quydinh`
--

CREATE TABLE `quydinh` (
  `MaQD` varchar(10) NOT NULL,
  `TonKhoMax` int(11) NOT NULL,
  `TonMinSauBan` int(11) NOT NULL,
  `SLNhapMin` int(11) NOT NULL,
  `TonMaxDeNhap` int(11) NOT NULL,
  `SoCaMin` int(11) NOT NULL,
  `TiLeBan` float NOT NULL,
  `NoThuongMax` decimal(20,2) NOT NULL,
  `NoVipMax` decimal(20,2) NOT NULL,
  `NgayTao` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quydinh`
--

INSERT INTO `quydinh` (`MaQD`, `TonKhoMax`, `TonMinSauBan`, `SLNhapMin`, `TonMaxDeNhap`, `SoCaMin`, `TiLeBan`, `NoThuongMax`, `NoVipMax`, `NgayTao`) VALUES
('QD001', 500, 20, 200, 300, 15, 1.05, 1000000.00, 3000000.00, '2025-06-15 00:00:00'),
('QD002', 500, 20, 100, 300, 15, 1.05, 1000000.00, 3000000.00, '2025-06-16 09:49:33'),
('QD003', 500, 20, 100, 300, 15, 1, 1000000.00, 3000000.00, '2025-06-16 09:49:51'),
('QD004', 600, 50, 100, 300, 12, 1, 1000000.00, 3000000.00, '2025-06-16 09:54:22'),
('QD005', 500, 20, 200, 300, 15, 1.05, 1000000.00, 3000000.00, '2025-06-16 09:58:09'),
('QD006', 500, 20, 100, 300, 15, 1.05, 1000000.00, 3000000.00, '2025-06-16 09:58:16'),
('QD007', 500, 50, 200, 250, 15, 1.5, 800000.00, 2500000.00, '2025-06-16 10:02:48');

-- --------------------------------------------------------

--
-- Table structure for table `rule`
--

CREATE TABLE `rule` (
  `SoLuongNhapToiThieu` int(11) DEFAULT NULL,
  `SoLuongTonToiDa` int(11) DEFAULT NULL,
  `SoLuongTonToiThieuSauBan` int(11) DEFAULT NULL,
  `SoLuongTonToiDaDeDuocNhapThem` int(11) DEFAULT NULL,
  `SoCaDangKyToiThieu` int(11) DEFAULT NULL,
  `TiLeTinhDonGiaBan` decimal(5,2) DEFAULT NULL,
  `SoTienNoToiDaThuong` bigint(20) DEFAULT NULL,
  `SoTienNoToiDaVIP` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rule`
--

INSERT INTO `rule` (`SoLuongNhapToiThieu`, `SoLuongTonToiDa`, `SoLuongTonToiThieuSauBan`, `SoLuongTonToiDaDeDuocNhapThem`, `SoCaDangKyToiThieu`, `TiLeTinhDonGiaBan`, `SoTienNoToiDaThuong`, `SoTienNoToiDaVIP`) VALUES
(200, 500, 20, 300, 15, 105.00, 1000000, 3000000);

-- --------------------------------------------------------

--
-- Table structure for table `sach`
--

CREATE TABLE `sach` (
  `MaSach` varchar(50) NOT NULL,
  `TenSach` varchar(255) NOT NULL,
  `MaDMS` varchar(10) NOT NULL,
  `TheLoai` varchar(255) NOT NULL,
  `NhaXuatBan` varchar(255) NOT NULL,
  `NgayXuatBan` date NOT NULL,
  `TacGia` varchar(255) NOT NULL,
  `NgonNgu` varchar(100) NOT NULL,
  `GiaBan` decimal(12,2) NOT NULL,
  `SoLuongTon` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sach`
--

INSERT INTO `sach` (`MaSach`, `TenSach`, `MaDMS`, `TheLoai`, `NhaXuatBan`, `NgayXuatBan`, `TacGia`, `NgonNgu`, `GiaBan`, `SoLuongTon`) VALUES
('SACH001', 'Dế Mèn Phiêu Lưu Ký', 'DM001', 'TL001,TL003,TL005', 'NXB Trẻ', '2005-02-12', 'Tô Hoài', 'Tiếng Việt', 90000.00, 117),
('SACH002', 'Truyện Kiều', 'DM009', 'TL001,TL011', 'NXB Trẻ', '1927-01-01', 'Nguyễn Du', 'Tiếng Nôm', 180000.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `sach_theloai`
--

CREATE TABLE `sach_theloai` (
  `MaSach` varchar(10) NOT NULL,
  `MaTL` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sach_theloai`
--

INSERT INTO `sach_theloai` (`MaSach`, `MaTL`) VALUES
('SACH001', 'TL001'),
('SACH001', 'TL003'),
('SACH001', 'TL005'),
('SACH002', 'TL001'),
('SACH002', 'TL011');

-- --------------------------------------------------------

--
-- Table structure for table `taikhoan`
--

CREATE TABLE `taikhoan` (
  `MaNV` varchar(10) NOT NULL,
  `TenDN` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Quyen` varchar(20) NOT NULL,
  `MatKhau` varchar(255) NOT NULL,
  `resetToken_hash` varchar(64) DEFAULT NULL,
  `resetToken_expiredAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `taikhoan`
--

INSERT INTO `taikhoan` (`MaNV`, `TenDN`, `Email`, `Quyen`, `MatKhau`, `resetToken_hash`, `resetToken_expiredAt`) VALUES
('NV000', 'admin@bookstore1', 'diemthuy123huynh@gmail.com', 'Admin', '$2y$10$sKxg5ZwiR2HUAGxfkJibZeAvdB6c/Sw5fQxK7GVklTMf3FetqVaoO', 'ef6850eab7b2006ffa9ed59a11198c2b6bf2e30d2cbf29195f365fdab551441d', '2025-06-20 06:13:52'),
('NV001', 'nguyenvana@123', 'dp1.1a1.10ngochan@gmail.com', 'Employee', '$2y$10$f9tJcIukNthbaL6GLpfNBeNZ6Py/VVpQSeTVZhYER2F8qBtSZXGcu', NULL, NULL),
('NV002', 'nguyenvanb@456', 'nguyenvanb@gmail.com', 'Employee', '$2y$10$ioDga8fuHX8l13wvy0b8L./gdWq5qyb4Djl/gKzEr9uz70AX3VUei', '', NULL),
('NV003', 'nguyenvanc@345', 'ngochan6e@gmail.com', 'Manager', '$2y$10$NobA6RmwXOJVvZbZ8yJm6uCtLDKXJUhKBem9MNE31CA5HFPkb5yy6', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `theloai`
--

CREATE TABLE `theloai` (
  `MaTL` varchar(10) NOT NULL,
  `TenTheLoai` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `theloai`
--

INSERT INTO `theloai` (`MaTL`, `TenTheLoai`) VALUES
('TL001', 'Truyện ngắn'),
('TL002', 'Trinh thám'),
('TL003', 'Khoa học viễn tưởng'),
('TL004', 'Ngôn tình'),
('TL005', 'Lãng mạn'),
('TL006', 'Kỹ năng sống'),
('TL007', 'Kinh doanh'),
('TL008', 'Truyện cổ tích'),
('TL009', 'Truyện dân gian'),
('TL010', 'Kinh dị'),
('TL011', 'Thơ ca'),
('TL012', 'Du ký'),
('TL013', 'Học thuật'),
('TL014', 'Tâm linh'),
('TL015', 'Nấu ăn - Ẩm thực'),
('TL016', 'Y học – Sức khỏe'),
('TL017', 'Ngoại ngữ'),
('TL018', 'Sách giáo khoa'),
('TL019', 'Hành động'),
('TL020', 'Pháp luật – Chính trị'),
('TL021', 'Sách bài tập'),
('TL022', 'Quản lý tài chính'),
('TL023', 'Khởi nghiệp'),
('TL024', 'Self-help (Tự lực)'),
('TL025', 'Văn hóa');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `calam`
--
ALTER TABLE `calam`
  ADD PRIMARY KEY (`MaCa`);

--
-- Indexes for table `chitiet_hoadon`
--
ALTER TABLE `chitiet_hoadon`
  ADD PRIMARY KEY (`MaCTHD`),
  ADD KEY `MaHD` (`MaHD`),
  ADD KEY `MaSach` (`MaSach`);

--
-- Indexes for table `chitiet_phieunhap`
--
ALTER TABLE `chitiet_phieunhap`
  ADD PRIMARY KEY (`MaCTPN`);

--
-- Indexes for table `danhmucsach`
--
ALTER TABLE `danhmucsach`
  ADD PRIMARY KEY (`MaDMS`);

--
-- Indexes for table `hoadon`
--
ALTER TABLE `hoadon`
  ADD PRIMARY KEY (`MaHD`);

--
-- Indexes for table `khachhang`
--
ALTER TABLE `khachhang`
  ADD PRIMARY KEY (`MaKH`),
  ADD UNIQUE KEY `SDT` (`SDT`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `khuyenmai`
--
ALTER TABLE `khuyenmai`
  ADD PRIMARY KEY (`MaKM`);

--
-- Indexes for table `lichlamviec`
--
ALTER TABLE `lichlamviec`
  ADD PRIMARY KEY (`MaNV`,`MaCa`),
  ADD KEY `MaCa` (`MaCa`);

--
-- Indexes for table `nhanvien`
--
ALTER TABLE `nhanvien`
  ADD PRIMARY KEY (`MaNV`),
  ADD UNIQUE KEY `SDT` (`SDT`);

--
-- Indexes for table `phieunhap`
--
ALTER TABLE `phieunhap`
  ADD PRIMARY KEY (`MaPN`);

--
-- Indexes for table `quydinh`
--
ALTER TABLE `quydinh`
  ADD PRIMARY KEY (`MaQD`);

--
-- Indexes for table `sach`
--
ALTER TABLE `sach`
  ADD PRIMARY KEY (`MaSach`),
  ADD KEY `sach_danhmucsach` (`MaDMS`);

--
-- Indexes for table `sach_theloai`
--
ALTER TABLE `sach_theloai`
  ADD PRIMARY KEY (`MaSach`,`MaTL`),
  ADD KEY `MaTL` (`MaTL`);

--
-- Indexes for table `taikhoan`
--
ALTER TABLE `taikhoan`
  ADD PRIMARY KEY (`MaNV`),
  ADD UNIQUE KEY `TenDN` (`TenDN`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `resetToken_hash` (`resetToken_hash`);

--
-- Indexes for table `theloai`
--
ALTER TABLE `theloai`
  ADD PRIMARY KEY (`MaTL`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chitiet_hoadon`
--
ALTER TABLE `chitiet_hoadon`
  MODIFY `MaCTHD` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `chitiet_phieunhap`
--
ALTER TABLE `chitiet_phieunhap`
  MODIFY `MaCTPN` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chitiet_hoadon`
--
ALTER TABLE `chitiet_hoadon`
  ADD CONSTRAINT `chitiet_hoadon_ibfk_1` FOREIGN KEY (`MaHD`) REFERENCES `hoadon` (`MaHD`),
  ADD CONSTRAINT `chitiet_hoadon_ibfk_2` FOREIGN KEY (`MaSach`) REFERENCES `sach` (`MaSach`);

--
-- Constraints for table `lichlamviec`
--
ALTER TABLE `lichlamviec`
  ADD CONSTRAINT `lichlamviec_ibfk_1` FOREIGN KEY (`MaNV`) REFERENCES `nhanvien` (`MaNV`),
  ADD CONSTRAINT `lichlamviec_ibfk_2` FOREIGN KEY (`MaCa`) REFERENCES `calam` (`MaCa`);

--
-- Constraints for table `sach`
--
ALTER TABLE `sach`
  ADD CONSTRAINT `sach_danhmucsach` FOREIGN KEY (`MaDMS`) REFERENCES `danhmucsach` (`MaDMS`);

--
-- Constraints for table `sach_theloai`
--
ALTER TABLE `sach_theloai`
  ADD CONSTRAINT `sach_theloai_ibfk_1` FOREIGN KEY (`MaSach`) REFERENCES `sach` (`MaSach`),
  ADD CONSTRAINT `sach_theloai_ibfk_2` FOREIGN KEY (`MaTL`) REFERENCES `theloai` (`MaTL`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
