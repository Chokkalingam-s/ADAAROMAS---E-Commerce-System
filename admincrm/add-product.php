<?php include('../config/db.php'); session_start();
if (!isset($_SESSION['admin_logged_in'])) header('Location: index.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Add Product</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f2f2f2;
    }

    .form-section {
      max-width: 1100px;
      margin: 2rem auto;
      padding: 2rem;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 3px 15px rgba(0, 0, 0, 0.05);
    }

    .form-label {
      font-weight: 600;
    }


    .form-control,
  .form-select {
    text-align: center;
    font-weight: 500;
  }

    .readonly-box {
      background: #f8f9fa;
      padding: 0.65rem 1rem;
      border-radius: 5px;
      font-weight: 500;
      font-size: 1rem;
      text-align: center;
    }

    @media (max-width: 768px) {
      .form-control, .form-select {
        font-size: 1rem;
        padding: 0.75rem 1rem;
      }

      .form-section {
        padding: 1.5rem 1rem;
      }

      .readonly-box {
        font-size: 1rem;
        padding: 0.75rem;
      }

      .btn {
        font-size: 1rem;
        padding: 0.75rem;
      }

    /* Mobile: Stack in 2-column rows for tighter layout */
    .row .col-md-6, .row .col-md-3 {
      flex: 0 0 50%;
      max-width: 50%;
    }

    .row .col-12 {
      flex: 0 0 100%;
      max-width: 100%;
    }
    }

      @media (min-width: 769px) {
    /* Laptop layout specific override if needed */
    .form-control, .form-select {
      padding: 0.75rem 1rem;
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
      <li class="nav-item"><a class="nav-link active" href="add-product.php">Add Product</a></li>
      <li class="nav-item"><a class="nav-link" href="manage-stock.php">Manage Stock</a></li>
      <li class="nav-item"><a class="nav-link" href="generate-coupon.php">Generate Coupon</a></li>
      <li class="nav-item"><a class="nav-link" href="orders.php">Orders</a></li>
      <li class="nav-item"><a class="nav-link" href="report.php">View Report</a></li>
      <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Logout</a></li>
    </ul>
  </div>
</nav>

<div class="form-section">
  <h4 class="mb-4">Add Product to Inventory</h4>

  <form action="handle-add-product.php" method="POST" enctype="multipart/form-data" id="addProductForm">
    <div class="row g-3">
      
      <!-- Row 1: Product Name -->
      <div class="col-12 col-md-3">
        <label for="productName" class="form-label">PRODUCT NAME</label>
        <input type="text" name="productName" id="productName" class="form-control" placeholder="Start typing..." autocomplete="off" required>
        <div id="suggestionsBox" class="border rounded bg-white shadow-sm mt-1 position-absolute d-none" style="z-index:1000;"></div>
      </div>

      <!-- Row 2: Category + Gender -->
      <div class="col-6 col-md-3">
        <label for="category" class="form-label">CATEGORY</label>
        <select name="category" id="category" class="form-select" style="text-align: left;" required>
          <option value="">Select Category</option>
          <option>Perfume</option>
          <option>Attar</option>
          <option>Essence Oil</option>
          <option>Diffuser</option>
        </select>
      </div>

      <div class="col-6 col-md-3">
        <label for="gender" class="form-label">GENDER (for Perfume)</label>
        <select name="gender" id="gender" class="form-select" style="text-align: left;" >
          <option value="">Select Gender</option>
          <option>Men</option>
          <option>Women</option>
          <option>Unisex</option>
        </select>
      </div>

      <!-- Row 3: Image Upload -->
      <div class="col-12 col-md-3">
        <label class="form-label">UPLOAD PRODUCT IMAGE</label>
        <input type="file" name="productImage" accept="image/png, image/jpeg" class="form-control" required>
      </div>

      <!-- Row 4: Size | Purchase Cost | Initial Stock -->
      <div class="col-4 col-md-2">
        <label for="size" class="form-label">SIZE (ml)</label>
        <select name="size" id="size" class="form-select" required></select>
      </div>

      <div class="col-4 col-md-2">
        <label class="form-label" style="color:red;">PURCHASE (₹)</label>
        <input type="number" name="costPrice" id="costPrice" class="form-control" style="color:red;"required>
      </div>

      <div class="col-4 col-md-2">
        <label for="stock" class="form-label">INITIAL STOCK</label>
        <input type="number" name="stock" id="stock" class="form-control" required>
      </div>

      <!-- Row 5: Bottle | Box | Packing -->
      <div class="col-4 col-md-2">
        <label class="form-label">BOTTLE (₹)</label>
        <div class="readonly-box" id="bottlePrice">0</div>
      </div>

      <div class="col-4 col-md-2">
        <label class="form-label">BOX (₹)</label>
        <div class="readonly-box">200</div>
      </div>

      <div class="col-4 col-md-2">
        <label class="form-label">PACKING (₹)</label>
        <div class="readonly-box">10</div>
      </div>

      <!-- Row 6: Shelf | Courier | Marketing -->
      <div class="col-4 col-md-2">
        <label class="form-label">SHELF (₹)</label>
        <div class="readonly-box">30</div>
      </div>

      <div class="col-4 col-md-2">
        <label class="form-label">COURIER (₹)</label>
        <div class="readonly-box">60</div>
      </div>

      <div class="col-4 col-md-2">
        <label class="form-label">MARKETING (₹)</label>
        <select id="marketingCost" class="form-select">
          <option>50</option>
          <option>100</option>
          <option>150</option>
          <option>200</option>
          <option>250</option>
        </select>
      </div>

      <!-- Row 7: MSP | Margin | ASP | Revenue -->
      <div class="col-3 col-md-3">
        <label class="form-label" style="color:Blue;">MSP (₹)</label>
        <input type="text" name="msp" id="msp" class="form-control" style="color:Blue;" readonly required>
      </div>

      <div class="col-3 col-md-3">
        <label class="form-label">PROFIT MARGIN (%)</label>
        <select name="margin" id="margin" class="form-select" required>
          <option value="">Select</option>
          <option>50</option>
          <option>75</option>
          <option>100</option>
          <option>125</option>
          <option>150</option>
          <option>175</option>
          <option>200</option>
          <option>300</option>
        </select>
      </div>

      <div class="col-3 col-md-3">
        <label class="form-label" style="color:orange;">ASP (₹)</label>
        <input type="text" name="asp" id="asp" class="form-control" style="color:orange;" readonly required>
      </div>

      <div class="col-3 col-md-3">
        <label class="form-label" style="color:green;">REVENUE (₹)</label>
        <input type="text" name="revenue" id="revenue" class="form-control" style="color:green;" readonly required>
      </div>

      <!-- Row 8: Display Margin | Display Price -->
      <div class="col-6 col-md-3">
        <label class="form-label">DISPLAY MARGIN (%)</label>
        <select name="displayMargin" id="displayMargin" class="form-select" required>
          <option value="">Select</option>
          <option>40</option>
          <option>45</option>
          <option>50</option>
          <option>55</option>
          <option>60</option>
          <option>65</option>
          <option>70</option>
          <option>75</option>
          <option>80</option>
        </select>
      </div>

      <div class="col-6 col-md-3">
        <label class="form-label">DISPLAY PRICE (₹)</label>
        <input type="text" name="mrp" id="mrp" class="form-control" readonly required>
      </div>

      <!-- Row 9: Description -->
      <div class="col-12">
        <label class="form-label">DESCRIPTION / NOTES</label>
        <textarea name="description" rows="3" class="form-control" required></textarea>
      </div>

    </div>

    <div class="mt-4 text-end">
      <button type="submit" class="btn btn-primary px-4 py-2" id="submitBtn" disabled>ADD PRODUCT</button>
    </div>
  </form>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
  .then(data => {
    allProducts = data.map(p => p.toLowerCase()); // Store lowercase versions for matching
  });

const input = document.getElementById("productName");
const suggestions = document.getElementById("suggestionsBox");

input.addEventListener("input", () => {
  const val = input.value.trim().toLowerCase();

  // If empty input, hide suggestion box
  if (!val) {
    suggestions.classList.add("d-none");
    suggestions.innerHTML = "";
    return;
  }

  // Show ALL matches (no .slice())
  const filtered = allProducts
    .filter(p => p.includes(val))
    .map(p => capitalizeWords(p));

  if (filtered.length === 0) {
    suggestions.classList.add("d-none");
    suggestions.innerHTML = "";
    return;
  }

  suggestions.innerHTML = filtered.map(name =>
    `<div class="p-2 suggestion-item" style="cursor:pointer;">${name}</div>`
  ).join('');
  
  suggestions.classList.remove("d-none");
});

// Capitalize helper
function capitalizeWords(str) {
  return str.replace(/\b\w/g, c => c.toUpperCase());
}

// Click to select
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
  "Attar": [6, 12, 18, 24, 36],
  "Essence Oil": [30, 60, 90],
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

const genderGroup = document.getElementById("genderGroup");
const genderSelect = document.getElementById("gender");

document.getElementById("category").addEventListener("change", function () {
  const selected = this.value;

  // Toggle gender dropdown visibility
  if (selected === "Perfume") {
    genderGroup.classList.remove("d-none");
    genderSelect.setAttribute("required", "required");
  } else {
    genderGroup.classList.add("d-none");
    genderSelect.removeAttribute("required");
    genderSelect.value = ""; // reset selection
  }

  // Populate sizes (already existing logic)
  const sizeSelect = document.getElementById("size");
  sizeSelect.innerHTML = "";
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

["costPrice", "margin", "displayMargin", "category", "size", "marketingCost"].forEach(id => {
  document.getElementById(id).addEventListener("change", calculatePrices);
  document.getElementById(id).addEventListener("input", calculatePrices);
});


function calculatePrices() {
  const cost = parseFloat(document.getElementById("costPrice").value || 0);
  const margin = parseFloat(document.getElementById("margin").value || 0);
  const dispMargin = parseFloat(document.getElementById("displayMargin").value || 0);
  const revenue = parseFloat(document.getElementById("revenue").value || 0);
    const marketing = parseFloat(document.getElementById("marketingCost").value || 0);

  const category = document.getElementById("category").value;
  const size = parseInt(document.getElementById("size").value || 0);

  let bottle = 0;
  if (category === "Perfume") {
    if (size === 30) bottle = 30;
    else if (size === 50) bottle = 50;
    else if (size === 100) bottle = 60;
  } else if (category === "Attar") {
    bottle = 20;
  } else if (category === "Essence Oil") {
    bottle = 30;
  }
  document.getElementById("bottlePrice").innerText = bottle;
    // Fixed charges
  const courier = 60;
  const box = 200;
  const packing = 10;
  const shelf = 30;

  const totalCost = cost + courier + box + packing + shelf + bottle + marketing;
  const msp = totalCost;
  const asp = roundToNearest50(msp + margin/100*msp);
  const mrp = roundToNearest50(asp + (asp * dispMargin / 100));
  const totalRevenue = asp - msp

  document.getElementById("msp").value = msp;
  document.getElementById("asp").value = asp;
  document.getElementById("mrp").value = mrp;
  document.getElementById("revenue").value = totalRevenue;
}
</script>

</body>
</html>
