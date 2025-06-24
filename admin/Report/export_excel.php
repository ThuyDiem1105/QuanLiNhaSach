<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require __DIR__ . '/../../vendor/autoload.php';
include __DIR__ . '/../../connect.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

if (!isset($_SESSION['loggedin'])) {
    header('Location: ../../loginFunction/login.php');
    exit;
}

// --- Helper Functions ---
function apply_styles($sheet) {
    // Column widths
    $sheet->getColumnDimension('A')->setWidth(5);
    $sheet->getColumnDimension('B')->setWidth(20);
    $sheet->getColumnDimension('C')->setWidth(35);
    $sheet->getColumnDimension('D')->setWidth(15);
    $sheet->getColumnDimension('E')->setWidth(15);
    $sheet->getColumnDimension('F')->setWidth(15);

    // Header style
    $header_style = [
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0D3C6B']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    ];
    $sheet->getStyle('A3:F3')->applyFromArray($header_style);

    // Title style
    $sheet->mergeCells('A1:F1');
    $title_style = [
        'font' => ['bold' => true, 'size' => 16],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
    ];
    $sheet->getStyle('A1')->applyFromArray($title_style);
    
    // Footer (Total) style
    $last_row = $sheet->getHighestRow();
    $sheet->getStyle('A'.$last_row.':F'.$last_row)->getFont()->setBold(true);

    // All cells border and alignment
    $all_cells_style = [
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
    ];
    $sheet->getStyle('A3:F'.$last_row)->applyFromArray($all_cells_style);
    $sheet->getStyle('D4:F'.$last_row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

}

// --- Get Parameters ---
$report_type = $_GET['report_type'] ?? 'ton';
$selectedMonth = $_GET['month'] ?? date('Y-m');

$month = (int)substr($selectedMonth, 5, 2);
$year = (int)substr($selectedMonth, 0, 4);

// --- Data Calculation ---
$data = [];
$filename_prefix = '';
$report_title = '';
$headers = [];
$totals = [];

// INVENTORY REPORT LOGIC
if ($report_type === 'ton') {
    $filename_prefix = 'BaoCaoKho';
    $report_title = "Báo Cáo Tồn Kho Tháng $month/$year";
    $headers = ['STT', 'Mã Sách', 'Tên Sách', 'Tồn Đầu', 'Phát Sinh', 'Tồn Cuối'];
    
    // Logic from report.php, slightly adapted
    $books_data = [];
    $all_books_result = $mysqli->query("SELECT MaSach, TenSach FROM sach ORDER BY MaSach");
    while ($book = $all_books_result->fetch_assoc()) {
        $books_data[$book['MaSach']] = ['TenSach' => $book['TenSach'], 'TonDau' => 0, 'PhatSinh' => 0];
    }
    
    $sql_imports = "SELECT pn.MaSach, pn.SoLuong, p.NgayNhap FROM chitiet_phieunhap pn JOIN phieunhap p ON p.MaPN = pn.MaPN";
    if ($imports_result = $mysqli->query($sql_imports)) {
        while ($import = $imports_result->fetch_assoc()) {
            if (!isset($books_data[$import['MaSach']])) continue;
            $import_date = new DateTime($import['NgayNhap']);
            if ($import_date->format('Y') < $year || ($import_date->format('Y') == $year && $import_date->format('m') < $month)) {
                $books_data[$import['MaSach']]['TonDau'] += (int)$import['SoLuong'];
            } elseif ($import_date->format('Y') == $year && $import_date->format('m') == $month) {
                $books_data[$import['MaSach']]['PhatSinh'] += (int)$import['SoLuong'];
            }
        }
    }

    $sql_sales = "SELECT ct.MaSach, ct.SoLuong, h.NgayLap FROM chitiet_hoadon ct JOIN hoadon h ON h.MaHD = ct.MaHD";
    if ($sales_result = $mysqli->query($sql_sales)) {
        while ($sale = $sales_result->fetch_assoc()) {
             if (!isset($books_data[$sale['MaSach']])) continue;
            $sale_date = new DateTime($sale['NgayLap']);
            if ($sale_date->format('Y') < $year || ($sale_date->format('Y') == $year && $sale_date->format('m') < $month)) {
                $books_data[$sale['MaSach']]['TonDau'] -= (int)$sale['SoLuong'];
            } elseif ($sale_date->format('Y') == $year && $sale_date->format('m') == $month) {
                $books_data[$sale['MaSach']]['PhatSinh'] -= (int)$sale['SoLuong'];
            }
        }
    }

    $stt = 1;
    foreach ($books_data as $maSach => $book) {
        if ($book['TonDau'] != 0 || $book['PhatSinh'] != 0) {
            $tonCuoi = $book['TonDau'] + $book['PhatSinh'];
            $data[] = [$stt++, $maSach, $book['TenSach'], $book['TonDau'], $book['PhatSinh'], $tonCuoi];
        }
    }
    $totals = [
        'D' => '=SUM(D4:D' . (count($data) + 3) . ')',
        'E' => '=SUM(E4:E' . (count($data) + 3) . ')',
        'F' => '=SUM(F4:F' . (count($data) + 3) . ')'
    ];
}

// DEBT REPORT LOGIC
if ($report_type === 'congno') {
    $filename_prefix = 'BaoCaoCongNo';
    $report_title = "Báo Cáo Công Nợ Khách Hàng Tháng $month/$year";
    $headers = ['STT', 'Mã KH', 'Tên Khách Hàng', 'Nợ Đầu', 'Phát Sinh', 'Nợ Cuối'];

    $sqlKH = "SELECT MaKH, HoTen FROM khachhang ORDER BY MaKH";
    if ($resultKH = $mysqli->query($sqlKH)) {
        $stt = 1;
        while ($kh = $resultKH->fetch_assoc()) {
            $maKH = $mysqli->real_escape_string($kh['MaKH']);
            
            $sqlNoDau = "SELECT (SELECT IFNULL(SUM(h.TongTien - h.TienTra), 0) FROM hoadon h WHERE h.MaKH = '$maKH' AND (YEAR(h.NgayLap) < $year OR (YEAR(h.NgayLap) = $year AND MONTH(h.NgayLap) < $month))) - (SELECT IFNULL(SUM(pt.SoTienThu), 0) FROM phieuthutien pt WHERE pt.MaKH = '$maKH' AND (YEAR(pt.NgayThu) < $year OR (YEAR(pt.NgayThu) = $year AND MONTH(pt.NgayThu) < $month))) AS NoDau";
            $noDau = (int)$mysqli->query($sqlNoDau)->fetch_assoc()['NoDau'];

            $sqlPhatSinhNo = "SELECT IFNULL(SUM(h.TongTien - h.TienTra), 0) AS PhatSinhNo FROM hoadon h WHERE h.MaKH = '$maKH' AND YEAR(h.NgayLap) = $year AND MONTH(h.NgayLap) = $month";
            $phatSinhNo = (int)$mysqli->query($sqlPhatSinhNo)->fetch_assoc()['PhatSinhNo'];

            $sqlPhatSinhThu = "SELECT IFNULL(SUM(pt.SoTienThu), 0) AS Thu FROM phieuthutien pt WHERE pt.MaKH = '$maKH' AND YEAR(pt.NgayThu) = $year AND MONTH(pt.NgayThu) = $month";
            $phatSinhThu = (int)$mysqli->query($sqlPhatSinhThu)->fetch_assoc()['Thu'];
            
            $phatSinh = $phatSinhNo - $phatSinhThu;
            $noCuoi = $noDau + $phatSinh;

            if ($noDau != 0 || $phatSinh != 0) {
                 $data[] = [$stt++, $kh['MaKH'], $kh['HoTen'], $noDau, $phatSinh, $noCuoi];
            }
        }
    }
     $totals = [
        'D' => '=SUM(D4:D' . (count($data) + 3) . ')',
        'E' => '=SUM(E4:E' . (count($data) + 3) . ')',
        'F' => '=SUM(F4:F' . (count($data) + 3) . ')'
    ];
}

// --- Spreadsheet Generation ---
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set Title
$sheet->getCell('A1')->setValue($report_title);

// Set Headers
$sheet->fromArray($headers, null, 'A3');

// Set Data
$sheet->fromArray($data, null, 'A4');

// Set Totals
$last_row = count($data) + 4;
$sheet->getCell('C' . $last_row)->setValue('Tổng');
foreach($totals as $col => $formula) {
    $sheet->getCell($col . $last_row)->setValue($formula);
}

// Apply Styles
apply_styles($sheet);
if ($report_type === 'congno') {
     // Format currency columns
    $currency_format = '#,##0 "đ"';
    $sheet->getStyle('D4:F'.$last_row)->getNumberFormat()->setFormatCode($currency_format);
}


// --- Output to browser ---
$filename = $filename_prefix . "_$month-$year.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

$mysqli->close();
exit;
?>