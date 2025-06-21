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
    <link rel="stylesheet" href="../../assets/rules-style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: #f2f6fa;
            margin: 0;
            padding: 30px;
            color: #2c3e50;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }

        h2 {
            text-align: center;
            color: #1e8449;
            margin-bottom: 25px;
        }

        .profile-detail {
            font-size: 18px;
            margin-bottom: 20px;
            color: #34495e;
        }

        table#shiftTable {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        table#shiftTable th,
        table#shiftTable td {
            padding: 14px 10px;
            border: 1px solid #ddd;
        }

        table#shiftTable th {
            background-color: #27ae60;
            color: white;
            font-size: 15px;
        }

        table#shiftTable tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table#shiftTable tbody tr:hover {
            background-color: #eaf3fc;
        }

        table#shiftTable input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #27ae60;
            cursor: pointer;
        }

        input[type="checkbox"]:checked {
            box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.3);
        }

        label {
            font-size: 16px;
            font-weight: 500;
            display: block;
            margin-top: 20px;
            color: #2c3e50;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-top: 10px;
            display: block;
        }
    </style>

    </head>
    <body>
        <div class="container">
            <h2>LỊCH LÀM VIỆC</h2>

            <div class="profile-detail">
                <strong>Mức lương hiện tại (VNĐ/ca):</strong> <?= htmlspecialchars($luong) ?>
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