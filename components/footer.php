</div>

<!-- Customize Modal -->
<div class="modal fade" id="customizeModal" tabindex="-1" aria-labelledby="customizeLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form id="customizeForm" enctype="multipart/form-data" method="POST" action="customize.php">
        <div class="modal-header bg-dark text-white">
          <h5 class="modal-title" id="customizeLabel">Customize Your Perfume Order</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">

          <!-- Need Description -->
          <div class="mb-3">
            <label class="form-label">Elaborate Your Need</label>
            <textarea class="form-control" name="description" rows="4" maxlength="300" placeholder="Describe your custom perfume needs..."></textarea>
          </div>

          <!-- Upload Image -->
          <div class="mb-3">
            <label class="form-label">Reference Image (Optional)</label>
            <input type="file" class="form-control" name="image" accept="image/*">
          </div>

          <!-- User Details -->
          <div class="row g-3">
            <div class="col-md-6">
              <input type="text" name="firstName" class="form-control" placeholder="First Name" required>
            </div>
            <div class="col-md-6">
              <input type="text" name="lastName" class="form-control" placeholder="Last Name" required>
            </div>
            <div class="col-md-6">
              <input type="text" name="phoneNo" class="form-control" placeholder="Phone (10 digits)" pattern="\d{10}" required>
            </div>
            <div class="col-md-6">
              <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="col-md-6">
              <select id="state" name="state" class="form-control" required>
                <option value="">Select State</option>
              </select>
            </div>
            <div class="col-md-6">
              <select id="district" name="district" class="form-control" required>
                <option value="">Select District</option>
              </select>
            </div>
            <div class="col-md-6">
              <input type="text" name="address1" class="form-control" placeholder="House No, Street, Area" required>
            </div>
            <div class="col-md-6">
              <input type="text" name="address2" class="form-control" placeholder="Apartment, Landmark (Optional)">
            </div>
            <div class="col-md-4">
              <input type="text" name="city" class="form-control" placeholder="City" required>
            </div>
            <div class="col-md-4">
              <input type="text" name="pincode" class="form-control" placeholder="Pincode" pattern="\d{6}" required>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-dark w-100">Submit Request</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Footer -->
<footer class="bg-dark text-light text-center py-3" style="">
  <p class="mb-0">© <?= date('Y') ?>  ADAAROMAS All Rights Reserved.</p>
</footer>
<!-- Add to header.php -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.0/nouislider.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>

<!-- State Json -->
<script>
document.addEventListener("DOMContentLoaded", function() {
  let stateSelect = document.getElementById("state");
  let districtSelect = document.getElementById("district");

  // Load JSON dynamically
  fetch("/state.json") // <-- adjust path if different for local /adaaromas would come
    .then(response => response.json())
    .then(data => {
      let states = data.states;

      // Populate states
      states.forEach(s => {
        let option = document.createElement("option");
        option.value = s.state;
        option.textContent = s.state;
        stateSelect.appendChild(option);
      });

      // On state change, populate districts
      stateSelect.addEventListener("change", function() {
        let selectedState = this.value;
        districtSelect.innerHTML = '<option value="">Select District</option>'; // Reset

        if (selectedState) {
          let selectedStateObj = states.find(s => s.state === selectedState);
          if (selectedStateObj) {
            selectedStateObj.districts.forEach(d => {
              let option = document.createElement("option");
              option.value = d;
              option.textContent = d;
              districtSelect.appendChild(option);
            });
          }
        }
      });
    })
    .catch(err => console.error("Failed to load states.json", err));
});
</script>

<!-- Admin Mode Script -->
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

// Helper: detect essence oil
function isEssenceOil(product) {
  return product.category && product.category.toLowerCase() === "essence oil";
}
function getEssenceOilItems(cart) {
  return cart.filter(p => isEssenceOil(p));
}
// Detect Attar
function isAttar(product) {
  return product.category && product.category.toLowerCase() === "attar";
}
function getAttarItems(cart) {
  return cart.filter(p => isAttar(p));
}

// Check Attar size >= 12ml
function attarIsValidSize(product) {
  return !isNaN(product.size) && Number(product.size) >= 12;
}

function addToCart(product) {
  const cart = getCart();
  const essenceItems = getEssenceOilItems(cart);
  const index = cart.findIndex(p => p.title === product.title);

if (isEssenceOil(product)) {
    if (index !== -1) {
        // EO already in cart → increment quantity
        cart[index].quantity += 1;
    } else {
        // New EO product being added
        const quantity = (essenceItems.length === 0) ? 3 : 1; // First EO → 3, others → 1
        cart.push({ ...product, quantity });
    }
} else if (isAttar(product)) {
  const attarItems = getAttarItems(cart);
  if (index !== -1) {
    cart[index].quantity += 1;
  } else {
    const quantity = (attarItems.length === 0) ? 3 : 1; // First Attar → 3, others → 1
    cart.push({ ...product, quantity });
  }
} else {
  // Normal products
  if (index !== -1) cart[index].quantity += 1;
  else cart.push({ ...product, quantity: 1 });
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

  const essenceItems = getEssenceOilItems(cart);
  item.quantity += change;

  if (isEssenceOil(item)) {
    if (essenceItems.length === 1) {
      // Only one EO product → must stay ≥ 3
      if (item.quantity < 3) {
        alert("❌ Minimum 3 units required for this Essence Oil.");
        item.quantity = 3;
      }
    } else if (essenceItems.length === 2) {
      // Two EO products → total must be ≥ 3
      const totalEO = essenceItems.reduce((sum, p) => sum + (p.title === item.title ? item.quantity : p.quantity), 0);
      if (totalEO < 3) {
        alert("❌ Minimum 3 units of Essence Oil required across both.");
        item.quantity -= change;
      }
      // Second EO cannot exceed 1
      const otherEO = essenceItems.find(p => p.title !== item.title);

    }
  } else {
    if (item.quantity < 1) item.quantity = 1;
  }

  if (isAttar(item)) {
  const attarItems = getAttarItems(cart);

  item.quantity += change;
  if (item.quantity < 1) item.quantity = 1;

  // Rule 1: At least 3 total Attar
  const totalAttar = attarItems.reduce((s, p) => s + p.quantity, 0);
  if (totalAttar < 3) {
    alert("❌ Minimum 3 units of Attar required.");
    item.quantity = Math.max(item.quantity, 3);
  }

  // Rule 2: At least 2 Attar ≥ 12ml
  const bigAttars = attarItems.filter(p => attarIsValidSize(p));
  if (bigAttars.length < 2) {
    alert("❌ At least 2 Attar products must be 12ml or above.");
    if (change > 0) item.quantity -= change; // rollback
  }
}


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
    const cleanImage = item.image.replace(/^\/adaaromas\//, '/');
itemDiv.innerHTML = `
  <img src="${cleanImage}" alt="${item.title}">
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

<!-- checkout.php script -->
<script>
let cartTotal = 0;
let discountedTotal = 0;
let appliedCoupon = null;
let codCharge = 0;

function renderCheckoutOrder() {
  const cart = getCart();
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
    if (totalPriceDisplay) totalPriceDisplay.innerText = "0";
    if (discountInfo) discountInfo.style.display = "none";
    return;
  }

  let total = 0;
  let savings = 0;

  const itemsHtml = cart.map(p => {
    total += p.price * p.quantity;
    savings += ( (p.mrp||0) - (p.price||0) ) * p.quantity;
    const safeTitle = encodeURIComponent(p.title);
    const sizeText = (p.size && !isNaN(p.size)) ? p.size + " ml" : (p.size ? p.size : "");
    return `
      <div class='order-item'>
        <div class="d-flex align-items-start">
          <div class="product-image-placeholder">
            <img src="${(p.image || '').replace(/^\/adaaromas\//, '/')}" alt="${p.title}">
          </div>
          <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <strong style="color: var(--deep-purple);">
                  ${p.title} ${sizeText ? `<span class="ms-2 text-muted small">${sizeText}</span>` : ""}
                </strong>
                <div class="quantity-controls" style="margin-top:6px;">
                  <button class="quantity-btn" onclick="updateCheckoutQuantityEncoded('${safeTitle}', -1)">-</button>
                  <span class="mx-2 fw-bold">${p.quantity}</span>
                  <button class="quantity-btn" onclick="updateCheckoutQuantityEncoded('${safeTitle}', 1)">+</button>
                </div>
                <div class="mt-1">
                  <small class="text-muted">
                    ${p.mrp ? `<s class="text-danger">₹${p.mrp}</s>` : ""} 
                    <span class="text-success fw-bold">₹${p.price}</span>
                  </small>
                </div>
              </div>
              <div class="text-end">
                <div class="fw-bold" style="color: var(--deep-purple);">₹${p.price * p.quantity}</div>
                <button class="btn btn-sm btn-outline-danger mt-3" onclick="removeFromCheckoutEncoded('${safeTitle}')">Skip x</button>
              </div>
            </div>
          </div>
        </div>
      </div>`;
  }).join("");

  const gst = Math.round(total * 0.18);
  const grandTotal = total + gst;

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

  if (appliedCoupon) applyDiscount(appliedCoupon);
  else if (totalPriceDisplay) totalPriceDisplay.innerText = cartTotal;
}

/* Update quantity from checkout */
function updateCheckoutQuantityEncoded(encodedTitle, change) {
  const title = decodeURIComponent(encodedTitle);
  const cart = getCart();
  const item = cart.find(p => p.title === title);
  if (!item) return;

  item.quantity += change;
  if (item.quantity < 1) item.quantity = 1;

  // Validate EO rules (same logic as cart)
  if (isEssenceOil(item)) {
    const eoItems = getEssenceOilItems(cart);
    const totalAfter = eoItems.reduce((s, it) => s + it.quantity, 0);
    const moreThanOneCount = eoItems.filter(it => it.quantity > 1).length;

    if (eoItems.length === 1 && eoItems[0].quantity < 3 ) {
      alert("❌ Minimum 3 units required for Essence Oil.");
      item.quantity = Math.max(item.quantity, 3);
    } else {
      if (moreThanOneCount > 1) {

      } else if (totalAfter < 3) {
        alert("❌ Combined Essence Oil quantity must be at least 3.");
        item.quantity -= change; // rollback
        if (item.quantity < 1) item.quantity = 1;
      }
    }
  }else if (isAttar(item)) {
  const attarItems = getAttarItems(cart);
  const totalAfter = attarItems.reduce((s, it) => s + it.quantity, 0);

  // Rule 1: At least 3 Attar total
  if (totalAfter < 3) {
    alert("❌ Minimum 3 units of Attar required.");
    item.quantity = Math.max(item.quantity, 3);
  }

  // Rule 2: At least 2 Attar must be ≥ 12ml
  const bigAttars = attarItems.filter(p => attarIsValidSize(p));
  if (bigAttars.length < 2) {
    alert("❌ At least 2 Attar products must be 12ml or above.");
    item.quantity -= change; // rollback
    if (item.quantity < 1) item.quantity = 1;
  }
}


  saveCart(cart);
  renderCheckoutOrder();
  renderCart();
  window.dispatchEvent(new Event("storage"));
}

/* Remove from checkout (with EO checks) */
function removeFromCheckoutEncoded(encodedTitle) {
  const title = decodeURIComponent(encodedTitle);
  let cart = getCart();
  const item = cart.find(p => p.title === title);
  if (!item) return;

  if (isEssenceOil(item)) {
    const remaining = cart.filter(p => p.title !== title);
    const remainingEOCount = getEssenceOilItems(remaining).reduce((s, i) => s + i.quantity, 0);
    if (remainingEOCount < 3 && remainingEOCount > 0) {
      alert("❌ Cannot remove this Essence Oil — minimum 3 Essence Oil units are required across the cart.");
      return;
    }
  }
  if (isAttar(item)) {
  const remaining = cart.filter(p => p.title !== title);
  const remainingAttars = getAttarItems(remaining);

  const totalAttar = remainingAttars.reduce((s, i) => s + i.quantity, 0);
  if (totalAttar < 3 && totalAttar > 0) {
    alert("❌ Cannot remove this Attar — minimum 3 Attar units are required.");
    return;
  }

  const bigAttars = remainingAttars.filter(p => attarIsValidSize(p));
  if (bigAttars.length < 2 && remainingAttars.length > 0) {
    alert("❌ At least 2 Attar products in your cart must be 12ml or above.");
    return;
  }
}


  cart = cart.filter(p => p.title !== title);
  saveCart(cart);
  renderCheckoutOrder();
  renderCart();
  window.dispatchEvent(new Event("storage"));
}

/* --- Storage listener so pages sync --- */
window.addEventListener("storage", function (e) {
  if (e.key === "cart") {
    renderCart();
    renderCheckoutOrder();
  }
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
  
  let codChecked = document.getElementById("codOption")?.checked;
  let codFee = codChecked ? 50 : 0;
  discountedTotal = Math.max(0, cartTotal - discount + codFee);
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
document.addEventListener("DOMContentLoaded", function () {
  let isCOD = false;

  const totalPriceEl = document.getElementById("totalPrice");
  const codOption = document.getElementById("codOption");
  const codInfo = document.getElementById("codInfo");

  let baseTotal = parseFloat(totalPriceEl.textContent) || 0;

  // Toggle COD
codOption.addEventListener("change", function() {
  isCOD = this.checked;
  let codFee = isCOD ? 50 : 0;
  let base = cartTotal;

  // If coupon is applied, use discountedTotal logic
  if (appliedCoupon) {
    applyDiscount(appliedCoupon);
  } else {
    totalPriceEl.textContent = (base + codFee);
    codInfo.style.display = isCOD ? "block" : "none";
    discountedTotal = base + codFee;
  }
});

  document.getElementById('rzp-button1').onclick = async function(e){
    e.preventDefault();
    const cart = JSON.parse(localStorage.getItem('cart')||'[]');
    if (!cart.length) return alert('Cart empty');

    const form = document.getElementById('billingForm');
    const formData = new FormData(form);
    const user = Object.fromEntries(formData);
    const finalAmount = parseFloat(totalPriceEl.textContent);
    const code = document.getElementById('couponCode').value.trim().toUpperCase();

    // COD flow
    if (isCOD) {
      const createUserResp = await fetch('create-order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ cart, user, finalAmount })
      }).then(r => r.json());

      if (!createUserResp.userId) {
        alert("Failed to create user for COD order");
        return;
      }

      const verifyResp = await fetch('verify-payment.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          razorpay_order_id: "CashOnDelivery",
          razorpay_payment_id: "CashOnDelivery",
          razorpay_signature: "CashOnDelivery",
          cart,
          finalAmount,
          userId: createUserResp.userId,
          user: {
            id: createUserResp.userId,
            name: user.firstName + ' ' + user.lastName,
            email: user.email
          },
          couponCode: code || null
        })
      }).then(r => r.json());

      if (verifyResp.success && verifyResp.orderId) {
        localStorage.removeItem('cart');
        window.location.href = "thankyou.php?orderId=" + verifyResp.orderId;
      } else {
        alert("Failed to place COD order");
      }
      return;
    }



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
