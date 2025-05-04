<?php
// 1) Define your lists
$categories = [
  'reference' => 'Tham khảo',
  'comic'     => 'Truyện tranh',
  'science'   => 'Khoa học',
];

$subtypesMap = [
  'reference' => ['Encyclopedia','Dictionary','Manual'],
  'comic'     => ['Horror','Science Fiction','Action','Funny'],
  'science'   => ['Biology','Physics','Chemistry'],
];

// 2) Read the selected category & subtype from the URL (or default '')
$selectedCat = $_GET['category'] ?? '';
$selectedSub = $_GET['subtype']  ?? '';

// 3) Pick the right sub‐type list (or empty)
$subtypes = $subtypesMap[$selectedCat] ?? [];
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Cascading no-JS</title></head>
<body>
  <form method="get" action="">
    <label for="category">Thể loại:</label>
    <select name="category" id="category">
      <option value="">-- Chọn thể loại --</option>
      <?php foreach ($categories as $key => $label): ?>
        <option
          value="<?= $key ?>"
          <?= $key === $selectedCat ? 'selected' : '' ?>>
          <?= $label ?>
        </option>
      <?php endforeach; ?>
    </select>

    <!-- The “Lọc” button simply reloads the page with ?category=… -->
    <button type="submit">Lọc</button>

    <br><br>

    <label for="subtype">Chủ đề:</label>
    <select name="subtype" id="subtype">
      <option value="">-- Chọn chủ đề --</option>
      <?php foreach ($subtypes as $option): ?>
        <option
          value="<?= $option ?>"
          <?= $option === $selectedSub ? 'selected' : '' ?>>
          <?= $option ?>
        </option>
      <?php endforeach; ?>
    </select>

    <br><br>

    <!-- Final “Gửi” to actually submit both -->
    <button type="submit" name="doSubmit" value="1">Gửi</button>
  </form>

<?php
if (isset($_GET['doSubmit'])) {
  echo "<h2>Bạn đã chọn:</h2>";
  echo "Thể loại: <strong>" . htmlspecialchars($categories[$selectedCat] ?? '—') . "</strong><br>";
  echo "Chủ đề:   <strong>" . htmlspecialchars($selectedSub ?: '—') . "</strong><br>";
}
?>
</body>
</html>
