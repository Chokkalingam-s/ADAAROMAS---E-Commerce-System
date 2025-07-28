<?php
include('../config/db.php');
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
  header('Location: index.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $orderId = $_POST['orderId'];
  $remarks = trim($_POST['remarks']);

  $stmt = $conn->prepare("UPDATE orders SET remarks = ? WHERE orderId = ?");
  $stmt->execute([$remarks, $orderId]);

  header('Location: orders.php');
  exit;
}
?>
