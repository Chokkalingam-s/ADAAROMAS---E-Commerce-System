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
$total = isset($data['finalAmount']) ? (float)$data['finalAmount'] : array_sum(array_map(fn($p) => $p['price'] * $p['quantity'], $cart));

$totalASP = 0;
$totalProfit = 0;
foreach ($cart as $item) {
    $productId = $item['productId'];
    $quantity = $item['quantity'];

    $stmtP = $conn->prepare("SELECT asp, revenue FROM products WHERE productId = ?");
    $stmtP->execute([$productId]);
    $product = $stmtP->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        $totalASP += $product['asp'] * $quantity;
        $totalProfit += $product['revenue'] * $quantity;
    }
}

$gst = round($total * 0.18);  // 18% GST
$loss = 0;
$stmt = $conn->prepare("INSERT INTO orders (userId, transactionId, billingAmount, TotalASP, GST, PROFIT, LOSS) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([$userId, $paymentId, $total, $totalASP, $gst, $totalProfit, $loss]);
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
  $mail->Subject = 'Order Confirmation - ADA Aromas';
  
  $productList = '';
  foreach ($cart as $item) {
    $productList .= "<li>{$item['title']} - Qty: {$item['quantity']} - ₹{$item['price']} x {$item['quantity']}</li>";
  }

$orderLink = "https://yourdomain.com/thankyou.php?orderId=$newOrderId";
$totalPrice = number_format($total, 2);

$mail->Body = "
<div style='max-width:600px;margin:auto;font-family:sans-serif;border:1px solid #eee;border-radius:10px;overflow:hidden;'>
  <div style='background:#000;color:#fff;padding:20px;text-align:center'>
    <h1 style='margin:0;font-size:28px;'>Thank you for shopping with ADA Aromas!</h1>
    <p style='margin:5px 0;'>Order ID: <strong>#{$newOrderId}</strong></p>
  </div>

  <div style='padding:20px;background:#fafafa;'>
    <p style='font-size:16px;'>Hi <strong>{$data['user']['name']}</strong>,</p>
    <p style='font-size:15px;'>We're excited to let you know that your order has been successfully placed and paid via Razorpay.</p>
    
    <table style='width:100%;border-collapse:collapse;margin-top:15px'>
      <thead>
        <tr style='background:#eee;text-align:left;'>
          <th style='padding:10px;border:1px solid #ddd;'>Product</th>
          <th style='padding:10px;border:1px solid #ddd;'>Qty</th>
          <th style='padding:10px;border:1px solid #ddd;'>Size</th>
          <th style='padding:10px;border:1px solid #ddd;'>Price</th>
        </tr>
      </thead>
      <tbody>";

foreach ($cart as $item) {
  $image = $item['image'] ?? 'https://via.placeholder.com/60';
  $mail->Body .= "
        <tr>
          <td style='padding:10px;border:1px solid #ddd;'>
            <img src='{$image}' alt='{$item['title']}' style='width:60px;height:auto;vertical-align:middle;margin-right:8px;border-radius:6px;'>
            {$item['title']}
          </td>
          <td style='padding:10px;border:1px solid #ddd;'>{$item['quantity']}</td>
          <td style='padding:10px;border:1px solid #ddd;'>{$item['size']}</td>
          <td style='padding:10px;border:1px solid #ddd;'>₹" . ($item['price'] * $item['quantity']) . "</td>
        </tr>";
}

$mail->Body .= "
      </tbody>
    </table>

    <div style='margin-top:20px;font-size:16px;'>
      <p><strong>Total Paid:</strong> ₹{$totalPrice}</p>
      <p><strong>Transaction ID:</strong> {$paymentId}</p>
    </div>

    <div style='text-align:center;margin:30px 0;'>
      <a href='{$orderLink}' style='padding:12px 25px;background:#000;color:#fff;border-radius:6px;text-decoration:none;font-weight:bold;'>View Full Order Details</a>
    </div>

    <p style='font-size:14px;color:#888;'>If you have any questions,  email to <a href='mailto:{$emailConfig['from_email']}' style='color:#888;text-decoration:underline;'>{$emailConfig['from_email']}</a> and we’ll get back to you shortly.</p>
    <p style='font-size:14px;color:#888;'>– Team ADA Aromas</p>
  </div>

  <div style='background:#000;color:#fff;padding:15px;text-align:center;font-size:13px'>
    Further Order Visit :
    <a href='#' style='color:#fff;text-decoration:underline;margin:0 5px;'>ADA AROMAS</a> 
  </div>
</div>";

  $mail->send();} catch (Exception $e) {
  error_log("Email not sent. Error: {$mail->ErrorInfo}");
}
