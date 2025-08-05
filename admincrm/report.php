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
    SELECT o.orderDate, o.billingAmount, o.TotalASP, o.GST, o.orderId, u.name, u.state
    FROM orders o
    JOIN users u ON o.userId = u.userId
    WHERE o.orderDate BETWEEN ? AND ?
    AND o.status != 'Replaced'
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

<div class="container mt-4 text-center">
  <h3>Sales Report</h3>
  <form class="row g-2 align-items-end mb-4 justify-content-center" method="GET">
    <div class="col-auto">
      <label class="form-label text-center "><b><i> Select Range </i></b></label>
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
          <tr class="text-center">
            <th>S.No</th>
            <th>Order ID</th>
            <th>Date</th>
            <th>Customer Name</th>
            <th>State</th>
            <th>Amount Received (₹)</th>
            <th>Revenue (₹)</th>
            <th>SGST %</th>
            <th>IGST %</th>
            <th>SGST (₹)</th>
            <th>IGST (₹)</th>
            <th>Total GST (₹)</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $totalBillingAmount = 0;
$totalRevenue = 0;
$totalSGST = 0;
$totalIGST = 0;
$totalGST = 0;
          foreach ($reportData as $row) {
            $totalBillingAmount += $row['billingAmount'];
            $totalRevenue += $row['TotalASP'];
            $totalSGST += $row['GST'] / 2;
            $totalIGST += $row['GST'] / 2;
            $totalGST += $row['GST'];
          }                                
          foreach ($reportData as $i => $row): 
          
          ?>
            <tr class="text-center">
              <td><?= $i + 1 ?></td>
              <td><?= htmlspecialchars($row['orderId']) ?></td>
              <td><?= date('Y-m-d', strtotime($row['orderDate'])) ?></td>
              <td><?= htmlspecialchars($row['name']) ?></td>
              <td><?= htmlspecialchars($row['state']) ?></td>
              <td><?= round($row['billingAmount']) ?></td>
              <td><?= round($row['TotalASP']) ?></td>
              <td>9%</td>
              <td>9%</td>
              <td><?= round($row['GST']/2 )?></td>
              <td><?= round($row['GST']/2 )?></td>
              <td><?= round($row['GST'] )?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
                <tfoot class="table-dark">
            <tr class="text-center">
                <td colspan="5" class="fw-bold">Total</td>
                <td class="fw-bold">Rs. <?= $totalBillingAmount ?></td>
                <td class="fw-bold">Rs. <?= $totalRevenue ?></td>
                <td>-</td>
                <td>-</td>
                <td class="fw-bold">Rs. <?= $totalSGST ?></td>
                <td class="fw-bold">Rs. <?= $totalIGST ?></td>
                <td class="fw-bold">Rs.  <?= $totalGST ?></td>
            </tr>
        </tfoot>
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

function getFormattedDate(date) {
  if (typeof date === 'string') {
    const d = new Date(date);
    return d.toLocaleDateString('en-GB').replace(/\//g, '-');
  }
  return date.toLocaleDateString('en-GB').replace(/\//g, '-');
}

// Get report range from PHP
const urlParams = new URLSearchParams(window.location.search);
const filter = urlParams.get('filter') || 'today';
let start, end;

if (filter === 'custom') {
  start = urlParams.get('start') || new Date().toISOString().slice(0, 10);
  end = urlParams.get('end') || new Date().toISOString().slice(0, 10);
} else {
  start = new Date().toISOString().slice(0, 10);
  end = new Date().toISOString().slice(0, 10);
}

// Generate proper label with uppercase brand name
const startLabel = getFormattedDate(start);
const endLabel = getFormattedDate(end);
const brandName = "ADA AROMAS";
const filename = `${brandName} Report - ${startLabel} to ${endLabel}`;


// ⬇ Download Excel
function downloadExcel() {
  const table = document.getElementById('reportTable');
  if (!table) return alert("Table not found");

  const clone = table.cloneNode(true);
  const rows = clone.querySelectorAll("tbody tr");

  // Replace ₹ with HTML entity (for Excel compatibility)
  rows.forEach(row => {
    row.innerHTML = row.innerHTML.replace(/₹/g, "&#8377;");
  });

  const html = `
    <html xmlns:o="urn:schemas-microsoft-com:office:office" 
          xmlns:x="urn:schemas-microsoft-com:office:excel" 
          xmlns="http://www.w3.org/TR/REC-html40">
    <head>
      <meta charset="utf-8">
    </head>
    <body>
      ${clone.outerHTML}
    </body>
    </html>`;

  const blob = new Blob([html], { type: 'application/vnd.ms-excel;charset=utf-8' });
  const url = URL.createObjectURL(blob);

  const a = document.createElement('a');
  a.href = url;
  a.download = filename + '.xls';
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
}

// ⬇ Download PDF
function downloadPDF() {
  const table = document.getElementById('reportTable');
  if (!table) return alert("Table not found");

  const content = `
    <html>
      <head>
        <title>${brandName} Report</title>
        <style>
          table { width: 100%; border-collapse: collapse; font-family: Arial; }
          th, td { border: 1px solid #333; padding: 8px; font-size: 13px; }
          th { background: #f0f0f0; }
          .report-title { 
            text-align: center; 
            font-size: 24px; 
            font-weight: bold; 
            margin-bottom: 10px;
            font-family: Arial;
          }
          .report-date {
            text-align: center;
            font-size: 16px;
            margin-bottom: 20px;
            font-family: Arial;
          }
        </style>
      </head>
      <body>
        <div class="report-title">${brandName} Report</div>
        <div class="report-date">Period: ${startLabel} to ${endLabel}</div>
        ${table.outerHTML}
      </body>
    </html>`;

  const w = window.open('', '', 'width=1000,height=800');
  w.document.write(content);
  w.document.close();
  w.focus();
  w.print();
}
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
