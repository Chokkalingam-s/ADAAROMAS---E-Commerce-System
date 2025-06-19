<?php
include('../config/db.php');
session_start();
if (!isset($_SESSION['admin_logged_in'])) die("Unauthorized");

function clean($string) {
  return strtolower(preg_replace('/[^A-Za-z0-9]/', '', $string));
}

$name = $_POST['productName'];
$category = $_POST['category'];
$size = $_POST['size'];
$stock = $_POST['stock'];
$cost = $_POST['costPrice'];
$msp = $_POST['msp'];
$margin = $_POST['margin'];
$asp = $_POST['asp'];
$mrp = $_POST['mrp'];
$desc = $_POST['description'];
$imagePath = '';

$check = $conn->prepare("SELECT * FROM products WHERE name = ? AND category = ?");
$check->execute([$name, $category]);
if ($check->rowCount() > 0) {
  die("<script>alert('Product already exists. Check inventory to manage stock.');window.location='add-product.php';</script>");
}

if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === UPLOAD_ERR_OK) {
  $tmp = $_FILES['productImage']['tmp_name'];
  $ext = pathinfo($_FILES['productImage']['name'], PATHINFO_EXTENSION);
  $fileName = clean($name) . clean($category) . '.' . $ext;
  $targetDir = "../assets/images/";
  $imagePath = $targetDir . $fileName;
  move_uploaded_file($tmp, $imagePath);
  $imagePath = substr($imagePath, 3); // remove ../
}

$insertProduct = $conn->prepare("INSERT INTO products (name, category, costPrice, margin, msp, asp, mrp, description, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$insertProduct->execute([$name, $category, $cost, $margin, $msp, $asp, $mrp, $desc, $imagePath]);
$productId = $conn->lastInsertId();

$insertStock = $conn->prepare("INSERT INTO product_stock (productId, size, stockInHand) VALUES (?, ?, ?)");
$insertStock->execute([$productId, $size, $stock]);

header("Location: add-product.php?success=1");
?>