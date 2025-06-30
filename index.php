<?php include "components/header.php"; ?>

  <!-- Banner -->
  
    <div class="banner">
      <img src="assets/images/banner.png" class="img-fluid w-100 banner" alt="Banner" />
    </div>
  

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
    SELECT p.*, ps.stockInHand
    FROM products p
    JOIN product_stock ps ON p.productId = ps.productId
    WHERE p.productId IN ($placeholders)
  ");
  $prodStmt->execute($productIds);
  $products = $prodStmt->fetchAll(PDO::FETCH_ASSOC);
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
        $inStock = $p['stockInHand'] > 0;

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
$giftStmt = $conn->prepare("
  SELECT p.*, ps.stockInHand 
  FROM products p
  JOIN product_stock ps ON p.productId = ps.productId
  ORDER BY RAND()
  LIMIT 8
");
$giftStmt->execute();
$giftProducts = $giftStmt->fetchAll(PDO::FETCH_ASSOC);

// Filter only one product per name + category
$uniqueProducts = [];
$seen = [];

foreach ($giftProducts as $p) {
  $key = $p['name'] . '|' . $p['category'];
  if (!in_array($key, $seen)) {
    $uniqueProducts[] = $p;
    $seen[] = $key;
  }
}
$giftProducts = $uniqueProducts;

?>

<!-- Gifting Section -->
<section class="py-5 bg-light">
  <div class="container">
    <h2 class="text-center mb-4">Perfect for Gifting</h2>
    <div class="row g-4">
      <?php
      foreach ($giftProducts as $p) {
        $title = $p['name'];
        $image = $p['image'];
        $price = $p['asp'];
        $mrp = $p['mrp'];
        $productId = $p['productId'];
        $size = $p['size'] ?? '1 Nos';
        $inStock = $p['stockInHand'] > 0;

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



<?php include "components/footer.php"; ?>
