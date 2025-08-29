<?php
include('../config/db.php');
session_start();
date_default_timezone_set('Asia/Kolkata');
if (!isset($_SESSION['admin_logged_in'])) header('Location: index.php');

$stmt = $conn->query("
  SELECT o.orderId, o.*, o.transactionId, o.orderDate, o.billingAmount,
         u.name as uname, u.phoneNo, u.email, u.state, u.district, u.city, u.address, u.pincode,
         od.productId, od.quantity, od.size,
         p.name AS productName, p.*
  FROM orders o
  JOIN users u ON o.userId = u.userId
  JOIN order_details od ON o.orderId = od.orderId
  JOIN products p ON od.productId = p.productId
  ORDER BY o.orderDate DESC
");
$orders = [];
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
  $orders[$row['orderId']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>CustomizeOrders - Admin CRM</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
  
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
      <li class="nav-item"><a class="nav-link " href="add-product.php">Add Product</a></li>
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




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>
