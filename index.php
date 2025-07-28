<?php include "components/header.php"; ?>

  <!-- Banner -->
  
<!-- Banner Carousel -->
<?php
$bannerDir = __DIR__ . "/BannerImages";
$bannerFiles = array_values(array_filter(scandir($bannerDir), function ($f) {
  return preg_match('/\.(jpg|jpeg|png|webp)$/i', $f);
}));
?>

<?php if (count($bannerFiles) > 0): ?>
<div id="bannerCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="4000">
  <div class="carousel-inner">
    <?php foreach ($bannerFiles as $index => $file): ?>
      <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
        <img src="BannerImages/<?= htmlspecialchars($file) ?>" class="d-block w-100 img-fluid" alt="Banner <?= $index + 1 ?>" style="object-fit: cover; max-height: 600px;">
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Optional: Carousel controls -->
  <?php if (count($bannerFiles) > 1): ?>
  <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
  </button>
  <?php endif; ?>
</div>
<?php endif; ?>

  

  <!-- Featured Products -->
<?php
include "config/db.php";

// Get top 8 best-selling productIds based on how many times they appear in orders
$topStmt = $conn->prepare("
  SELECT od.productId, COUNT(*) as saleCount
  FROM order_details od
  GROUP BY od.productId
  ORDER BY saleCount DESC
  LIMIT 8
");
$topStmt->execute();
$topSellingIds = $topStmt->fetchAll(PDO::FETCH_COLUMN);

// If fewer than 8 best-sellers, fill remaining with random products
$productIds = $topSellingIds;
$needed = 8 - count($productIds);

if ($needed > 0) {
  $placeholders = $productIds ? implode(',', $productIds) : '0';
  $randStmt = $conn->prepare("
    SELECT p.productId
    FROM products p
    WHERE p.productId NOT IN ($placeholders)
    ORDER BY RAND()
    LIMIT $needed
  ");
  $randStmt->execute();
  $randomIds = $randStmt->fetchAll(PDO::FETCH_COLUMN);
  $productIds = array_merge($productIds, $randomIds);
}

// Fetch product details for final productId list
if (count($productIds)) {
  $placeholders = implode(',', array_fill(0, count($productIds), '?'));
  $prodStmt = $conn->prepare("
    SELECT p.*, ps.stockInHand, ps.size
    FROM products p
    JOIN product_stock ps ON p.productId = ps.productId
    WHERE p.productId IN ($placeholders)
  ");
  $prodStmt->execute($productIds);
  $products = $prodStmt->fetchAll(PDO::FETCH_ASSOC);
  foreach ($products as &$row) {
    $row['asp'] = intval($row['asp']);
    $row['mrp'] = intval($row['mrp']);
}
  // Filter only one product per name + category
$uniqueProducts = [];
$seen = [];

foreach ($products as $p) {
  $key = $p['name'] . '|' . $p['category'];
  if (!in_array($key, $seen)) {
    $uniqueProducts[] = $p;
    $seen[] = $key;
  }
}
$products = $uniqueProducts;

} else {
  $products = [];
}
?>

<!-- Best Sellers Section -->
<section class="py-5">
  <div class="container">
    <h2 class="text-center mb-4">Best Sellers</h2>
    <div class="row g-4">
      <?php
      foreach ($products as $p) {
        $title = $p['name'];
        $image = $p['image'];
        $price = $p['asp'];
        $mrp = $p['mrp'];
        $productId = $p['productId'];
        $size = $p['size'] ?? '1 Nos';
        // Step 1: Get all same name + category product variants
$variantStmt = $conn->prepare("
  SELECT p.productId, ps.size, ps.stockInHand, p.asp, p.mrp
  FROM products p
  JOIN product_stock ps ON p.productId = ps.productId
  WHERE p.name = ? AND p.category = ?
  ORDER BY ps.size * 1 ASC
");
$variantStmt->execute([$p['name'], $p['category']]);
$variants = $variantStmt->fetchAll(PDO::FETCH_ASSOC);

// Step 2: Find first available variant (lowest size with stock > 0)
$available = null;
foreach ($variants as $v) {
  if ($v['stockInHand'] > 0) {
    $available = $v;
    break;
  }
}

if ($available) {
  // Use the available variant details
  $productId = $available['productId'];
  $size = $available['size'];
  $price = $available['asp'];
  $mrp = $available['mrp'];
  $inStock = true;
} else {
  // fallback - still render with lowest size but disable cart
  $fallback = $variants[0];
  $productId = $fallback['productId'];
  $size = $fallback['size'];
  $price = $fallback['asp'];
  $mrp = $fallback['mrp'];
  $inStock = false;
}


        // Get dynamic rating and review count for the full product (same name + category)
        $relatedStmt = $conn->prepare("SELECT productId FROM products WHERE name = ? AND category = ?");
        $relatedStmt->execute([$p['name'], $p['category']]);
        $relatedIds = $relatedStmt->fetchAll(PDO::FETCH_COLUMN);

        $rating = 0;
        $reviews = 0;
        if ($relatedIds) {
          $placeholders = implode(',', array_fill(0, count($relatedIds), '?'));
          $avgStmt = $conn->prepare("SELECT ROUND(AVG(rating),1) FROM products WHERE productId IN ($placeholders)");
          $avgStmt->execute($relatedIds);
          $rating = $avgStmt->fetchColumn() ?: 0;

          $revStmt = $conn->prepare("SELECT COUNT(*) FROM reviews WHERE productId IN ($placeholders)");
          $revStmt->execute($relatedIds);
          $reviews = $revStmt->fetchColumn() ?: 0;
        }

        $discount = round((($mrp - $price) / $mrp) * 100);

        
        include "components/product-card.php";
      }
      ?>
    </div>
  </div>
</section>
  <!-- Gifting Section -->
<?php
// Fetch more random variants (to get at least 4 unique products)
$giftStmt = $conn->prepare("
  SELECT p.*, ps.stockInHand, ps.size
  FROM products p
  JOIN product_stock ps ON p.productId = ps.productId
  ORDER BY RAND()
  LIMIT 15
");
$giftStmt->execute();
$giftProductsRaw = $giftStmt->fetchAll(PDO::FETCH_ASSOC);

// Filter to get only 4 unique products (by name + category)
$giftProducts = [];
$seen = [];

foreach ($giftProductsRaw as $p) {
  $key = $p['name'] . '|' . $p['category'];
  if (!isset($seen[$key])) {
    $seen[$key] = true;
    $giftProducts[] = $p;
  }
  if (count($giftProducts) === 4) break; // stop at 4 unique products
}

?>

<!-- Gifting Section -->
<section class="py-5 bg-light">
  <div class="container">
    <h2 class="text-center mb-4">Perfect for Gifting</h2>
    <div class="row g-4">
      <?php foreach ($giftProducts as $p):
        $title = $p['name'];
        $image = $p['image'];

        // STEP 1: Get all variants (same name + category)
        $variantStmt = $conn->prepare("
          SELECT p.productId, ps.size, ps.stockInHand, p.asp, p.mrp
          FROM products p
          JOIN product_stock ps ON p.productId = ps.productId
          WHERE p.name = ? AND p.category = ?
          ORDER BY ps.size * 1 ASC
        ");
        $variantStmt->execute([$p['name'], $p['category']]);
        $variants = $variantStmt->fetchAll(PDO::FETCH_ASSOC);

        // STEP 2: Find first in-stock variant
        $available = null;
        foreach ($variants as $v) {
          if ($v['stockInHand'] > 0) {
            $available = $v;
            break;
          }
        }

        if ($available) {
          // Use in-stock variant
          $productId = $available['productId'];
          $size = $available['size'];
          $price = intval($available['asp']);
          $mrp = intval($available['mrp']);
          $inStock = true;
        } else {
          // Fallback to first variant
          $fallback = $variants[0];
          $productId = $fallback['productId'];
          $size = $fallback['size'];
          $price = intval($fallback['asp']);
          $mrp = intval($fallback['mrp']);
          $inStock = false;
        }

        // STEP 3: Rating & Reviews across all variants
        $relatedStmt = $conn->prepare("SELECT productId FROM products WHERE name = ? AND category = ?");
        $relatedStmt->execute([$p['name'], $p['category']]);
        $relatedIds = $relatedStmt->fetchAll(PDO::FETCH_COLUMN);

        $rating = 0;
        $reviews = 0;
        if ($relatedIds) {
          $placeholders = implode(',', array_fill(0, count($relatedIds), '?'));
          $avgStmt = $conn->prepare("SELECT ROUND(AVG(rating),1) FROM products WHERE productId IN ($placeholders)");
          $avgStmt->execute($relatedIds);
          $rating = $avgStmt->fetchColumn() ?: 0;

          $revStmt = $conn->prepare("SELECT COUNT(*) FROM reviews WHERE productId IN ($placeholders)");
          $revStmt->execute($relatedIds);
          $reviews = $revStmt->fetchColumn() ?: 0;
        }

        $discount = round((($mrp - $price) / $mrp) * 100);

        // Use same product-card.php to maintain UI
        include "components/product-card.php";
      endforeach; ?>
    </div>
  </div>
</section>



<?php include "components/footer.php"; ?>
