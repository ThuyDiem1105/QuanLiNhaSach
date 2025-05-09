document.getElementById("loginForm").addEventListener("submit", function (e) {
  e.preventDefault();
  const username = document.getElementById("username").value;
  const password = document.getElementById("password").value;

  // Giả lập kiểm tra 
  if (username === "admin" && password === "123456") {
    window.location.href = "index.html"; // chuyển tới trang chủ
  } else {
    document.getElementById("error").textContent = "Sai tên đăng nhập hoặc mật khẩu!";
  }
});
