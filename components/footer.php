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
<script>
function toggleCartSidebar() {
  document.getElementById("cartSidebar").classList.toggle("open");
  renderCart();
}

// LocalStorage Cart Functions
function getCart() {
  return JSON.parse(localStorage.getItem("cart") || "[]");
}

function saveCart(cart) {
  localStorage.setItem("cart", JSON.stringify(cart));
  updateCartCount();
}

// Add item to cart
function addToCart(product) {
  const cart = getCart();
  const index = cart.findIndex(p => p.title === product.title);
  if (index !== -1) {
    cart[index].quantity += 1;
  } else {
    cart.push({ ...product, quantity: 1 });
  }
  saveCart(cart);
  renderCart();
}

// Remove item
function removeFromCart(title) {
  const cart = getCart().filter(p => p.title !== title);
  saveCart(cart);
  renderCart();
}

// Update quantity
function updateQuantity(title, change) {
  const cart = getCart();
  const item = cart.find(p => p.title === title);
  if (!item) return;
  item.quantity += change;
  if (item.quantity < 1) item.quantity = 1;
  saveCart(cart);
  renderCart();
}

// Render cart items
function renderCart() {
  const cart = getCart();
  const cartItemsDiv = document.getElementById("cartItems");
  cartItemsDiv.innerHTML = "";

  if (cart.length === 0) {
    cartItemsDiv.innerHTML = "<p class='text-muted'>No items in cart.</p>";
    return;
  }

  cart.forEach(item => {
    const itemDiv = document.createElement("div");
    itemDiv.className = "cart-item";

    itemDiv.innerHTML = `
     <img src="${item.image}" alt="${item.title}">
      <div class="flex-grow-1">
        <h6 class="mb-0">${item.title}</h6>
        <div class="d-flex align-items-center gap-2 mt-1">
          <button class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="updateQuantity('${item.title}', -1)">-</button>
          <span>${item.quantity}</span>
          <button class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="updateQuantity('${item.title}', 1)">+</button>
        </div>
        <p class="mb-0 mt-1 text-muted">₹${item.price} x ${item.quantity} = ₹${item.price * item.quantity}</p>
      </div>
      <button class="btn btn-sm text-danger" onclick="removeFromCart('${item.title}')">
        <i class="bi bi-x-lg"></i>
      </button>
    `;

    cartItemsDiv.appendChild(itemDiv);
  });
}

function updateCartCount() {
  const count = getCart().reduce((sum, item) => sum + item.quantity, 0);
  document.getElementById("cartCount").textContent = count;
}

document.addEventListener("DOMContentLoaded", () => {
  updateCartCount();
});
</script>

</body>
</html>
