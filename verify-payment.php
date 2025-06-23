<?php
require 'config/db.php';
require 'config/razorpay_api.php';
require_once __DIR__ . '/vendor/autoload.php';
use Razorpay\Api\Api;
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$cart = $input['cart'];
$orderId = $input['razorpay_order_id'];
$paymentId = $input['razorpay_payment_id'];
$signature = $input['razorpay_signature'];

$api = new Api($keyId, $keySecret);
try {
  $api->utility->verifyPaymentSignature([
    'razorpay_order_id'=>$orderId,
    'razorpay_payment_id'=>$paymentId,
    'razorpay_signature'=>$signature
  ]);
} catch (\Exception $e) {
  echo json_encode(['success'=>false]);
  exit;
}

// Store user (guest simplified)
$stmt = $conn->prepare("INSERT INTO users(name,phoneNo,email, state,district,address,city,pincode)
VALUES (?,?,?,?,?,?,?,?)");
$stmt->execute([
  'Guest', '0000000000', 'guest@adaaromas.com',
  'NA','NA','NA','NA','000000'
]);
$userId = $conn->lastInsertId();

// Store order
$stmt = $conn->prepare("INSERT INTO orders(userId,transactionId,billingAmount)
VALUES(?,?,?)");
$stmt->execute([$userId, $paymentId, array_sum(array_map(fn($p)=>$p['price']*$p['quantity'],$cart))]);
$newOrderId = $conn->lastInsertId();

// Insert order details
$stmt = $conn->prepare("INSERT INTO order_details(orderId,productId,quantity,size)
VALUES(?,?,?,?)");

foreach($cart as $item){
  $stmt->execute([$newOrderId, $item['productId'], $item['quantity'], $item['size']]);
}

echo json_encode(['success'=>true,'orderId'=>$newOrderId]);
