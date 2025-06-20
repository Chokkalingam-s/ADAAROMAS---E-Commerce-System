<?php 
include('../config/db.php'); 
session_start();
if (!isset($_SESSION['admin_logged_in'])) exit;

// Parse input
$data = json_decode(file_get_contents('php://input'), true);
$stockId = $data['stockId'];

// Step 1: Get productId from stockId
$stmt = $conn->prepare("SELECT productId FROM product_stock WHERE stockId = ?");
$stmt->execute([$stockId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
  echo json_encode(['success' => false, 'message' => 'Stock ID not found']);
  exit;
}

$productId = $product['productId'];
$productName = $product['name'];

// Step 2: Delete this stockId
$stmt = $conn->prepare("DELETE FROM product_stock WHERE stockId = ?");
$stmt->execute([$stockId]);

 $stmt = $conn->prepare("DELETE FROM products WHERE productId = ?");
  $stmt->execute([$productId]);

// Step 3: Check if other sizes exist for this product
$stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE name = ?");
$stmt->execute([$productName]);
$remainingCount = $stmt->fetchColumn();

// Step 4: If no more stock variants exist, delete from products + remove image
if ($remainingCount == 0) {
  // Fetch image path
  $stmt = $conn->prepare("SELECT image FROM products WHERE productId = ?");
  $stmt->execute([$productId]);
  $imgData = $stmt->fetch(PDO::FETCH_ASSOC);

  // Delete product
  $stmt = $conn->prepare("DELETE FROM products WHERE productId = ?");
  $stmt->execute([$productId]);

  // Delete image from file system
  if ($imgData && file_exists("../" . $imgData['image'])) {
    unlink("../" . $imgData['image']);
  }
}

echo json_encode(['success' => true]);
