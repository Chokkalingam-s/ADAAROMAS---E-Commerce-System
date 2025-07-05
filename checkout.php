<?php include "components/header.php"; ?>

<style>
  body {
    overflow-x: hidden;
  }

  .checkout-container {
    display: flex;
    flex-direction: row;
    min-height: calc(100vh - 100px); /* adjust if header/footer size changes */
    padding-top: 40px;
    padding-bottom: 40px;
    gap: 40px;
  }

  @media (max-width: 768px) {
    .checkout-container {
      flex-direction: column;
      padding: 20px 15px;
      min-height: auto;
    }

    .order-summary {
      margin-top: 32px;
    }
  }

  .form-section {
    flex: 1;
  }

  .order-summary {
    flex: 1;
    display: flex;
    flex-direction: column;
  }

  #checkoutOrderSummary {
    flex-grow: 1;
    overflow: auto;
    max-height: 400px;
  }

  @media (max-width: 768px) {
    #checkoutOrderSummary {
      max-height: none;
    }
  }

  .form-control,
  .form-select {
    font-size: 0.95rem;
  }

  .btn {
    font-size: 0.95rem;
  }
</style>

<div class="container checkout-container">
  <!-- Billing Details -->
  <div class="form-section">
    <h4 class="mb-3">Billing Details</h4>
    <form id="billingForm">
      <div class="row g-2">
        <div class="col-6">
          <label class="form-label">First Name*</label>
          <input type="text" name="firstName" class="form-control" required>
        </div>
        <div class="col-6">
          <label class="form-label">Last Name*</label>
          <input type="text" name="lastName" class="form-control" required>
        </div>
      </div>

      <div class="mt-3">
        <label class="form-label">Country*</label>
        <input type="text" name="country" class="form-control" required>
      </div>

      <div class="row g-2 mt-2">
        <div class="col-6">
          <label class="form-label">State*</label>
          <input type="text" name="state" class="form-control" required>
        </div>
        <div class="col-6">
          <label class="form-label">District*</label>
          <input type="text" name="district" class="form-control" required>
        </div>
      </div>

      <div class="mt-3">
        <label class="form-label">Address Line 1*</label>
        <input type="text" name="addressLine1" class="form-control" required>
      </div>
      <div class="mt-2">
        <label class="form-label">Address Line 2</label>
        <input type="text" name="addressLine2" class="form-control">
      </div>

      <div class="row g-2 mt-2">
        <div class="col-6">
          <label class="form-label">Town/City*</label>
          <input type="text" name="city" class="form-control" required>
        </div>
        <div class="col-6">
          <label class="form-label">Pincode*</label>
          <input type="text" name="pincode" class="form-control" required>
        </div>
      </div>

      <div class="mt-3">
        <label class="form-label">Phone Number*</label>
        <input type="tel" id="billingPhone" name="phone" class="form-control" required>
      </div>
      <div class="mt-2">
        <label class="form-label">Email*</label>
        <input type="email" id="billingEmail" name="email" class="form-control" required>
      </div>

      <div class="form-check mt-3">
        <input class="form-check-input" type="checkbox" id="shipDifferent">
        <label class="form-check-label" for="shipDifferent">Ship to different address?</label>
      </div>

      <div class="mt-2 d-none" id="shippingAddressBox">
        <label class="form-label">Shipping Address</label>
        <textarea class="form-control" name="shippingAddress" rows="2"></textarea>
      </div>
    </form>
  </div>

  <!-- Order Summary -->
  <div class="order-summary">
    <h4>Your Order Details</h4>
    <div id="checkoutOrderSummary" class="border p-3 rounded mb-3"></div>

    <div class="input-group mb-2">
      <input type="text" class="form-control" id="couponCode" placeholder="Enter coupon code">
      <button class="btn btn-outline-primary" id="applyCouponBtn">Apply</button>
    </div>
    <div id="couponMsg" class="text-danger small mb-2"></div>

    <small class="text-muted">Don’t have a coupon?
      <a href="#" onclick="requestCoupon(event)">Request for a coupon</a>
    </small>
    <div id="couponRequestStatus" class="mt-2 small"></div>

    <div class="mt-auto">
      <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
      <button id="rzp-button1" class="btn btn-success w-100 mt-4">
        Pay ₹<span id="totalPrice">0</span>
      </button>
      <p class="text-success" id="discountInfo" style="display: none;"></p>
    </div>
  </div>
</div>

<?php include "components/footer.php"; ?>
