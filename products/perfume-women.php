<?php
// products/perfume-women.php
$category = "Perfume-Women";
$pageTitle = "Women's Perfume Collection";

$products = [
  ["title" => "Ombre Nomade", "image" => "../assets/images/image.png", "price" => 849, "mrp" => 1999, "rating" => 5.0, "reviews" => 1, "stock" => true, "date" => "2024-06-01"],
  ["title" => "Flora", "image" => "../assets/images/image.png", "price" => 949, "mrp" => 1999,  "rating" => 4.04, "reviews" => 90, "stock" => true, "date" => "2024-05-28"],
  ["title" => "Aventus", "image" => "../assets/images/image.png", "price" => 749, "mrp" => 1999,"rating" => 4.09, "reviews" => 43, "stock" => false, "date" => "2024-04-10"],
  ["title" => "Creed Viking", "image" => "../assets/images/image.png", "price" => 849, "mrp" => 1999,  "rating" => 4.5, "reviews" => 12, "stock" => true, "date" => "2024-03-15"],
  ["title" => "Tom Ford Noir", "image" => "../assets/images/image.png", "price" => 849, "mrp" => 1999, "rating" => 4.2, "reviews" => 25, "stock" => true, "date" => "2024-02-20"],
  ["title" => "Bleu de Chanel", "image" => "../assets/images/image.png", "price" => 849, "mrp" => 1999,  "rating" => 4.8, "reviews" => 30, "stock" => false, "date" => "2024-01-05"],
  ["title" => "Dior Sauvage", "image" => "../assets/images/image.png", "price" => 849, "mrp" => 1999,  "rating" => 4.7, "reviews" => 60, "stock" => true, "date" => "2023-12-15"],
  ["title" => "Yves Saint Laurent", "image" => "../assets/images/image.png", "price" => 849, "mrp" => 1999,  "rating" => 4.6, "reviews" => 20, "stock" => true, "date" => "2023-11-10"]
];

$inStock = $_GET['inStock'] ?? '1';
$outOfStock = $_GET['outOfStock'] ?? '1';
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
              In stock <span class="text-muted ms-2">(<?= count(array_filter($products, fn($p) => $p['stock'])) ?>)</span>
            </label>
            <label class="form-check-label d-flex align-items-center">
              <input class="form-check-input me-2" type="checkbox" name="outOfStock" value="1" <?= $outOfStock == '1' ? 'checked' : '' ?>>
              Out of stock <span class="text-muted ms-2">(<?= count(array_filter($products, fn($p) => !$p['stock'])) ?>)</span>
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