<?php 
include('../config/db.php'); 
session_start(); 
if (!isset($_SESSION['admin_logged_in'])) header('Location: index.php');

// === Fetch cancelled product summary ===
$sql_summary = "
  SELECT 
    p.name, 
    p.category, 
    SUM(od.quantity) AS totalCancelled
  FROM orders o
  JOIN order_details od ON o.orderId = od.orderId
  JOIN products p ON od.productId = p.productId
  WHERE o.status = 'Cancelled'
  GROUP BY p.name, p.category
  ORDER BY totalCancelled DESC
";
$stmt = $conn->query($sql_summary);
$summaryData = $stmt->fetchAll(PDO::FETCH_ASSOC);
$sql_sizes = "
  SELECT 
    p.name, 
    p.category, 
    ps.size, 
    p.asp
  FROM products p
  JOIN product_stock ps ON ps.productId = p.productId
";
$stmt3 = $conn->query($sql_sizes);
$sizeData = $stmt3->fetchAll(PDO::FETCH_ASSOC);


// === Fetch cancelled product details (sizes, asp etc.) ===
$sql_details = "
  SELECT 
    p.name, 
    p.category, 
    ps.size, 
    AVG(p.asp) AS asp, 
    SUM(od.quantity) AS qtyCancelled
  FROM orders o
  JOIN order_details od ON o.orderId = od.orderId
  JOIN products p ON od.productId = p.productId
  JOIN product_stock ps ON ps.productId = p.productId AND ps.size = od.size
  WHERE o.status = 'Cancelled'
  GROUP BY p.name, p.category, ps.size
";

$stmt2 = $conn->query($sql_details);
$detailData = $stmt2->fetchAll(PDO::FETCH_ASSOC);

$productSizes = [];
foreach ($sizeData as $row) {
  $key = $row['name'] . '|' . $row['category'];
  if (!isset($productSizes[$key])) {
    $productSizes[$key] = [];
  }
  $productSizes[$key][] = $row['size'] . " ML – ₹" . number_format($row['asp']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Cancellation Survey</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>

    body {
  background-color: #f8f9fa;
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
}

h2, h4 {
  font-weight: 600;
  color: #343a40;
}

.card {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  margin-bottom: 20px;
  padding: 20px;
}

canvas {
  max-height: 350px !important;
}

.table {
  background: #fff;
  border-radius: 6px;
  overflow: hidden;
  font-size: 0.9rem;
}

.table th {
  background: #343a40;
  color: #fff;
  font-weight: 500;
}

.table tbody tr:hover {
  background-color: #f1f3f5;
}

.dataTables_wrapper .dataTables_filter input {
  border-radius: 20px;
  padding: 5px 12px;
  border: 1px solid #ced4da;
}

.dataTables_wrapper .dataTables_length select {
  border-radius: 20px;
  padding: 4px 8px;
  border: 1px solid #ced4da;
}

@media (min-width: 992px) {
  .row-charts {
    display: flex;
    gap: 20px;
  }
  .row-charts > div {
    flex: 1;
  }
}


  </style>
</head>
<body>
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
      <li class="nav-item"><a class="nav-link" href="customize-orders.php">Customize Orders</a></li>
      <li class="nav-item"><a class="nav-link" href="stats.php">Stats</a></li>
      <li class="nav-item"><a class="nav-link" href="report.php">Report</a></li>
      <li class="nav-item"><a class="nav-link  active" href="cancel.php">Cancellation Survey</a></li>
      <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Logout</a></li>
    </ul>
  </div>
</nav>

<div class="container mt-4">
  <h2 class="mb-4">📊 Cancellation Survey</h2>

  <!-- Charts Row -->
  <div class="row-charts mb-4">
    <div class="card">
      <h5 class="mb-3">Cancelled Products Share</h5>
      <canvas id="pieChart"></canvas>
    </div>
    <div class="card">
      <h5 class="mb-3">Cancelled Products Count</h5>
      <canvas id="barChart"></canvas>
    </div>
  </div>

  <!-- Summary Table -->
  <div class="card">
    <h4>Summary of Cancelled Products</h4>
    <table id="summaryTable" class="table table-bordered table-striped mt-3">
      <thead>
        <tr>
          <th>Product</th>
          <th>Category</th>
          <th>Total Cancelled</th>
          <th>Available Sizes & Prices</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($summaryData as $row): 
          $key = $row['name'] . '|' . $row['category'];
          $sizes = isset($productSizes[$key]) ? implode("<br>", $productSizes[$key]) : 'N/A';
        ?>
          <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['category']) ?></td>
            <td><?= $row['totalCancelled'] ?></td>
            <td><?= $sizes ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Detailed Sizes Table -->
  <!-- <div class="card">
    <h4>Details by Size</h4>
    <table id="detailTable" class="table table-bordered table-striped mt-3">
      <thead>
        <tr>
          <th>Product</th>
          <th>Category</th>
          <th>Size</th>
          <th>Cancelled Qty</th>
          <th>ASP</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($detailData as $row): ?>
          <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['category']) ?></td>
            <td><?= $row['size'] ?> ML</td>
            <td><?= $row['qtyCancelled'] ?></td>
            <td>₹<?= number_format($row['asp']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div> -->
</div>


<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
// Prepare chart data
const summary = <?php echo json_encode($summaryData); ?>;

const labels = summary.map(s => s.name + " ("+ s.category +")");
const dataQty = summary.map(s => s.totalCancelled);

// Pie Chart
new Chart(document.getElementById("pieChart"), {
  type: 'pie',
  data: {
    labels: labels,
    datasets: [{
      data: dataQty,
      backgroundColor: labels.map((_,i)=>`hsl(${i*40},70%,60%)`)
    }]
  }
});

// Bar Chart
new Chart(document.getElementById("barChart"), {
  type: 'bar',
  data: {
    labels: labels,
    datasets: [{
      label: "Cancelled Qty",
      data: dataQty,
      backgroundColor: "rgba(54, 162, 235, 0.6)"
    }]
  },
  options: { responsive:true, scales:{y:{beginAtZero:true}} }
});

// Tables with sorting
$(document).ready(function(){
  $('#summaryTable').DataTable({
    order: [[2, 'desc']]
  });
  $('#detailTable').DataTable({
    order: [[3, 'desc']]
  });
});
</script>




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
