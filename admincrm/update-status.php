<?php 
include('../config/db.php');
session_start();
date_default_timezone_set('Asia/Kolkata');
if (!isset($_SESSION['admin_logged_in'])) header('Location: index.php');

$orderId = $_POST['orderId'];
$newStatus = $_POST['newStatus'];
$currentStatus = $_POST['currentStatus'];

// Update the order status
$conn->prepare("UPDATE orders SET status = ? WHERE orderId = ?")->execute([$newStatus, $orderId]);

// Stock Management Logic
if ($currentStatus === 'Pending' && $newStatus === 'Confirmed') {
  // Reduce stock on confirmation
  $items = $conn->prepare("SELECT productId, quantity FROM order_details WHERE orderId = ?");
  $items->execute([$orderId]);
  foreach ($items->fetchAll() as $item) {
    $conn->prepare("
      UPDATE product_stock 
      SET stockInHand = stockInHand - ? 
      WHERE productId = ?
    ")->execute([$item['quantity'], $item['productId']]);
  }
}

if ($currentStatus === 'Confirmed' && $newStatus === 'Cancelled') {
  // Restore stock on cancellation
  $items = $conn->prepare("SELECT productId, quantity FROM order_details WHERE orderId = ?");
  $items->execute([$orderId]);
  foreach ($items->fetchAll() as $item) {
    $conn->prepare("
      UPDATE product_stock 
      SET stockInHand = stockInHand + ? 
      WHERE productId = ?
    ")->execute([$item['quantity'], $item['productId']]);
  }
}

header("Location: orders.php");
exit;

