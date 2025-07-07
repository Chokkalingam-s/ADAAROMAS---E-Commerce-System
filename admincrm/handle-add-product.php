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
$gender = ($_POST['category'] === 'Perfume') ? $_POST['gender'] : null;
$revenue = $_POST['revenue'];

$imagePath = '';

$imageUploaded = false;
if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === UPLOAD_ERR_OK) {
  $tmp = $_FILES['productImage']['tmp_name'];
  $ext = pathinfo($_FILES['productImage']['name'], PATHINFO_EXTENSION);
  $fileName = clean($name) . clean($category) .  '.' . $ext;
  $targetDir = "../assets/images/";
  $imagePath = $targetDir . $fileName;
  move_uploaded_file($tmp, $imagePath);
  $imagePath = substr($imagePath, 3); // remove ../ for DB
  $imageUploaded = true;
}

// Check for existing same name + category + size combo
$check = $conn->prepare("
  SELECT ps.size FROM products p
  JOIN product_stock ps ON p.productId = ps.productId
  WHERE p.name = ? AND p.category = ? AND ps.size = ?
");
$check->execute([$name, $category, $size]);

if ($check->rowCount() > 0) {
  echo "<script>alert('Product with this name, category, and size already exists.');window.location='add-product.php';</script>";
  exit;
}

// Create a new product entry (even if same name & category) for new size and price
$insertProduct = $conn->prepare("
  INSERT INTO products (name, category, gender, costPrice, margin, msp, asp, mrp, description, image , revenue)
  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$insertProduct->execute([$name, $category, $gender, $cost, $margin, $msp, $asp, $mrp, $desc, $imagePath, $revenue]);

$productId = $conn->lastInsertId();

// Create new stock
$insertStock = $conn->prepare("INSERT INTO product_stock (productId, size, stockInHand) VALUES (?, ?, ?)");
$insertStock->execute([$productId, $size, $stock]);

echo "<script>alert('New product with specified size added successfully.');window.location='add-product.php';</script>";
exit;
