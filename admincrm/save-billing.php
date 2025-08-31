<?php
include('../config/db.php');
if(isset($_POST['orderId'])){
  $stmt = $conn->prepare("UPDATE orders SET TotalASP=?, GST=?, billingAmount=?, PROFIT=?, remarks=? WHERE orderId=?");
  $stmt->execute([
    $_POST['TotalASP'],
    $_POST['GST'],
    $_POST['billingAmount'],
    $_POST['PROFIT'],
    $_POST['remarks'],
    $_POST['orderId']
  ]);
}
?>
