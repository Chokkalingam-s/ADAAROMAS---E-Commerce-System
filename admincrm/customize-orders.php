<?php
include('../config/db.php');
session_start();
date_default_timezone_set('Asia/Kolkata');
if (!isset($_SESSION['admin_logged_in'])) header('Location: index.php');

// Fetch customized orders
$stmt = $conn->prepare("SELECT * FROM orders WHERE isCustomized=1 ORDER BY orderDate DESC");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Customize Orders - Admin CRM</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .order-card { border:1px solid #ddd; border-radius:10px; margin-bottom:15px; background:#fff; }
    .order-header { cursor:pointer; padding:12px; background:#f8f9fa; border-bottom:1px solid #ddd; display:flex; justify-content:space-between; align-items:center; }
    .order-body { display:none; padding:15px; }
    .order-body.active { display:block; }
    .user-details p { margin:0; padding:2px 0; }
    .save-btn { margin-top:10px; }
    .order-pending    { background: #fffbe6; }   /* light yellow */
.order-cancelled  { background: #ffeaea; }   /* light red */
.order-delivered  { background: #f2f2f2; color: #222; } /* light black/grey */
.order-replaced   { background: #e6f0ff; }   /* light blue */
.order-confirmed  { background: #eaffea; }   /* light green */
.billing-inputs {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}
.billing-inputs .input-wrap {
  min-width: 120px;
  flex: 1 1 0;
}
@media (max-width: 767px) {
  .billing-inputs {
    flex-direction: row !important; /* force row on mobile */
    gap: 8px;
  }
  .billing-inputs .input-wrap {
    min-width: 0;
    flex: 1 1 0;
  }
  .billing-form .remarks {
    margin-top: 10px;
  }
}

  </style>
</head>
<body class="bg-light">
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3">
    <a class="navbar-brand" href="../admincrm">ADA AROMAS Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="adminNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="add-product.php">Add Product</a></li>
        <li class="nav-item"><a class="nav-link" href="manage-stock.php">Manage Stock</a></li>
        <li class="nav-item"><a class="nav-link" href="generate-coupon.php">Generate Coupon</a></li>
        <li class="nav-item"><a class="nav-link" href="orders.php">Orders</a></li>
        <li class="nav-item"><a class="nav-link active" href="customize-orders.php">Customize Orders</a></li>
        <li class="nav-item"><a class="nav-link" href="stats.php">Stats</a></li>
        <li class="nav-item"><a class="nav-link" href="report.php">Report</a></li>
        <li class="nav-item"><a class="nav-link" href="cancel.php">Cancellation Survey</a></li>
        <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </nav>

  <div class="container my-4">
    <h3 class="mb-3">Customized Orders</h3>
    <?php foreach($orders as $order): ?>
      <?php
        $cstmt = $conn->prepare("SELECT * FROM customize WHERE orderId=?");
        $cstmt->execute([$order['orderId']]);
        $custom = $cstmt->fetch(PDO::FETCH_ASSOC);

        $ustmt = $conn->prepare("SELECT * FROM users WHERE userId=?");
        $ustmt->execute([$order['userId']]);
        $user = $ustmt->fetch(PDO::FETCH_ASSOC);
      ?>
<div class="order-card order-<?= strtolower($order['status']) ?>" data-id="<?= $order['orderId'] ?>">
        <!-- header -->
        <div class="order-header order-<?= strtolower($order['status']) ?>">
          <span><strong>Order #<?= $order['orderId'] ?></strong></span>
          <select class="form-select form-select-sm status-dropdown" data-id="<?= $order['orderId'] ?>" style="width:auto">
            <?php foreach(['Pending','Confirmed','Replaced','Delivered','Cancelled'] as $st): ?>
              <option value="<?= $st ?>" <?= $order['status']==$st?'selected':'' ?>><?= $st ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- body -->
<!-- body -->
<div class="order-body">
  <h6>Customize Request & User Details</h6>
  <div class="row">
    <!-- Left: Description + Image -->
    <div class="col-md-6">
      <p><?= htmlspecialchars($custom['description']) ?></p>
<?php if($custom['imageUrl']): ?>
  <img src="/adaaromas/<?= ltrim($custom['imageUrl'], '/') ?>" class="img-fluid rounded mb-3" style="max-width:250px">
<?php endif; ?>
    </div>

    <!-- Right: User Details in tabular format -->
    <div class="col-md-6">
      <table class="table table-sm table-bordered">
<tbody>
  <tr><th>Name</th><td colspan="3"><?= $user['name'] ?></td></tr>
  <tr><th>Phone</th><td colspan="3"><?= $user['phoneNo'] ?></td></tr>
  <tr><th>Email</th><td colspan="3"><?= $user['email'] ?></td></tr>
  <tr>
    <th>State</th><td><?= $user['state'] ?></td>
    <th>District</th><td><?= $user['district'] ?></td>
  </tr>
  <tr>
    <th>City</th><td><?= $user['city'] ?></td>
    <th>Pincode</th><td><?= $user['pincode'] ?></td>
  </tr>
  <tr><th>Address</th><td colspan="3"><?= $user['address'] ?></td></tr>
</tbody>
      </table>
    </div>
  </div>

<h6>Billing Details</h6>
<form class="billing-form" data-id="<?= $order['orderId'] ?>">
  <div class="billing-inputs d-flex flex-row flex-wrap g-2 align-items-center">
    <div class="input-wrap">
      <label for="asp-<?= $order['orderId'] ?>" class="form-label mb-1">Total ASP</label>
      <input type="number" class="form-control asp" id="asp-<?= $order['orderId'] ?>" name="TotalASP" placeholder="Total ASP" value="<?= $order['TotalASP'] ?>">
    </div>
    <div class="input-wrap">
      <label for="gst-<?= $order['orderId'] ?>" class="form-label mb-1">GST (18%)</label>
      <input type="number" class="form-control gst" id="gst-<?= $order['orderId'] ?>" name="GST" placeholder="GST (18%)" value="<?= $order['GST'] ?>" readonly>
    </div>
    <div class="input-wrap">
      <label for="billing-<?= $order['orderId'] ?>" class="form-label mb-1">Billing Amount</label>
      <input type="number" step="0.01" class="form-control billing" id="billing-<?= $order['orderId'] ?>" name="billingAmount" placeholder="Billing Amount" value="<?= $order['billingAmount'] ?>">
    </div>
    <div class="input-wrap">
      <label for="profit-<?= $order['orderId'] ?>" class="form-label mb-1">Profit</label>
      <input type="number" class="form-control profit" id="profit-<?= $order['orderId'] ?>" name="PROFIT" placeholder="Profit" value="<?= $order['PROFIT'] ?>">
    </div>
  </div>
  <div class="mt-2">
    <label for="remarks-<?= $order['orderId'] ?>" class="form-label mb-1">Remarks</label>
    <textarea class="form-control remarks" id="remarks-<?= $order['orderId'] ?>" name="remarks" placeholder="Remarks"><?= $order['remarks'] ?></textarea>
  </div>
  <button type="submit" class="btn btn-success btn-sm mt-2">Save Billing Details</button>
</form>
</div>

      </div>
    <?php endforeach; ?>
  </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function(){
  // toggle only when header clicked
  $(".order-header").on("click", function(e){
    $(this).siblings(".order-body").toggleClass("active");
  });

  // prevent card closing when inside inputs
  $(".order-body").on("click", function(e){
    e.stopPropagation();
  });

  // status change
  $(".status-dropdown").change(function(){
    let id = $(this).data("id");
    let status = $(this).val();
    $.post("update-status-customize.php", {orderId:id, status:status});
  });

  // auto calculate GST + billing
  $(".asp").on("input", function(){
    let asp = parseFloat($(this).val())||0;
    let gst = (asp * 0.18).toFixed(0);
    let billing = (asp + parseFloat(gst)).toFixed(2);
    let parent = $(this).closest(".billing-form");
    parent.find(".gst").val(gst);
    parent.find(".billing").val(billing);
  });

  // save billing details
  $(".billing-form").submit(function(e){
    e.preventDefault();
    let id = $(this).data("id");
    let data = $(this).serialize()+"&orderId="+id;
    $.post("save-billing.php", data, function(){
      alert("Billing details updated");
    });
  });
});

$(".status-dropdown").change(function(){
  let id = $(this).data("id");
  let status = $(this).val();
  $.post("update-status-customize.php", {orderId:id, status:status});

  // Change card and header color instantly
  let card = $(this).closest(".order-card");
  let header = card.find(".order-header");
  let statuses = ["pending","confirmed","replaced","delivered","cancelled"];
  statuses.forEach(st => {
    card.removeClass("order-" + st);
    header.removeClass("order-" + st);
  });
  card.addClass("order-" + status.toLowerCase());
  header.addClass("order-" + status.toLowerCase());
});
</script>
</body>
</html>
