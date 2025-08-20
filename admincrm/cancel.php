<?php 
include('../config/db.php'); 
session_start(); 
if (!isset($_SESSION['admin_logged_in'])) header('Location: index.php');

// === Fetch cancelled product summary ===
$sql_summary = "
  SELECT 
    p.name, 
    p.category, 
    SUM(od.quantity) AS totalCancelled, 
    AVG(p.asp) AS avgASP
  FROM orders o
  JOIN order_details od ON o.orderId = od.orderId
  JOIN products p ON od.productId = p.productId
  WHERE o.status = 'Cancelled'
  GROUP BY p.name, p.category
  ORDER BY totalCancelled DESC
";

$stmt = $conn->query($sql_summary);
$summaryData = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Cancellation Survey</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>

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
      <li class="nav-item"><a class="nav-link" href="stats.php">Stats</a></li>
      <li class="nav-item"><a class="nav-link" href="report.php">Report</a></li>
      <li class="nav-item"><a class="nav-link  active" href="cancel.php">Cancellation Survey</a></li>
      <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Logout</a></li>
    </ul>
  </div>
</nav>

<div class="container mt-4">
  <h2 class="mb-4">Cancellation Survey</h2>

  <!-- Row for charts -->
  <div class="row">
    <div class="col-md-6">
      <canvas id="pieChart"></canvas>
    </div>
    <div class="col-md-6">
      <canvas id="barChart"></canvas>
    </div>
  </div>

  <!-- Summary Table -->
  <h4 class="mt-5">Summary of Cancelled Products</h4>
  <table id="summaryTable" class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>Product</th>
        <th>Category</th>
        <th>Total Cancelled</th>
        <th>ASP</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($summaryData as $row): ?>
        <tr>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['category']) ?></td>
          <td><?= $row['totalCancelled'] ?></td>
          <td><?= number_format($row['avgASP']) ?></td>

        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <!-- Detailed Sizes Table -->
  <h4 class="mt-5">Details by Size</h4>
  <table id="detailTable" class="table table-bordered table-striped">
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
          <td><?= $row['size'] ?></td>
          <td><?= $row['qtyCancelled'] ?></td>
          <td><?= number_format($row['asp'],2) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
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
