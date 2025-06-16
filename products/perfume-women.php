<?php
// products/perfume-Women.php
$category = "Perfume-Women";
$pageTitle = "Women's Perfume Collection";
$products = [
  ["title" => "Ombre Nomade", "image" => "../assets/images/image.png", "price" => 849, "mrp" => 1999, "discount" => 58, "rating" => 5.0, "reviews" => 1, "stock" => true, "date" => "2024-06-01"],
  ["title" => "Flora", "image" => "../assets/images/image.png", "price" => 849, "mrp" => 1999, "discount" => 58, "rating" => 4.04, "reviews" => 90, "stock" => true, "date" => "2024-05-28"],
  ["title" => "Aventus", "image" => "../assets/images/image.png", "price" => 849, "mrp" => 1999, "discount" => 58, "rating" => 4.09, "reviews" => 43, "stock" => false, "date" => "2024-04-10"],
  ["title" => "Creed Viking", "image" => "../assets/images/image.png", "price" => 849, "mrp" => 1999, "discount" => 58, "rating" => 4.5, "reviews" => 12, "stock" => true, "date" => "2024-03-15"],
  ["title" => "Tom Ford Noir", "image" => "../assets/images/image.png", "price" => 849, "mrp" => 1999, "discount" => 58, "rating" => 4.2, "reviews" => 25, "stock" => true, "date" => "2024-02-20"],
  ["title" => "Bleu de Chanel", "image" => "../assets/images/image.png", "price" => 849, "mrp" => 1999, "discount" => 58, "rating" => 4.8, "reviews" => 30, "stock" => false, "date" => "2024-01-05"],
  ["title" => "Dior Sauvage", "image" => "../assets/images/image.png", "price" => 849, "mrp" => 1999, "discount" => 58, "rating" => 4.7, "reviews" => 60, "stock" => true, "date" => "2023-12-15"],
  ["title" => "Yves Saint Laurent La Nuit", "image" => "../assets/images/image.png", "price" => 849, "mrp" => 1999, "discount" => 58, "rating" => 4.6, "reviews" => 20, "stock" => true, "date" => "2023-11-10"]
];

include "../components/header.php";
?>

<div class="container-fluid mt-4 mb-5">
  <h2 class="text-center mb-4"><?= $pageTitle ?></h2>

  <div class="row">
    <!-- Filter Sidebar -->
    <div class="col-md-3 mb-4 mb-md-0">
      <h5>Filters</h5>
      <div class="border rounded p-3 bg-light">
        <div class="mb-3">
          <h6 class="mb-2">Availability</h6>
          <div><input type="checkbox" id="inStock" checked> <label for="inStock">In stock</label></div>
          <div><input type="checkbox" id="outOfStock"> <label for="outOfStock">Out of stock</label></div>
        </div>

        <div>
          <h6 class="mb-2">Price</h6>
          <input type="range" class="form-range" min="0" max="2000" id="priceRange" step="10">
          <div class="d-flex justify-content-between mt-2">
            <input type="text" id="minPrice" class="form-control w-45" value="0" readonly>
            <span class="mx-2">to</span>
            <input type="text" id="maxPrice" class="form-control w-45" value="2000" readonly>
          </div>
        </div>
      </div>
    </div>

    <!-- Products Grid -->
    <div class="col-md-9">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <span><strong id="productCount"></strong></span>
        <select class="form-select w-auto" id="sortSelect">
          <option value="best" selected>Best selling</option>
          <option value="low">Price, low to high</option>
          <option value="high">Price, high to low</option>
          <option value="az">Alphabetically, A-Z</option>
          <option value="za">Alphabetically, Z-A</option>
          <option value="old">Date, old to new</option>
          <option value="new">Date, new to old</option>
        </select>
      </div>

      <div class="row g-4" id="productGrid">
        <!-- Product cards will be rendered by JS -->
      </div>
    </div>
  </div>
</div>

<script>
const products = <?= json_encode($products) ?>;

function renderProducts(productList) {
  const grid = document.getElementById("productGrid");
  const count = document.getElementById("productCount");
  grid.innerHTML = "";
  count.textContent = `${productList.length} products`;
  productList.forEach(p => {
    const card = document.createElement("div");
    card.className = "col-sm-6 col-md-4 col-lg-3";
    card.innerHTML = `
      <div class="product-card shadow position-relative overflow-hidden rounded">
        <div class="badge bg-dark position-absolute top-0 start-0 m-2 px-2 py-1 fs-6 text-white">SAVE ${p.discount}%</div>
        <div class="product-image-wrapper position-relative">
          <img src="${p.image}" class="img-fluid w-100 product-img" alt="${p.title}">
          <button class="add-to-cart-btn btn btn-light fw-bold">+ Add to cart</button>
        </div>
        <div class="text-center mt-3">
          <h5 class="mb-1">${p.title}</h5>
          <p class="mb-1 fs-6">
            Rs. ${p.price} <span class="text-muted text-decoration-line-through fs-6">Rs. ${p.mrp}</span>
          </p>
          <p class="text-warning mb-1">
            ⭐ ${p.rating} <span class="text-primary">| <i class="bi bi-patch-check-fill"></i> (${p.reviews} Reviews)</span>
          </p>
        </div>
      </div>`;
    grid.appendChild(card);
  });
}

function applyFilters() {
  let filtered = [...products];
  const inStock = document.getElementById("inStock").checked;
  const outOfStock = document.getElementById("outOfStock").checked;
  const priceLimit = parseInt(document.getElementById("priceRange").value);

  filtered = filtered.filter(p => (p.price <= priceLimit));
  if (!inStock) filtered = filtered.filter(p => !p.stock);
  if (!outOfStock) filtered = filtered.filter(p => p.stock);

  const sort = document.getElementById("sortSelect").value;
  if (sort === "low") filtered.sort((a, b) => a.price - b.price);
  if (sort === "high") filtered.sort((a, b) => b.price - a.price);
  if (sort === "az") filtered.sort((a, b) => a.title.localeCompare(b.title));
  if (sort === "za") filtered.sort((a, b) => b.title.localeCompare(a.title));
  if (sort === "old") filtered.sort((a, b) => new Date(a.date) - new Date(b.date));
  if (sort === "new") filtered.sort((a, b) => new Date(b.date) - new Date(a.date));

  renderProducts(filtered);
}

document.addEventListener("DOMContentLoaded", () => {
  document.getElementById("priceRange").addEventListener("input", e => {
    document.getElementById("maxPrice").value = e.target.value;
    applyFilters();
  });

  document.getElementById("inStock").addEventListener("change", applyFilters);
  document.getElementById("outOfStock").addEventListener("change", applyFilters);
  document.getElementById("sortSelect").addEventListener("change", applyFilters);

  applyFilters();
});
</script>

<?php include "../components/footer.php"; ?>