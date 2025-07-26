<?php
include('../config/db.php');
session_start();
date_default_timezone_set('Asia/Kolkata');
if (!isset($_SESSION['admin_logged_in'])) header('Location: index.php');

$stmt = $conn->query("
  SELECT o.orderId, o.*, o.transactionId, o.orderDate, o.billingAmount,
         u.name, u.phoneNo, u.email, u.state, u.district, u.city, u.address, u.pincode,
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
  <title>All Orders - Admin CRM</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .card-header:hover { cursor: pointer; background: #f8f9fa; }
    .card-body { display: none; }
    .card.show .card-body { display: block; }
    .status-badge { font-size: 0.8rem; padding: 4px 8px; border-radius: 5px; }
    .bg-Pending { background-color: #ffc107; color: #000; }
    .bg-Cancelled { background-color: #dc3545; color: #fff; }
    .bg-Confirmed { background-color: #28a745; color: #fff; }
    .bg-Delivered { background-color: #343a40; color: #fff; }
    .bg-Replaced { background-color: #17a2b8; color: #fff; }
    .table td, .table th { vertical-align: middle; }
.status-badge {
  font-size: 0.8rem;
  padding: 4px 10px;
  border-radius: 5px;
  font-weight: 500;
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
      <li class="nav-item"><a class="nav-link " href="add-product.php">Add Product</a></li>
      <li class="nav-item"><a class="nav-link" href="manage-stock.php">Manage Stock</a></li>
      <li class="nav-item"><a class="nav-link" href="generate-coupon.php">Generate Coupon</a></li>
      <li class="nav-item"><a class="nav-link active" href="orders.php">Orders</a></li>
      <li class="nav-item"><a class="nav-link" href="stats.php">Stats</a></li>
      <li class="nav-item"><a class="nav-link" href="report.php">Report</a></li>
      <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Logout</a></li>
    </ul>
  </div>
</nav>

<div class="container py-4">
  <h2 class="mb-4">All Orders</h2>

  <div class="card mb-4 shadow-sm p-3">
    <form id="filterForm" class="row g-3 align-items-center">
      <div class="col-auto">
        <label for="statusFilter" class="form-label fw-bold mb-0">Filter by Status:</label>
      </div>
      <div class="col-auto">
        <select id="statusFilter" class="form-select">
          <option value="All">All</option>
          <option value="Pending">Pending</option>
          <option value="Confirmed">Confirmed</option>
          <option value="Cancelled">Cancelled</option>
          <option value="Delivered">Delivered</option>
          <option value="Replaced">Replaced</option>
        </select>
      </div>
    </form>
  </div>
<div class="row">
  <?php foreach ($orders as $orderId => $items): 
    $first = $items[0];
    $totalAmount = array_sum(array_map(fn($x) => $x['asp'] * $x['quantity'], $items));
    $displayId = $first['isReplacement']
      ? $first['originalOrderId'] . 'R'
      : $orderId;
  ?>
    <div class="col-lg-4 mb-4">
      <div class="card shadow-sm border-light">
        <div class="card-header d-flex justify-content-between align-items-center toggle-card">
          <div> 
            <strong>Order #<?= $displayId ?></strong> |
            <span class="status-badge bg-<?= $first['status'] ?>"><?= $first['status'] ?></span> |
            <small><?= date('d M Y, h:i A', strtotime($first['orderDate'])) ?></small>
          </div>
          <form method="POST" action="update-status.php" class="d-flex gap-2">
            <input type="hidden" name="orderId" value="<?= $orderId ?>">
            <input type="hidden" name="currentStatus" value="<?= $first['status'] ?>">
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
            <?= $first['address'] ?>, <?= $first['city'] ?>, <?= $first['district'] ?>, <?= $first['state'] ?> - <?= $first['pincode'] ?>
          </p>

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
                    <td><strong>₹<?= number_format($item['asp'] * $item['quantity'], 2) ?></strong></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <?php
            $gst = $first['GST'];
            $estimatedPayable = $first['TotalASP'] + $gst;
            $discount = $estimatedPayable - $first['billingAmount'];
          ?>
          <div class="row justify-content-end">
            <div class="col-md-12">
              <div class="border rounded p-3 bg-light">
                <h6 class="text-muted fw-semibold mb-3">Order Summary</h6>
                <div class="d-flex justify-content-between mb-2">
                  <span>Total Product Value</span>
                  <span>₹<?= number_format($totalAmount, 2) ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                  <span>GST (18%)</span>
                  <span>₹<?= number_format($gst, 2) ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                  <span><strong>Estimated Total</strong></span>
                  <span><strong>₹<?= number_format($estimatedPayable, 2) ?></strong></span>
                </div>
                <div class="d-flex justify-content-between mb-2 text-success">
                  <span>Discount</span>
                  <span>− ₹<?= number_format($discount, 2) ?></span>
                </div>
                <div class="d-flex justify-content-between border-top pt-2 mt-2">
                  <span><strong>Total Paid</strong></span>
                  <span><strong class="text-primary">₹<?= number_format($first['billingAmount'], 2) ?></strong></span>
                </div>
              </div>
            </div>
          </div>

          <?php if (!str_contains($orderId, 'R')): ?>
            <div class="text-end mt-3">
              <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#replaceModal<?= $orderId ?>">Replace Order</button>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="replaceModal<?= $orderId ?>" tabindex="-1">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <form method="POST" action="place-replacement.php">
                    <input type="hidden" name="orderId" value="<?= $orderId ?>">
                    <div class="modal-header">
                      <h5 class="modal-title">Place Replacement for Order #<?= $orderId ?></h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <table class="table">
                        <thead>
                          <tr><th>Product</th><th>Size</th><th>Original Qty</th><th>Replace Qty</th></tr>
                        </thead>
                        <tbody>
                          <?php foreach ($items as $item): ?>
                            <tr>
                              <td><?= $item['productName'] ?></td>
                              <td><?= $item['size'] ?></td>
                              <td><?= $item['quantity'] ?></td>
                              <td>
                                <input type="number" class="form-control" name="replaceQty[<?= $item['productId'] ?>][<?= $item['size'] ?>]" min="0" max="<?= $item['quantity'] ?>" value="0">
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>
                    <div class="modal-footer">
                      <button type="submit" class="btn btn-primary">Confirm Replacement</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.querySelectorAll('.toggle-card').forEach(header => {
    header.addEventListener('click', () => {
      header.parentElement.classList.toggle('show');
    });
  });
</script>
<script>
  document.getElementById('statusFilter').addEventListener('change', function () {
    const selected = this.value;
    document.querySelectorAll('.card.shadow-sm').forEach(card => {
      const badge = card.querySelector('.status-badge');
      if (!badge) return;
      const status = badge.textContent.trim();
      if (selected === 'All' || status === selected) {
        card.style.display = 'block';
      } else {
        card.style.display = 'none';
      }
    });
  });
</script>

</body>
</html>
