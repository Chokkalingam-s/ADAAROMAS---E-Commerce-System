<?php
include('../config/db.php');

// Set your desired category, e.g. Perfume, Attar, Essence Oil, etc.
$category = 'Perfume'; // Or 'Perfume'/'Perfume-Men'/'Perfume-Women'/...
$pageTitle = "Perfume Collection";
// Prepare main query: Fetch all product+size+stock
// MAIN QUERY
$stmt = $conn->prepare("
  SELECT 
    p.productId, p.name, p.category, p.asp, p.mrp, p.image, p.rating, p.reviewCount,
    ps.size, ps.stockInHand
  FROM products p
  JOIN product_stock ps ON p.productId = ps.productId
  WHERE p.category = ?
  ORDER BY p.name ASC, ps.size ASC
");
$stmt->execute([$category]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// STEP 1: Group by name-category (lowest size used for display, but stock from all sizes)
$grouped = [];
$stockMap = [];

foreach ($rows as $row) {
  $key = strtolower(trim($row['name'])) . '_' . strtolower(trim($row['category']));
  
  if (!isset($grouped[$key])) {
    $grouped[$key] = [
      "title" => $row['name'],
      "productId" => (int)$row['productId'],
      "size" => $row['size'] ?? 'No size specified', // Use size if available, else default
      "category" => $row['category'],
      "image" => "../" . $row['image'],
      "price" => (int)$row['asp'],
      "mrp" => (int)$row['mrp'],
      "rating" => $row['rating'],
      "reviews" => $row['reviewCount'],
      "stock" => false, // default
      "date" => $row['created_at'] ?? '2024-01-01'
    ];
  }

  // track if at least one size is in stock
  if (!isset($stockMap[$key])) $stockMap[$key] = [];
  $stockMap[$key][] = (int)$row['stockInHand'];
}

// STEP 2: Mark stock status
foreach ($grouped as $key => &$item) {
  $hasStock = array_filter($stockMap[$key], fn($v) => $v > 0);
  $item['stock'] = count($hasStock) > 0;
}
unset($item); // break reference

$products = array_values($grouped);

// STOCK COUNTERS
$stockInHand = 0;
$stockOutOfHand = 0;
foreach ($products as $p) {
  if ($p['stock']) $stockInHand++;
  else $stockOutOfHand++;
}

// FILTER
$inStock = $_GET['inStock'] ?? '1';
$outOfStock = $_GET['outOfStock'] ?? '0';
$min = $_GET['min'] ?? 0;
$max = $_GET['max'] ?? 2000;

$filteredProducts = array_filter($products, function ($p) use ($inStock, $outOfStock, $min, $max) {
  $stockStatus = $p['stock'] ? 'in' : 'out';
  $stockMatch = ($stockStatus === 'in' && $inStock === '1') || ($stockStatus === 'out' && $outOfStock === '1');
  return $stockMatch && $p['price'] >= $min && $p['price'] <= $max;
});

include "../components/header.php";
?>

<?php include "render.php"; ?>

<?php include "../components/footer.php"; ?>