<?php
session_start();
include __DIR__ . '/../../connect.php';
// Đọc danh mục sách
$danhMucArr = [];
$result = $mysqli->query("SELECT MaDMS, TenDanhMuc FROM danhmucsach");
while ($row = $result->fetch_assoc()) {
    $danhMucArr[$row['MaDMS']] = $row['TenDanhMuc'];
}
$result->free();

//đọc thể loại
$theLoaiArr = [];
$result = $mysqli->query("SELECT MaTL, TenTheLoai FROM theloai");
while ($row = $result->fetch_assoc()) {
    $theLoaiArr[$row['MaTL']] = $row['TenTheLoai'];
}
$result->free();

$result = $mysqli->query("SELECT * FROM sach");
?>


<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý sách</title>
  <link rel="stylesheet" href="../../style.css" type="text/css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
  <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
</head>
<body>
  <div class="main-content">
    <div class="header">
      <div class="search-filter">
        <input type="text" id="searchTensach" name="ten_sach" placeholder="Tìm theo tên...">
        <input type="text" id="searchDanhmuc" name="danh_muc" placeholder="Tìm theo danh mục..">
        <input type="text" id="searchTheloai" name="the_loai" placeholder="Tìm theo thể loại...">
      </div>
      <button class="add-button" onclick="createNewBook()">+ Thêm sách mới</button>
    </div>

    <table id="bookTable">
      <thead>
        <tr>
          <th>Mã sách</th>
          <th>Tên sách</th>
          <th>Danh mục</th>
          <th>Mã thể loại</th>
          <th>Tác giả</th>
          <th>Số lượng tồn</th>
          <th>Giá bán</th>
          <th>Chi tiết</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <?php
        $tlArr = explode(',', $row['TheLoai']);
        $tenTheloaiArr = [];
        foreach ($tlArr as $theloai){
          $tenTheloaiArr[] = $theLoaiArr[$theloai];
        }
        ?>
        <tr>
          <td><?= htmlspecialchars($row['MaSach']) ?></td>
          <td><?= htmlspecialchars($row['TenSach']) ?></td>
          <td><?= htmlspecialchars($danhMucArr[$row['MaDMS']]) ?></td>
          <td><?= htmlspecialchars(implode(', ', $tenTheloaiArr)) ?></td>
          <td><?= htmlspecialchars($row['TacGia']) ?></td>
          <td><?= htmlspecialchars($row['SoLuongTon']) ?></td>
          <td><?= htmlspecialchars($row['GiaBan']) ?></td>
          <td class="action-buttons">
            <button class="view-btn" onclick="openForm(
              '<?= $row['MaSach'] ?>',
              '<?= $row['TenSach'] ?>',
              '<?= $row['MaDMS'] ?>',
              '<?= $row['TheLoai'] ?>',
              '<?= $row['TacGia'] ?>',
              '<?= $row['NhaXuatBan'] ?>',
              '<?= $row['NgayXuatBan'] ?>',
              '<?= $row['NgonNgu'] ?>',
              '<?= $row['SoLuongTon'] ?>',
              '<?= $row['GiaBan'] ?>'
            )">Xem</button>
            <button class="delete-btn" onclick="deleteBook('<?= $row['MaSach'] ?>')">Xóa</button>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    <div id="toast"></div>
  </div>

  <div id="bookFormOverlay" class="overlay">
    <div class="form-popup">
      <h3>Chi tiết sách</h3>
      <form id="bookForm" onsubmit="return false;" action="" method="post" novalidate>
        <input type="hidden" id="form_mode" name="form_mode" value="new">

        <label>Mã sách:</label><input type="text" name="ma_sach" required readonly>
        <span class="error" id="error_masach"></span>

        <label>Tên sách:</label><input type="text" name="ten_sach" required readonly>
        <span class="error" id="error_tensach"></span>
        
        <label>Danh mục sách:</label>
        <select id="danh_muc" name="danh_muc" required>
          <option value="">-- Chọn danh mục sách --</option>
          <?php foreach ($danhMucArr as $ma_danhmuc => $ten_danhmuc): ?>
              <option value="<?= $ma_danhmuc ?>"><?= $ten_danhmuc ?></option>
          <?php endforeach; ?>
        </select>
        <span class="error" id="error_danhmuc"></span>

        <label>Thể loại tương ứng:</label>
         <select id="the_loai" name="the_loai[]" multiple required>
            <option value="">-- Chọn thể loại tương ứng --</option>
            <?php foreach ($theLoaiArr as $ma_theloai => $ten_theloai): ?>
              <option value="<?= $ma_theloai ?>"><?= $ten_theloai ?></option>
            <?php endforeach; ?>
          </select>
          <span class="error" id="error_theloai"></span>

        <label>Tác giả:</label>
        <input type="text" name="tac_gia" required readonly>
        <span class="error" id="error_tacgia"></span>

        <label>Ngôn ngữ:</label><input type="text" name="ngon_ngu" required readonly>
        <span class="error" id="error_ngonngu"></span>
        
        <label>Nhà xuất bản:</label><input type="text" name="nxb" required readonly>
        <span class="error" id="error_nxb"></span>
        
        <label>Ngày xuất bản:</label><input type="date" name="ngay_xb" required readonly>
        <span class="error" id="error_ngayxb"></span>

        
        <label>Số lượng tồn:</label><input type="number" name="sl_ton" required readonly>
        <span class="error" id="error_slton"></span>

        <label>Đơn giá bán:</label><input type="text" name="gia_ban" required readonly>
        <span class="error" id="error_giaban"></span>
        
        <div class="form-buttons">
          <button type="submit" class="btn-save" onclick="saveBook()" style="display: none;">Lưu</button>
          <button type="button" class="btn-edit" onclick="enableEditing()">Sửa</button>
          <button type="button" class="btn-cancel" onclick="closeForm()">Đóng</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    let editingIndex = -1;
    let danhmucChoices, theloaiChoices;

    // Khởi tạo danh sách các danh mục và thể loại
    window.addEventListener("DOMContentLoaded", () => {
      //không sort mà để theo thứ tự đã initilize, ít nên ẩn tìm kiếm
      danhmucChoices = new Choices("#danh_muc", { shouldSort: false, searchEnabled: true });
      theloaiChoices = new Choices("#the_loai", { removeItemButton: true, shouldSort: false, searchEnabled: true });
      danhmucChoices.disable();
      theloaiChoices.disable();
    });

    // Button Xem chi tiết sách
    function openForm(maSach, tenSach, danhMuc, theLoaiStr, tacGia, nhaXB, ngayXB, ngonNgu, soluongTon, giaBan) {
      document.getElementById("bookFormOverlay").classList.add("show");

      const form = document.forms['bookForm'];
      form.ma_sach.value = maSach;
      form.ten_sach.value = tenSach;
      form.tac_gia.value = tacGia;
      form.ngon_ngu.value = ngonNgu;
      form.nxb.value = nhaXB;
      form.ngay_xb.value = ngayXB;
      form.sl_ton.value = soluongTon;
      form.gia_ban.value = parseInt(giaBan);

      form.danh_muc.value = danhMuc;
      danhmucChoices.setChoiceByValue(danhMuc);

      if(theLoaiStr){
        const theLoaiArr = theLoaiStr.split(',').map(s => s.trim());
        theloaiChoices.removeActiveItems();
        theLoaiArr.forEach(val => { theloaiChoices.setChoiceByValue(val); });
      }

      //lấy vị trí dòng (sách) được chọn để xem
      editingIndex = Array.from(document.querySelector('#sachTable tbody').rows)
        .findIndex(row => row.cells[0].textContent === maSach);
      
      // hiện tại chỉ được phép xem, không được chỉnh sửa thông tin  
      for (let input of form.querySelectorAll("input")) input.readOnly = true;
      danhmucChoices.disable();
      theloaiChoices.disable();

      //đang ở chế độ xem nên ẩn nút Lưu
      document.querySelector(".btn-save").style.display = "none";
      document.querySelector(".btn-edit").style.display = "inline-block";
      document.getElementById("bookFormOverlay").classList.add("show");
      bookFormOverlay.classList.add("show");
    }

    // Button Sửa
    function enableEditing() {
      const form = document.forms['bookForm'];
      document.getElementById("form_mode").value = "edit";

      for (let input of form.querySelectorAll("input")) {
        if (input.name !== "ma_sach" && input.name !== "ten_sach") input.readOnly = false;
      }
      danhmucChoices.enable();
      theloaiChoices.enable();

      const maSach = form.ma_sach.value;

      document.querySelector(".btn-save").style.display = "inline-block";
      document.querySelector(".btn-edit").style.display = "none";
    }

    // Kiểm tra thông tin form
    function checkValidFormValues(form) {
      let isValid = true;
      document.querySelectorAll(".error").forEach(el => el.textContent = "");

      const maSach = form.ma_sach.value.trim();
      const tenSach = form.ten_sach.value.trim();

      const danhMuc = form.danh_muc.value;
      const theLoai = Array.from(form.the_loai.selectedOptions).map(o => o.value);

      const tacGia = form.tac_gia.value.trim();
      const nhaXB = form.nxb.value.trim();
      const ngayXB = form.ngay_xb.value;
      const ngonNgu = form.ngon_ngu.value.trim();
      const soluongTon = form.sl_ton.value;
      const giaBan = parseInt(form.gia_ban.value);

      // Tên sách
      if (!tenSach) {
        document.getElementById("error_tensach").textContent = "Tên sách không được để trống!";
        isValid = false;
      }
      // Ngày xuất bản
      if (!ngayXB) {
        document.getElementById("error_ngayxb").textContent = "Vui lòng chọn ngày xuất bản hợp lệ!";
        isValid = false;
      } else if (new Date(ngayXB) >= new Date()) {
        document.getElementById("error_ngayxb").textContent = "Ngày xuất bản phải trước ngày hiện tại!";
        isValid = false;
      }
      // Tác giả
      if (!tacGia) {
        document.getElementById("error_tacgia").textContent = "Tác giả không được để trống!";
        isValid = false;
      }
      //Ngôn ngữ
      if (!ngonNgu) {
        document.getElementById("error_ngonngu").textContent = "Ngôn ngữ không được để trống!";
        isValid = false;
      }
      //Nhà xuất bản
      if (!nhaXB) {
        document.getElementById("error_nxb").textContent = "Nhà xuất bản không được để trống!";
        isValid = false;
      }
      // Danh mục sách
      if (!danhMuc) {
        document.getElementById("error_danhmuc").textContent = "Danh mục sách không được để trống!";
        isValid = false;
      }
      // Thể loại
      if (!theLoai) {
        document.getElementById("error_theloai").textContent = "Vui lòng chọn ít nhất một thể loại cho sách!";
        isValid = false;
      }
      // Số lượng tồn
      // if (!soluongTon) {
      //   document.getElementById("error_slton").textContent = "Vui lòng thêm số lượng tồn của sách!";
      //   isValid = false;
      // }
      // Gía bán
      if (!giaBan) {
        document.getElementById("error_giaban").textContent = "Vui lòng thêm giá bán cho sách!";
        isValid = false;
      }
      return isValid;
    }

    //Button Lưu sách
    function saveBook() {
      const form = document.forms['bookForm'];
      const table = document.getElementById("bookTable").getElementsByTagName("tbody")[0];
      if(!checkValidFormValues(form)) return;

      const formMode = document.getElementById("form_mode").value;
      const maSach = form.ma_sach.value.trim();
      const tenSach = form.ten_sach.value.trim();

      const danhMuc = form.danh_muc.value;
      const theLoaiArr = Array.from(form.the_loai.selectedOptions).map(o => o.value);
      const theLoaiStr = theLoaiArr.join(',');

      const tacGia = form.tac_gia.value.trim();
      const nhaXB = form.nxb.value.trim();
      const ngayXB = form.ngay_xb.value;
      const ngonNgu = form.ngon_ngu.value.trim();
      const soluongTon = form.sl_ton.value;
      const giaBan = parseInt(form.gia_ban.value);

      const formData = new FormData(form);    
      formData.append("form_mode", formMode);
      formData.append("danh_muc", danhMuc);
      formData.append("gia_ban", giaBan);
      formData.append("the_loai", theLoaiStr);
      fetch('save_book.php', {
        method: "POST",
        body: formData,
      })
      .then(res => res.text())
      .then(response => {
        console.log("Raw response:", response);
        if(response.trim() === "OK") {
          showToast("Lưu thông tin thành công!");
          setTimeout(() => {
            location.reload();
          }, 1000);
        } else if (response.trim() === "book_exists") {
          alert("Sách đã tồn tại! Bạn có thể cập nhật lại thông tin sách.");
        } else {
          alert("Lỗi: " + response);
        }
      })
      .catch(error => {
        console.error("Lỗi: ", error);
        alert("Lỗi khi gửi dữ liệu.");
      });
      closeForm();
    }

    //Button Thêm sách mới
    function createNewBook() {
      const form = document.forms['bookForm'];
      document.getElementById("form_mode").value = "new";

      form.reset();
      const table = document.getElementById("bookTable").getElementsByTagName("tbody")[0];
      const nextId = "SACH" + String(table.rows.length + 1).padStart(3, '0');
      form.ma_sach.value = nextId;
      for (let input of form.querySelectorAll("input")) {
        if (input.name !== "ma_sach" && input.name !== "sl_ton") input.readOnly = false;
      }
      document.querySelector(".btn-save").style.display = "inline-block";
      document.querySelector(".btn-edit").style.display = "none";

      editingIndex = -1;
      danhmucChoices.enable();
      theloaiChoices.enable();
      form.danh_muc.selectedIndex = 0;
      theloaiChoices.removeActiveItems();

      document.getElementById("bookFormOverlay").classList.add("show");
    }

    //Hiển thị tin nhắn thông báo
    function showToast(message) {
      const toast = document.getElementById("toast");
      toast.textContent = message;
      toast.classList.add("show");
      setTimeout(() => toast.classList.remove("show"), 3000);
    }

    document.addEventListener("keydown", e => {
      if (e.key === "Escape") closeForm();
    });

    document.getElementById("bookFormOverlay").addEventListener("click", e => {
      if (e.target === e.currentTarget) closeForm();
    });

    
    document.querySelectorAll('button').forEach(btn => {
      btn.addEventListener('click', e => {
        const circle = document.createElement('span');
        circle.classList.add('ripple');
        circle.style.left = `${e.offsetX}px`;
        circle.style.top = `${e.offsetY}px`;
        btn.appendChild(circle);
        setTimeout(() => circle.remove(), 600);
      });
    });

    // Button Xóa sách
    function deleteBook(maSach) {
      if (confirm("Bạn có chắc muốn xóa sách này không?")) {
        fetch('delete_book.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: 'ma_sach=' + encodeURIComponent(maSach),
        })
        .then(res => res.text())
        .then(response => {
          if(response.trim() === "OK") {
            showToast("Xóa sách thành công!");
            setTimeout(() => {
            location.reload();
            }, 1000);
          } else {
            alert("Lỗi: " + response);
          }
        })
        .catch(error => {
          console.error("Lỗi: ", error);
          alert("Lỗi khi xóa sách.");
        });
      }
    }

    
    //Tìm kiếm 
    document.getElementById("searchTensach").addEventListener("input", filterTable);
    document.getElementById("searchDanhmuc").addEventListener("input", filterTable);
    document.getElementById("searchTheloai").addEventListener("input", filterTable);

    function filterTable() {
      const tensachFilter = document.getElementById("searchTensach").value.toLowerCase();
      const danhmucFilter = document.getElementById("searchDanhmuc").value.toLowerCase();
      const theloaiFilter = document.getElementById("searchTheloai").value.toLowerCase();

      const rows = document.querySelectorAll("#bookTable tbody tr");

      rows.forEach(row => {
        const tensach = row.cells[1].textContent.toLowerCase();
        const danhmuc = row.cells[2].textContent.toLowerCase();
        const theloai = row.cells[3].textContent.toLowerCase();
        const matchTensach = tensach.includes(tensachFilter);
        const matchDanhmuc = danhmuc.includes(danhmucFilter);
        const matchTheloai = theloai.includes(theloaiFilter);

        if (matchDanhmuc && matchTensach && matchTheloai) {
          row.style.display = "";
        } else {
          row.style.display = "none";
        }
      });
    }    

    function closeForm() {
      document.getElementById("bookFormOverlay").classList.remove("show");
    }
  </script>
</body>
</html>
