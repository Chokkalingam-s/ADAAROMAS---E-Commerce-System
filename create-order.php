<?php
// create-order.php
require 'config/db.php';
require 'config/razorpay_api.php'; // stores $keyId, $keySecret
require_once __DIR__ . '/vendor/autoload.php';
use Razorpay\Api\Api;
header('Content-Type: application/json');

$cart = json_decode(file_get_contents('php://input'), true)['cart'];
$total = array_sum(array_map(fn($p)=>$p['price']*$p['quantity'], $cart));
if ($total <= 0) exit(json_encode(['error'=>'Cart empty']));

$api = new Api($keyId, $keySecret);
$order = $api->order->create([
    'amount' => $total * 100,
    'currency'=>'INR',
    'receipt'=>'order_'.time()
]);

echo json_encode([
    'id'=>$order->id,
    'amount'=>$order->amount,
    'currency'=>$order->currency
]);
