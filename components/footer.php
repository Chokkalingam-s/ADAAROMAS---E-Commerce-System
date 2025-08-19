</div>
<!-- Footer -->
<footer class="bg-dark text-light text-center py-3" style="">
  <p class="mb-0">© <?= date('Y') ?>  ADA Aromas. All Rights Reserved.</p>
</footer>
<!-- Add to header.php -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.0/nouislider.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
<script>
  let adminMode = null;

// Show password panel when checkbox is ticked
document.getElementById('isAdmin').addEventListener('change', function() {
  document.getElementById('adminPanel').style.display = this.checked ? 'block' : 'none';
});

// Verify password
document.getElementById('verifyAdminBtn').addEventListener('click', function() {
  const pass = document.getElementById('adminPassword').value.trim();
  if (pass === "atuldevaroraorder") {
    alert("✅ Admin Verified");
    document.getElementById('adminType').style.display = 'block';
  } else {
    alert("❌ Wrong password");
  }
});

// Store admin mode selection
document.querySelectorAll('input[name="adminMode"]').forEach(radio => {
  radio.addEventListener('change', function() {
    adminMode = this.value;
    renderCheckoutOrder();
  });
});

// Modify GST in renderCheckoutOrder if admin gift
const originalRender = renderCheckoutOrder;
renderCheckoutOrder = function() {
  const cart = JSON.parse(localStorage.getItem("cart") || "[]");
  let total = cart.reduce((sum, p) => sum + p.price * p.quantity, 0);
  let gst = (adminMode === "admin_gift") ? 0 : Math.round(total * 0.18);
  let grandTotal = total + gst;

  // Call original render logic
  originalRender();

  // Overwrite totals if admin mode is on
  if (adminMode) {
    document.querySelector("#checkoutOrderSummary").innerHTML += `
      <div class="alert alert-info mt-2">Admin Mode: ${adminMode.replace("_", " ").toUpperCase()}</div>
    `;
    document.getElementById('totalPrice').innerText = grandTotal;
    discountedTotal = grandTotal;
    cartTotal = grandTotal;
  }
};

</script>

<!-- cart.php script -->
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
  document.getElementById("mobileCartCount").textContent = count;
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
        <div class="cart-item-header">
          <div class="cart-item-left">
            <div class="cart-title">${item.title}</div>
            <div class="cart-size">${isNaN(item.size) ? item.size : item.size + "ml"}</div>
          </div>
          <div class="cart-item-right">
            <div class="cart-current-price">₹${item.price.toLocaleString()}</div>
            ${item.mrp ? `<div class="cart-original-price">₹${item.mrp.toLocaleString()}</div>` : ""}
          </div>
        </div>
        <div class="cart-item-controls">
          <div class="qty-controls">
            <button class="qty-btn" onclick="updateQuantity('${item.title}', -1)">−</button>
            <span class="qty-display">${item.quantity}</span>
            <button class="qty-btn" onclick="updateQuantity('${item.title}', 1)">+</button>
          </div>
          <button class="remove-btn" onclick="removeFromCart('${item.title}')">Remove</button>
        </div>
      </div>
    `;
    container.appendChild(itemDiv);
  });

 
  totalSpan.textContent = `₹${total.toLocaleString()}`;
}

function renderRecommendations() {
  const container = document.getElementById("recommendationBox");
  const hidden = document.getElementById("hiddenRecommendations");
  if (container && hidden) {
    container.innerHTML = hidden.innerHTML;
  }
}


document.addEventListener("DOMContentLoaded", () => {
  updateCartCount();
  renderRecommendations();
});
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
    summary.innerHTML = `
      <div class="text-center py-4">
        <div style="font-size: 3rem; margin-bottom: 1rem;">🛍️</div>
        <p class="text-muted">No items in cart.</p>
        <small>Add some beautiful fragrances to continue</small>
      </div>`;
    totalPriceDisplay.innerText = "0";
    discountInfo.style.display = "none";
    return;
  }

  let total = 0;
  let savings = 0;
  
  const itemsHtml = cart.map(p => {
    total += p.price * p.quantity;
    savings += (p.mrp - p.price) * p.quantity;
    
    return `
      <div class='order-item'>
        <div class="d-flex align-items-start">
          <div class="product-image-placeholder">
            <img src="${p.image}" alt="${p.title}">
          </div>
          <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <strong style="color: var(--deep-purple);">
  ${p.title}
  ${
    (p.size > 1)
      ? `<span class="ms-2 text-muted small">${p.size} ml</span>`
      : (p.size ? `<span class="ms-2 text-muted small">${p.size}</span>` : "")
  }
</strong>
                <div class="quantity-controls">
                  <button class="quantity-btn" onclick="updateCheckoutQuantity('${p.title}', -1)">-</button>
                  <span class="mx-2 fw-bold">${p.quantity}</span>
                  <button class="quantity-btn" onclick="updateCheckoutQuantity('${p.title}', 1)">+</button>
                </div>
                <div class="mt-1">
                  <small class="text-muted">
                    <s class="text-danger">₹${p.mrp}</s> 
                    <span class="text-success fw-bold">₹${p.price}</span>
                  </small>
                </div>
              </div>
              <div class="text-end">
                <div class="fw-bold" style="color: var(--deep-purple);">₹${p.price * p.quantity}</div>
      
             <button class="btn btn-sm btn-outline-danger mt-5" onclick="removeFromCheckout('${p.title}')">
              Skip x
            </button>
              </div>
            </div>
          </div>
        </div>
      </div>`;
  }).join("");

  let gst = Math.round(total * 0.18);
let grandTotal = total + gst;


summary.innerHTML = itemsHtml + `
  <div class="order-total mt-3">
    <div class="total-row">
      <span>Subtotal:</span>
      <span>₹${total}</span>
    </div>
    <div class="total-row savings-text">
      <span>Total Savings:</span>
      <span>₹${savings}</span>
    </div>
    <div class="total-row">
      <span>GST (18%):</span>
      <span>₹${gst}</span>
    </div>
    <div class="total-row final-amount">
      <strong>Total:</strong>
      <strong>₹${grandTotal}</strong>
    </div>
  </div>
`;


cartTotal = grandTotal;
discountedTotal = grandTotal;

  
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
  statusBox.className = 'status-message';

  const form = document.getElementById("billingForm");
  const formData = new FormData(form);
  const user = Object.fromEntries(formData);

  // Check required fields
  const requiredFields = ["firstName", "lastName", "state", "district", "city", "addressLine1", "pincode", "email", "phone"];
  for (let field of requiredFields) {
    if (!user[field] || user[field].trim() === "") {
      statusBox.innerText = "Please fill in all billing details before requesting a coupon.";
      statusBox.classList.add("status-error");
      statusBox.style.display = "block";
      return;
    }
  }

  const cart = JSON.parse(localStorage.getItem("cart") || "[]");
  if (!cart.length) {
    statusBox.innerText = "Your cart is empty. Add products before requesting a coupon.";
    statusBox.classList.add("status-error");
    statusBox.style.display = "block";
    return;
  }

  statusBox.innerText = "✅ Coupon request sent! Check your billing email within 5 minutes.";
  statusBox.classList.add("status-success");
  statusBox.style.display = "block";

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
      statusBox.classList.remove("status-success");
      statusBox.classList.add("status-error");
    }
  } catch (err) {
    statusBox.innerText = "✅ Coupon request sent! Check your billing email within 5 minutes.";
    statusBox.classList.remove("status-error");
    statusBox.classList.add("status-success");
  }
}

document.addEventListener("DOMContentLoaded", renderCheckoutOrder);

// Apply Coupon Logic
document.getElementById('applyCouponBtn').addEventListener('click', () => {
  const code = document.getElementById('couponCode').value.trim().toUpperCase();
  const msg = document.getElementById('couponMsg');
  const discountInfo = document.getElementById('discountInfo');
  const totalPriceDisplay = document.getElementById('totalPrice');
  const applyBtn = document.getElementById('applyCouponBtn');
  
  msg.style.display = 'none';
  discountInfo.style.display = 'none';

  if (!code) {
    msg.innerText = "Enter a coupon code.";
    msg.style.display = 'block';
    return;
  }

  applyBtn.classList.add('loading');
  applyBtn.disabled = true;

  fetch('apply-coupon.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ code })
  })
  .then(res => res.json())
  .then(data => {
    applyBtn.classList.remove('loading');
    applyBtn.disabled = false;
    
    if (!data.success) {
      msg.innerText = data.message;
      msg.style.display = 'block';
      totalPriceDisplay.innerText = cartTotal;
      return;
    }

    appliedCoupon = data;
    applyDiscount(data);
  })
  .catch(() => {
    applyBtn.classList.remove('loading');
    applyBtn.disabled = false;
    msg.innerText = "Something went wrong. Try again.";
    msg.style.display = 'block';
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

<!-- razorpay script -->
<script>
  document.addEventListener("DOMContentLoaded", function () {



document.getElementById('rzp-button1').onclick = async function(e){
  e.preventDefault();
  const cart = JSON.parse(localStorage.getItem('cart')||'[]');
  if (!cart.length) return alert('Cart empty');

  const form = document.getElementById('billingForm');
  const formData = new FormData(form);
  const user = Object.fromEntries(formData);
  const finalAmount = parseFloat(document.getElementById("totalPrice").textContent);
  const code = document.getElementById('couponCode').value.trim().toUpperCase();



  console.log("🛒 Cart:", cart);
console.log("👤 User:", user);
console.log("💰 Final Amount:", finalAmount);

if (adminMode) {
  // Step 1: Create user and get userId
  const createUserResp = await fetch('create-order.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ cart, user, finalAmount })
  }).then(r => r.json());

  if (!createUserResp.userId) {
    alert("Failed to create user for admin order");
    return;
  }

  // Step 2: Skip Razorpay and directly store order
  const verifyResp = await fetch('verify-payment.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      razorpay_order_id: "ADMIN-ORDER",
      razorpay_payment_id: "ADMIN-PAYMENT",
      razorpay_signature: "ADMIN-MODE",
      cart,
      finalAmount,
      user,
      userId: createUserResp.userId,
      couponCode: code || null,
      adminMode
    })
  }).then(r => r.json());

  if (verifyResp.success && verifyResp.orderId) {
    localStorage.removeItem('cart');
    window.location.href = "thankyou.php?orderId=" + verifyResp.orderId;
  } else {
    alert("Failed to place admin order");
  }
  return;
}



const resp = await fetch('create-order.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ cart, user, finalAmount })
}).then(r => r.json());

console.log("Create Order Response:", resp);

            console.log("💳 Coupon Sent to Server:", appliedCoupon?.couponCode);

  const options = {
    key: "rzp_test_bSYra6WoLaVMY4",
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
            },
            couponCode: code || null


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
  setTimeout(() => safeRedirect(verifyResp.orderId), 6000); // retry redirect if modal was blocking
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
  rzp.on('payment.failed', function (response){
  alert('Payment Failed. Please try again.');
});
  rzp.on('payment.success', function (response) {
    console.log('Payment Success:', response);
  });

  // Close the modal if it was open
  if (document.querySelector('.razorpay-modal')) {
    document.querySelector('.razorpay-modal').remove();
  }
};

});
</script>


</body>
</html>
