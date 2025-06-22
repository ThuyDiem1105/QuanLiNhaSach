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

// --- Truy vấn báo cáo kho ---
$baoCaoKho = [];
$sumTonDau = $sumPhatSinh = $sumTonCuoi = 0;
if ($selectedMonthTon) {
    $sqlKho = "SELECT k.MaSach, s.TenSach, k.TonDau, k.PhatSinh, k.TonCuoi 
               FROM baocaokho k 
               LEFT JOIN sach s ON k.MaSach COLLATE utf8mb4_unicode_ci = s.MaSach COLLATE utf8mb4_unicode_ci 
               WHERE k.Thang = $monthTon AND k.Nam = $yearTon 
               ORDER BY k.MaSach";
    $resultKho = $mysqli->query($sqlKho);
    if ($resultKho) {
        while ($row = $resultKho->fetch_assoc()) {
            $baoCaoKho[] = $row;
            $sumTonDau += $row['TonDau'];
            $sumPhatSinh += $row['PhatSinh'];
            $sumTonCuoi += $row['TonCuoi'];
        }
        $resultKho->free();
    }
}

// --- Truy vấn báo cáo công nợ ---
$baoCaoCongNo = [];
$sumNoDau = $sumNoPhatSinh = $sumNoCuoi = 0;
if ($selectedMonthCongNo) {
    $sqlCongNo = "SELECT c.MaKH, k.HoTen, c.NoDau, c.PhatSinh, c.NoCuoi 
                  FROM baocaocongno c 
                  LEFT JOIN khachhang k ON c.MaKH COLLATE utf8mb4_unicode_ci = k.MaKH COLLATE utf8mb4_unicode_ci 
                  WHERE c.Thang = $monthCongNo AND c.Nam = $yearCongNo 
                  ORDER BY c.MaKH";
    $resultCongNo = $mysqli->query($sqlCongNo);
    if ($resultCongNo) {
        while ($row = $resultCongNo->fetch_assoc()) {
            $baoCaoCongNo[] = $row;
            $sumNoDau += $row['NoDau'];
            $sumNoPhatSinh += $row['PhatSinh'];
            $sumNoCuoi += $row['NoCuoi'];
        }
        $resultCongNo->free();
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

    <!-- Form -->
    <form method="get">
        <div class="report-filter">
            <div class="filter-group filter-ton">
                <label for="report-month-ton">Chọn tháng:</label>
                <input type="month" id="report-month-ton" name="month_ton"
                       value="<?php echo htmlspecialchars($selectedMonthTon); ?>">
                <button class="filter-btn" type="submit">Xem báo cáo</button>
                <button class="export-btn" type="button">⭳ Xuất Excel</button>
            </div>
        </div>
    </form>

    <form method="get">
        <div class="report-filter">
            <div class="filter-group filter-congno" style="display:none;">
                <label for="report-month-congno">Chọn tháng:</label>
                <input type="month" id="report-month-congno" name="month_congno"
                       value="<?php echo htmlspecialchars($selectedMonthCongNo); ?>">
                <button class="filter-btn" type="submit">Xem báo cáo</button>
                <button class="export-btn" type="button">⭳ Xuất Excel</button>
            </div>
        </div>
    </form>

    <!-- Báo cáo kho -->
    <div class="report-content report-ton <?php echo $selectedMonthTon ? '' : 'hidden'; ?>">

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
    <div class="report-content report-congno <?php echo $selectedMonthCongNo ? '' : 'hidden'; ?>">
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
