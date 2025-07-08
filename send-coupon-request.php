<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__ . '/vendor/autoload.php';

$config = require 'config/email_config.php'; 

header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);
$user = $input['user'] ?? [];
$cart = $input['cart'] ?? [];

if (!$user || !$cart) {
  echo json_encode(['success' => false, 'message' => 'Incomplete data']);
  exit;
}

try {
  $mail = new PHPMailer(true);
  $mail->isSMTP();
  $mail->Host = $config['smtp_host'];
  $mail->SMTPAuth = true;
  $mail->Username = $config['smtp_username'];
  $mail->Password = $config['smtp_password'];
  $mail->SMTPSecure = $config['smtp_secure'];
  $mail->Port = $config['smtp_port'];

  $mail->setFrom($config['from_email'], $config['from_name']);
  $mail->addAddress('Adaaromas@rudraksha.org.in'); // Can also add BCC for backup copy
   $mail->addBCC('chokka7878@gmail.com');
    $mail->addReplyTo($user['email'], "{$user['firstName']} {$user['lastName']}");
  $mail->isHTML(true);
  $mail->Subject = 'Request for Coupon - ADA Aromas';

  $cartHtml = '';
  foreach ($cart as $item) {
    $cartHtml .= "<tr>
      <td style='padding:8px;border:1px solid #ccc;'>{$item['title']}</td>
      <td style='padding:8px;border:1px solid #ccc;'>{$item['quantity']}</td>
      <td style='padding:8px;border:1px solid #ccc;'>{$item['price']}</td>
      <td style='padding:8px;border:1px solid #ccc;'>" . ($item['price'] * $item['quantity']) . "</td>
    </tr>";
  }

  $mail->Body = "
    <div style='font-family:Arial,sans-serif; max-width:600px; margin:auto; border:1px solid #e2e2e2; padding:20px; background:#fafafa;'>
      <h2 style='color:#d63384;'>Coupon Request</h2>
      <p>A customer has requested a coupon. Below are the details:</p>

      <h4 style='margin-top:20px;'>Customer Info</h4>
      <p><strong>Name:</strong> {$user['firstName']} {$user['lastName']}<br>
         <strong>Email:</strong> {$user['email']}<br>
         <strong>Phone:</strong> {$user['phone']}<br>
         <strong>Address:</strong> {$user['address1']}, {$user['address2']}<br>
         {$user['city']}, {$user['district']}, {$user['state']} - {$user['pincode']}</p>

      <h4 style='margin-top:20px;'>Cart Summary</h4>
      <table style='width:100%; border-collapse:collapse;'>
        <thead>
          <tr style='background:#f8d7da; color:#721c24;'>
            <th style='padding:10px;border:1px solid #ccc;'>Product</th>
            <th style='padding:10px;border:1px solid #ccc;'>Quantity</th>
            <th style='padding:10px;border:1px solid #ccc;'>Price</th>
            <th style='padding:10px;border:1px solid #ccc;'>Total</th>
          </tr>
        </thead>
        <tbody>
          {$cartHtml}
        </tbody>
      </table>

      <p style='margin-top:30px;'>Please follow up with the customer at <strong>{$user['email']}</strong> or <strong>{$user['phone']}</strong>.</p>

      <p style='margin-top:40px; text-align:center; font-size:12px; color:#888;'>This is an automated request from ADA Aromas website checkout page.</p>
    </div>
  ";

  $mail->send();
  echo json_encode(['success' => true]);
} catch (Exception $e) {
  error_log("Coupon email failed: " . $mail->ErrorInfo);
  echo json_encode(['success' => false, 'message' => 'Email failed to send.']);
}
