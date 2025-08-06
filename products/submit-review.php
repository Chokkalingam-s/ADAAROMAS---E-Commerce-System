<?php
include('../config/db.php');
header('Content-Type: application/json');

$rating = (int)$_POST['rating'];
$feedback = trim($_POST['feedback']);
$phoneNo = trim($_POST['phoneNo']);
$orderId = trim($_POST['orderId']);
$productId = (int)$_POST['productId'];

if (!$rating || !$feedback || !$phoneNo || !$orderId || !$productId) {
  echo json_encode(['success' => false, 'message' => 'All fields are required.']);
  exit;
}

// Step 1: Validate user
$userStmt = $conn->prepare("SELECT userId FROM users WHERE phoneNo = ?");
$userStmt->execute([$phoneNo]);
$user = $userStmt->fetch();
if (!$user) {
  echo json_encode(['success' => false, 'message' => 'This mobile number is not registered.']);
  exit;
}
$userId = $user['userId'];

// Step 2: Check order ownership
$orderCheck = $conn->prepare("SELECT * FROM orders WHERE orderId = ? AND userId = ? AND status IN ('Confirmed', 'Delivered')");
$orderCheck->execute([$orderId, $userId]);
$order = $orderCheck->fetch();
if (!$order) {
  echo json_encode(['success' => false, 'message' => 'Wrong Order ID. Check your email for the correct one.']);
  exit;
}

// Step 3: Check if product (variant) belongs to this order
$productCheck = $conn->prepare("
  SELECT COUNT(*) FROM order_details 
  WHERE orderId = ? AND productId = ?
");
$productCheck->execute([$orderId, $productId]);
$hasProduct = $productCheck->fetchColumn();
if (!$hasProduct) {
  echo json_encode(['success' => false, 'message' => 'This product is not part of the order.']);
  exit;
}

// Step 4: Prevent duplicate review
$duplicateCheck = $conn->prepare("SELECT COUNT(*) FROM reviews WHERE orderId = ? AND productId = ?");
$duplicateCheck->execute([$orderId, $productId]);
if ($duplicateCheck->fetchColumn()) {
  echo json_encode(['success' => false, 'message' => 'You have already reviewed this product for this order.']);
  exit;
}

// Step 5: Insert review
$ins = $conn->prepare("INSERT INTO reviews (productId, userId, orderId, feedback) VALUES (?, ?, ?, ?)");
$ins->execute([$productId, $userId, $orderId, $feedback]);

// Step 6: Update average rating & review count across variants
$nameCatStmt = $conn->prepare("SELECT name, category FROM products WHERE productId = ?");
$nameCatStmt->execute([$productId]);
$nameCat = $nameCatStmt->fetch();

$relatedStmt = $conn->prepare("SELECT productId FROM products WHERE name = ? AND category = ?");
$relatedStmt->execute([$nameCat['name'], $nameCat['category']]);
$relatedIds = $relatedStmt->fetchAll(PDO::FETCH_COLUMN);

foreach ($relatedIds as $pid) {
  $prodStmt = $conn->prepare("SELECT rating, noOfRatings FROM products WHERE productId = ?");
  $prodStmt->execute([$pid]);
  $prod = $prodStmt->fetch();

  $oldRating = $prod['rating'];
  $oldCount = $prod['noOfRatings'];
  $newCount = $oldCount + 1;
  $newRating = round((($oldRating * $oldCount) + $rating) / $newCount, 2);

  $update = $conn->prepare("UPDATE products SET rating = ?, noOfRatings = ?, reviewCount = reviewCount + 1 WHERE productId = ?");
  $update->execute([$newRating, $newCount, $pid]);
}

echo json_encode(['success' => true, 'message' => 'Thank you for your review!']);
