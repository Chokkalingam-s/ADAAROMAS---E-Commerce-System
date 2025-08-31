<?php
include('../config/db.php');
if(isset($_POST['orderId'], $_POST['status'])){
  $stmt = $conn->prepare("UPDATE orders SET status=? WHERE orderId=?");
  $stmt->execute([$_POST['status'], $_POST['orderId']]);
}
?>
