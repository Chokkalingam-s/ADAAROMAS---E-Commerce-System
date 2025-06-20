<?php
include "config/db.php";
date_default_timezone_set('Asia/Kolkata');

header('Content-Type: application/json');
$input = json_decode(file_get_contents("php://input"), true);
$code = strtoupper(trim($input['code'] ?? ''));

if (!$code) exit(json_encode(['success' => false, 'message' => 'Coupon code required.']));

$stmt = $conn->prepare("SELECT * FROM coupons WHERE couponCode = ?");
$stmt->execute([$code]);
$coupon = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$coupon) {
  echo json_encode(['success' => false, 'message' => 'Invalid coupon code.']);
} elseif (strtotime($coupon['expiryTime']) < time()) {
  echo json_encode(['success' => false, 'message' => 'Coupon expired.']);
} else {
  echo json_encode([
    'success' => true,
    'percentage' => (int)$coupon['percentage'],
    'flatAmount' => (float)$coupon['flatAmount']
  ]);
}
?>
