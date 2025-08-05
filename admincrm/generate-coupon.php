<?php
include('../config/db.php');
session_start();
date_default_timezone_set('Asia/Kolkata');
if (!isset($_SESSION['admin_logged_in'])) header('Location: index.php');

function generateCouponCode($conn) {
  $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  do {
    $randomNumber = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    $randomLetters = $letters[rand(0, 25)] . $letters[rand(0, 25)];
    $code = "ADA{$randomNumber}{$randomLetters}";

    // Check if code already exists in DB
    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM coupons WHERE couponCode = ?");
    $checkStmt->execute([$code]);
    $exists = $checkStmt->fetchColumn();

  } while ($exists > 0);

  return $code;
}


$inserted = false;
$generatedCoupon = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$flatAmount = isset($_POST['flatAmount']) && $_POST['flatAmount'] !== '' ? floatval($_POST['flatAmount']) : 0;
$percentage = isset($_POST['percentage']) && $_POST['percentage'] !== '' ? intval($_POST['percentage']) : 0;
  $expiry = $_POST['expiry'] ?? '';
  
  if ((!$flatAmount && !$percentage) || ($flatAmount && $percentage) || !$expiry) {
    die("Invalid input");
  }

  $minutesMap = [
    '5min' => 5, '10min' => 10, '30min' => 30,
    '12hr' => 12 * 60, '1d' => 1440, '1w' => 10080,
    '1m' => 43200, '3m' => 129600, '6m' => 259200, '1y' => 525600
  ];

  $minutes = $minutesMap[$expiry] ?? 0;
  $expiryTime = date('Y-m-d H:i:s', strtotime("+$minutes minutes"));

  $code = generateCouponCode($conn);
  $stmt = $conn->prepare("INSERT INTO coupons (couponCode, expiryTime, percentage, flatAmount) VALUES (?, ?, ?, ?)");
  $stmt->execute([$code, $expiryTime, $percentage, $flatAmount]);
  $generatedCoupon = $code;
  $inserted = true;
}

// Fetch all coupons
$coupons = $conn->query("SELECT * FROM coupons ORDER BY couponId DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Generate Coupon</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .copy-btn { cursor: pointer; }
    .copy-btn:hover { color: green; }
.bg-available > td {
  background-color: #d4edda !important;  /* Light green */
  color: #155724 !important;
}
.bg-used > td {
  background-color: #f8d7da !important;  /* Light red */
  color: #721c24 !important;
}
.bg-expired > td {
  background-color: #e2e3e5 !important;  /* Light gray */
  color: #383d41 !important;
}
  .filter-btn.active {
    background-color: #0d6efd;
    color: white;
    border-color: #0d6efd;
  }
/* Apply color themes even in mobile view (card divs) */
.bg-used {
  background-color: #f8d7da !important;
  color: #721c24 !important;
}
.bg-available {
  background-color: #d4edda !important;
  color: #155724 !important;
}
.bg-expired {
  background-color: #e2e3e5 !important;
  color: #383d41 !important;
}


  .bg-available:hover, .bg-used:hover, .bg-expired:hover { opacity: 0.8; }
  .bg-available td, .bg-used td, .bg-expired td { vertical-align: middle; }
  </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3">
  <a class="navbar-brand" href="../admincrm">ADA AROMAS Admin</a>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="adminNav">
    <ul class="navbar-nav ms-auto">
      <li class="nav-item"><a class="nav-link" href="add-product.php">Add Product</a></li>
      <li class="nav-item"><a class="nav-link" href="manage-stock.php">Manage Stock</a></li>
      <li class="nav-item"><a class="nav-link active" href="generate-coupon.php">Generate Coupon</a></li>
      <li class="nav-item"><a class="nav-link" href="orders.php">Orders</a></li>
      <li class="nav-item"><a class="nav-link" href="stats.php">Stats</a></li>
      <li class="nav-item"><a class="nav-link" href="report.php">Report</a></li>
      <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Logout</a></li>
    </ul>
  </div>
</nav>

<div class="container py-3 text-center">
  <h3 class="mb-4">Generate Coupon Code</h3>
  <form method="POST" class="row g-3 bg-white border p-4 rounded shadow-sm">
    <div class="col-md-4">
      <label>Flat Amount (₹)</label>
      <input type="number" name="flatAmount" id="flatAmount" class="form-control" min="1">
    </div>
    <div class="col-md-4">
      <label>Discount %</label>
      <input type="number" name="percentage" id="percentage" class="form-control" min="1" max="100">
    </div>
    <div class="col-md-4">
      <label>Expiry Time</label>
      <select name="expiry" id="expiry" class="form-select" required>
        <option value="">-- Select Expiry --</option>
        <option value="5min">5 Minutes</option>
        <option value="10min">10 Minutes</option>
        <option value="30min">30 Minutes</option>
        <option value="12hr">12 Hours</option>
        <option value="1d">1 Day</option>
        <option value="1w">1 Week</option>
        <option value="1m">1 Month</option>
        <option value="3m">3 Months</option>
        <option value="6m">6 Months</option>
        <option value="1y">1 Year</option>
      </select>
    </div>
    <div class="col-12">
      <button type="submit" id="generateBtn" class="btn btn-success" disabled>Generate Coupon</button>
    </div>
    <?php if ($inserted): ?>
      <div class="alert alert-success mt-3">
        Coupon <strong><?= $generatedCoupon ?></strong> generated successfully!
        <span class="copy-btn ms-3 text-primary" onclick="copyText('<?= $generatedCoupon ?>')">📋 Copy</span>
      </div>
    <?php endif; ?>
  </form>

  <hr class="my-5">

  <h4>All Generated Coupons</h4>
  <div class="d-flex flex-wrap gap-2 my-3 justify-content-center">
  <button class="btn btn-outline-primary filter-btn active" data-filter="all">All</button>
  <button class="btn btn-outline-success filter-btn" data-filter="active">Active</button>
  <button class="btn btn-outline-danger filter-btn" data-filter="used">Used</button>
  <button class="btn btn-outline-secondary filter-btn" data-filter="expired">Expired</button>
</div>

<!-- Mobile Optimized Table View -->
<div class="d-block d-md-none">
  <?php $i = 1; foreach ($coupons as $c): 
    $isExpired = strtotime($c['expiryTime']) < time();
    $rowClass = '';
    if ($c['availability'] == 1) $rowClass = 'bg-used';
    elseif ($isExpired) $rowClass = 'bg-expired';
    else $rowClass = 'bg-available';
  ?>
  <div class="border rounded shadow-sm p-3 mb-3 <?= $rowClass ?>" 
     data-status="<?php
      if ($c['availability'] == 1) echo 'used';
      elseif ($isExpired) echo 'expired';
      else echo 'active';
     ?>">

    <div class="d-flex justify-content-between">
      <span class="fw-bold">#<?= $i++ ?> - <?= $c['couponCode'] ?></span><button 
  class="btn btn-outline-primary btn-sm px-2 py-1" 
  onclick="copyText('<?= $c['couponCode'] ?>')">
  📋 Copy
</button>

    </div>
    <div class="small mt-2">
      <div><strong>Flat:</strong> ₹<?= $c['flatAmount'] ?> | <strong>Discount:</strong> <?= $c['percentage'] ?>%</div>
      <div><strong>Expires:</strong> <?= date('d-M-Y h:i A', strtotime($c['expiryTime'])) ?></div>
    </div>
    <div class="mt-2 text-end">
      <button class="btn btn-danger btn-sm remove-coupon">Remove</button>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Desktop Table View -->
<div class="table-responsive d-none d-md-block">
  <table class="table table-bordered bg-white">
    <thead>
      <tr>
        <th>#</th>
        <th>Coupon Code</th>
        <th>Flat Amount (₹)</th>
        <th>Discount (%)</th>
        <th>Expiry Time</th>
        <th>Copy</th>
        <th>Remove</th>
      </tr>
    </thead>
    <tbody>
      <?php $i = 1; foreach ($coupons as $c): 
        $isExpired = strtotime($c['expiryTime']) < time();
        $rowClass = '';
        if ($c['availability'] == 1) $rowClass = 'bg-used';
        elseif ($isExpired) $rowClass = 'bg-expired';
        else $rowClass = 'bg-available';
      ?>
              <tr data-code="<?= $c['couponCode'] ?>" class="<?= $rowClass ?>" data-status="<?php
  if ($c['availability'] == 1) {
    echo 'used';
  } else if ($isExpired) {
    echo 'expired';
  } else {
    echo 'active';
  }
?>"
>
        <td><?= $i++ ?></td>
        <td><strong><?= $c['couponCode'] ?></strong></td>
        <td><?= $c['flatAmount'] ?></td>
        <td><?= $c['percentage'] ?></td>
        <td><?= date('d-M-Y h:i A', strtotime($c['expiryTime'])) ?></td>
        <td><span class="copy-btn text-primary" onclick="copyText('<?= $c['couponCode'] ?>')">📋 Copy</span></td>
        <td><button class="btn btn-danger btn-sm remove-coupon">Remove</button></td>
   </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  const flat = document.getElementById('flatAmount');
  const percent = document.getElementById('percentage');
  const expiry = document.getElementById('expiry');
  const btn = document.getElementById('generateBtn');

  function validate() {
    const hasFlat = flat.value.trim() !== '';
    const hasPercent = percent.value.trim() !== '';
    const hasExpiry = expiry.value !== '';
    btn.disabled = (hasFlat && hasPercent) || (!hasFlat && !hasPercent) || !hasExpiry;
  }

  flat.addEventListener('input', validate);
  percent.addEventListener('input', validate);
  expiry.addEventListener('change', validate);

  function copyText(text) {
    navigator.clipboard.writeText(text);
    alert('Coupon copied: ' + text);
  }

  // Remove Coupon Handler
document.querySelectorAll('.remove-coupon').forEach(btn => {
  btn.addEventListener('click', () => {
    if (!confirm("Are you sure to delete this coupon?")) return;
    const tr = btn.closest('tr');
    const code = tr.dataset.code;

    fetch('handle-delete-coupon.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ couponCode: code })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) tr.remove();
      else alert("Failed to delete.");
    });
  });
});

</script>
<script>
  function copyText(text) {
    const tempInput = document.createElement('input');
    tempInput.value = text;
    document.body.appendChild(tempInput);
    tempInput.select();
    tempInput.setSelectionRange(0, 99999); // For iOS
    document.execCommand('copy');
    document.body.removeChild(tempInput);

    // Show temporary feedback (optional)
    const copied = document.createElement('div');
    copied.textContent = 'Copied!';
    copied.style.position = 'fixed';
    copied.style.bottom = '20px';
    copied.style.left = '50%';
    copied.style.transform = 'translateX(-50%)';
    copied.style.background = '#28a745';
    copied.style.color = 'white';
    copied.style.padding = '5px 12px';
    copied.style.borderRadius = '6px';
    copied.style.zIndex = 9999;
    document.body.appendChild(copied);
    setTimeout(() => copied.remove(), 1500);
  }
</script>

<script>
  const filterButtons = document.querySelectorAll('.filter-btn');
  const desktopRows = document.querySelectorAll('tbody tr');
  const mobileCards = document.querySelectorAll('.d-block.d-md-none > .border');

  filterButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      filterButtons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      const filter = btn.dataset.filter;

      // Desktop rows
      desktopRows.forEach(row => {
        const status = row.dataset.status;
        row.style.display = (filter === 'all' || filter === status) ? '' : 'none';
      });

      // Mobile cards
      mobileCards.forEach(card => {
        const status = card.dataset.status;
        card.style.display = (filter === 'all' || filter === status) ? '' : 'none';
      });
    });
  });
</script>
</body>
</html>
