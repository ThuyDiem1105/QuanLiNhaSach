-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 25, 2025 at 05:15 PM
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
-- Table structure for table `phieunhap`
--

CREATE TABLE `PHIEUNHAP` (
  `MaPN` varchar(10) NOT NULL PRIMARY KEY,
  `NgayLapPhieu` DATE DEFAULT CURRENT_DATE NOT NULL,
  `NgayNhap` DATE NOT NULL,
  `TongTien` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `chitiet_phieunhap`
--

CREATE TABLE `CHITIET_PHIEUNHAP` (
  `MaCTPN ` INT PRIMARY KEY NOT NULL,
  `MaPN` varchar(10) NOT NULL,
  `MaSach` varchar(10) NOT NULL,
  `SoLuong` int(11) NOT NULL,
  `DonGiaNhap` DECIMAL(12,2) NOT NULL,
  `ThanhTien` DECIMAL(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
--- số lượng nhập tối thiểu là 150
ALTER TABLE chitiet_phieunhap ADD CONSTRAINT check_soluong CHECK(SoLuong >= 150);

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
-- Table structure for table `khachhang`
--

CREATE TABLE `khachhang` (
  `MaKH` varchar(10) NOT NULL,
  `HoTen` varchar(100) NOT NULL,
  `SDT` varchar(15) NOT NULL,
  `Loai` enum('Thường','VIP') NOT NULL DEFAULT 'Thường',
  `SoTienNo` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `khachhang`
--

INSERT INTO `khachhang` (`MaKH`, `HoTen`, `SDT`, `Loai`, `SoTienNo`) VALUES
('KH002', 'Trần Thị B', '0912345678', 'VIP', 0);

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
('SACH001', 'Dế Mèn Phiêu Lưu Ký', 'DM001', 'TL001,TL003,TL005', 'NXB Trẻ', '2005-02-12', 'Tô Hoài', 'Tiếng Việt', 90000.00, 0),
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
  `MatKhauGoc` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `taikhoan`
--

INSERT INTO `taikhoan` (`MaNV`, `TenDN`, `Email`, `Quyen`, `MatKhau`, `MatKhauGoc`) VALUES
('NV002', 'nguyenvanb@456', 'nguyenvanb@gmail.com', 'Quản lý', '$2y$10$ioDga8fuHX8l13wvy0b8L./gdWq5qyb4Djl/gKzEr9uz70AX3VUei', 'Nguyenvanb@456');

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
-- Indexes for table `danhmucsach`
--
ALTER TABLE `danhmucsach`
  ADD PRIMARY KEY (`MaDMS`);

--
-- Indexes for table `khachhang`
--
ALTER TABLE `khachhang`
  ADD PRIMARY KEY (`MaKH`);

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
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `theloai`
--
ALTER TABLE `theloai`
  ADD PRIMARY KEY (`MaTL`);

--
-- Constraints for dumped tables
--

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
