
<?php
include('./config/db.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__ . '/vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect input
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $name = $firstName . " " . $lastName;
    $phoneNo = $_POST['phoneNo'];
    $email = $_POST['email'];
    $state = $_POST['state'];
    $district = $_POST['district'];
    $address = $_POST['address1'] . " " . $_POST['address2'];
    $city = $_POST['city'];
    $pincode = $_POST['pincode'];
    $description = $_POST['description'];

    // Insert user (or fetch existing by email)
    $stmt = $conn->prepare("SELECT userId FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $userId = $stmt->fetchColumn();

    if (!$userId) {
        $stmt = $conn->prepare("INSERT INTO users (name, phoneNo, email, state, district, address, city, pincode, createdAt) 
                                VALUES (?,?,?,?,?,?,?,?,NOW())");
        $stmt->execute([$name, $phoneNo, $email, $state, $district, $address, $city, $pincode]);
        $userId = $conn->lastInsertId();
    }

    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (userId, status, orderDate, isCustomized) VALUES (?, 'Pending', NOW(), 1)");
    $stmt->execute([$userId]);
    $orderId = $conn->lastInsertId();

    // Handle image upload
    $imageUrl = null;
    if (!empty($_FILES['image']['name'])) {
        $targetDir = __DIR__ . "/assets/customize/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $fileName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $fileName;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $imageUrl = "assets/customize/" . $fileName;
        }
    }

    // Insert customize record
    $stmt = $conn->prepare("INSERT INTO customize (orderId, description, imageUrl) VALUES (?,?,?)");
    $stmt->execute([$orderId, $description, $imageUrl]);

    // Send email to admin
    $config = require 'config/email_config.php';
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
        $mail->addAddress("Adaaromas@rudraksha.org.in");
        $mail->addBCC("chokka7878@gmail.com");
        $mail->addReplyTo($email, $name);

        $mail->isHTML(true);
        $mail->Subject = "New Customization Request - Order #$orderId";
        $mail->Body = "
            <h2>New Customization Request</h2>
            <p><strong>Name:</strong> $name</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Phone:</strong> $phoneNo</p>
            <p><strong>Address:</strong> $address, $city, $district, $state - $pincode</p>
            <p><strong>Description:</strong> $description</p>
            " . ($imageUrl ? "<p><strong>Reference Image:</strong><br><img src='http://adaaromas.co.in/$imageUrl' width='200'></p>" : "") . "
            <p>Status: Pending (Customized Order)</p>
        ";

        $mail->send();
    } catch (Exception $e) {
        error_log("Customize email failed: " . $mail->ErrorInfo);
    }

    header("Location: thankyou.php?orderId=$orderId");
    exit;
}
?>


