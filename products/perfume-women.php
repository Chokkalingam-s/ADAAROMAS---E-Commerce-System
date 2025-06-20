<?php
include('../config/db.php');

// Set your desired category, e.g. Perfume, Attar, Essence Oil, etc.
$category = 'Perfume'; // Or 'Perfume'/'Perfume-Men'/'Perfume-Women'/...
$pageTitle = "Perfume Collection";
$gender = 'Women';
$stmt = $conn->prepare("
  SELECT 
    p.productId, p.name, p.category, p.asp, p.mrp, p.image, p.rating, p.reviewCount,
    ps.size, ps.stockInHand
  FROM products p
  JOIN product_stock ps ON p.productId = ps.productId
  WHERE p.category = ?  AND p.gender = ?
  ORDER BY p.name ASC, ps.size ASC
");
$stmt->execute([$category, $gender]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// STEP 1: Group by name-category (lowest size used for display, but stock from all sizes)
$grouped = [];
$stockMap = [];

foreach ($rows as $row) {
  $key = strtolower(trim($row['name'])) . '_' . strtolower(trim($row['category']));
  
  if (!isset($grouped[$key])) {
    $grouped[$key] = [
      "title" => $row['name'],
      "image" => "../" . $row['image'],
      "price" => (int)$row['asp'],
      "mrp" => (int)$row['mrp'],
      "rating" => $row['rating'],
      "reviews" => $row['reviewCount'],
      "stock" => false, // default
      "date" => $row['created_at'] ?? '2024-01-01'
    ];
  }

  // track if at least one size is in stock
  if (!isset($stockMap[$key])) $stockMap[$key] = [];
  $stockMap[$key][] = (int)$row['stockInHand'];
}

// STEP 2: Mark stock status
foreach ($grouped as $key => &$item) {
  $hasStock = array_filter($stockMap[$key], fn($v) => $v > 0);
  $item['stock'] = count($hasStock) > 0;
}
unset($item); // break reference

$products = array_values($grouped);

// STOCK COUNTERS
$stockInHand = 0;
$stockOutOfHand = 0;
foreach ($products as $p) {
  if ($p['stock']) $stockInHand++;
  else $stockOutOfHand++;
}

// FILTER
$inStock = $_GET['inStock'] ?? '1';
$outOfStock = $_GET['outOfStock'] ?? '0';
$min = $_GET['min'] ?? 0;
$max = $_GET['max'] ?? 2000;

$filteredProducts = array_filter($products, function ($p) use ($inStock, $outOfStock, $min, $max) {
  $stockStatus = $p['stock'] ? 'in' : 'out';
  $stockMatch = ($stockStatus === 'in' && $inStock === '1') || ($stockStatus === 'out' && $outOfStock === '1');
  return $stockMatch && $p['price'] >= $min && $p['price'] <= $max;
});

include "../components/header.php";
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.js"></script>

<div class="container-fluid mt-4 mb-5">
  <h2 class="text-center mb-4"><?= $pageTitle ?></h2>
  <div class="row">
    <div class="col-md-3 mb-4 mb-md-0">
      <h4 class="fw-semibold mb-3">Filters</h4>
      <div class="filter-tags mb-3">
        <?php if ($min > 0 || $max < 2000): ?>
          <span class="filter-tag">
            Price: Rs. <?= $min ?> – Rs. <?= $max ?>
            <a href="?inStock=<?= $inStock ?>&outOfStock=<?= $outOfStock ?>" class="filter-tag-close" title="Remove filter">&times;</a>
          </span>
        <?php endif; ?>
      </div>
      <form method="GET" id="filterForm">
        <div class="border rounded p-3 bg-white shadow-sm">
          <div class="mb-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span class="fw-semibold">Availability</span>
              <span class="chevron">&#9660;</span>
            </div>
<label class="form-check-label d-flex align-items-center mb-1">
  <input class="form-check-input me-2" type="checkbox" name="inStock" value="1" <?= $inStock == '1' ? 'checked' : '' ?>>
  In stock <span class="text-muted ms-2">(<?= $stockInHand ?>)</span>
</label>

<label class="form-check-label d-flex align-items-center">
  <input class="form-check-input me-2" type="checkbox" name="outOfStock" value="1" <?= $outOfStock == '1' ? 'checked' : '' ?>>
  Out of stock <span class="text-muted ms-2">(<?= $stockOutOfHand ?>)</span>
</label>

          </div>
          <div class="mb-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span class="fw-semibold">Price</span>
              <span class="chevron">&#9660;</span>
            </div>
            <div id="priceSlider"></div>
            <div class="d-flex justify-content-between mt-2">
              <div class="price-box">₹ <span id="minVal"><?= $min ?></span></div>
              <span>to</span>
              <div class="price-box">₹ <span id="maxVal"><?= $max ?></span></div>
            </div>
            <input type="hidden" id="min" name="min" value="<?= $min ?>">
            <input type="hidden" id="max" name="max" value="<?= $max ?>">
          </div>
        </div>
      </form>
    </div>
    <div class="col-md-9">
      <div class="row g-4" id="productGrid">
        <?php
        $sortOrder = $_GET['sort'] ?? 'best';
        if ($sortOrder === 'low') usort($filteredProducts, fn($a, $b) => $a['price'] <=> $b['price']);
        if ($sortOrder === 'high') usort($filteredProducts, fn($a, $b) => $b['price'] <=> $a['price']);
        if ($sortOrder === 'az') usort($filteredProducts, fn($a, $b) => strcmp($a['title'], $b['title']));
        if ($sortOrder === 'za') usort($filteredProducts, fn($a, $b) => strcmp($b['title'], $a['title']));
        if ($sortOrder === 'old') usort($filteredProducts, fn($a, $b) => strtotime($a['date']) <=> strtotime($b['date']));
        if ($sortOrder === 'new') usort($filteredProducts, fn($a, $b) => strtotime($b['date']) <=> strtotime($a['date']));

        foreach ($filteredProducts as $p): 
          extract($p);
          $discount = round((($mrp - $price) / $mrp) * 100);
          $inStock = $p['stock'];
          include "../components/product-card.php";
        endforeach;
        ?>
      </div>
    </div>
  </div>
</div>

<script>
  const minVal = document.getElementById('minVal');
  const maxVal = document.getElementById('maxVal');
  const minInput = document.getElementById('min');
  const maxInput = document.getElementById('max');
  const slider = document.getElementById('priceSlider');
  const form = document.getElementById('filterForm');

  noUiSlider.create(slider, {
    start: [parseInt(minInput.value), parseInt(maxInput.value)],
    connect: true,
    step: 10,
    range: {
      min: 0,
      max: 2000
    },
    tooltips: false,
    format: {
      to: value => Math.round(value),
      from: value => Number(value)
    }
  });

  slider.noUiSlider.on('update', (values, handle) => {
    const [minValNum, maxValNum] = values;
    minVal.innerText = minValNum;
    maxVal.innerText = maxValNum;
    minInput.value = minValNum;
    maxInput.value = maxValNum;
  });

  slider.noUiSlider.on('change', () => form.submit());

  document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
    cb.addEventListener('change', () => form.submit());
  });
</script>

<?php include "../components/footer.php"; ?>