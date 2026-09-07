// SwiftMart - main.js
// TODO (backend phase): thay các hàm dưới bằng fetch() gọi tới
// public/cart-add.php, public/wishlist-toggle.php (kèm CSRF token, session).

function addToCart(productId, quantity) {
  quantity = quantity || 1;
  console.log('Thêm vào giỏ:', productId, 'số lượng:', quantity);

  // Cập nhật tạm số lượng hiển thị trên icon giỏ hàng (client-side only)
  var countEl = document.getElementById('cart-count');
  if (countEl) {
    countEl.textContent = (parseInt(countEl.textContent) || 0) + parseInt(quantity);
  }

  // fetch(BASE_URL + 'cart-add.php', {
  //   method: 'POST',
  //   headers: {'Content-Type': 'application/json'},
  //   body: JSON.stringify({ product_id: productId, quantity: quantity })
  // }).then(res => res.json()).then(data => { ... });
}

function toggleWishlist(productId, el) {
  fetch(SWIFTMART_BASE_URL + 'ajax/wishlist-toggle.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ product_id: productId })
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        el.classList.toggle('active', data.action === 'added');
      } else {
        alert(data.message || 'Có lỗi xảy ra.');
      }
    })
    .catch(() => alert('Không thể kết nối máy chủ.'));
}

// Đếm ngược Flash Sale trên trang chủ
document.addEventListener('DOMContentLoaded', function () {
  var el = document.getElementById('flash-countdown');
  if (!el) return;
  var end = new Date(el.dataset.end).getTime();

  function tick() {
    var diff = end - Date.now();
    if (diff <= 0) { el.lastChild.textContent = '00:00:00'; return; }
    var h = String(Math.floor(diff / 3600000)).padStart(2, '0');
    var m = String(Math.floor((diff % 3600000) / 60000)).padStart(2, '0');
    var s = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');
    el.lastChild.textContent = h + ':' + m + ':' + s;
  }
  tick();
  setInterval(tick, 1000);
});
