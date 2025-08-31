<?php
include('../config/db.php');
session_start();
date_default_timezone_set('Asia/Kolkata');
if (!isset($_SESSION['admin_logged_in'])) header('Location: index.php');

// Fetch all customized orders
$stmt = $conn->prepare("SELECT * FROM orders WHERE isCustomized = 1 ORDER BY orderDate DESC");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Customize Orders - Admin CRM</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .order-card { cursor:pointer; transition:all 0.3s; border-radius:10px; }
    .order-card:hover { transform:scale(1.02); }
    .expanded { background:#fff; border:2px solid #0d6efd; }
    .order-details { display:none; padding:15px; border-top:1px solid #ddd; }
    .form-label { font-weight:600; }
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
  <h3 class="mb-4">Customized Orders</h3>

  <?php foreach($orders as $order): ?>
    <div class="card shadow-sm mb-3 order-card" data-order-id="<?= $order['orderId'] ?>">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <h5 class="mb-1">Order #<?= $order['orderId'] ?></h5>
          <small class="text-muted">Date: <?= date("d M Y, h:i A", strtotime($order['orderDate'])) ?></small>
        </div>
        <div>
          <select class="form-select status-dropdown" data-order-id="<?= $order['orderId'] ?>" style="width:160px;">
            <?php 
              $statuses = ['Pending','Confirmed','Replaced','Delivered','Cancelled'];
              foreach($statuses as $s) {
                $selected = ($s == $order['status']) ? "selected" : "";
                echo "<option value='$s' $selected>$s</option>";
              }
            ?>
          </select>
        </div>
      </div>
      <div class="order-details" id="details-<?= $order['orderId'] ?>"></div>
    </div>
  <?php endforeach; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){

  // Expand/Collapse order card
  $(".order-card").click(function(e){
    if($(e.target).hasClass("status-dropdown")) return; // prevent toggle if dropdown clicked
    let orderId = $(this).data("order-id");
    let details = $("#details-" + orderId);

    if(details.is(":visible")){
      details.slideUp();
      $(this).removeClass("expanded");
    } else {
      $(".order-details").slideUp();
      $(".order-card").removeClass("expanded");
      details.html("<div class='text-center py-3'>Loading...</div>").slideDown();
      $(this).addClass("expanded");

      // fetch order details
      $.post("update_customize_order.php", { action: "fetch", orderId: orderId }, function(data){
        details.html(data);
      });
    }
  });

  // Update status instantly
  $(".status-dropdown").change(function(){
    let orderId = $(this).data("order-id");
    let status = $(this).val();
    $.post("update_customize_order.php", { action: "update_status", orderId: orderId, status: status }, function(res){
      alert(res);
    });
  });

  // Save billing details
  $(document).on("submit", ".billing-form", function(e){
    e.preventDefault();
    let form = $(this);
    $.post("update_customize_order.php", form.serialize(), function(res){
      alert(res);
    });
  });

});
</script>
</body>
</html>
