<?php
include('../config/db.php');
session_start();
if (!isset($_SESSION['admin_logged_in'])) header('Location: index.php');

// Fetch inventory data joined from products + product_stock
$stmt = $conn->query("
  SELECT p.productId, p.name, p.category, p.costPrice, p.margin, p.asp, p.msp, p.mrp, p.image,
         ps.stockId, ps.size, ps.stockInHand
  FROM products p
  JOIN product_stock ps ON p.productId = ps.productId
");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" /><title>Manage Stock</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .table-responsive { max-height: calc(100vh - 200px); overflow-y: auto; }
    td, th { vertical-align: middle; }
    .img-thumb { width:50px; height:50px; object-fit:cover; border-radius:5px; }
    @media(max-width:768px){
      .hide-mobile { display:none!important; }
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
      <li class="nav-item"><a class="nav-link active" href="manage-stock.php">Manage Stock</a></li>
      <li class="nav-item"><a class="nav-link" href="generate-coupon.php">Generate Coupon</a></li>
      <li class="nav-item"><a class="nav-link" href="orders.php">Orders</a></li>
      <li class="nav-item"><a class="nav-link" href="report.php">View Report</a></li>
      <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Logout</a></li>
    </ul>
  </div>
</nav>
  
  <div class="container-fluid mt-4">
    <h3>Manage Stock & Inventory</h3>
    <div class="row mb-3">
      <div class="col-md-4"><input type="text" id="searchInput" class="form-control" placeholder="Search products..."></div>
      <div class="col-md-3">
        <select id="filterCategory" class="form-select">
          <option value="">All Categories</option>
          <option>Perfume</option><option>Attar</option>
          <option>Essence Oil</option><option>Diffuser</option>
        </select>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-striped table-bordered align-middle">
        <thead class="table-light">
          <tr>
            <th>Image</th><th>Name</th><th>Size</th><th>Category</th>
            <th>Cost ₹</th><th>MSP ₹</th><th>% Margin</th><th>ASP ₹</th><th>% Disp. Mgn</th><th>MRP ₹</th><th>Stock</th><th>Actions</th>
          </tr>
        </thead>
        <tbody id="stockTableBody">
          <?php foreach ($rows as $r): ?>
          <tr data-id="<?= $r['stockId'] ?>" data-name="<?= strtolower($r['name']) ?>" data-category="<?= strtolower($r['category']) ?>">
            <td><img src="../<?= $r['image'] ?>" class="img-thumb"></td>
            <td><?= htmlspecialchars($r['name']) ?></td>
            <td><?= htmlspecialchars($r['size']) ?> ml</td>
            <td><?= htmlspecialchars($r['category']) ?></td>
            <td><input type="number" class="form-control form-control-sm costPrice" value="<?= $r['costPrice'] ?>"></td>
            <td><input type="text" class="form-control form-control-sm msp" value="<?= $r['msp'] ?>" readonly></td>
            <td><input type="number" class="form-control form-control-sm margin" value="<?= $r['margin'] ?>"></td> 
            <td><input type="text" class="form-control form-control-sm asp" value="<?= $r['asp'] ?>" readonly></td>
            <td><input type="number" class="form-control form-control-sm displayMargin" value="<?= round((($r['mrp'] - $r['asp']) / $r['asp']) * 100 / 5) * 5 ?>"></td>
            <td><input type="text" class="form-control form-control-sm mrp" value="<?= $r['mrp'] ?>" readonly></td>
            <td><input type="number" class="form-control form-control-sm stockInHand" value="<?= $r['stockInHand'] ?>"></td>
            <td class="text-nowrap">
              <button class="btn btn-sm btn-secondary apply-btn" disabled>Apply</button>
              <button class="btn btn-sm btn-danger remove-btn" data-id="<?= $r['stockId'] ?>">Remove</button>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Remove Confirmation Modal -->
  <div class="modal fade" id="confirmRemoveModal"><div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5>Confirm Removal</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">Are you sure you want to delete this item?</div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button id="confirmRemoveBtn" class="btn btn-danger">Yes, Remove</button>
      </div>
    </div>
  </div></div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function calculateASP(msp, margin) {
  let asp = msp + (msp * margin / 100);
  return Math.round(asp / 50) * 50;
}

function calculateMSP(cost) {
  // Fixed charges
  const courier = 60, box = 100, packing = 10, shelf = 30;
  return Math.round((cost + courier + box + packing + shelf) / 50) * 50;
}

function calculateMRP(asp, dispMargin) {
  let mrp = asp + (asp * dispMargin / 100);
  return Math.round(mrp / 50) * 50;
}

// Enable apply-btn only when a field changes
document.querySelectorAll('#stockTableBody tr').forEach(row => {
  const costInput = row.querySelector('.costPrice');
  const marginInput = row.querySelector('.margin');
  const displayMarginInput = row.querySelector('.displayMargin');
  const stockInput = row.querySelector('.stockInHand');
  const mspField = row.querySelector('.msp');
  const aspField = row.querySelector('.asp');
  const mrpField = row.querySelector('.mrp');
  const applyBtn = row.querySelector('.apply-btn');

  const initialValues = {
    cost: costInput.value,
    margin: marginInput.value,
    displayMargin: displayMarginInput.value,
    stock: stockInput.value
  };

  const enableApply = () => {
    const changed =
      costInput.value != initialValues.cost ||
      marginInput.value != initialValues.margin ||
      displayMarginInput.value != initialValues.displayMargin ||
      stockInput.value != initialValues.stock;

    applyBtn.disabled = !changed;
    applyBtn.classList.toggle('btn-success', changed);
    applyBtn.classList.toggle('btn-secondary', !changed);
  };

  // Bind change events
  [costInput, marginInput, displayMarginInput, stockInput].forEach(input => {
    input.addEventListener('input', () => {
      const cost = parseFloat(costInput.value) || 0;
      const msp = calculateMSP(cost);
        mspField.value = msp;
      const margin = parseFloat(marginInput.value) || 0;
      const asp = calculateASP(msp, margin);
      aspField.value = asp;

      const disp = parseFloat(displayMarginInput.value) || 0;
      mrpField.value = calculateMRP(asp, disp);

      enableApply();
    });
  });

  applyBtn.addEventListener('click', () => {
    const data = {
      stockId: row.dataset.id,
      costPrice: costInput.value,
      margin: marginInput.value,
      asp: aspField.value,
      displayMargin: displayMarginInput.value,
      mrp: mrpField.value,
      stockInHand: stockInput.value
    };
    fetch('handle-update-stock.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    }).then(res => res.json()).then(res => {
      if (res.success) {
        alert('Updated successfully');
        initialValues.cost = costInput.value;
        initialValues.margin = marginInput.value;
        initialValues.displayMargin = displayMarginInput.value;
        initialValues.stock = stockInput.value;
        enableApply();
      }
    });
  });
});

// Remove logic
let removeId = null;
document.querySelectorAll('.remove-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    removeId = btn.dataset.id;
    new bootstrap.Modal(document.getElementById('confirmRemoveModal')).show();
  });
});
document.getElementById('confirmRemoveBtn').addEventListener('click', () => {
  fetch('handle-remove.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ stockId: removeId })
  }).then(r => r.json()).then(() => location.reload());
});

// Search & Filter
function filterTable() {
  const term = document.getElementById('searchInput').value.toLowerCase();
  const cat = document.getElementById('filterCategory').value.toLowerCase();
  document.querySelectorAll('#stockTableBody tr').forEach(tr => {
    const name = tr.dataset.name;
    const category = tr.dataset.category;
    tr.style.display = (name.includes(term) && (cat === "" || category === cat)) ? '' : 'none';
  });
}
document.getElementById('searchInput').addEventListener('input', filterTable);
document.getElementById('filterCategory').addEventListener('change', filterTable);
</script>

</body>
</html>
