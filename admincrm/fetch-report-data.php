<?php
require '../config/db.php';
header('Content-Type: application/json');

$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// 1. Stock in Hand (costPrice × stockInHand)
$stockStmt = $conn->query("
  SELECT SUM(p.costPrice * ps.stockInHand) AS stockInHand
  FROM products p
  JOIN product_stock ps ON p.productId = ps.productId
");
$stockInHand = round($stockStmt->fetchColumn() ?: 0);

// 2. Total Revenue (from all billingAmounts)
$revenueStmt = $conn->query("SELECT SUM(billingAmount) AS totalRevenue FROM orders WHERE status != 'Cancelled'");
$totalRevenue = round($revenueStmt->fetchColumn() ?: 0);

// 3. Total Cost of Sold Products
$costStmt = $conn->query("
  SELECT SUM(od.quantity * p.costPrice) AS totalCost
  FROM order_details od
  JOIN orders o ON od.orderId = o.orderId
  JOIN products p ON od.productId = p.productId
  WHERE o.status != 'Cancelled'
");
$totalCost = round($costStmt->fetchColumn() ?: 0);

// 4. Profit = revenue - cost
$profit = $totalRevenue - $totalCost;

// 5. Yearly Sales (selected year)
$yearlyStmt = $conn->prepare("SELECT SUM(billingAmount) FROM orders WHERE YEAR(orderDate) = ? AND status != 'Cancelled'");
$yearlyStmt->execute([$year]);
$yearlySales = round($yearlyStmt->fetchColumn() ?: 0);

// 6. Category-wise Sales
$catStmt = $conn->query("
  SELECT p.category, SUM(od.quantity * p.mrp) AS total
  FROM order_details od
  JOIN orders o ON od.orderId = o.orderId
  JOIN products p ON od.productId = p.productId
  WHERE o.status != 'Cancelled'
  GROUP BY p.category
");
$categorySales = [];
while ($row = $catStmt->fetch(PDO::FETCH_ASSOC)) {
  $categorySales[$row['category']] = round($row['total']);
}

// 7. Product-wise Sales
$productStmt = $conn->query("
  SELECT p.name, SUM(od.quantity * p.mrp) AS total
  FROM order_details od
  JOIN orders o ON od.orderId = o.orderId
  JOIN products p ON od.productId = p.productId
  WHERE o.status != 'Cancelled'
  GROUP BY p.productId
  ORDER BY total DESC
  LIMIT 8
");
$productSales = [];
while ($row = $productStmt->fetch(PDO::FETCH_ASSOC)) {
  $productSales[$row['name']] = round($row['total']);
}

// 8. Monthly Sales (bar chart)
$monthlyStmt = $conn->prepare("
  SELECT MONTH(orderDate) AS m, SUM(billingAmount) AS total
  FROM orders
  WHERE YEAR(orderDate) = ? AND status != 'Cancelled'
  GROUP BY m
");
$monthlyStmt->execute([$year]);
$monthlySales = array_fill(1, 12, 0);
while ($row = $monthlyStmt->fetch(PDO::FETCH_ASSOC)) {
  $monthlySales[intval($row['m'])] = round($row['total']);
}

// Format months for x-axis
$monthlySalesFormatted = [];
$monthNames = [1=>'Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
foreach ($monthlySales as $m => $value) {
  $monthlySalesFormatted[$monthNames[$m]] = $value;
}

// Final Response
echo json_encode([
  'stockInHand' => $stockInHand,
  'totalRevenue' => $totalRevenue,
  'totalProfit' => $profit,
  'yearlySales' => $yearlySales,
  'categorySales' => $categorySales,
  'productSales' => $productSales,
  'monthlySales' => $monthlySalesFormatted
]);
