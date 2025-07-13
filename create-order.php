<?php
// create-order.php
require 'config/db.php';
require 'config/razorpay_api.php';
require_once __DIR__ . '/vendor/autoload.php';
use Razorpay\Api\Api;
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['cart']) || !isset($input['user'])) {
  http_response_code(400);
  echo json_encode(['error' => 'Invalid input received']);
  exit;
}

$cart = $input['cart'];
$userData = $input['user'];
$total = (isset($input['finalAmount']) && is_numeric($input['finalAmount']) && $input['finalAmount'] > 0)
    ? (float)$input['finalAmount']
    : array_sum(array_map(fn($p)=>$p['asp']*$p['quantity'],$cart));


if ($total <= 0) exit(json_encode(['error'=>"Cart empty"]));

$fullname = trim($userData['firstName'].' '.$userData['lastName']);
$address = trim($userData['addressLine1'].' '.$userData['addressLine2']);
$stmt = $conn->prepare("INSERT INTO users (name, phoneNo, email, state, district, address, city, pincode)
VALUES (?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE userId=LAST_INSERT_ID(userId)");
$stmt->execute([$fullname, $userData['phone'], $userData['email'], $userData['state'], $userData['district'], $address, $userData['city'], $userData['pincode']]);
$userId = $conn->lastInsertId();

$api = new Api($keyId, $keySecret);
$order = $api->order->create(['amount'=>$total*100,'currency'=>'INR','receipt'=>'order_'.time()]);

echo json_encode(['id'=>$order->id,'amount'=>$order->amount,'currency'=>$order->currency,'userId'=>$userId]);
