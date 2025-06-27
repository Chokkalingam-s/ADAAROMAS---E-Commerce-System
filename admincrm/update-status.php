<?php
include('../config/db.php');
session_start();
date_default_timezone_set('Asia/Kolkata');
if (!isset($_SESSION['admin_logged_in'])) header('Location: index.php');

$orderId = $_POST['orderId'];
$newStatus = $_POST['newStatus'];

$conn->prepare("UPDATE orders SET status = ? WHERE orderId = ?")->execute([$newStatus, $orderId]);

// Reduce stock if status changed to Confirmed
if ($newStatus === 'Confirmed') {
  $items = $conn->prepare("SELECT productId, size, quantity FROM order_details WHERE orderId = ?");
  $items->execute([$orderId]);
  foreach ($items->fetchAll() as $item) {
    $update = $conn->prepare("UPDATE product_stock SET stockInHand = stockInHand - ? WHERE productId = ? AND size = ?");
    $update->execute([$item['quantity'], $item['productId'], $item['size']]);
  }
}

header("Location: orders.php");
exit;
