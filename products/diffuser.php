<?php
include('../config/db.php');

// Set your desired category, e.g. Perfume, Attar, Essence Oil, etc.
$category = 'Diffuser'; // Or 'Perfume'/'Perfume-Men'/'Perfume-Women'/...
$pageTitle = "Diffuser Collection";
$stmt = $conn->prepare("
  SELECT 
    p.productId, p.name, p.category, p.asp, p.mrp, p.image,p.backImage, p.rating, p.reviewCount,
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
  // Temporarily store all sizes
  $grouped[$key] = [
    "title" => $row['name'],
    "productId" => (int)$row['productId'],
    "category" => $row['category'],
    "image" => "../" . $row['image'],
    "backImage" => isset($row['backImage']) ? "../" . $row['backImage'] : null,
    "rating" => $row['rating'],
    "reviews" => $row['reviewCount'],
    "date" => $row['created_at'] ?? '2024-01-01',
    "stockOptions" => [] // Temp hold all size options for selection
  ];
}

// Store all size/stock options for this product
$grouped[$key]["stockOptions"][] = [
  "size" => $row['size'],
  "asp" => (int)$row['asp'],
  "mrp" => (int)$row['mrp'],
  "stock" => (int)$row['stockInHand']
];


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



foreach ($grouped as $key => &$item) {
  $inStockSizes = array_filter($item['stockOptions'], fn($s) => $s['stock'] > 0);

  if (count($inStockSizes) > 0) {
    // Get the lowest in-stock size (sorted by size, assuming size is numeric like 50, 100)
    usort($inStockSizes, fn($a, $b) => $a['size'] <=> $b['size']);
    $best = $inStockSizes[0];

    $item['stock'] = true;
    $item['size'] = $best['size'];
    $item['price'] = $best['asp'];
    $item['mrp'] = $best['mrp'];
  } else {
    // No in-stock sizes, fallback to lowest size
    usort($item['stockOptions'], fn($a, $b) => $a['size'] <=> $b['size']);
    $fallback = $item['stockOptions'][0];

    $item['stock'] = false;
    $item['size'] = $fallback['size'];
    $item['price'] = $fallback['asp'];
    $item['mrp'] = $fallback['mrp'];
  }

  unset($item['stockOptions']); // Cleanup temp field
}
unset($item);

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
$max = $_GET['max'] ?? 5000;

$filteredProducts = array_filter($products, function ($p) use ($inStock, $outOfStock, $min, $max) {
  $stockStatus = $p['stock'] ? 'in' : 'out';
  $stockMatch = ($stockStatus === 'in' && $inStock === '1') || ($stockStatus === 'out' && $outOfStock === '1');
  return $stockMatch && $p['price'] >= $min && $p['price'] <= $max;
});

include "../components/header.php";
?>

<?php include "render.php"; ?>

<?php include "../components/footer.php"; ?>