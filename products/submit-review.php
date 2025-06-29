<?php
include('../config/db.php');
header('Content-Type: application/json');

$rating = (int)$_POST['rating'];
$feedback = trim($_POST['feedback']);
$name = trim($_POST['name']);
$phoneNo = trim($_POST['phoneNo']);
$productId = (int)$_POST['productId'];

if (!$rating || !$feedback || !$name || !$phoneNo || !$productId) {
  echo json_encode(['success' => false, 'message' => 'All fields required']);
  exit;
}

// Check if user exists
$userStmt = $conn->prepare("SELECT userId FROM users WHERE phoneNo = ?");
$userStmt->execute([$phoneNo]);
$user = $userStmt->fetch();

if (!$user) {
  echo json_encode(['success' => false, 'message' => 'Mobile number not registered.']);
  exit;
}

$userId = $user['userId'];

// Check if this user purchased this product
$check = $conn->prepare("
  SELECT COUNT(*) FROM orders o 
  JOIN order_details od ON o.orderId = od.orderId
  WHERE o.userId = ? AND od.productId = ? AND o.status IN ('Confirmed', 'Delivered')
");
$check->execute([$userId, $productId]);
$purchased = $check->fetchColumn();

if (!$purchased) {
  echo json_encode(['success' => false, 'message' => 'Oops! You haven\'t bought this product or it\'s not delivered yet.']);
  exit;
}

// Insert review
$ins = $conn->prepare("INSERT INTO reviews (productId, userId, feedback) VALUES (?, ?, ?)");
$ins->execute([$productId, $userId, $feedback]);

// Update average rating & review count
$prodStmt = $conn->prepare("SELECT rating, noOfRatings FROM products WHERE productId = ?");
$prodStmt->execute([$productId]);
$prod = $prodStmt->fetch();

$oldRating = $prod['rating'];
$oldCount = $prod['noOfRatings'];

$newCount = $oldCount + 1;
$newRating = round((($oldRating * $oldCount) + $rating) / $newCount, 2);

$update = $conn->prepare("UPDATE products SET rating = ?, noOfRatings = ?, reviewCount = reviewCount + 1 WHERE productId = ?");
$update->execute([$newRating, $newCount, $productId]);

echo json_encode(['success' => true, 'message' => 'Thank you for your review!']);
