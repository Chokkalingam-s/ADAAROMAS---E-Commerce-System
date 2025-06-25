<?php
require 'config/db.php';
require 'config/razorpay_api.php';
require_once __DIR__ . '/vendor/autoload.php';
use Razorpay\Api\Api;
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$cart = $data['cart'];
$userId = $data['userId'];
$orderId = $data['razorpay_order_id'];
$paymentId = $data['razorpay_payment_id'];
$signature = $data['razorpay_signature'];

$api = new Api($keyId, $keySecret);
try {
  $api->utility->verifyPaymentSignature([
    'razorpay_order_id' => $orderId,
    'razorpay_payment_id' => $paymentId,
    'razorpay_signature' => $signature
  ]);
} catch (Exception $e) {
  echo json_encode(['success'=>false]); exit;
}

// Store order
$total = array_sum(array_map(fn($p)=>$p['price']*$p['quantity'], $cart));
$stmt = $conn->prepare("INSERT INTO orders (userId, transactionId, billingAmount) VALUES (?, ?, ?)");
$stmt->execute([$userId, $paymentId, $total]);
$newOrderId = $conn->lastInsertId();

// For each cart item: store details & reduce stock
$stmtOD = $conn->prepare("INSERT INTO order_details (orderId, productId, quantity, size) VALUES (?, ?, ?, ?)");
foreach ($cart as $item) {
  $stmtOD->execute([$newOrderId, $item['productId'], $item['quantity'], $item['size']]);
}

echo json_encode(['success'=>true, 'orderId'=>$newOrderId]);