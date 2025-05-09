-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 23, 2025 at 12:39 PM
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
-- Table structure for table `baocaocongno`
--

CREATE TABLE `baocaocongno` (
  `Thang` int(11) NOT NULL,
  `Nam` int(11) NOT NULL,
  `MaKhachHang` varchar(10) NOT NULL,
  `NoDau` float DEFAULT NULL,
  `PhatSinh` float DEFAULT NULL,
  `NoCuoi` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `baocaokho`
--

CREATE TABLE `baocaokho` (
  `Thang` int(11) NOT NULL,
  `Nam` int(11) NOT NULL,
  `MaSach` varchar(10) NOT NULL,
  `TonDau` int(11) DEFAULT NULL,
  `PhatSinh` int(11) DEFAULT NULL,
  `TonCuoi` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ct_hoadon`
--

CREATE TABLE `ct_hoadon` (
  `MaHoaDon` varchar(10) NOT NULL,
  `MaSach` varchar(10) NOT NULL,
  `SoLuong` int(11) DEFAULT NULL,
  `DonGiaBan` float DEFAULT NULL,
  `ThanhTien` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ct_phieunhap`
--

CREATE TABLE `ct_phieunhap` (
  `MaPhieuNhap` varchar(10) NOT NULL,
  `MaSach` varchar(10) NOT NULL,
  `SoLuong` int(11) DEFAULT NULL,
  `DonGiaNhap` float DEFAULT NULL,
  `ThanhTien` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ct_tacgia`
--

CREATE TABLE `ct_tacgia` (
  `MaTacGia` varchar(10) NOT NULL,
  `MaDauSach` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dausach`
--

CREATE TABLE `dausach` (
  `MaDauSach` varchar(10) NOT NULL,
  `TenDauSach` varchar(150) DEFAULT NULL,
  `TheLoai` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hoadon`
--

CREATE TABLE `hoadon` (
  `MaHoaDon` varchar(10) NOT NULL,
  `MaKhachHang` varchar(10) DEFAULT NULL,
  `NgayLap` date DEFAULT NULL,
  `TongTien` float DEFAULT NULL,
  `DaThanhToan` float DEFAULT NULL,
  `ConLai` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `khachhang`
--

CREATE TABLE `khachhang` (
  `MaKhachHang` varchar(10) NOT NULL,
  `TenKhachHang` varchar(100) DEFAULT NULL,
  `DiaChi` varchar(150) DEFAULT NULL,
  `SDT` varchar(20) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `SoTienNo` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nhanvien`
--

CREATE TABLE `nhanvien` (
  `MaNhanVien` varchar(10) NOT NULL,
  `HoTen` varchar(100) DEFAULT NULL,
  `NgaySinh` date DEFAULT NULL,
  `SDT` varchar(20) DEFAULT NULL,
  `NoiO` varchar(100) DEFAULT NULL,
  `ChucVu` varchar(50) DEFAULT NULL,
  `CaLam` varchar(20) DEFAULT NULL,
  `Luong` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `phieunhap`
--

CREATE TABLE `phieunhap` (
  `MaPhieuNhap` varchar(10) NOT NULL,
  `NgayNhap` date DEFAULT NULL,
  `TongTien` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sach`
--

CREATE TABLE `sach` (
  `MaSach` varchar(10) NOT NULL,
  `MaDauSach` varchar(10) DEFAULT NULL,
  `NXB` varchar(100) DEFAULT NULL,
  `NamXuatBan` int(11) DEFAULT NULL,
  `SoLuongTon` int(11) DEFAULT NULL,
  `DonGiaNhap` float DEFAULT NULL,
  `DonGiaBan` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tacgia`
--

CREATE TABLE `tacgia` (
  `MaTacGia` varchar(10) NOT NULL,
  `TenTacGia` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `MaNhanVien` varchar(10) NOT NULL,
  `TenDangNhap` varchar(50) DEFAULT NULL,
  `MatKhau` varchar(100) DEFAULT NULL,
  `QuyenDuocCap` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `baocaocongno`
--
ALTER TABLE `baocaocongno`
  ADD PRIMARY KEY (`Thang`,`Nam`,`MaKhachHang`),
  ADD KEY `MaKhachHang` (`MaKhachHang`);

--
-- Indexes for table `baocaokho`
--
ALTER TABLE `baocaokho`
  ADD PRIMARY KEY (`Thang`,`Nam`,`MaSach`),
  ADD KEY `MaSach` (`MaSach`);

--
-- Indexes for table `ct_hoadon`
--
ALTER TABLE `ct_hoadon`
  ADD PRIMARY KEY (`MaHoaDon`,`MaSach`),
  ADD KEY `MaSach` (`MaSach`);

--
-- Indexes for table `ct_phieunhap`
--
ALTER TABLE `ct_phieunhap`
  ADD PRIMARY KEY (`MaPhieuNhap`,`MaSach`),
  ADD KEY `MaSach` (`MaSach`);

--
-- Indexes for table `ct_tacgia`
--
ALTER TABLE `ct_tacgia`
  ADD PRIMARY KEY (`MaTacGia`,`MaDauSach`),
  ADD KEY `MaDauSach` (`MaDauSach`);

--
-- Indexes for table `dausach`
--
ALTER TABLE `dausach`
  ADD PRIMARY KEY (`MaDauSach`);

--
-- Indexes for table `hoadon`
--
ALTER TABLE `hoadon`
  ADD PRIMARY KEY (`MaHoaDon`),
  ADD KEY `MaKhachHang` (`MaKhachHang`);

--
-- Indexes for table `khachhang`
--
ALTER TABLE `khachhang`
  ADD PRIMARY KEY (`MaKhachHang`);

--
-- Indexes for table `nhanvien`
--
ALTER TABLE `nhanvien`
  ADD PRIMARY KEY (`MaNhanVien`);

--
-- Indexes for table `phieunhap`
--
ALTER TABLE `phieunhap`
  ADD PRIMARY KEY (`MaPhieuNhap`);

--
-- Indexes for table `sach`
--
ALTER TABLE `sach`
  ADD PRIMARY KEY (`MaSach`),
  ADD KEY `MaDauSach` (`MaDauSach`);

--
-- Indexes for table `tacgia`
--
ALTER TABLE `tacgia`
  ADD PRIMARY KEY (`MaTacGia`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`MaNhanVien`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `baocaocongno`
--
ALTER TABLE `baocaocongno`
  ADD CONSTRAINT `baocaocongno_ibfk_1` FOREIGN KEY (`MaKhachHang`) REFERENCES `khachhang` (`MaKhachHang`);

--
-- Constraints for table `baocaokho`
--
ALTER TABLE `baocaokho`
  ADD CONSTRAINT `baocaokho_ibfk_1` FOREIGN KEY (`MaSach`) REFERENCES `sach` (`MaSach`);

--
-- Constraints for table `ct_hoadon`
--
ALTER TABLE `ct_hoadon`
  ADD CONSTRAINT `ct_hoadon_ibfk_1` FOREIGN KEY (`MaHoaDon`) REFERENCES `hoadon` (`MaHoaDon`),
  ADD CONSTRAINT `ct_hoadon_ibfk_2` FOREIGN KEY (`MaSach`) REFERENCES `sach` (`MaSach`);

--
-- Constraints for table `ct_phieunhap`
--
ALTER TABLE `ct_phieunhap`
  ADD CONSTRAINT `ct_phieunhap_ibfk_1` FOREIGN KEY (`MaPhieuNhap`) REFERENCES `phieunhap` (`MaPhieuNhap`),
  ADD CONSTRAINT `ct_phieunhap_ibfk_2` FOREIGN KEY (`MaSach`) REFERENCES `sach` (`MaSach`);

--
-- Constraints for table `ct_tacgia`
--
ALTER TABLE `ct_tacgia`
  ADD CONSTRAINT `ct_tacgia_ibfk_1` FOREIGN KEY (`MaTacGia`) REFERENCES `tacgia` (`MaTacGia`),
  ADD CONSTRAINT `ct_tacgia_ibfk_2` FOREIGN KEY (`MaDauSach`) REFERENCES `dausach` (`MaDauSach`);

--
-- Constraints for table `hoadon`
--
ALTER TABLE `hoadon`
  ADD CONSTRAINT `hoadon_ibfk_1` FOREIGN KEY (`MaKhachHang`) REFERENCES `khachhang` (`MaKhachHang`);

--
-- Constraints for table `sach`
--
ALTER TABLE `sach`
  ADD CONSTRAINT `sach_ibfk_1` FOREIGN KEY (`MaDauSach`) REFERENCES `dausach` (`MaDauSach`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`MaNhanVien`) REFERENCES `nhanvien` (`MaNhanVien`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
