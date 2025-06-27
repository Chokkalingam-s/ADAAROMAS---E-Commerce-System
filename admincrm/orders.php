<?php
include('../config/db.php');
session_start();
date_default_timezone_set('Asia/Kolkata');
if (!isset($_SESSION['admin_logged_in'])) header('Location: index.php');

$orders = $conn->query("
  SELECT o.orderId, o.status, o.transactionId, o.orderDate, o.billingAmount,
         u.name, u.phoneNo, u.email, u.state, u.district, u.city, u.address, u.pincode,
         od.productId, od.quantity, od.size,
         p.name AS productName, p.image
  FROM orders o
  JOIN users u ON o.userId = u.userId
  JOIN order_details od ON o.orderId = od.orderId
  JOIN products p ON od.productId = p.productId
  ORDER BY o.orderDate DESC
")->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Order Details</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .form-section { max-width: 800px; margin: auto; padding: 2rem; background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    .form-section h4 { margin-bottom: 1.5rem; }
    .readonly-box { background: #f8f9fa; padding: 0.5rem 1rem; border-radius: 5px; }
  </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3">
  <a class="navbar-brand" href="../admincrm">ADA Aromas Admin</a>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="adminNav">
    <ul class="navbar-nav ms-auto">
      <li class="nav-item"><a class="nav-link " href="add-product.php">Add Product</a></li>
      <li class="nav-item"><a class="nav-link" href="manage-stock.php">Manage Stock</a></li>
      <li class="nav-item"><a class="nav-link" href="generate-coupon.php">Generate Coupon</a></li>
      <li class="nav-item"><a class="nav-link active" href="orders.php">Orders</a></li>
      <li class="nav-item"><a class="nav-link" href="report.php">View Report</a></li>
      <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Logout</a></li>
    </ul>
  </div>
</nav>
<div class="container py-4">
  <h2 class="mb-4">All Orders</h2>

  <?php foreach ($orders as $orderId => $items): 
    $first = $items[0];
    $badgeColor = match ($first['status']) {
      'Pending' => 'warning',
      'Cancelled' => 'danger',
      'Confirmed' => 'success',
      'Delivered' => 'dark',
      default => 'secondary',
    };
  ?>
    <div class="card mb-4 shadow-sm border-light">
      <div class="card-header d-flex justify-content-between align-items-center bg-light">
        <div>
          <strong>Order #<?= $orderId ?></strong> |
          <span class="badge bg-<?= $badgeColor ?>"><?= $first['status'] ?></span> |
          <small><?= date('d M Y, h:i A', strtotime($first['orderDate'])) ?></small>
        </div>
        <form method="POST" action="update-status.php" class="d-flex gap-2">
          <input type="hidden" name="orderId" value="<?= $orderId ?>">
          <select name="newStatus" class="form-select form-select-sm">
            <?php foreach (['Pending','Confirmed','Cancelled','Delivered'] as $opt): ?>
              <option value="<?= $opt ?>" <?= $opt === $first['status'] ? 'selected' : '' ?>><?= $opt ?></option>
            <?php endforeach; ?>
          </select>
          <button class="btn btn-sm btn-primary">Update</button>
        </form>
      </div>
      <div class="card-body">
        <h5 class="text-muted mb-2">Billing Info</h5>
        <p><strong><?= $first['name'] ?></strong> | <?= $first['phoneNo'] ?> | <?= $first['email'] ?><br>
        <?= $first['address'] ?>, <?= $first['city'] ?>, <?= $first['district'] ?>, <?= $first['state'] ?> - <?= $first['pincode'] ?></p>

        <h5 class="text-muted mt-4">Order Details</h5>
        <div class="table-responsive">
          <table class="table table-bordered align-middle">
            <thead class="table-light">
              <tr>
                <th>Image</th>
                <th>Product</th>
                <th>Size</th>
                <th>Quantity</th>
                <th>Total (₹)</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($items as $item): ?>
                <tr>
                  <td><img src="../<?= $item['image'] ?>" width="60" height="60" style="object-fit:cover;"></td>
                  <td><?= $item['productName'] ?></td>
                  <td><?= $item['size'] ?></td>
                  <td><?= $item['quantity'] ?></td>
                  <td><strong>₹<?= number_format($first['billingAmount'], 2) ?></strong></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <p class="text-end mb-0"><strong>Total Paid:</strong> ₹<?= number_format($first['billingAmount'], 2) ?></p>
      </div>
    </div>
  <?php endforeach; ?>
</div>
</body>
</html>

