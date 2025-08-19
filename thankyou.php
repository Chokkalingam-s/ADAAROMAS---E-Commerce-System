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
    color: #ffbf00ff; /* yellow */
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

  .btn-outline-success
  {
    border-color: #28a745;
    color: #28a745;
    width: 100%;
    /* center of the page */
  }
</style>

<div class="container py-5">
  <div class="text-center">
    <h2 class="text-success"><i class="bi bi-bag-check-fill"></i> Thank You for Your Order!</h2>
    <p>Your order <strong>#<?= $orderId ?></strong> has been successfully placed and is currently 
<strong id="orderStatus" class="blink status-<?= strtolower($order['status']) ?>">
    <?= strtoupper($order['status']) ?>
</strong>

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
             Orders are <strong>Shipped</strong> within <strong>24 Business Hours</strong>, and Expected to be <strong>Delivered</strong> within <strong>3-4 Working Days</strong>.
      </div>
    </div>
  </div>



<?php
// Assuming you already have $orderId from the URL or session
$orderId = $_GET['orderId'] ?? null;
?>

<?php
// Assuming you already have $orderId from the URL or session
$orderId = $_GET['orderId'] ?? null;
?>

<?php if ($orderId): ?>
<div class="row mt-5 align-items-center">
  <?php if (strtolower($order['status']) !== 'cancelled'): ?>
    <div class="col-12 col-md-4 d-flex justify-content-md-end justify-content-center mb-3 mb-md-0">
      <form id="cancelForm" class="d-inline-block">
        <input type="hidden" name="orderId" value="<?= htmlspecialchars($orderId) ?>">
        <label for="cancelCode">Enter Cancellation Code:</label><br>
        <input type="text" id="cancelCode" name="cancelCode" required style="padding:8px;margin-top:5px;">
        <button type="submit" name="cancelOrder" style="padding:8px 15px;background:#d9534f;color:#fff;border:none;border-radius:4px;cursor:pointer;">
          Cancel Order
        </button>
      </form>
    </div>
    <div class="col-12 col-md-4 d-flex justify-content-center mb-3 mb-md-0">
      <a href="index.php" class="btn btn-outline-success">Continue Shopping</a>
    </div>
    <div class="col-12 col-md-4"></div>
  <?php else: ?>
    <div class="col-12 col-md-4"></div>
    <div class="col-12 col-md-4 d-flex justify-content-center mb-3 mb-md-0">
      <a href="index.php" class="btn btn-outline-success">Continue Shopping</a>
    </div>
    <div class="col-12 col-md-4"></div>
  <?php endif; ?>
</div>
<?php endif; ?>
</div>
<script>
document.getElementById('cancelForm')?.addEventListener('submit', function(e) {
    e.preventDefault();

    let formData = new FormData(this);

    fetch('cancel_order.php', { // Separate PHP file for handling cancellation
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Update status text instantly
            let statusEl = document.getElementById('orderStatus');
            statusEl.textContent = 'Cancelled';
            statusEl.className = 'blink status-cancelled';

            alert('Order cancelled successfully!');
        } else {
            alert(data.message || 'Cancellation failed.');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error cancelling order.');
    });
});
</script>

<?php include "components/footer.php"; ?>
