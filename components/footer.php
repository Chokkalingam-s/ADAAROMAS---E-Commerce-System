<!-- Footer -->
<footer class="bg-dark text-light text-center py-3 mt-5">
  <p class="mb-0">© 2025 ADA Aromas. All Rights Reserved.</p>
</footer>
<!-- Add to header.php -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.0/nouislider.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/main.js"></script>
<script>
  function toggleCartSidebar() {
    document.getElementById("cartSidebar").classList.toggle("open");
  }

  const cart = [];
  function addToCart(product) {
    cart.push(product);
    updateCartSidebar();
  }

  function updateCartSidebar() {
    const cartItems = document.getElementById("cartItems");
    const count = document.getElementById("cartCount");
    cartItems.innerHTML = cart.map(p => `
      <div class="cart-item">
        <img src="${p.image}" alt="${p.title}">
        <div>
          <strong>${p.title}</strong><br>
          <span>₹${p.price}</span>
        </div>
      </div>
    `).join('');
    count.textContent = cart.length;
  }
</script>
</body>
</html>
