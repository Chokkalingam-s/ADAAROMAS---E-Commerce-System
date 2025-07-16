<?php
include('../config/db.php');
session_start();
date_default_timezone_set('Asia/Kolkata');
if (!isset($_SESSION['admin_logged_in'])) header('Location: index.php');

$orderId = $_POST['orderId'];
$replaceQty = $_POST['replaceQty'];
$replacementId = (int)('0780' . $orderId); // e.g., 07803


// Check if already replaced
$check = $conn->prepare("SELECT COUNT(*) FROM orders WHERE orderId = ?");
$check->execute([$replacementId]);
if ($check->fetchColumn() > 0) {
  die("Replacement already exists for Order #$orderId");
}

// Fetch original order
$order = $conn->prepare("SELECT * FROM orders WHERE orderId = ?");
$order->execute([$orderId]);
$orderData = $order->fetch(PDO::FETCH_ASSOC);

// Clone order into new replacement
$conn->prepare("
  INSERT INTO orders (orderId, userId, status, transactionId, orderDate, billingAmount, TotalASP, GST, PROFIT, LOSS)
  VALUES (?, ?, 'Replaced', ?, NOW(), 0, 0, 0, 0, 0)
")->execute([
  $replacementId,
  $orderData['userId'],
  $orderData['transactionId']
]);

$totalASP = $gst = $profit = $loss = 0;

foreach ($replaceQty as $productId => $sizes) {
  foreach ($sizes as $size => $qty) {
    if ($qty > 0) {
      // Fetch ASP
      $stmt = $conn->prepare("SELECT asp FROM products WHERE productId = ?");
      $stmt->execute([$productId]);
      $asp = $stmt->fetchColumn();

      $lineTotal = $asp * $qty;
      $totalASP += $lineTotal;
      $loss += $lineTotal;

      // Insert into order_details
      $conn->prepare("
        INSERT INTO order_details (orderId, productId, size, quantity)
        VALUES (?, ?, ?, ?)
      ")->execute([$replacementId, $productId, $size, $qty]);

      // Update stock
      $conn->prepare("
        UPDATE product_stock 
        SET stockInHand = stockInHand - ? 
        WHERE productId = ?
      ")->execute([$qty, $productId]);
    }
  }
}

// Update replacement order totals
$conn->prepare("
  UPDATE orders 
  SET TotalASP = ?, GST = ?, billingAmount = 0, LOSS = ?, PROFIT = 0 
  WHERE orderId = ?
")->execute([$totalASP, $gst, $loss, $replacementId]);

// Reduce PROFIT from original order and update LOSS
$conn->prepare("UPDATE orders SET PROFIT = PROFIT - ?, LOSS = LOSS + ? WHERE orderId = ?")
     ->execute([$loss, $loss, $orderId]);

header("Location: orders.php");
exit;
