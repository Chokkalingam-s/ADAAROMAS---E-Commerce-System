<?php
require 'vendor/autoload.php';
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
    $mail->addAddress("chokka7878@gmail.com", "Testing User");

    $mail->isHTML(true);
    $mail->Subject = 'Order Confirmation - ADA AROMAS (TEST)';

    // Fake order data
    $data['user']['name'] = "Test User";
    $newOrderId = rand(1000,9999);
    $paymentId = "TXN" . rand(10000,99999);
    $cancelCode = strtoupper(substr(md5(time()),0,6));
    $orderLink = "https://yourdomain.com/thankyou.php?orderId=$newOrderId";

    $cart = [
        ["title"=>"Luxury Oud Perfume","quantity"=>1,"size"=>"100 ML","price"=>2499,"image"=>"https://via.placeholder.com/60"],
        ["title"=>"Floral Musk Spray","quantity"=>2,"size"=>"50 ML","price"=>1499,"image"=>"https://via.placeholder.com/60"]
    ];

    $totalASP = 2499 + (2*1499);
    $gst = $totalASP * 0.18;
    $total = $totalASP + $gst;

    // Use the same template
    include "mail_template.php";
    $mail->Body = $mailBody;

    $mail->send();
    echo "✅ Test mail sent!";
} catch (Exception $e) {
    echo "❌ Mail error: {$mail->ErrorInfo}";
}
