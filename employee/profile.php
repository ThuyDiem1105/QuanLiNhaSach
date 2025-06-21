<?php
session_start();
include __DIR__ . '/../connect.php';

if (!isset($_SESSION['loggedin'])){     
    header('Location: ../../loginFunction/login.php'); 
}

$id = $_SESSION['id'];
$stmt = $mysqli->prepare('SELECT CaLam, Luong FROM nhanvien WHERE MaNV = ?');
$stmt->bind_param('s', $id);
$stmt->execute();
$stmt->bind_result($caLam, $luong);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html>
    <head>
    <meta charset="UTF-8">
    <title>LỊCH LÀM VIỆC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/style.css" type="text/css">
    <style>
        body {
            font-family: fontweb;
            background-color: #f7faff;
            margin: 0;
            padding: 30px;
            color: #495057;
        }

        .container {
            display: block;
            width: 100%;
            max-width: 1100px;
            margin: auto;
            background: #ffffff;
            padding: 35px 50px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.07);
            border: 1px solid #e9ecef;
        }

        h2 {
            text-align: center;
            color: #0d3c6b;
            margin-bottom: 25px;
            font-size: 26px;
            font-weight: 700;
            padding-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
        }

        .profile-detail {
            font-size: 17px;
            margin-bottom: 30px;
            color: #2a617a;
            background-color: #e6f7ff;
            padding: 18px 25px;
            border-radius: 12px;
            border: 1px solid #cce9ff;
        }
        
        .profile-detail strong {
            color: #004a7c;
        }

        table#shiftTable {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: none;
            border: 1px solid #e9ecef;
        }

        table#shiftTable th,
        table#shiftTable td {
            padding: 16px;
            border: none;
            border-bottom: 1px solid #e9ecef;
            text-align: center;
            vertical-align: middle;
        }
        
        table#shiftTable tr:last-child td {
            border-bottom: none;
        }

        table#shiftTable th {
            background-color: #e6fcf5;
            color: #0d3c6b;
            font-size: 15px;
            font-weight: 600;
        }

        table#shiftTable td {
            font-size: 15px;
        }

        table#shiftTable tbody tr:nth-child(even) {
            background-color: #ffffff;
        }

        table#shiftTable tbody tr:hover {
            background-color: #f1f9ff;
        }

        table#shiftTable input[type="checkbox"] {
            transform: scale(1.4);
            accent-color: #1c5083;
            cursor: pointer;
        }

        input[type="checkbox"]:checked {
            box-shadow: none;
        }

        label {
            font-size: 18px;
            font-weight: 600;
            display: block;
            margin-top: 30px;
            margin-bottom: 10px;
            color: #0d3c6b;
        }

        .error {
            color: #e74c3c;
            font-size: 15px;
            margin-top: 15px;
            display: block;
            text-align: center;
            background-color: #fde_DE_e;
            padding: 10px;
            border-radius: 5px;
        }
    </style>

    </head>
    <body>
        <div class="container">
            <h2>LỊCH LÀM VIỆC</h2>

            <div class="profile-detail">
                <strong>Mức lương hiện tại (VNĐ/giờ):</strong> <?= htmlspecialchars($luong) ?>
            </div>

            <label><strong>Ca làm trong tuần của bạn:</strong></label>
            <table id="shiftTable">
                <thead>
                    <tr>
                        <th>Ngày</th>
                        <th>Ca 1 (sáng)<br>7:00–10:30</th>
                        <th>Ca 2 (trưa)<br>10:30–14:00</th>
                        <th>Ca 3 (chiều)<br>14:00–17:30</th>
                        <th>Ca 4 (tối)<br>17:30–21:00</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $days = ['Mon' => 'Thứ 2', 'Tue' => 'Thứ 3', 'Wed' => 'Thứ 4', 'Thu' => 'Thứ 5', 'Fri' => 'Thứ 6', 'Sat' => 'Thứ 7', 'Sun' => 'Chủ nhật'];
                    foreach ($days as $key => $label): ?>
                        <tr>
                            <td><?= $label ?></td>
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                                <td>
                                    <input
                                        type="checkbox"
                                        name="shifts[]"
                                        value="<?= "{$key}-ca{$i}" ?>"
                                        <?= in_array("{$key}-ca{$i}", $_POST['shifts'] ?? [], true) ? 'checked' : '' ?>>
                                </td>
                            <?php endfor; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <span class="error" id="error_ca_lam"></span>
        </div>

        <script>
            const caLam = <?= json_encode($caLam) ?>;

            function displayShifts() {
                const checkboxes = document.querySelectorAll('#shiftTable input[type=checkbox]');
                checkboxes.forEach(cb => cb.checked = false);

                if (caLam) {
                    const shifts = caLam.split(',');
                    shifts.forEach(shift => {
                        const cb = document.querySelector(`#shiftTable input[type="checkbox"][value="${shift.trim()}"]`);
                        if (cb) cb.checked = true;
                    });
                }
            }

            displayShifts();
        </script>
    </body>
    <!-- <body>
        <div class="profile-detail">
            <strong>Mức lương hiện tại (VNĐ/ca): </strong><?=htmlspecialchars($luong)?>
        </div>

        <label><strong>Ca làm trong tuần của bạn: </strong></label>
        <table id="shiftTable" style="border-collapse: collapse; width: 100%; text-align: center;">
            <thead>
                <tr>
                    <th>Ngày</th>
                    <th>Ca 1 (sáng) từ 7:00-10:30</th>
                    <th>Ca 2 (trưa) từ 10:30-14:00</th>
                    <th>Ca 3 (chiều) từ 14:00-17:30</th>
                    <th>Ca 4 (tối) từ 17:30-21:00</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $days = ['Mon'=>'Thứ 2','Tue'=>'Thứ 3','Wed'=>'Thứ 4','Thu'=>'Thứ 5','Fri'=>'Thứ 6','Sat'=>'Thứ 7','Sun'=>'Chủ nhật'];
                foreach ($days as $key => $label): ?>
                <tr>
                    <td><?= $label ?></td>
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                    <td style="text-align:center">
                    <input
                    type="checkbox"
                    name="shifts[]"
                    value="<?= "{$key}-ca{$i}" ?>"
                    <?= in_array("{$key}-ca{$i}", $_POST['shifts'] ?? [], true) ? 'checked' : '' ?>>
                    </td>
                    <?php endfor; ?> 
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <span class="error" id="error_ca_lam"></span>

        <script>
            const caLam = <?= json_encode($caLam) ?>;

            function displayShifts() {
            //tất cả checkbox ca làm đều trống khi load
            const checkboxes = document.querySelectorAll('#shiftTable input[type=checkbox]');
            checkboxes.forEach(cb => cb.checked = false);
            //nếu nhân viên đã có lịch làm việc
            if (caLam) {
                //ca làm được lưu trữ dưới dạng mảng: [Mon-ca1, Tue-ca2] -> ca 1 thứ 2 và ca 2 thứ 3
                const shifts = caLam.split(',');
                shifts.forEach(shift => {
                    const trimmedShift = shift.trim();
                    const cb = document.querySelector(`#shiftTable input[type="checkbox"][value="${trimmedShift}"]`);
                    if (cb) cb.checked = true;
                });
            }
        }

        displayShifts();
        </script>
    </body> -->
</html>