<?php
include('../config/db.php');
session_start();
if (!isset($_SESSION['admin_logged_in'])) die("Unauthorized");

$name = $_POST['productName'];
$category = $_POST['category'];
$size = $_POST['size'];
$cost = $_POST['costPrice'];
$msp = $_POST['msp'];
$margin = $_POST['margin'];
$asp = $_POST['asp'];
$mrp = $_POST['mrp'];
$desc = $_POST['description'];

// Check if product already exists
$stmt = $conn->prepare("SELECT productId FROM products WHERE name = ? AND category = ?");
$stmt->execute([$name, $category]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
  $stmt = $conn->prepare("INSERT INTO products (name, category, costPrice, margin, msp, asp, mrp, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->execute([$name, $category, $cost, $margin, $msp, $asp, $mrp, $desc]);
  $productId = $conn->lastInsertId();
} else {
  $productId = $product['productId'];
}

// Insert stock
$stmt = $conn->prepare("INSERT INTO product_stock (productId, size, stockInHand) VALUES (?, ?, 0)");
$stmt->execute([$productId, $size]);

header("Location: add-product.php?success=1");
?>
