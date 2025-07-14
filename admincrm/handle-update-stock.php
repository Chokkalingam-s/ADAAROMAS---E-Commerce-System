<?php
include('../config/db.php'); session_start();
if(!isset($_SESSION['admin_logged_in'])) exit;
$data = json_decode(file_get_contents('php://input'), true);

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
  $data['stockInHand'], $data['revenue'], $data['damagestock'], $data['stockId']
]);

echo json_encode(['success'=>true]);
