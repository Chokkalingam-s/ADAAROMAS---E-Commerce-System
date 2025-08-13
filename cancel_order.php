<?php
header('Content-Type: application/json');
require 'config/db.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$orderId = intval($_POST['orderId'] ?? 0);
$enteredCode = strtoupper(trim($_POST['cancelCode'] ?? ''));

if (!$orderId || !$enteredCode) {
    echo json_encode(['success' => false, 'message' => 'Missing data']);
    exit;
}

// Fetch order
$stmt = $conn->prepare("SELECT o.*, u.name AS userName, u.email AS userEmail, u.phoneNo, u.address, u.city, u.state, u.pincode
                        FROM orders o
                        JOIN users u ON o.userId = u.userId
                        WHERE o.orderId = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit;
}
if ($order['cancelCode'] !== $enteredCode) {
    echo json_encode(['success' => false, 'message' => 'Invalid cancellation code']);
    exit;
}

// Update status
$conn->prepare("UPDATE orders SET status = 'Cancelled' WHERE orderId = ?")
     ->execute([$orderId]);

// Fetch items
$stmtOD = $conn->prepare("SELECT od.*, p.name, p.asp 
                          FROM order_details od
                          JOIN products p ON od.productId = p.productId
                          WHERE od.orderId = ?");
$stmtOD->execute([$orderId]);
$items = $stmtOD->fetchAll(PDO::FETCH_ASSOC);

$productListHTML = "<table border='1' cellpadding='6' cellspacing='0' style='border-collapse:collapse;width:100%;'>
<tr><th>Product</th><th>Qty</th><th>Size</th><th>Price</th></tr>";
foreach ($items as $item) {
    $productListHTML .= "<tr>
        <td>{$item['name']}</td>
        <td>{$item['quantity']}</td>
        <td>{$item['size']}ml</td>
        <td>₹" . number_format($item['asp'] * $item['quantity']) . "</td>
    </tr>";
}
$productListHTML .= "</table>";

// Send email
$config = require 'config/email_config.php';
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = $config['smtp_host'];
    $mail->SMTPAuth = true;
    $mail->Username = $config['smtp_username'];
    $mail->Password = $config['smtp_password'];
    $mail->SMTPSecure = $config['smtp_secure'];
    $mail->Port = $config['smtp_port'];

    $mail->setFrom($config['from_email'], $config['from_name']);
    $mail->addAddress('Adaaromas@rudraksha.org.in');
    $mail->addBCC('chokka7878@gmail.com');
    $mail->addReplyTo($order['userEmail'], $order['userName']);

    $mail->isHTML(true);
    $mail->Subject = "Cancellation of Order - ID: {$orderId}";
    $mail->Body = "
        <h2>Order Cancelled</h2>
        <p><strong>Order ID:</strong> {$orderId}</p>
        <p><strong>Transaction ID:</strong> {$order['transactionId']}</p>
        <p><strong>User:</strong> {$order['userName']} ({$order['userEmail']}, {$order['phoneNo']})</p>
        <p><strong>Address:</strong> {$order['address']}, {$order['city']}, {$order['state']} - {$order['pincode']}</p>
        <p><strong>Order Date:</strong> {$order['orderDate']}</p>
        <p><strong>Billing Amount:</strong> ₹" . number_format($order['billingAmount']) . "</p>
        <h3>Products:</h3>
        {$productListHTML}
        <p style='color:red;font-weight:bold;'>This order has been cancelled by the customer.</p>
    ";

    $mail->send();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Order cancelled but email failed']);
    exit;
}

echo json_encode(['success' => true]);
flush(); // Push output to client