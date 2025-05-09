document.addEventListener("DOMContentLoaded", () => {
    const user = JSON.parse(localStorage.getItem("user"));
    if (!user || user.role !== "admin") {
      alert("Không có quyền truy cập");
      window.location.href = "../login.html";
      return;
    }
  
    const products = [
      { id: 1, name: "Loa Lớn", price: 1000000, stock: 15 },
      { id: 2, name: "Loa Mini", price: 500000, stock: 30 },
      { id: 3, name: "Radio", price: 300000, stock: 20 },
    ];
  
    const table = document.getElementById("productTable");
    products.forEach(p => {
      table.innerHTML += `
        <tr>
          <td class="px-4 py-2 border">${p.id}</td>
          <td class="px-4 py-2 border">${p.name}</td>
          <td class="px-4 py-2 border">${p.price.toLocaleString()}đ</td>
          <td class="px-4 py-2 border">${p.stock}</td>
        </tr>
      `;
    });
  });
  