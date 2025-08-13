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

  <?php
require 'vendor/autoload.php'; // For PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['cancelOrder'])) {
    $orderId = intval($_POST['orderId']);
    $enteredCode = strtoupper(trim($_POST['cancelCode']));

    // Fetch order and user details
    $stmt = $conn->prepare("
        SELECT o.*, u.name AS userName, u.email AS userEmail, u.phoneNo, u.address, u.city, u.state, u.pincode
        FROM orders o
        JOIN users u ON o.userId = u.userId
        WHERE o.orderId = ?
    ");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo "<p style='color:red;'>Order not found.</p>";
    } elseif ($order['cancelCode'] !== $enteredCode) {
        echo "<p style='color:red;'>Invalid cancellation code.</p>";
    } else {
        // Update order status
        $conn->prepare("UPDATE orders SET status = 'Cancelled' WHERE orderId = ?")
             ->execute([$orderId]);

        // Fetch cart items
$stmtOD = $conn->prepare("
    SELECT od.*, p.name, p.asp 
    FROM order_details od
    JOIN products p ON od.productId = p.productId
    WHERE od.orderId = ?
");
$stmtOD->execute([$orderId]);
$items = $stmtOD->fetchAll(PDO::FETCH_ASSOC);


        // Build product list HTML
$productListHTML = "<table border='1' cellpadding='6' cellspacing='0' style='border-collapse:collapse;width:100%;'>
    <tr><th>Product</th><th>Qty</th><th>Size</th><th>Price</th></tr>";

foreach ($items as $item) {
    $productListHTML .= "<tr>
        <td>{$item['name']}</td>
        <td>{$item['quantity']}</td>
        <td>{$item['size']}</td>
        <td>₹" . number_format($item['asp'] * $item['quantity'], 2) . "</td>
    </tr>";
}
$productListHTML .= "</table>";


        // Send cancellation email
        $mail = new PHPMailer(true);
        $config = require 'config/email_config.php';
        
        try {
            $mail->isSMTP();
            $mail->Host = $config['smtp_host'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['smtp_username'];
            $mail->Password = $config['smtp_password'];
            $mail->SMTPSecure = $config['smtp_secure'];
            $mail->Port = $config['smtp_port'];

            $mail->setFrom($config['from_email'], $config['from_name']);
            $mail->addAddress('Adaaromas@rudraksha.org.in');
            $mail->addBCC('chokka7878@gmail.com');
            $mail->addReplyTo($order['userEmail'], $order['userName']);

            $mail->isHTML(true);
            $mail->Subject = "Cancellation of Order - ID: {$orderId}";

            $mail->Body = "
                <h2>Order Cancelled</h2>
                <p><strong>Order ID:</strong> {$orderId}</p>
                <p><strong>Transaction ID:</strong> {$order['transactionId']}</p>
                <p><strong>User:</strong> {$order['userName']} ({$order['userEmail']}, {$order['phoneNo']})</p>
                <p><strong>Address:</strong> {$order['address']}, {$order['city']}, {$order['state']} - {$order['pincode']}</p>
                <p><strong>Order Date:</strong> {$order['orderDate']}</p>
                <p><strong>Billing Amount:</strong> ₹" . number_format($order['billingAmount'], 2) . "</p>
                <h3>Products:</h3>
                {$productListHTML}
                <p style='color:red;font-weight:bold;'>This order has been cancelled by the customer.</p>
            ";

            $mail->send();
            echo "<p style='color:green;'>Order cancelled and email notification sent to the company.</p>";
        } catch (Exception $e) {
            echo "<p style='color:red;'>Order cancelled, but email could not be sent: {$mail->ErrorInfo}</p>";
        }
    }
}
?>

<?php
// Assuming you already have $orderId from the URL or session
$orderId = $_GET['orderId'] ?? null;
?>

<?php if ($orderId): ?>
<div style="margin-top:20px;">
  <h3>Cancel Your Order</h3>
  <form method="POST" action="">
    <input type="hidden" name="orderId" value="<?= htmlspecialchars($orderId) ?>">
    <label for="cancelCode">Enter Cancellation Code:</label><br>
    <input type="text" id="cancelCode" name="cancelCode" required style="padding:8px;margin-top:5px;">
    <button type="submit" name="cancelOrder" style="padding:8px 15px;background:#d9534f;color:#fff;border:none;border-radius:4px;cursor:pointer;">
      Cancel Order
    </button>
  </form>
</div>
<?php endif; ?>

  <div class="text-center mt-5">
    <a href="index.php" class="btn btn-outline-success">Continue Shopping</a>
  </div>
</div>

<?php include "components/footer.php"; ?>
