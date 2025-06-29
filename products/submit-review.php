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
// Step 1: Get all productIds with same name and category (same product across sizes)
$nameCatStmt = $conn->prepare("SELECT name, category FROM products WHERE productId = ?");
$nameCatStmt->execute([$productId]);
$nameCat = $nameCatStmt->fetch();

$relatedStmt = $conn->prepare("SELECT productId FROM products WHERE name = ? AND category = ?");
$relatedStmt->execute([$nameCat['name'], $nameCat['category']]);
$relatedIds = $relatedStmt->fetchAll(PDO::FETCH_COLUMN);

if (!$relatedIds) {
  echo json_encode(['success' => false, 'message' => 'Product reference error.']);
  exit;
}

// Step 2: Check if user bought any variant (any size) of this product
$placeholders = rtrim(str_repeat('?,', count($relatedIds)), ',');
$check = $conn->prepare("
  SELECT COUNT(*) FROM orders o 
  JOIN order_details od ON o.orderId = od.orderId
  WHERE o.userId = ? AND od.productId IN ($placeholders) AND o.status IN ('Confirmed', 'Delivered')
");
$check->execute(array_merge([$userId], $relatedIds));
$purchased = $check->fetchColumn();

if (!$purchased) {
  echo json_encode(['success' => false, 'message' => 'Oops! You haven\'t bought this product (any size) or it\'s not delivered yet.']);
  exit;
}


// Insert review
$ins = $conn->prepare("INSERT INTO reviews (productId, userId, feedback) VALUES (?, ?, ?)");
$ins->execute([$productId, $userId, $feedback]);

// Update average rating & review count
foreach ($relatedIds as $pid) {
    // Fetch current rating and count
    $prodStmt = $conn->prepare("SELECT rating, noOfRatings FROM products WHERE productId = ?");
    $prodStmt->execute([$pid]);
    $prod = $prodStmt->fetch();

    if (!$prod) continue;

    $oldRating = $prod['rating'];
    $oldCount = $prod['noOfRatings'];
    $newCount = $oldCount + 1;
    $newRating = round((($oldRating * $oldCount) + $rating) / $newCount, 2);

    // Update product entry
    $update = $conn->prepare("UPDATE products SET rating = ?, noOfRatings = ?, reviewCount = reviewCount + 1 WHERE productId = ?");
    $update->execute([$newRating, $newCount, $pid]);
}
echo json_encode(['success' => true, 'message' => 'Thank you for your review!']);
