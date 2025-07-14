<?php
include('../config/db.php'); session_start();
if(!isset($_SESSION['admin_logged_in'])) exit;
$data = json_decode(file_get_contents('php://input'), true);

// Get existing stockInHand and damagestock
$stmt = $conn->prepare("SELECT stockInHand, damagestock FROM product_stock WHERE stockId = ?");
$stmt->execute([$data['stockId']]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$existingStockInHand = $row['stockInHand'];
$existingDamage = $row['damagestock'];
$inputDamage = $data['damagestock'];
$inputStockInHand = $data['stockInHand'];

// If damage changed, recalculate stockInHand
if ($inputDamage != $existingDamage) {
    $damageDiff = $inputDamage - $existingDamage;
    $newStockInHand = $existingStockInHand - $damageDiff;
    if ($newStockInHand < 0) $newStockInHand = 0;
} else {
    // If only stock changed, use the input value
    $newStockInHand = $inputStockInHand;
}
// Prevent negative stock
if ($newStockInHand < 0) $newStockInHand = 0;

$stmt = $conn->prepare("
  UPDATE product_stock ps
  JOIN products p ON ps.productId = p.productId
  SET
    p.costPrice = ?, p.margin = ?, p.msp=?, p.asp = ?, p.mrp = ?,
    ps.stockInHand = ?, p.revenue = ? , ps.damagestock = ?
  WHERE ps.stockId = ?
");
$stmt->execute([
  $data['costPrice'], $data['margin'], $data['msp'], $data['asp'], $data['mrp'],
  $newStockInHand, $data['revenue'], $inputDamage, $data['stockId']
]);
echo json_encode([
    'success' => true,
    'stockInHand' => $newStockInHand, // send updated stock
    'damagestock' => $inputDamage
]);