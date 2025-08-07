<?php
require 'config/db.php';
$orderId = $_GET['orderId'] ?? null;

if (!$orderId) {
  echo "<h2>Order ID missing!</h2>";
  exit;
}

// Fetch order and user details
$stmt = $conn->prepare("
  SELECT o.*, u.name, u.phoneNo, u.email, u.address, u.city, u.state, u.pincode
  FROM orders o
  JOIN users u ON o.userId = u.userId
  WHERE o.orderId = ?
");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
  echo "<h2>Order not found!</h2>";
  exit;
}

// Fetch products in the order
$stmt = $conn->prepare("
  SELECT od.quantity, od.size, p.name, p.asp, p.image
  FROM order_details od
  JOIN products p ON od.productId = p.productId
  WHERE od.orderId = ?
");
$stmt->execute([$orderId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include "components/header.php"; ?>

<style>
  .blink {
    animation: blinker 1.5s linear infinite;
  }
  @keyframes blinker {
    50% { opacity: 0.5; }
  }
  .status-pending {
    color: #ffc107; /* yellow */
  }
  .status-confirmed {
    color: #28a745; /* green */
  }
  .status-delivered {
    color: #6c757d; /* grey */
  }
  .status-cancelled {
    color: #dc3545; /* red */
  }
</style>

<div class="container py-5">
  <div class="text-center">
    <h2 class="text-success"><i class="bi bi-bag-check-fill"></i> Thank You for Your Order!</h2>
    <p>Your order <strong>#<?= $orderId ?></strong> has been successfully placed and is currently 
  <strong class="blink status-<?= strtolower($order['status']) ?>">
    <?= $order['status'] ?>
  </strong>.
</p>
    <p>A confirmation email has been sent to <strong><?= $order['email'] ?></strong>.</p>
  </div>

  <hr>

  <div class="row mt-4">
    <div class="col-md-6">
      <h4>Order Summary</h4>
      <ul class="list-group mb-3">
        <?php foreach ($items as $item): ?>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <div>
              <img src="<?= $item['image'] ?>" width="50" height="50" class="me-2 rounded">
              <strong><?= $item['name'] ?></strong> (<?= $item['size'] ?>ml) × <?= $item['quantity'] ?>
            </div>
            <span>₹<?= $item['asp'] * $item['quantity'] ?></span>
          </li>
        <?php endforeach; ?>
        <li class="list-group-item d-flex justify-content-between">
          <strong>Total</strong>
          <strong>₹<?= number_format($order['billingAmount'], 2) ?></strong>
        </li>
        <li class="list-group-item">
          Payment ID: <strong><?= $order['transactionId'] ?></strong>
        </li>
      </ul>
    </div>

    <div class="col-md-6">
      <h4>Shipping To</h4>
      <div class="border p-3 rounded">
        <p><strong><?= $order['name'] ?></strong></p>
        <p><?= $order['address'] ?>, <?= $order['city'] ?></p>
        <p><?= $order['state'] ?> - <?= $order['pincode'] ?></p>
        <p>Phone: <?= $order['phoneNo'] ?></p>
        <p>Email: <?= $order['email'] ?></p>
      </div>

      <div class="alert alert-info mt-4">
        <strong>Delivery Policy:</strong><br>
        Orders are shipped within <strong>24–48 hours</strong> and delivered in <strong>5–6 business days</strong>.<br>
      </div>
    </div>
  </div>

  <div class="text-center mt-5">
    <a href="index.php" class="btn btn-outline-success">Continue Shopping</a>
  </div>
</div>

<?php include "components/footer.php"; ?>
