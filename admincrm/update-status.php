<?php
include('../config/db.php');
session_start();
date_default_timezone_set('Asia/Kolkata');
if (!isset($_SESSION['admin_logged_in'])) header('Location: index.php');

$orderId = $_POST['orderId'];
$newStatus = $_POST['newStatus'];
$currentStatus = $_POST['currentStatus'];

$conn->prepare("UPDATE orders SET status = ? WHERE orderId = ?")->execute([$newStatus, $orderId]);

// Only reduce stock if transitioning from Pending → Confirmed
if ($currentStatus === 'Pending' && $newStatus === 'Confirmed') {
  $items = $conn->prepare("SELECT productId, size, quantity FROM order_details WHERE orderId = ?");
  $items->execute([$orderId]);
  foreach ($items->fetchAll() as $item) {
    $conn->prepare("
      UPDATE product_stock 
      SET stockInHand = stockInHand - ? 
      WHERE productId = ? 
    ")->execute([$item['quantity'], $item['productId']]);
  }
}

header("Location: orders.php");
exit;
