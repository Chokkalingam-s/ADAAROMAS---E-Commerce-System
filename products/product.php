<?php
include('../components/header.php');
include('../config/db.php');

$productId = $_GET['id'] ?? null;

if (!$productId) {
  echo "<p class='text-danger'>Invalid Product ID</p>";
  exit;
}

// Fetch from products & product_stock tables
$stmt = $conn->prepare("
  SELECT p.*, ps.stockInHand
  FROM products p
  JOIN product_stock ps ON p.productId = ps.productId
  WHERE p.productId = ?
");
$stmt->execute([$productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
  echo "<p class='text-danger'>Product not found</p>";
  exit;
}

// Process available sizes for the same product name & category
$sizeStmt = $conn->prepare("
  SELECT p.productId, size, asp, mrp, ps.stockInHand
  FROM products p
  JOIN product_stock ps ON p.productId = ps.productId
  WHERE p.name = ? AND p.category = ?
  ORDER BY size ASC
");

$sizeStmt->execute([$product['name'], $product['category']]);
$sizes = $sizeStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container py-5">
  <div class="row">
    <!-- Image & Title -->
    <div class="col-md-6">
      <img src="<?= '../' . $product['image'] ?>" class="img-fluid mb-3">
      <!-- <div class="d-flex gap-2 flex-wrap">
        <?php foreach (explode(',', $product['gallery'] ?? '') as $img): ?>
          <img src="<?= '../' . trim($img) ?>" class="img-thumbnail" style="width: 80px; height: 80px;">
        <?php endforeach; ?>
      </div> -->
    </div>

    <!-- Details -->
    <div class="col-md-6">
      <h2><?= $product['name'] ?></h2>
      <p class="text-muted"><?= $product['description'] ?></p>
      <p><strong>Rs. <?= $product['asp'] ?></strong> <span class="text-muted"><del>Rs. <?= $product['mrp'] ?></del></span></p>

      <div class="mb-3">
        <label class="form-label fw-bold">Available Sizes:</label>
        <div class="d-flex flex-column gap-1">
<?php foreach ($sizes as $s): ?>
  <div class="border rounded p-2 d-flex justify-content-between align-items-center <?= $s['stockInHand'] <= 0 ? 'text-muted' : '' ?>">
    <span>
  <?= ($s['size'] === '0' || $s['size'] == 0 || empty($s['size'])) ? '1 Nos' : $s['size'] . 'ml' ?>
</span>

    <span>
      Rs. <?= $s['asp'] ?>
      <small class="text-muted"><del>Rs. <?= $s['mrp'] ?></del></small>
    </span>
  <?php
        $absImage = "/adaaromas/assets/images/" . basename($product['image']); 
      ?>
    <?php if ($s['stockInHand'] > 0): ?>
      <button 
        class="add-to-cart-btn btn btn-light fw-bold btn-sm btn-outline-success"
        onclick='addToCart({
          productId: <?= (int)$s['productId'] ?>,
          <?php
  $displaySize = ($s['size'] === '0' || $s['size'] == 0 || empty($s['size'])) ? '1 Nos' : $s['size'] . 'ml';
  $cartSize = ($s['size'] === '0' || $s['size'] == 0 || empty($s['size'])) ? '1 Nos' : $s['size'];
?>
title: "<?= htmlspecialchars($product['name'], ENT_QUOTES) ?>",
size: "<?= $cartSize ?>",
          price: <?= (float)$s['asp'] ?>,
          mrp: <?= (float)$s['mrp'] ?>,
          image: "<?= $absImage ?>"
        })'>
        + Add to cart
      </button>
    <?php else: ?>
      <button class="btn btn-sm btn-outline-secondary" disabled>Out of Stock</button>
    <?php endif; ?>
  </div>
<?php endforeach; ?>

        </div>
      </div>

      <!-- Reuse common content from below -->
      <div class="accordion" id="productInfoAccordion">
        <div class="accordion-item">
          <h2 class="accordion-header" id="heading1">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
              Product Details
            </button>
          </h2>
          <div id="collapse1" class="accordion-collapse collapse show">
            <div class="accordion-body">
              <?= $product['description'] ?? 'Detailed description will be added soon.' ?>
            </div>
          </div>
        </div>
        <!-- Add "Shipping & Return", "Guarantee", etc. as fixed content here -->
      </div>
    </div>
  </div>
</div>

<?php include('../components/footer.php'); ?>
