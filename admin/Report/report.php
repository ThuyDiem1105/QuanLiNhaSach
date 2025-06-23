<?php
session_start();
include __DIR__ . '/../../connect.php';

if (!isset($_SESSION['loggedin'])){     
    header('Location: ../../loginFunction/login.php'); 
}
//phân quyền
$role = $_SESSION['role'];

$selectedMonthTon = isset($_GET['month_ton']) && preg_match('/^\d{4}-\d{2}$/', $_GET['month_ton']) ? $_GET['month_ton'] : null;
$selectedMonthCongNo = isset($_GET['month_congno']) && preg_match('/^\d{4}-\d{2}$/', $_GET['month_congno']) ? $_GET['month_congno'] : null;

$monthTon = $selectedMonthTon ? (int)substr($selectedMonthTon, 5, 2) : (int)date('m');
$yearTon = $selectedMonthTon ? (int)substr($selectedMonthTon, 0, 4) : (int)date('Y');

$monthCongNo = $selectedMonthCongNo ? (int)substr($selectedMonthCongNo, 5, 2) : (int)date('m');
$yearCongNo = $selectedMonthCongNo ? (int)substr($selectedMonthCongNo, 0, 4) : (int)date('Y');

// --- Báo cáo kho (Tối ưu hóa) ---
$baoCaoKho = [];
$sumTonDau = $sumPhatSinh = $sumTonCuoi = 0;
if ($selectedMonthTon) {
    // 1. Lấy tất cả sách và khởi tạo dữ liệu
    $books_data = [];
    $all_books_result = $mysqli->query("SELECT MaSach, TenSach FROM sach ORDER BY MaSach");
    if ($all_books_result) {
        while ($book = $all_books_result->fetch_assoc()) {
            $books_data[$book['MaSach']] = [
                'TenSach' => $book['TenSach'],
                'TonDau' => 0,
                'PhatSinh' => 0,
            ];
        }
        $all_books_result->free();
    }

    // 2. Lấy tất cả giao dịch nhập và xử lý
    $sql_imports = "SELECT pn.MaSach, pn.SoLuong, p.NgayNhap 
                    FROM chitiet_phieunhap pn 
                    JOIN phieunhap p ON p.MaPN = pn.MaPN";
    if ($imports_result = $mysqli->query($sql_imports)) {
        while ($import = $imports_result->fetch_assoc()) {
            if (!isset($books_data[$import['MaSach']])) continue;
            
            $import_date = new DateTime($import['NgayNhap']);
            $import_year = (int)$import_date->format('Y');
            $import_month = (int)$import_date->format('m');
            
            if ($import_year < $yearTon || ($import_year == $yearTon && $import_month < $monthTon)) {
                $books_data[$import['MaSach']]['TonDau'] += (int)$import['SoLuong'];
            } elseif ($import_year == $yearTon && $import_month == $monthTon) {
                $books_data[$import['MaSach']]['PhatSinh'] += (int)$import['SoLuong'];
            }
        }
        $imports_result->free();
    }

    // 3. Lấy tất cả giao dịch bán và xử lý
    $sql_sales = "SELECT ct.MaSach, ct.SoLuong, h.NgayLap 
                  FROM chitiet_hoadon ct 
                  JOIN hoadon h ON h.MaHD = ct.MaHD";
    if ($sales_result = $mysqli->query($sql_sales)) {
        while ($sale = $sales_result->fetch_assoc()) {
            if (!isset($books_data[$sale['MaSach']])) continue;

            $sale_date = new DateTime($sale['NgayLap']);
            $sale_year = (int)$sale_date->format('Y');
            $sale_month = (int)$sale_date->format('m');
            
            if ($sale_year < $yearTon || ($sale_year == $yearTon && $sale_month < $monthTon)) {
                $books_data[$sale['MaSach']]['TonDau'] -= (int)$sale['SoLuong'];
            } elseif ($sale_year == $yearTon && $sale_month == $monthTon) {
                $books_data[$sale['MaSach']]['PhatSinh'] -= (int)$sale['SoLuong'];
            }
        }
        $sales_result->free();
    }
    
    // 4. Tính toán tồn cuối và tổng hợp để hiển thị
    foreach ($books_data as $maSach => $data) {
        if ($data['TonDau'] != 0 || $data['PhatSinh'] != 0) {
            $tonCuoi = $data['TonDau'] + $data['PhatSinh'];
            $baoCaoKho[] = [
                'MaSach' => $maSach,
                'TenSach' => $data['TenSach'],
                'TonDau' => $data['TonDau'],
                'PhatSinh' => $data['PhatSinh'],
                'TonCuoi' => $tonCuoi,
            ];
            $sumTonDau += $data['TonDau'];
            $sumPhatSinh += $data['PhatSinh'];
            $sumTonCuoi += $tonCuoi;
        }
    }
}

// --- Báo cáo công nợ (Sửa lỗi logic) ---
$baoCaoCongNo = [];
$sumNoDau = $sumNoPhatSinh = $sumNoCuoi = 0;
if ($selectedMonthCongNo) {
    // Lấy tất cả khách hàng
    $sqlKH = "SELECT MaKH, HoTen FROM khachhang ORDER BY MaKH";
    $resultKH = $mysqli->query($sqlKH);
    if ($resultKH) {
        while ($kh = $resultKH->fetch_assoc()) {
            $maKH = $mysqli->real_escape_string($kh['MaKH']);
            
            // Nợ đầu kỳ: (Tổng nợ từ hóa đơn) - (Tổng tiền đã thu) trước kỳ
            $sqlNoDau = "SELECT
                (SELECT IFNULL(SUM(h.TongTien - h.TienTra), 0) FROM hoadon h
                    WHERE h.MaKH = '$maKH' AND (YEAR(h.NgayLap) < $yearCongNo OR (YEAR(h.NgayLap) = $yearCongNo AND MONTH(h.NgayLap) < $monthCongNo)))
                -
                (SELECT IFNULL(SUM(pt.SoTienThu), 0) FROM phieuthutien pt
                    WHERE pt.MaKH = '$maKH' AND (YEAR(pt.NgayThu) < $yearCongNo OR (YEAR(pt.NgayThu) = $yearCongNo AND MONTH(pt.NgayThu) < $monthCongNo)))
                AS NoDau";
            $resultNoDau = $mysqli->query($sqlNoDau);
            $noDau = ($resultNoDau && $row = $resultNoDau->fetch_assoc()) ? (int)$row['NoDau'] : 0;

            // Phát sinh trong kỳ: (Nợ mới từ hóa đơn) - (Tiền thu trong kỳ)
            $sqlPhatSinhNo = "SELECT IFNULL(SUM(h.TongTien - h.TienTra), 0) AS PhatSinhNo FROM hoadon h
                WHERE h.MaKH = '$maKH' AND YEAR(h.NgayLap) = $yearCongNo AND MONTH(h.NgayLap) = $monthCongNo";
            $resultPhatSinhNo = $mysqli->query($sqlPhatSinhNo);
            $phatSinhNo = ($resultPhatSinhNo && $row = $resultPhatSinhNo->fetch_assoc()) ? (int)$row['PhatSinhNo'] : 0;

            $sqlPhatSinhThu = "SELECT IFNULL(SUM(pt.SoTienThu), 0) AS Thu FROM phieuthutien pt
                WHERE pt.MaKH = '$maKH' AND YEAR(pt.NgayThu) = $yearCongNo AND MONTH(pt.NgayThu) = $monthCongNo";
            $resultPhatSinhThu = $mysqli->query($sqlPhatSinhThu);
            $phatSinhThu = ($resultPhatSinhThu && $row = $resultPhatSinhThu->fetch_assoc()) ? (int)$row['Thu'] : 0;
            
            $phatSinh = $phatSinhNo - $phatSinhThu;
            $noCuoi = $noDau + $phatSinh;

            if ($noDau != 0 || $phatSinh != 0) {
                 $baoCaoCongNo[] = [
                    'MaKH' => $kh['MaKH'],
                    'HoTen' => $kh['HoTen'],
                    'NoDau' => $noDau,
                    'PhatSinh' => $phatSinh,
                    'NoCuoi' => $noCuoi
                ];
                $sumNoDau += $noDau;
                $sumNoPhatSinh += $phatSinh;
                $sumNoCuoi += $noCuoi;
            }
        }
        $resultKH->free();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo tổng hợp</title>
    <link rel="stylesheet" href="../../assets/general-style.css">
    <link rel="stylesheet" href="../../assets/reports-style.css">
    <script src="reports-script.js" defer></script>
    <style>
        .filter-group {
            display: flex;
            gap: 10px;
            align-items: center;
            margin: 1rem 0;
        }
        .hidden {
            display: none !important;
        }
    </style>
</head>
<body>
<section class="report-section">
    <h2 class="title">
        <img src="../../assets/sheet.png" class="title-icon">
        Báo cáo tổng hợp
    </h2>

    <!-- Tabs -->

    <div class="report-tabs">
        <button class="report-tab <?php echo (!isset($_GET['active_tab']) || $_GET['active_tab'] === 'ton') ? 'active' : ''; ?>" data-type="ton">Báo cáo kho</button>
        <button class="report-tab <?php echo (isset($_GET['active_tab']) && $_GET['active_tab'] === 'congno') ? 'active' : ''; ?>" data-type="congno">Báo cáo công nợ</button>
    </div>

    <!-- A single form for both filters -->
    <form method="get" id="report-form">
        <input type="hidden" name="active_tab" id="active-tab-input" value="<?php echo htmlspecialchars($_GET['active_tab'] ?? 'ton'); ?>">

        <div class="report-filter">
            <!-- Filter for Inventory Report -->
            <div class="filter-group filter-ton <?php echo (!isset($_GET['active_tab']) || $_GET['active_tab'] === 'ton') ? '' : 'hidden'; ?>">
                <label for="report-month-ton">Chọn tháng:</label>
                <input type="month" id="report-month-ton" name="month_ton" value="<?php echo htmlspecialchars($selectedMonthTon); ?>">
                <button class="filter-btn" type="submit">Xem báo cáo</button>
                <button class="export-btn" type="button">⭳ Xuất Excel</button>
            </div>

            <!-- Filter for Debt Report -->
            <div class="filter-group filter-congno <?php echo (isset($_GET['active_tab']) && $_GET['active_tab'] === 'congno') ? '' : 'hidden'; ?>">
                <label for="report-month-congno">Chọn tháng:</label>
                <input type="month" id="report-month-congno" name="month_congno" value="<?php echo htmlspecialchars($selectedMonthCongNo); ?>">
                <button class="filter-btn" type="submit">Xem báo cáo</button>
                <button class="export-btn" type="button">⭳ Xuất Excel</button>
            </div>
        </div>
    </form>

    <!-- Báo cáo kho -->
    <?php
        $active_tab = $_GET['active_tab'] ?? 'ton';
        $show_ton_report = ($active_tab === 'ton' && $selectedMonthTon);
        $show_congno_report = ($active_tab === 'congno' && $selectedMonthCongNo);
    ?>
    <div class="report-content report-ton <?php echo $show_ton_report ? '' : 'hidden'; ?>">

    <h3>Báo cáo kho tháng 
        <?php
            if ($selectedMonthTon) {
                $m = (int)substr($selectedMonthTon, 5, 2);
                $y = (int)substr($selectedMonthTon, 0, 4);
                echo $m . '/' . $y;
            }
        ?>
    </h3>
        <table class="report-table">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Mã sách</th>
                    <th>Tên sách</th>
                    <th>Tồn đầu</th>
                    <th>Phát sinh</th>
                    <th>Tồn cuối</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($baoCaoKho) > 0): ?>
                    <?php $stt = 1; foreach ($baoCaoKho as $row): ?>
                        <tr>
                            <td><?= $stt++ ?></td>
                            <td><?= htmlspecialchars($row['MaSach']) ?></td>
                            <td><?= htmlspecialchars($row['TenSach'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['TonDau']) ?></td>
                            <td><?= htmlspecialchars($row['PhatSinh']) ?></td>
                            <td><?= htmlspecialchars($row['TonCuoi']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6">Không có dữ liệu cho tháng được chọn.</td></tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">Tổng</th>
                    <th><?= $sumTonDau ?></th>
                    <th><?= $sumPhatSinh ?></th>
                    <th><?= $sumTonCuoi ?></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Báo cáo công nợ -->
    <div class="report-content report-congno <?php echo $show_congno_report ? '' : 'hidden'; ?>">
    <h3>Báo cáo công nợ tháng 
        <?php
            if ($selectedMonthCongNo) {
                $m = (int)substr($selectedMonthCongNo, 5, 2);
                $y = (int)substr($selectedMonthCongNo, 0, 4);
                echo $m . '/' . $y;
            }
        ?>
    </h3>
        <table class="report-table">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Mã khách hàng</th>
                    <th>Tên khách hàng</th>
                    <th>Nợ đầu</th>
                    <th>Phát sinh</th>
                    <th>Nợ cuối</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($baoCaoCongNo) > 0): ?>
                    <?php $stt = 1; foreach ($baoCaoCongNo as $row): ?>
                        <tr>
                            <td><?= $stt++ ?></td>
                            <td><?= htmlspecialchars($row['MaKH']) ?></td>
                            <td><?= htmlspecialchars($row['HoTen'] ?? '') ?></td>
                            <td><?= number_format($row['NoDau'], 0, ',', '.') ?>đ</td>
                            <td><?= number_format($row['PhatSinh'], 0, ',', '.') ?>đ</td>
                            <td><?= number_format($row['NoCuoi'], 0, ',', '.') ?>đ</td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6">Không có dữ liệu cho tháng được chọn.</td></tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">Tổng</th>
                    <th><?= number_format($sumNoDau, 0, ',', '.') ?>đ</th>
                    <th><?= number_format($sumNoPhatSinh, 0, ',', '.') ?>đ</th>
                    <th><?= number_format($sumNoCuoi, 0, ',', '.') ?>đ</th>
                </tr>
            </tfoot>
        </table>
    </div>
</section>
</body>
</html>
