<?php include "components/header.php"; ?>

<div class="container py-5">
  <div class="row">
    <!-- Billing Details -->
    <div class="col-md-6">
      <h4>Billing Details</h4>
      <form id="billingForm">
        <div class="row g-2">
          <div class="col-md-6">
            <label class="form-label">First Name*</label>
            <input type="text" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Last Name*</label>
            <input type="text" class="form-control" required>
          </div>
        </div>
        <div class="mt-3">
          <label class="form-label">Country*</label>
          <input type="text" class="form-control" required>
        </div>
        <div class="row g-2 mt-2">
          <div class="col-md-6">
            <label class="form-label">State*</label>
            <input type="text" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">District*</label>
            <input type="text" class="form-control" required>
          </div>
        </div>
        <div class="mt-3">
          <label class="form-label">Address Line 1*</label>
          <input type="text" class="form-control" required>
        </div>
        <div class="mt-2">
          <label class="form-label">Address Line 2</label>
          <input type="text" class="form-control">
        </div>
        <div class="row g-2 mt-2">
          <div class="col-md-6">
            <label class="form-label">Town/City*</label>
            <input type="text" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Pincode*</label>
            <input type="text" class="form-control" required>
          </div>
        </div>
        <div class="mt-3">
          <label class="form-label">Phone Number*</label>
          <input type="tel" id="billingPhone" class="form-control" required>
        </div>
        <div class="mt-2">
          <label class="form-label">Email*</label>
          <input type="email" id="billingEmail" class="form-control" required>
        </div>
        <div class="form-check mt-3">
          <input class="form-check-input" type="checkbox" id="shipDifferent">
          <label class="form-check-label" for="shipDifferent">Ship to different address?</label>
        </div>
        <div class="mt-2 d-none" id="shippingAddressBox">
          <label class="form-label">Shipping Address</label>
          <textarea class="form-control"></textarea>
        </div>
      </form>
    </div>

    <!-- Order Summary -->
    <div class="col-md-6">
      <h4>Your Order Details</h4>
      <div id="checkoutOrderSummary" class="border p-3 rounded mb-3"></div>

      <div class="input-group mb-2">
        <input type="text" class="form-control" placeholder="Enter coupon code">
        <button class="btn btn-outline-primary">Apply</button>
      </div>
      <small class="text-muted">Don’t have a coupon? <a href="#" onclick="requestCoupon(event)">Request for a coupon</a></small>

      <div class="text-center mt-4">
        <button class="btn btn-success w-100">Pay with Razorpay</button>
      </div>
    </div>
  </div>
</div>



<?php include "components/footer.php"; ?>
