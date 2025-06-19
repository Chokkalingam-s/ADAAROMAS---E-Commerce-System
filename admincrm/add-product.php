<?php include('../config/db.php'); session_start();
if (!isset($_SESSION['admin_logged_in'])) header('Location: index.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Add Product</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .form-section { max-width: 800px; margin: auto; padding: 2rem; background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    .form-section h4 { margin-bottom: 1.5rem; }
    .readonly-box { background: #f8f9fa; padding: 0.5rem 1rem; border-radius: 5px; }
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
      <li class="nav-item"><a class="nav-link active" href="add-product.php">Add Product</a></li>
      <li class="nav-item"><a class="nav-link" href="manage-stock.php">Manage Stock</a></li>
      <li class="nav-item"><a class="nav-link" href="generate-coupon.php">Generate Coupon</a></li>
      <li class="nav-item"><a class="nav-link" href="orders.php">Orders</a></li>
      <li class="nav-item"><a class="nav-link" href="report.php">View Report</a></li>
      <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Logout</a></li>
    </ul>
  </div>
</nav>

<div class="form-section mt-5">
  <h4>Add Product to Inventory</h4>
<form action="handle-add-product.php" method="POST" id="addProductForm" enctype="multipart/form-data" novalidate>
    <!-- Product Name -->
    <div class="mb-3 position-relative" style="z-index:999;">
        <label for="productName" class="form-label">Product Name</label>
        <input type="text" name="productName" id="productName" class="form-control" placeholder="Start typing product name..." autocomplete="off" required>
        <div id="suggestionsBox" class="border rounded bg-white shadow-sm mt-1 position-absolute w-100 d-none" style="z-index:1000;"></div>
    </div>

    <!-- Category -->
    <div class="mb-3">
        <label for="category" class="form-label">Category</label>
        <select name="category" id="category" class="form-select" required>
            <option value="">Select Category</option>
            <option value="Perfume">Perfume</option>
            <option value="Attar">Attar</option>
            <option value="Essence Oil">Essence Oil</option>
            <option value="Diffuser">Diffuser</option>
        </select>
    </div>

    <!-- Size (dynamic) -->
    <div class="mb-3">
        <label for="size" class="form-label">Size (ml)</label>
        <select name="size" id="size" class="form-select" required></select>
    </div>

       <div class="mb-3">
      <label for="stock" class="form-label">Initial Stock</label>
      <input type="number" name="stock" id="stock" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Upload Product Image (jpg/png)</label>
        <input type="file" name="productImage" accept="image/png, image/jpeg" class="form-control" required>
    </div>
    <!-- Cost Price -->
    <div class="mb-3">
        <label for="costPrice" class="form-label">₹ Cost Price</label>
        <input type="number" name="costPrice" id="costPrice" class="form-control" required>
    </div>

    <!-- Fixed Charges -->
    <div class="row mb-3">
        <div class="col"><label class="form-label">Courier (₹)</label><div class="readonly-box">60</div></div>
        <div class="col"><label class="form-label">Box (₹)</label><div class="readonly-box">100</div></div>
        <div class="col"><label class="form-label">Packing (₹)</label><div class="readonly-box">10</div></div>
        <div class="col"><label class="form-label">Shelf (₹)</label><div class="readonly-box">30</div></div>
    </div>

    <!-- MSP -->
    <div class="mb-3">
        <label class="form-label">₹ Minimum Selling Price (MSP)</label>
        <input type="text" name="msp" id="msp" class="form-control" readonly required>
    </div>

    <!-- Margin -->
    <div class="mb-3">
        <label class="form-label">Margin (%)</label>
        <select name="margin" id="margin" class="form-select" required>
            <option value="">Select Margin</option>
            <option>200</option><option>300</option><option>400</option><option>500</option>
        </select>
    </div>

    <!-- ASP -->
    <div class="mb-3">
        <label class="form-label">₹ Actual Selling Price (ASP)</label>
        <input type="text" name="asp" id="asp" class="form-control" readonly required>
    </div>

    <!-- Display Price Margin -->
    <div class="mb-3">
        <label class="form-label">Display Price Margin (%)</label>
        <select name="displayMargin" id="displayMargin" class="form-select" required>
            <option value="">Select Margin</option>
            <option>40</option><option>45</option><option>50</option><option>55</option><option>60</option><option>65</option><option>70</option><option>75</option>
        </select>
    </div>

    <!-- MRP -->
    <div class="mb-3">
        <label class="form-label">₹ MRP</label>
        <input type="text" name="mrp" id="mrp" class="form-control" readonly required>
    </div>

    <!-- Description -->
    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" rows="4" class="form-control" required></textarea>
    </div>

    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>Add Product</button>
</form>
</div>
<script>
    // Enable submit only if all required fields are filled
    const form = document.getElementById('addProductForm');
    const submitBtn = document.getElementById('submitBtn');

    function checkFormValidity() {
        // HTML5 validation
        if (form.checkValidity()) {
            submitBtn.disabled = false;
        } else {
            submitBtn.disabled = true;
        }
    }

    // Listen to input/change events on all form elements
    form.addEventListener('input', checkFormValidity);
    form.addEventListener('change', checkFormValidity);

    // Initial check
    document.addEventListener('DOMContentLoaded', checkFormValidity);
</script>


<script>
const productList = document.getElementById("productList");
const productInput = document.getElementById("productName");

let allProducts = [];

fetch('assets/products.json')
  .then(res => res.json())
  .then(data => allProducts = data);

const input = document.getElementById("productName");
const suggestions = document.getElementById("suggestionsBox");

input.addEventListener("input", () => {
  const val = input.value.toLowerCase();
  if (!val) return suggestions.classList.add("d-none");

  const filtered = allProducts.filter(p => p.toLowerCase().includes(val)).slice(0, 8);

  if (filtered.length === 0) {
    suggestions.classList.add("d-none");
    return;
  }

  suggestions.innerHTML = filtered.map(name => `<div class="p-2 suggestion-item" style="cursor:pointer;">${name}</div>`).join('');
  suggestions.classList.remove("d-none");
});

document.addEventListener("click", (e) => {
  if (e.target.classList.contains("suggestion-item")) {
    input.value = e.target.innerText;
    suggestions.classList.add("d-none");
  } else if (!input.contains(e.target)) {
    suggestions.classList.add("d-none");
  }
});


// Category to sizes
const sizes = {
  "Perfume": [30, 50, 100],
  "Attar": [6, 12, 20, 30, 50],
  "Essence Oil": [30, 60, 90, 120],
  "Diffuser": []
};

document.getElementById("category").addEventListener("change", function () {
  const sizeSelect = document.getElementById("size");
  sizeSelect.innerHTML = "";
  const selected = this.value;
  if (sizes[selected]) {
    sizes[selected].forEach(sz => {
      const opt = document.createElement("option");
      opt.value = sz;
      opt.textContent = `${sz} ml`;
      sizeSelect.appendChild(opt);
    });
    if (selected === "Diffuser") {
      const opt = document.createElement("option");
      opt.value = "0";
      opt.textContent = "N/A";
      sizeSelect.appendChild(opt);
    }
  }
});

// Price Calculations
function roundToNearest50(value) {
  return Math.round(value / 50) * 50;
}

document.getElementById("costPrice").addEventListener("input", calculatePrices);
document.getElementById("margin").addEventListener("change", calculatePrices);
document.getElementById("displayMargin").addEventListener("change", calculatePrices);

function calculatePrices() {
  const cost = parseFloat(document.getElementById("costPrice").value || 0);
  const margin = parseFloat(document.getElementById("margin").value || 0);
  const dispMargin = parseFloat(document.getElementById("displayMargin").value || 0);

  const msp = cost + 200;
  const asp = roundToNearest50(msp + margin/100*msp);
  const mrp = roundToNearest50(asp + (asp * dispMargin / 100));

  document.getElementById("msp").value = msp;
  document.getElementById("asp").value = asp;
  document.getElementById("mrp").value = mrp;
}
</script>

</body>
</html>
