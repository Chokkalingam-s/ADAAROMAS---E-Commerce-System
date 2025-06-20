<?php
include('../config/db.php');
session_start();
if (!isset($_SESSION['admin_logged_in'])) exit;

$data = json_decode(file_get_contents('php://input'), true);
$code = $data['couponCode'] ?? '';

$stmt = $conn->prepare("DELETE FROM coupons WHERE couponCode = ?");
$success = $stmt->execute([$code]);


echo json_encode(['success' => $success]);
if ($success) {
    http_response_code(200);
} else {
    http_response_code(500);
}