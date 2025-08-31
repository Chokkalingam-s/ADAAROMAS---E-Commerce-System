<?php
include('../config/db.php');
session_start();
if (!isset($_SESSION['admin_logged_in'])) die("Unauthorized");

// FETCH ORDER DETAILS
if ($_POST['action'] == "fetch") {
    $orderId = intval($_POST['orderId']);

    // Get customize request
    $stmt = $conn->prepare("SELECT * FROM customize WHERE orderId=?");
    $stmt->execute([$orderId]);
    $custom = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get order details
    $stmt = $conn->prepare("SELECT * FROM orders WHERE orderId=?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get user details
    $stmt = $conn->prepare("SELECT * FROM users WHERE userId=?");
    $stmt->execute([$order['userId']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    ?>
    <div class="row">
      <div class="col-md-6">
        <h6>Customize Request</h6>
        <p><?= nl2br(htmlspecialchars($custom['description'])) ?></p>
        <?php if($custom['imageUrl']): ?>
          <img src="<?= $custom['imageUrl'] ?>" class="img-fluid rounded shadow">
        <?php endif; ?>
      </div>
      <div class="col-md-6">
        <h6>User Details</h6>
        <ul class="list-group">
          <li class="list-group-item"><strong>Name:</strong> <?= $user['name'] ?></li>
          <li class="list-group-item"><strong>Phone:</strong> <?= $user['phoneNo'] ?></li>
          <li class="list-group-item"><strong>Email:</strong> <?= $user['email'] ?></li>
          <li class="list-group-item"><strong>State:</strong> <?= $user['state'] ?></li>
          <li class="list-group-item"><strong>District:</strong> <?= $user['district'] ?></li>
          <li class="list-group-item"><strong>City:</strong> <?= $user['city'] ?></li>
          <li class="list-group-item"><strong>Pincode:</strong> <?= $user['pincode'] ?></li>
          <li class="list-group-item"><strong>Address:</strong> <?= $user['address'] ?></li>
        </ul>
      </div>
    </div>

    <hr>
    <h6>Billing Details</h6>
    <form class="billing-form">
      <input type="hidden" name="action" value="update_billing">
      <input type="hidden" name="orderId" value="<?= $orderId ?>">

      <div class="row">
        <div class="col-md-4 mb-2">
          <label class="form-label">Total ASP</label>
          <input type="number" class="form-control" name="TotalASP" value="<?= $order['TotalASP'] ?>">
        </div>
        <div class="col-md-4 mb-2">
          <label class="form-label">GST (%)</label>
          <input type="number" class="form-control" name="GST" value="<?= $order['GST'] ?>">
        </div>
        <div class="col-md-4 mb-2">
          <label class="form-label">Billing Amount</label>
          <input type="number" step="0.01" class="form-control" name="billingAmount" value="<?= $order['billingAmount'] ?>">
        </div>
        <div class="col-md-4 mb-2">
          <label class="form-label">Profit</label>
          <input type="number" class="form-control" name="PROFIT" value="<?= $order['PROFIT'] ?>">
        </div>
        <div class="col-md-8 mb-2">
          <label class="form-label">Remarks</label>
          <textarea class="form-control" name="remarks"><?= $order['remarks'] ?></textarea>
        </div>
      </div>
      <button class="btn btn-success mt-2">Save Billing Details</button>
    </form>
    <?php
    exit;
}

// UPDATE STATUS
if ($_POST['action'] == "update_status") {
    $orderId = intval($_POST['orderId']);
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE orders SET status=? WHERE orderId=?");
    if ($stmt->execute([$status, $orderId])) echo "Status updated successfully!";
    else echo "Failed to update status.";
    exit;
}

// UPDATE BILLING
if ($_POST['action'] == "update_billing") {
    $orderId = intval($_POST['orderId']);
    $TotalASP = intval($_POST['TotalASP']);
    $GST = intval($_POST['GST']);
    $billingAmount = floatval($_POST['billingAmount']);
    $PROFIT = intval($_POST['PROFIT']);
    $remarks = $_POST['remarks'];

    $stmt = $conn->prepare("UPDATE orders SET TotalASP=?, GST=?, billingAmount=?, PROFIT=?, remarks=? WHERE orderId=?");
    if ($stmt->execute([$TotalASP, $GST, $billingAmount, $PROFIT, $remarks, $orderId])) {
        echo "Billing details updated!";
    } else {
        echo "Failed to update billing.";
    }
    exit;
}
?>
