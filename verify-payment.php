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

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
  $mail->isSMTP();
  $mail->Host = 'smtp.gmail.com'; // Or your mail server
  $mail->SMTPAuth = true;
  $mail->Username = 'your_email@gmail.com'; // Replace with your sender email
  $mail->Password = 'your_app_password';   // Use App Password if Gmail
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
  $mail->Port = 587;

  $mail->setFrom('your_email@gmail.com', 'ADA Aromas');
  $mail->addAddress($input['email'], $input['name']);  // Dynamic user email/name

  $mail->isHTML(true);
  $mail->Subject = 'Order Confirmation - ADA Aromas';
  
  $productList = '';
  foreach ($cart as $item) {
    $productList .= "<li>{$item['title']} - Qty: {$item['quantity']} - ₹{$item['price']} x {$item['quantity']}</li>";
  }

  $mail->Body = "
    <h2>Thank you for your purchase, {$input['name']}!</h2>
    <p>Your order (ID: <strong>#{$newOrderId}</strong>) has been successfully placed and paid.</p>
    <h4>Order Summary:</h4>
    <ul>{$productList}</ul>
    <p><strong>Total Paid:</strong> ₹" . array_sum(array_map(fn($p) => $p['price'] * $p['quantity'], $cart)) . "</p>
    <p>Transaction ID: {$paymentId}</p>
    <br><p>You’ll receive shipping updates soon!</p>
    <p>- Team ADA Aromas</p>
  ";

  $mail->send();
} catch (Exception $e) {
  error_log("Email not sent. Error: {$mail->ErrorInfo}");
}
