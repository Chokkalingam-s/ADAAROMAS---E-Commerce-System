<?php
include('../config/db.php');
session_start();
date_default_timezone_set('Asia/Kolkata');
if (!isset($_SESSION['admin_logged_in'])) header('Location: index.php');

// Date filter logic
$filterType = $_GET['filter'] ?? 'today';
$startDate = '';
$endDate = '';

if ($filterType === 'today') {
  $startDate = date('Y-m-d 00:00:00');
  $endDate = date('Y-m-d 23:59:59');
} elseif ($filterType === 'week') {
  $startDate = date('Y-m-d 00:00:00', strtotime('-6 days'));
  $endDate = date('Y-m-d 23:59:59');
} elseif ($filterType === 'month') {
  $startDate = date('Y-m-d 00:00:00', strtotime('-29 days'));
  $endDate = date('Y-m-d 23:59:59');
} elseif ($filterType === 'custom' && isset($_GET['start']) && isset($_GET['end'])) {
  $startDate = $_GET['start'] . " 00:00:00";
  $endDate = $_GET['end'] . " 23:59:59";
}

// Fetch orders if valid range
$reportData = [];
if ($startDate && $endDate) {
  $stmt = $conn->prepare("
    SELECT o.orderDate, o.billingAmount, o.TotalASP, o.GST, u.name, u.state
    FROM orders o
    JOIN users u ON o.userId = u.userId
    WHERE o.orderDate BETWEEN ? AND ?
    ORDER BY o.orderDate DESC
  ");
  $stmt->execute([$startDate, $endDate]);
  $reportData = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sales Report - ADA Aromas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
      <li class="nav-item"><a class="nav-link" href="generate-coupon.php">Generate Coupon</a></li>
      <li class="nav-item"><a class="nav-link" href="orders.php">Orders</a></li>
      <li class="nav-item"><a class="nav-link" href="stats.php">Stats</a></li>
      <li class="nav-item"><a class="nav-link active" href="report.php">Report</a></li>
      <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Logout</a></li>
    </ul>
  </div>
</nav>

<div class="container mt-4">
  <h3>Sales Report</h3>
  <form class="row g-2 align-items-end mb-4" method="GET">
    <div class="col-auto">
      <label class="form-label">Select Range</label>
      <select name="filter" class="form-select" onchange="toggleCustomDates(this.value)">
        <option value="today" <?= $filterType === 'today' ? 'selected' : '' ?>>Today</option>
        <option value="week" <?= $filterType === 'week' ? 'selected' : '' ?>>Past Week</option>
        <option value="month" <?= $filterType === 'month' ? 'selected' : '' ?>>Past Month</option>
        <option value="custom" <?= $filterType === 'custom' ? 'selected' : '' ?>>Custom</option>
      </select>
    </div>
    <div class="col-auto custom-date-fields" style="<?= $filterType === 'custom' ? '' : 'display:none;' ?>">
      <label class="form-label">Start Date</label>
      <input type="date" name="start" class="form-control" value="<?= $_GET['start'] ?? '' ?>">
    </div>
    <div class="col-auto custom-date-fields" style="<?= $filterType === 'custom' ? '' : 'display:none;' ?>">
      <label class="form-label">End Date</label>
      <input type="date" name="end" class="form-control" value="<?= $_GET['end'] ?? '' ?>">
    </div>
    <div class="col-auto">
      <button class="btn btn-dark" type="submit">Apply</button>
    </div>
  </form>

  <?php if (count($reportData) > 0): ?>
    <div class="table-responsive">
      <table class="table table-bordered table-striped" id="reportTable">
        <thead class="table-dark">
          <tr>
            <th>S.No</th>
            <th>Date</th>
            <th>Customer Name</th>
            <th>State</th>
            <th>Amount Received (₹)</th>
            <th>Revenue (₹)</th>
            <th>GST %</th>
            <th>GST Collected (₹)</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($reportData as $i => $row): ?>
            <tr>
              <td><?= $i + 1 ?></td>
              <td><?= date('Y-m-d', strtotime($row['orderDate'])) ?></td>
              <td><?= htmlspecialchars($row['name']) ?></td>
              <td><?= htmlspecialchars($row['state']) ?></td>
              <td><?= $row['billingAmount'] ?></td>
              <td><?= $row['TotalASP'] ?></td>
              <td>18%</td>
              <td><?= $row['GST'] ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <button onclick="downloadExcel()" class="btn btn-success me-2">Download Excel</button>
    <button onclick="downloadPDF()" class="btn btn-danger">Download PDF</button>
  <?php else: ?>
    <div class="alert alert-warning mt-4">No data found for selected date range.</div>
  <?php endif; ?>
</div>

<script>
function toggleCustomDates(val) {
  const fields = document.querySelectorAll('.custom-date-fields');
  fields.forEach(f => f.style.display = (val === 'custom') ? '' : 'none');
}

function downloadExcel() {
  const tableHTML = document.getElementById('reportTable').outerHTML;
  const blob = new Blob([tableHTML], { type: 'application/vnd.ms-excel' });
  const url = URL.createObjectURL(blob);
  const link = document.createElement('a');
  link.href = url;
  link.download = 'Sales_Report.xls';
  link.click();
}

function downloadPDF() {
  const printContent = document.getElementById('reportTable').outerHTML;
  const w = window.open('', '', 'width=900,height=700');
  w.document.write('<html><head><title>Sales Report</title></head><body>');
  w.document.write(printContent);
  w.document.write('</body></html>');
  w.document.close();
  w.print();
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
