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

if (!empty($data['adminMode'])) {
    // Admin order
    $paymentId = "ADMIN-" . strtoupper($data['adminMode']);
} elseif ($orderId === "CashOnDelivery") {
    // ✅ COD flow
    $paymentId = "CashOnDelivery";
} else {
    // Razorpay verification as before
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
}


if (!empty($data['couponCode'])) {
    $couponCode = $data['couponCode'];
    $couponCode = strtoupper($data['couponCode']);

    $stmt = $conn->prepare("UPDATE coupons SET availability = 1 WHERE couponCode = ? AND availability = 0");
    $stmt->execute([$couponCode]);
    
    
}

// Store order
$total = $data['finalAmount'];

$totalASP = 0;
$totalProfit = $total;
foreach ($cart as $item) {
    $productId = $item['productId'];
    $quantity = $item['quantity'];

    $stmtP = $conn->prepare("SELECT asp, msp, revenue FROM products WHERE productId = ?");
    $stmtP->execute([$productId]);
    $product = $stmtP->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        $totalASP += $product['asp'] * $quantity;
        $totalProfit = $totalProfit - $product['msp'] * $quantity;
    }
}

if (!empty($data['adminMode']) && $data['adminMode'] === 'admin_gift') {
    $total = $totalASP; // billingAmount should be totalASP for admin_gift
    $totalProfit=0;
}


$gst = (!empty($data['adminMode']) && $data['adminMode'] === 'admin_gift') ? 0 : round($totalASP * 0.18);
$totalProfit = $totalProfit - $gst;
$loss = 0;
$cancelCode = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 8));
$stmt = $conn->prepare("
    INSERT INTO orders (userId, transactionId, billingAmount, TotalASP, GST, PROFIT, LOSS, cancelCode) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
"); 
$stmt->execute([$userId, $paymentId, $total, $totalASP, $gst, $totalProfit, $loss, $cancelCode]);

$newOrderId = $conn->lastInsertId();

// For each cart item: store details & reduce stock
$stmtOD = $conn->prepare("INSERT INTO order_details (orderId, productId, quantity, size) VALUES (?, ?, ?, ?)");
foreach ($cart as $item) {
  $stmtOD->execute([$newOrderId, $item['productId'], $item['quantity'], $item['size']]);
}




echo json_encode(['success'=>true, 'orderId'=>$newOrderId]);
flush(); // ✅ Push output to client
if (function_exists('fastcgi_finish_request')) {
    fastcgi_finish_request(); // ✅ Let PHP continue mailing in background
}


// Send confirmation email
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
$emailConfig = require 'config/email_config.php';
$mail = new PHPMailer(true);

try {
  $mail->isSMTP();
  $mail->Host = $emailConfig['smtp_host'];
  $mail->SMTPAuth = true;
  $mail->Username = $emailConfig['smtp_username'];
  $mail->Password = $emailConfig['smtp_password'];
  $mail->SMTPSecure = $emailConfig['smtp_secure'];
  $mail->Port = $emailConfig['smtp_port'];

  $mail->setFrom($emailConfig['from_email'], $emailConfig['from_name']);
  $mail->addReplyTo($emailConfig['from_email'], $emailConfig['from_name']);
  $mail->addAddress($data['user']['email'], $data['user']['name']);  // ✅ Correct access

  $mail->isHTML(true);
  $mail->Subject = 'Order Confirmation - ADA AROMAS';
  
  $productList = '';
  foreach ($cart as $item) {
    $productList .= "<li>{$item['title']} - Qty: {$item['quantity']} - ₹{$item['price']} x {$item['quantity']}</li>";
  }

$orderLink = "https://adaaromas.com/thankyou.php?orderId=$newOrderId";
$totalPrice = number_format($total, 2);

include "mail_template.php";
$mail->Body = $mailBody;
  $mail->send();} catch (Exception $e) {
  error_log("Email not sent. Error: {$mail->ErrorInfo}");
}
