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
    // Skip Razorpay verification for admin
    $paymentId = "ADMIN-" . strtoupper($data['adminMode']);
} else {

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

// Compute details
$totalProductValue = $totalASP;
$gstAmount = $gst;
$estimatedTotal = $totalProductValue + $gstAmount;
$discount = $estimatedTotal - $total;
$totalPaid = $total;

// Format values
$formattedProductValue = number_format($totalProductValue, 2);
$formattedGST = number_format($gstAmount, 2);
$formattedEstimatedTotal = number_format($estimatedTotal, 2);
$formattedDiscount = number_format($discount, 2);
$formattedTotalPaid = number_format($totalPaid, 2);

// Add after product table
$mail->Body .= "
    </tbody>
    <tfoot>
      <tr>
        <td colspan='3' style='padding:10px;border:1px solid #ddd;text-align:right;'><strong>Total Product Value:</strong></td>
        <td style='padding:10px;border:1px solid #ddd;'>₹{$formattedProductValue}</td>
      </tr>
      <tr>
        <td colspan='3' style='padding:10px;border:1px solid #ddd;text-align:right;'><strong>GST (18%):</strong></td>
        <td style='padding:10px;border:1px solid #ddd;'>₹{$formattedGST}</td>
      </tr>";

if ($discount > 0) {
  $mail->Body .= "
      <tr>
        <td colspan='3' style='padding:10px;border:1px solid #ddd;text-align:right;'><strong>Estimated Total:</strong></td>
        <td style='padding:10px;border:1px solid #ddd;'>₹{$formattedEstimatedTotal}</td>
      </tr>
      <tr>
        <td colspan='3' style='padding:10px;border:1px solid #ddd;text-align:right;'><strong style='color:green;'>Discount:</strong></td>
        <td style='padding:10px;border:1px solid #ddd;color:green;'>– ₹{$formattedDiscount}</td>
      </tr>";
}

$mail->Body .= "
      <tr>
        <td colspan='3' style='padding:10px;border:1px solid #ddd;text-align:right;'><strong>Total Paid:</strong></td>
        <td style='padding:10px;border:1px solid #ddd;'>₹{$formattedTotalPaid}</td>
      </tr>
    </tfoot>
    </table>

    <p style='margin-top:15px;font-size:15px;'><strong>Transaction ID:</strong> {$paymentId}</p>

    <div style='margin-top:25px;text-align:center;'>
      <span style='font-size:18px;font-weight:bold;color:#e60073;animation:blinker 1.2s linear infinite;display:inline-block;'>
        🎁 Product will be delivered with exciting FREEBIES & GIFTS! 🎁
      </span>
    </div>

    <style>
    @keyframes blinker {
      50% { opacity: 0; }
    }
    </style>

<div style='text-align:center;margin:30px 0;'>
  <a href='{$orderLink}' style='padding:12px 25px;background:#000;color:#fff;border-radius:6px;text-decoration:none;font-weight:bold;'>View Full Order Details</a>
  <p style='margin-top:10px;font-size:14px;color:#555;'>
    Use this code to cancel your order if needed: 
    <strong style='color:#d9534f;'>{$cancelCode}</strong>
  </p>
</div>


    <p style='font-size:14px;color:#888;'>If you have any questions, email to <a href='mailto:{$emailConfig['from_email']}' style='color:#888;text-decoration:underline;'>{$emailConfig['from_email']}</a> and we’ll get back to you shortly.</p>
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
