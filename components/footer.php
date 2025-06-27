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
  renderCart();
}

function getCart() {
  return JSON.parse(localStorage.getItem("cart") || "[]");
}

function saveCart(cart) {
  localStorage.setItem("cart", JSON.stringify(cart));
  updateCartCount();
}

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

function removeFromCart(title) {
  const cart = getCart().filter(p => p.title !== title);
  saveCart(cart);
  renderCart();
}

function updateQuantity(title, change) {
  const cart = getCart();
  const item = cart.find(p => p.title === title);
  if (!item) return;
  item.quantity += change;
  if (item.quantity < 1) item.quantity = 1;
  saveCart(cart);
  renderCart();
}

function updateCartCount() {
  const count = getCart().reduce((sum, item) => sum + item.quantity, 0);
  document.getElementById("cartCount").textContent = count;
}

function renderCart() {
  const cart = getCart();
  const container = document.getElementById("cartItems");
  const itemCount = document.getElementById("cartItemCount");
  const totalSpan = document.getElementById("cartTotal");
  container.innerHTML = "";
  let total = 0;

  if (cart.length === 0) {
    container.innerHTML = "<p class='text-muted'>No items in cart.</p>";
    totalSpan.textContent = "₹0";
    itemCount.textContent = "0 items";
    return;
  }

  itemCount.textContent = cart.length + (cart.length === 1 ? " item" : " items");

  cart.forEach(item => {
    const itemTotal = item.price * item.quantity;
    total += itemTotal;
const itemDiv = document.createElement("div");
    itemDiv.className = "cart-item";
    itemDiv.innerHTML = `
      <img src="${item.image}" alt="${item.title}">
      <div class="cart-info">
        <div class="d-flex justify-content-between">
          <div class="cart-title">${item.title}</div>
          <div class="cart-size">
  ${isNaN(item.size) ? item.size : item.size + "ml"}
</div>

          <div class="text-end">
            <strong>₹${item.price.toLocaleString()}</strong><br>
            ${item.mrp ? `<span class="original-price">₹${item.mrp.toLocaleString()}</span>` : ""}
          </div>
        </div>

        <div class="d-flex align-items-center gap-2 qty-controls mt-2">
          <button class="btn btn-sm btn-outline-secondary px-2 py-0" onclick="updateQuantity('${item.title}', -1)">−</button>
          <span>${item.quantity}</span>
          <button class="btn btn-sm btn-outline-secondary px-2 py-0" onclick="updateQuantity('${item.title}', 1)">+</button>
          <button class="remove-btn ms-3" onclick="removeFromCart('${item.title}')">Remove</button>
        </div>
      </div>
    `;
    container.appendChild(itemDiv);
  });

 
  totalSpan.textContent = `₹${total.toLocaleString()}`;
}

function renderRecommendations() {
  const container = document.getElementById("recommendationBox");
  container.innerHTML = "";

  const products = [
    {
      title: "The Vikings",
      price: 849,
      mrp: 1999,
      image: "/adaaromas/assets/images/image.png",
      rating: 5,
      reviews: 1
    },
    {
      title: "Savage Perfume",
      price: 1099,
      mrp: 2598,
      image: "/adaaromas/assets/images/image.png",
      rating: 0,
      reviews: 0
    },
    {
      title: "Ombre Leather",
      price: 849,
      mrp: 1999,
      image: "/adaaromas/assets/images/image.png",
      rating: 4.2,
      reviews: 12
    }
  ];

  products.forEach(p => {
    const card = document.createElement("div");
    card.className = "rec-card";

    card.innerHTML = `
      <img src="${p.image}" alt="${p.title}">
      <div class="rec-title">${p.title}</div>
      <div class="rec-price">₹${p.price} <span class="rec-old">₹${p.mrp}</span></div>
      <div class="rec-star"><i class="bi bi-star-fill"></i> ${p.rating} (${p.reviews})</div>
      <div class="rec-add" onclick="addToCart(${JSON.stringify(p).replace(/"/g, '&quot;')})">+ Add to cart</div>
    `;

    container.appendChild(card);
  });
}


document.addEventListener("DOMContentLoaded", () => {
  updateCartCount();
  renderRecommendations();
});
</script>
<!-- checkout.php script -->
<script>
let cartTotal = 0;
let discountedTotal = 0;
let appliedCoupon = null;

function renderCheckoutOrder() {
  const cart = JSON.parse(localStorage.getItem("cart") || "[]");
  const summary = document.getElementById("checkoutOrderSummary");
  const totalPriceDisplay = document.getElementById("totalPrice");
  const discountInfo = document.getElementById("discountInfo");

  if (!summary) return;

  if (cart.length === 0) {
    summary.innerHTML = "<p>No items in cart.</p>";
    totalPriceDisplay.innerText = "0";
    discountInfo.style.display = "none";
    return;
  }

  let total = 0;
  let savings = 0;
  summary.innerHTML = cart.map(p => {
    total += p.price * p.quantity;
    savings += (p.mrp - p.price) * p.quantity;
    return `
      <div class='d-flex justify-content-between align-items-center mb-2'>
        <div class="w-75">
          <strong>${p.title}</strong>
          <div class="text-muted">${p.size || "No size specified"}</div>
          <div class="d-flex align-items-center gap-2 mt-1">
            <button class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="updateCheckoutQuantity('${p.title}', -1)">-</button>
            <span>${p.quantity}</span>
            <button class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="updateCheckoutQuantity('${p.title}', 1)">+</button>
          </div>
          <small class="text-muted"><s>₹${p.mrp}</s> → ₹${p.price}</small>
        </div>
        <div class="text-end">
          <button class="btn btn-sm text-danger mb-1" onclick="removeFromCheckout('${p.title}')"><i class="bi bi-x-lg"></i></button>
          <div><strong>₹${p.price * p.quantity}</strong></div>
        </div>
      </div>`;
  }).join("");

  summary.innerHTML += `
    <hr>
    <div class='d-flex justify-content-between'><strong>Total:</strong><strong>₹${total}</strong></div>
    <div class='d-flex justify-content-between text-success'><span>Total Savings:</span><span>₹${savings}</span></div>
  `;

  cartTotal = total;
  discountedTotal = total;

  // If coupon is already applied, re-apply
  if (appliedCoupon) applyDiscount(appliedCoupon);
  else totalPriceDisplay.innerText = cartTotal;
}

function updateCheckoutQuantity(title, change) {
  const cart = JSON.parse(localStorage.getItem("cart") || "[]");
  const item = cart.find(p => p.title === title);
  if (!item) return;
  item.quantity += change;
  if (item.quantity < 1) item.quantity = 1;
  localStorage.setItem("cart", JSON.stringify(cart));
  renderCheckoutOrder();
  if (typeof renderCart === 'function') renderCart();
  window.dispatchEvent(new Event("storage"));
}

function removeFromCheckout(title) {
  let cart = JSON.parse(localStorage.getItem("cart") || "[]");
  cart = cart.filter(p => p.title !== title);
  localStorage.setItem("cart", JSON.stringify(cart));
  renderCheckoutOrder();
  if (typeof renderCart === 'function') renderCart();
  window.dispatchEvent(new Event("storage"));
}

window.addEventListener("storage", function (e) {
  if (e.key === "cart") renderCheckoutOrder();
});

async function requestCoupon(e) {
  e.preventDefault();

  const statusBox = document.getElementById("couponRequestStatus");
  statusBox.innerText = '';
  statusBox.className = 'mt-2 small';

  const form = document.getElementById("billingForm");
  const formData = new FormData(form);
  const user = Object.fromEntries(formData);

  // Check required fields
  const requiredFields = ["firstName", "lastName", "state", "district", "city", "addressLine1", "pincode", "email", "phone"];
  for (let field of requiredFields) {
    if (!user[field] || user[field].trim() === "") {
    statusBox.innerText = "Please fill in all billing details before requesting a coupon.";
    statusBox.classList.add("text-danger");
    return;
    }
  }

  const cart = JSON.parse(localStorage.getItem("cart") || "[]");
  if (!cart.length) {
    statusBox.innerText = "Your cart is empty. Add products before requesting a coupon.";
    statusBox.classList.add("text-danger");
    return;
  }

  statusBox.innerText = "✅ Coupon request sent! Check your billing email within 5 minutes.";
  statusBox.classList.remove("text-danger");
  statusBox.classList.add("text-success");

  // Send request to server
try {
    const resp = await fetch("send-coupon-request.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ user, cart })
    });

    const data = await resp.json();
    if (!data.success) {
      statusBox.innerText = "❌ Request failed to send. Try again later.";
      statusBox.classList.remove("text-success");
      statusBox.classList.add("text-danger");
    }
  } catch (err) {
  statusBox.innerText = "✅ Coupon request sent! Check your billing email within 5 minutes.";
  statusBox.classList.remove("text-danger");
  statusBox.classList.add("text-success");
  }
}


const shipCheckbox = document.getElementById("shipDifferent");
shipCheckbox.addEventListener("change", function () {
  document.getElementById("shippingAddressBox").classList.toggle("d-none", !this.checked);
});

document.addEventListener("DOMContentLoaded", renderCheckoutOrder);

// 🧾 Apply Coupon Logic
document.getElementById('applyCouponBtn').addEventListener('click', () => {
  const code = document.getElementById('couponCode').value.trim().toUpperCase();
  const msg = document.getElementById('couponMsg');
  const discountInfo = document.getElementById('discountInfo');
  const totalPriceDisplay = document.getElementById('totalPrice');

  msg.innerText = '';
  discountInfo.style.display = 'none';

  if (!code) {
    msg.innerText = "Enter a coupon code.";
    return;
  }

  fetch('apply-coupon.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ code })
  })
  .then(res => res.json())
  .then(data => {
    if (!data.success) {
      msg.innerText = data.message;
      totalPriceDisplay.innerText = cartTotal;
      return;
    }

    appliedCoupon = data; // Store for future refresh (quantity change, etc)
    applyDiscount(data);
  })
  .catch(() => {
    msg.innerText = "Something went wrong. Try again.";
  });
});

function applyDiscount(coupon) {
  let discount = 0;
  if (coupon.flatAmount > 0) discount = coupon.flatAmount;
  else if (coupon.percentage > 0) discount = (coupon.percentage / 100) * cartTotal;

  discountedTotal = Math.max(0, cartTotal - discount);
  document.getElementById('totalPrice').innerText = discountedTotal.toFixed(0);
  document.getElementById('discountInfo').innerText = `Coupon applied! You saved ₹${discount.toFixed(0)}.`;
  document.getElementById('discountInfo').style.display = 'block';
  
}
</script>

<!--Enable Payment button script -->
<script>
const payButton = document.getElementById('rzp-button1');
const billingForm = document.getElementById('billingForm');

// Disable button initially
payButton.disabled = true;
payButton.style.opacity = 0.6;

// Monitor changes on form
billingForm.addEventListener('input', checkFormValidity);

function checkFormValidity() {
  const form = billingForm;
  const requiredFields = form.querySelectorAll('[required]');
  let allFilled = true;

  requiredFields.forEach(field => {
    if (!field.value.trim()) {
      allFilled = false;
    }
  });

  payButton.disabled = !allFilled;
  payButton.style.opacity = allFilled ? 1 : 0.6;
}
</script>
<!-- razorpay script -->
<script>
document.getElementById('rzp-button1').onclick = async function(e){
  e.preventDefault();
  const cart = JSON.parse(localStorage.getItem('cart')||'[]');
  if (!cart.length) return alert('Cart empty');

  const form = document.getElementById('billingForm');
  const formData = new FormData(form);
  const user = Object.fromEntries(formData);
  const finalAmount = parseFloat(document.getElementById("totalPrice").textContent);

const resp = await fetch('create-order.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ cart, user, finalAmount })
}).then(r => r.json());


  const options = {
    key: "rzp_test_XymxxA8kk9Jv4x",
    amount: resp.amount,
    currency: resp.currency,
    name: "ADA Aromas",
    description: "Order Payment",
    order_id: resp.id,
    handler: async function (res) {
      try {
        const verifyResp = await fetch('verify-payment.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            ...res,
            cart,
            finalAmount,
            userId: resp.userId,
            user: {
              id: resp.userId,
              name: user.firstName + ' ' + user.lastName,
              email: user.email
            }
          })
        }).then(r => r.json());

let redirected = false;
function safeRedirect(orderId) {
  if (!redirected) {
    redirected = true;
    localStorage.removeItem('cart');
    window.location.href = "thankyou.php?orderId=" + orderId;
  }
}

if (verifyResp.success && verifyResp.orderId) {
  safeRedirect(verifyResp.orderId);
  setTimeout(() => safeRedirect(verifyResp.orderId), 2000); // retry redirect if modal was blocking
} else {

        }

      } catch (err) {

        console.error(err);
      }
    },
    theme: { color: "#28a745" }
  };

  const rzp = new Razorpay(options);
  rzp.open();
};
</script>


</body>
</html>
