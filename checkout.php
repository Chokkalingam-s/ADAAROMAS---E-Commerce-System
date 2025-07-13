<?php include "components/header.php"; ?>

<style>
  :root {
    --primary-gold: #D4AF37;
    --primary-rose: #E8B4B8;
    --deep-purple: #4A154B;
    --soft-lavender: #F3F0FF;
    --warm-white: #FEFCF8;
    --charcoal: #2C2C2C;
    --light-gray: #F8F9FA;
    --success-green: #28A745;
    --danger-red: #DC3545;
    --shadow-light: 0 2px 10px rgba(0,0,0,0.1);
    --shadow-medium: 0 4px 20px rgba(0,0,0,0.15);
    --gradient-gold: linear-gradient(135deg, #D4AF37 0%, #F4E4BC 100%);
    --gradient-purple: linear-gradient(135deg, #4A154B 0%, #7B2D8E 100%);
    --gradient-green: linear-gradient(135deg, #28A745 0%, #6EDC8C 100%);
  }

  body {
    overflow-x: hidden;
    background: linear-gradient(135deg, var(--soft-lavender) 0%, var(--warm-white) 100%);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }

  .checkout-container {
    display: flex;
    flex-direction: row;
    min-height: calc(100vh - 200px);
    padding: 0 2rem 3rem;
    gap: 3rem;
    max-width: 1400px;
    margin: 0 auto;
  }

  @media (max-width: 768px) {
    .checkout-container {
      flex-direction: column;
      padding: 0 1rem 2rem;
      gap: 2rem;
    }
    
    .checkout-hero {
      padding: 1.5rem 0;
      margin-bottom: 1rem;
    }
    
    .order-summary {
      margin-top: 0;
    }
  }

  .form-section {
    flex: 1.2;
  }

  .order-summary {
    flex: 1;
    display: flex;
    flex-direction: column;
  }

  .section-card {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: var(--shadow-light);
    border: 1px solid rgba(212, 175, 55, 0.2);
    position: relative;
    overflow: hidden;
  }

  .section-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--gradient-gold);
  }

  .section-title {
    color: var(--deep-purple);
    font-weight: 600;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .section-title::before {
    
    font-size: 1.2em;
  }

  .form-control, .form-select {
    font-size: 0.95rem;
    border: 2px solid #E9ECEF;
    border-radius: 12px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
    background: var(--warm-white);
  }

  .form-control:focus, .form-select:focus {
    border-color: var(--primary-gold);
    box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
    background: white;
  }

  .form-label {
    font-weight: 500;
    color: var(--charcoal);
    margin-bottom: 0.5rem;
  }

  .required-field::after {
    content: '*';
    color: var(--danger-red);
    margin-left: 4px;
  }

  #checkoutOrderSummary {
    flex-grow: 1;
    overflow: auto;
    max-height: 450px;
    background: var(--light-gray);
    border-radius: 12px;
    padding: 0.75rem;
    margin-bottom: 1.5rem;
  }

  @media (max-width: 768px) {
    #checkoutOrderSummary {
      max-height: none;
    }
  }

  .order-item {
    background: white;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1rem;
    box-shadow: var(--shadow-light);
    border-left: 4px solid var(--primary-rose);
  }

  .order-item:last-child {
    margin-bottom: 0;
  }

  .product-image-placeholder {
    width: 60px;
    height: 60px;
    background: var(--gradient-gold);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    margin-right: 1rem;
  }

  .product-image-placeholder img {
  max-width: 100%;
  max-height: 100%;
  border-radius: 5px;
  object-fit: fill;
  display: block;
}

  .quantity-controls {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.5rem;
  }

  .quantity-btn {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 2px solid var(--primary-gold);
    background: white;
    color: var(--primary-gold);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: bold;
  }

  .quantity-btn:hover {
    background: var(--primary-gold);
    color: white;
    transform: scale(1.1);
  }

  .remove-btn {
    color: var(--danger-red);
    background: none;
    border: none;
    font-size: 1.2rem;
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 4px;
    transition: all 0.3s ease;
  }

  .remove-btn:hover {
    background: rgba(220, 53, 69, 0.1);
    transform: scale(1.1);
  }

  .coupon-section {
    background: linear-gradient(135deg, rgba(212, 175, 55, 0.1) 0%, rgba(232, 180, 184, 0.1) 100%);
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1.5rem;
    border: 1px solid rgba(212, 175, 55, 0.3);
  }

  .input-group {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: var(--shadow-light);
  }

  .input-group .form-control {
    border-radius: 0;
    border-right: none;
  }

  .btn-coupon {
    background: var(--gradient-gold);
    border: none;
    color: var(--deep-purple);
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    border-radius: 0;
    transition: all 0.3s ease;
  }

  .btn-coupon:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-medium);
  }

  .coupon-link {
    color: var(--deep-purple);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
  }

  .coupon-link:hover {
    color: var(--primary-gold);
    text-decoration: underline;
  }

  .order-total {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: var(--shadow-light);
    border: 2px solid var(--primary-gold);
  }

  .total-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #E9ECEF;
  }

  .total-row:last-child {
    border-bottom: none;
    font-weight: bold;
    font-size: 1.1rem;
    color: var(--deep-purple);
  }

  .savings-text {
    color: var(--success-green);
    font-weight: 500;
  }

  #rzp-button1 {
    background: var(--gradient-purple);
    border: none;
    border-radius: 12px;
    padding: 1rem 2rem;
    font-size: 1.1rem;
    font-weight: 600;
    color: white;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }

  #rzp-button1::before {
    content: '🔒';
    margin-right: 0.5rem;
  }

  #rzp-button1:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-medium);
  }

  #rzp-button1:active {
    transform: translateY(-1px);
  }

  .discount-info {
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.1) 0%, rgba(40, 167, 69, 0.05) 100%);
    border: 1px solid rgba(40, 167, 69, 0.3);
    border-radius: 8px;
    padding: 0.75rem;
    margin-top: 1rem;
    color: var(--success-green);
    font-weight: 500;
  }

  .discount-info::before {
    content: '🎉 ';
  }

  .status-message {
    border-radius: 8px;
    padding: 0.75rem;
    margin-top: 0.5rem;
    font-weight: 500;
  }

  .status-success {
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.1) 0%, rgba(40, 167, 69, 0.05) 100%);
    border: 1px solid rgba(40, 167, 69, 0.3);
    color: var(--success-green);
  }

  .status-error {
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.1) 0%, rgba(220, 53, 69, 0.05) 100%);
    border: 1px solid rgba(220, 53, 69, 0.3);
    color: var(--danger-red);
  }

  /* Enhanced mobile responsiveness */
  @media (max-width: 576px) {
    .section-card {
      padding: 1rem;
      border-radius: 12px;
    }
    
    .checkout-container {
      padding: 0 0.5rem 2rem;
    }
    
    .product-image-placeholder {
      width: 40px;
      height: 40px;
      font-size: 1.2rem;
    }
    
    .order-item {
      padding: 0.75rem;
    }
  }

  /* Loading animation */
  .loading {
    opacity: 0.7;
    pointer-events: none;
  }

  .loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid var(--primary-gold);
    border-top: 2px solid transparent;
    border-radius: 50%;
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
   .autocomplete-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 10px;
    box-shadow: var(--shadow-light);
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    margin-top: 2px;
  }

  .autocomplete-suggestions div {
    padding: 10px 14px;
    cursor: pointer;
    transition: background 0.2s;
  }

  .autocomplete-suggestions div:hover {
    background-color: #f8f9fa;
  }

  .form-control:focus + .autocomplete-suggestions {
    display: block;
  }

  .position-relative {
    position: relative;
  }
</style>


<div class="container mt-2 checkout-container">
  <!-- Billing Details -->
  <div class="form-section">
    <div class="section-card">
      <h4 class="section-title">Billing Details</h4>
      <form id="billingForm">
        <div class="row g-3">
          <div class="col-6 col-md-6">
            <label class="form-label required-field">First Name</label>
            <input type="text" name="firstName" class="form-control" required placeholder="First name">
          </div>
          <div class="col-6 col-md-6">
            <label class="form-label required-field">Last Name</label>
            <input type="text" name="lastName" class="form-control" required placeholder="Last name">
          </div>
        </div>
      
        <div class="row g-3 mt-2">
  
          <input type="hidden" name="country" value="India">
  
        <div class="col-6 col-md-6 position-relative">
          <label class="form-label required-field">State</label>
          <input type="text" id="stateInput" name="state" class="form-control" placeholder="Select state" autocomplete="off" required>
          <div id="stateSuggestions" class="autocomplete-suggestions" style="display:none;"></div>
        </div>

        <div class="col-6 col-md-6 position-relative">
          <label class="form-label required-field">District</label>
          <input type="text" id="districtInput" name="district" class="form-control" placeholder="Select district" autocomplete="off" required>
          <div id="districtSuggestions" class="autocomplete-suggestions" style="display:none;"></div>
        </div>

        </div>
        
        <div class="mt-3">
          <label class="form-label required-field">Address Line 1</label>
          <input type="text" name="addressLine1" class="form-control" required placeholder="House number, street name">
        </div>
        
        <div class="mt-3">
          <label class="form-label">Address Line 2</label>
          <input type="text" name="addressLine2" class="form-control" placeholder="Apartment, suite, etc. (optional)">
        </div>
        
        <div class="row g-3 mt-2">
          <div class="col-6 col-md-6">
            <label class="form-label required-field">Town/City</label>
            <input type="text" name="city" class="form-control" required placeholder="Enter your city">
          </div>
          <div class="col-6 col-md-6">
            <label class="form-label required-field">Pincode</label>
            <input type="text" name="pincode" class="form-control" required placeholder="Enter pincode">
          </div>
        </div>
          <div class="row g-3 mt-2">
        <div class="col-md-6">
          <label class="form-label required-field">Phone Number</label>
          <input type="tel" id="billingPhone" name="phone" class="form-control" required placeholder="+91 XXXXX XXXXX">
        </div>

        <div class="col-md-6">
          <label class="form-label required-field">Email</label>
          <input type="email" id="billingEmail" name="email" class="form-control" required placeholder="your.email@example.com">
        </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Order Summary -->
  <div class="order-summary">
    <div class="section-card">
      <h4 class="section-title">Your Order Details</h4>
      <div id="checkoutOrderSummary"></div>
      
      <div class="coupon-section">
        <div class="input-group mb-2">
          <input type="text" class="form-control" id="couponCode" placeholder="Enter coupon code">
          <button class="btn btn-coupon" id="applyCouponBtn">Apply</button>
        </div>
        <div id="couponMsg" class="status-message status-error" style="display: none;"></div>
        <small class="text-muted">Don't have a coupon? 
          <a href="#" class="coupon-link" onclick="requestCoupon(event)">Request for a coupon</a>
        </small>
        <div id="couponRequestStatus" class="status-message"></div>
      </div>

      <div class="mt-auto">
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
        <button id="rzp-button1" class="btn w-100">
          Pay ₹<span id="totalPrice">0</span>
        </button>
        <div class="discount-info" id="discountInfo" style="display: none;"></div>
      </div>
    </div>
  </div>
</div>

<?php include "components/footer.php"; ?>

<!-- Load state and district data -->
<script>
  document.addEventListener("DOMContentLoaded", () => {
    const stateInput = document.getElementById("stateInput");
    const stateSuggestions = document.getElementById("stateSuggestions");
    const districtInput = document.getElementById("districtInput");
    const districtSuggestions = document.getElementById("districtSuggestions");

    let statesData = [];
    let selectedState = null;

    // Fetch state and district JSON
    fetch("state.json")
      .then(res => res.json())
      .then(data => {
        statesData = data.states;
        attachStateListeners();
      });

    function attachStateListeners() {
      const stateNames = statesData.map(s => s.state);

      // On focus, show all states
      stateInput.addEventListener("focus", () => {
        showSuggestions(stateInput, stateSuggestions, stateNames, selectState);
      });

      // On typing, filter states
      stateInput.addEventListener("input", () => {
        const filtered = filterList(stateInput.value, stateNames);
        showSuggestions(stateInput, stateSuggestions, filtered, selectState);
      });

      // District input setup
      districtInput.addEventListener("focus", () => {
        if (!selectedState) return;
        showSuggestions(districtInput, districtSuggestions, selectedState.districts, () => {});
      });

      districtInput.addEventListener("input", () => {
        if (!selectedState) return;
        const filtered = filterList(districtInput.value, selectedState.districts);
        showSuggestions(districtInput, districtSuggestions, filtered, () => {});
      });

      // Close dropdown when clicking outside
      document.addEventListener("click", function (e) {
        if (!stateInput.contains(e.target)) stateSuggestions.style.display = "none";
        if (!districtInput.contains(e.target)) districtSuggestions.style.display = "none";
      });
    }

    function filterList(input, list) {
      return list.filter(item => item.toLowerCase().includes(input.toLowerCase()));
    }

    function showSuggestions(inputEl, suggestionBox, options, onSelect) {
      suggestionBox.innerHTML = "";
      suggestionBox.style.display = "block";
      options.slice(0, 10).forEach(item => {
        const div = document.createElement("div");
        div.textContent = item;
        div.addEventListener("click", () => {
          inputEl.value = item;
          suggestionBox.style.display = "none";
          onSelect(item);
        });
        suggestionBox.appendChild(div);
      });
    }

    function selectState(stateName) {
      selectedState = statesData.find(s => s.state === stateName);
      districtInput.value = "";
      districtSuggestions.innerHTML = "";
    }
  });
</script>

