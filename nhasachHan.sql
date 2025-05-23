--- BẢNG NHÂN VIÊN
CREATE TABLE IF NOT EXISTS `NHANVIEN` (
    `MaNV` VARCHAR(10) NOT NULL PRIMARY KEY,
    `HoTen` VARCHAR(255) NOT NULL,
    `NgaySinh` DATE NOT NULL,
    `SDT` VARCHAR(20) NOT NULL UNIQUE,
    `NoiO` VARCHAR(255) NOT NULL,
    `ChucVu` VARCHAR(255) NOT NULL,
    `CaLam` VARCHAR(255) NOT NULL,
    `Luong` DECIMAL(12,2) NOT NULL
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- BẢNG TÀI KHOẢN
CREATE TABLE IF NOT EXISTS `TAIKHOAN` (
    `MaNV` VARCHAR(10) NOT NULL PRIMARY KEY,
    `TenDN` VARCHAR(255) NOT NULL UNIQUE,
    `Email` DATE NOT NULL UNIQUE,
    `Quyen` VARCHAR(20) NOT NULL,
    `MatKhau` VARCHAR(255) NOT NULL,
    `MatKhauGoc` VARCHAR(255) NOT NULL
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--- KHÓA NGOẠI 
ALTER TABLE nhanvien ADD CONSTRAINT nhanvien_taikhoan
FOREIGN KEY (MaNV) REFERENCES taikhoan(MaNV) 

--- BẢNG THỂ LOẠI
CREATE TABLE IF NOT EXISTS `THELOAI` (
    `MaTL` VARCHAR(10) NOT NULL PRIMARY KEY,
    `MaDMS` VARCHAR(10) NOT NULL,
    `TenTheLoai` VARCHAR(255) NOT NULL
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO theloai (MaTL, TenTheLoai) VALUES 
('TL001', 'Truyện ngắn'), ('TL002', 'Trinh thám'), ('TL003', 'Khoa học viễn tưởng'), ('TL004', 'Ngôn tình'), ('TL005', 'Lãng mạn'),
('TL006', 'Kỹ năng sống'), ('TL007', 'Kinh doanh'), ('TL008', 'Truyện cổ tích'), ('TL009', 'Truyện dân gian'), ('TL010', 'Kinh dị'),
('TL011', 'Thơ ca'), ('TL012', 'Du ký'), ('TL013', 'Học thuật'), ('TL014', 'Tâm linh'), ('TL015', 'Nấu ăn - Ẩm thực'),
('TL016', 'Y học – Sức khỏe'), ('TL017', 'Ngoại ngữ'), ('TL018', 'Sách giáo khoa'), ('TL019', 'Hành động'), ('TL020', 'Pháp luật – Chính trị'),
('TL021', 'Sách bài tập'), ('TL022', 'Quản lý tài chính'), ('TL023', 'Khởi nghiệp'), ('TL024', 'Self-help (Tự lực)'), ('TL025', 'Văn hóa');

--- BẢNG DANH MỤC SÁCH
CREATE TABLE IF NOT EXISTS `DANHMUCSACH` ( 
    `MaDMS` VARCHAR(10) NOT NULL PRIMARY KEY, 
    `TenDanhMuc` VARCHAR(255) NOT NULL 
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `danhmucsach`(`MaDMS`, `TenDanhMuc`) VALUES ('DM001','Tham khảo');
INSERT INTO `danhmucsach`(`MaDMS`, `TenDanhMuc`) VALUES ('DM002','Giáo trình');
INSERT INTO `danhmucsach`(`MaDMS`, `TenDanhMuc`) VALUES ('DM003','Truyện tranh');
INSERT INTO `danhmucsach`(`MaDMS`, `TenDanhMuc`) VALUES ('DM004','Tiểu thuyết');
INSERT INTO `danhmucsach`(`MaDMS`, `TenDanhMuc`) VALUES ('DM005','Khoa học tự nhiên');
INSERT INTO `danhmucsach`(`MaDMS`, `TenDanhMuc`) VALUES ('DM006','Kinh doanh - tài chính');
INSERT INTO `danhmucsach`(`MaDMS`, `TenDanhMuc`) VALUES ('DM007','Tâm lý');
INSERT INTO `danhmucsach`(`MaDMS`, `TenDanhMuc`) VALUES ('DM008','Tôn giáo - Tâm linh');
INSERT INTO `danhmucsach`(`MaDMS`, `TenDanhMuc`) VALUES ('DM009','Văn học nghệ thuật');
INSERT INTO `danhmucsach`(`MaDMS`, `TenDanhMuc`) VALUES ('DM010','Thiếu nhi');
INSERT INTO `danhmucsach`(`MaDMS`, `TenDanhMuc`) VALUES ('DM011','Phát triển bản thân');
INSERT INTO `danhmucsach`(`MaDMS`, `TenDanhMuc`) VALUES ('DM012','Công nghệ - Kỹ thuật')

--- BẢNG SÁCH
CREATE TABLE IF NOT EXISTS `Sach` (
    `MaSach` VARCHAR(50) NOT NULL PRIMARY KEY,
    `TenSach` VARCHAR(255) NOT NULL,
    `MaDMS` VARCHAR(10) NOT NULL,
    `MaTL` VARCHAR(10) NOT NULL,
    `NhaXuatBan` VARCHAR(255) NOT NULL,
    `NgayXuatBan` DATE NOT NULL,
    `TacGia` VARCHAR(255) NOT NULL,
    `NgonNgu` VARCHAR(100) NOT NULL,
    `GiaBan` DECIMAL(12,2) NOT NULL
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE sach ADD CONSTRAINT sach_danhmucsach FOREIGN KEY (MaDMS) REFERENCES danhmucsach(MaDMS);

--- BẢNG JOIN GIỮA THỂ LOẠI VÀ SÁCH
CREATE TABLE IF NOT EXISTS `sach_theloai` (
  `MaSach` VARCHAR(10) NOT NULL,
  `MaTL` VARCHAR(10) NOT NULL,
  PRIMARY KEY (MaSach, MaTL),
  FOREIGN KEY (MaSach) REFERENCES sach(MaSach),
  FOREIGN KEY (MaTL) REFERENCES theloai(MaTL)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--- BẢNG CA LÀM STATIC
CREATE TABLE IF NOT EXISTS `CALAM` (
    `MaCa` VARCHAR(10) NOT NULL PRIMARY KEY,
    `Thu` VARCHAR(10) NOT NULL,
    `LoaiCa` VARCHAR(10) NOT NULL,
    `BatDau` TIME,
    `KetThuc` TIME
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--- BẢNG LỊCH LÀM VIỆC
CREATE TABLE IF NOT EXISTS `LICHLAMVIEC` (
    `MaNV` VARCHAR(10) NOT NULL,
    `MaCa` VARCHAR(10) NOT NULL,
    PRIMARY KEY (MaNV, MaCa),
    FOREIGN KEY (MaNV) REFERENCES nhanvien(MaNV),
    FOREIGN KEY (MaCa) REFERENCES calam(MaCa)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--- BẢNG KHÁCH HÀNG
CREATE TABLE IF NOT EXISTS `KHACHHANG` (
    `MaKH` VARCHAR(10) NOT NULL PRIMARY KEY,
    `TenKH` VARCHAR(255) NOT NULL,
    `DiaChi` VARCHAR(255) NOT NULL,
    `SDT` VARCHAR(20) NOT NULL UNIQUE,
    `Email` DATE NOT NULL UNIQUE,
    `Loai` VARCHAR(255) NOT NULL,
    `SoTienNo` DECIMAL(12,2)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `PhieuNhapSach` (
    `MaPhieu` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `MaSach` VARCHAR(10) NOT NULL,
    `NgayLapPhieu` DATETIME CURRENT_TIMESTAMP NOT NULL,
    `NgayNhap` DATETIME NOT NULL,
    `SoLuong` INT NOT NULL,
    `DonGiaNhap` DECIMAL(12,2) NOT NULL,
    `NguonNhap` VARCHAR(255) NOT NULL,
    `ThanhTien` DECIMAL(12,2) NOT NULL
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


// $categories = [
//     'reference' => 'Tham khảo - Giáo trình',
//     'comic_novel'     => 'Truyện - Tiểu thuyết',
//     'science_economy'   => 'Khoa học công nghệ - Kinh tế',
//     'literature'   => 'Văn học nghệ thuật',
//     'culture_social'   => 'Văn hóa xã hội - lịch sử',
//     'spirituality'   => 'Tâm lý, tâm linh, tôn giáo',
//     'children'   => 'Thiếu nhi',
//   ];
  
// $subGenres = [
//     'reference' => ['Giáo khoa','Từ điển','Giáo trình', 'Luyện kiến thức'],
//     'comic_novel' => ['Kinh dị', 'Trinh thám', 'Lãng mạng', 'Viễn tưởng', 'Hành động'],
//     'science_economy' => ['Kinh doanh', 'Quản lý tài chính', 'Khởi nghiệp', 'Khoa học tự nhiên', 'Kỹ thuật'],
//     'literature'   => ['Thơ ca', 'Truyện ngắn', 'Văn học đương đại', 'Văn học hiện đại'],
//     'culture_social'   => ['Bản sắc văn hóa', 'Chính trị', 'Xã hội'],
//     'spirituality'   => ['Kinh sách', 'Tôn giáo', 'Self-help', 'Giáo dục giới tính', 'Rối loạn tâm lý'],
//     'children'   => ['Truyện tranh', 'Truyện cổ tích', 'Kỹ năng sống'],
//   ];