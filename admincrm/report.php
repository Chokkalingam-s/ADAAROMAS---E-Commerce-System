<?php
include('../config/db.php');
session_start();
date_default_timezone_set('Asia/Kolkata');
if (!isset($_SESSION['admin_logged_in'])) header('Location: index.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sales Report - ADA Aromas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
  body {
    font-size: 14px;
  }
  .metric-box {
    background: #f8f9fa;
    padding: 12px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 0 5px #ddd;
    min-height: 90px;
  }
  .metric-box h5 {
    font-size: 0.9rem;
    margin-bottom: 5px;
  }
  .metric-box h4 {
    font-size: 1.1rem;
    margin: 0;
  }
  .chart-container {
    background: #fff;
    padding: 12px;
    border-radius: 10px;
    box-shadow: 0 0 5px #ddd;
    height: 100%;
  }
  canvas {
    max-height: 250px !important;
  }

  * 📱 Mobile styles */
@media (max-width: 768px) {
  .row.g-2 > .col-md-3 {
    flex: 0 0 50%;
    max-width: 50%;
  }

  .row.g-2 > .col-md-6 {
    flex: 0 0 100%;
    max-width: 100%;
  }

.chart-container {
    padding: 10px;
    margin-bottom: 10px;
  }

  canvas {
    max-height: 200px !important;
    width: 100% !important;
  }

  .navbar-brand {
    font-size: 1rem;
  }
}

@media (max-width: 576px) {
  .metric-box {
    padding: 10px;
    font-size: 0.9rem;
  }

  .metric-box h5 { font-size: 0.85rem; }
  .metric-box h4 { font-size: 1rem; }

  .chart-container {
    padding: 12px;
    margin-bottom: 12px;
  }

  canvas {
    max-height: 200px !important;
    width: 100% !important;
  }

  .navbar-brand {
    font-size: 1rem;
  }

  h5.mb-3 {
    font-size: 1rem;
  }

  .form-select.w-auto {
    font-size: 0.85rem;
  }
}

</style>

</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3">
  <a class="navbar-brand" href="../admincrm">ADA Aromas Admin</a>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="adminNav">
    <ul class="navbar-nav ms-auto">
      <li class="nav-item"><a class="nav-link " href="add-product.php">Add Product</a></li>
      <li class="nav-item"><a class="nav-link" href="manage-stock.php">Manage Stock</a></li>
      <li class="nav-item"><a class="nav-link" href="generate-coupon.php">Generate Coupon</a></li>
      <li class="nav-item"><a class="nav-link" href="orders.php">Orders</a></li>
      <li class="nav-item"><a class="nav-link active" href="report.php">View Report</a></li>
      <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Logout</a></li>
    </ul>
  </div>
</nav>

<div class="container-fluid p-3">
  <h4 class="mb-3">📊 Sales & Profit Report</h4>

  <!-- Metrics Grid -->
  <div class="row g-2 mb-3">
    <div class="col-6 col-md-3 mb-3"><div class="metric-box"><h5>Stock in Hand</h5><h4 id="stockInHand">₹0</h4></div></div>
    <div class="col-6 col-md-3 mb-3"><div class="metric-box"><h5>Total Revenue</h5><h4 id="totalRevenue">₹0</h4></div></div>
    <div class="col-6 col-md-3 mb-3"><div class="metric-box"><h5>Profit</h5><h4 id="totalProfit">₹0</h4></div></div>
    <div class="col-6 col-md-3 mb-3"><div class="metric-box"><h5>Yearly Sales</h5><h4 id="yearlySales">₹0</h4></div></div>
  </div>

  <!-- Charts Row -->
  <div class="row g-2 mb-3">
    <div class="col-md-6 col-12 mb-4 chart-container">
      <h6 class="mb-2">🧴 Category-wise Sales</h6>
      <canvas id="categoryPie"></canvas>
    </div>
    <div class="col-md-6 col-12 mb-4 chart-container">
      <h6 class="mb-2">🔥 Top Products Sold</h6>
      <canvas id="productPie"></canvas>
    </div>
  </div>

  <!-- Monthly Sales Chart -->
  <div class="row g-2">
    <div class="col-12 mb-4 chart-container">
      <div class="mb-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0">📈 Monthly Sales Trend</h6>
        <select id="yearFilter" class="form-select form-select-sm w-auto">
          <?php for ($y = 2025; $y <= 2040; $y++): ?>
            <option value="<?= $y ?>" <?= $y == date('Y') ? 'selected' : '' ?>><?= $y ?></option>
          <?php endfor; ?>
        </select>
      </div>
      <canvas id="monthlyBar"></canvas>
    </div>
  </div>
</div>


<script>
async function fetchReportData(year = new Date().getFullYear()) {
  const resp = await fetch('fetch-report-data.php?year=' + year);
  const data = await resp.json();

  document.getElementById('stockInHand').textContent = '₹' + data.stockInHand;
  document.getElementById('totalRevenue').textContent = '₹' + data.totalRevenue;
  document.getElementById('totalProfit').textContent = '₹' + data.totalProfit;
  document.getElementById('yearlySales').textContent = '₹' + data.yearlySales;

  loadPieChart('categoryPie', data.categorySales);
  loadPieChart('productPie', data.productSales);
  loadBarChart('monthlyBar', data.monthlySales);
}

function loadPieChart(canvasId, chartData) {
  new Chart(document.getElementById(canvasId), {
    type: 'pie',
    data: {
      labels: Object.keys(chartData),
      datasets: [{
        data: Object.values(chartData),
        backgroundColor: ['#6f42c1', '#198754', '#fd7e14', '#0dcaf0', '#dc3545']
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'bottom' }
      }
    }
  });
}

function loadBarChart(canvasId, chartData) {
  new Chart(document.getElementById(canvasId), {
    type: 'bar',
    data: {
      labels: Object.keys(chartData),
      datasets: [{
        label: 'Sales (₹)',
        data: Object.values(chartData),
        backgroundColor: '#0d6efd'
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          ticks: { callback: val => '₹' + val }
        }
      }
    }
  });
}

document.getElementById('yearFilter').addEventListener('change', e => {
  document.getElementById('monthlyBar').remove(); // remove old canvas
  const newCanvas = document.createElement('canvas');
  newCanvas.id = 'monthlyBar';
  document.querySelector('.chart-container:last-child').appendChild(newCanvas);
  fetchReportData(e.target.value);
});

fetchReportData();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
