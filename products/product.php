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

<?php
// Fetch all related productIds
$relatedStmt = $conn->prepare("SELECT productId FROM products WHERE name = ? AND category = ?");
$relatedStmt->execute([$product['name'], $product['category']]);
$relatedIds = $relatedStmt->fetchAll(PDO::FETCH_COLUMN);

// For overall average rating
$avgStmt = $conn->prepare("
  SELECT ROUND(AVG(rating), 2) as avgRating
  FROM products
  WHERE productId IN (" . implode(',', array_fill(0, count($relatedIds), '?')) . ")
");
$avgStmt->execute($relatedIds);
$avgRating = $avgStmt->fetchColumn();

// Fetch reviews across all sizes
$placeholders = implode(',', array_fill(0, count($relatedIds), '?'));
$reviewStmt = $conn->prepare("
  SELECT u.name, r.feedback, r.createdAt
  FROM reviews r
  JOIN users u ON u.userId = r.userId
  WHERE r.productId IN ($placeholders)
  ORDER BY r.createdAt DESC
");
$reviewStmt->execute($relatedIds);
$reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="mt-5" id="reviewSection">
  <div class="mb-4">
    <h4>Customer Reviews</h4>
    <div class="d-flex align-items-center mb-2">
      <div class="me-2" style="font-size: 1.5rem; color: #ffc107;">
        <?php
        $fullStars = floor($avgRating);
        for ($i = 1; $i <= 5; $i++) {
          echo $i <= $fullStars ? '★' : '☆';
        }
        ?>
      </div>
      <span class="fw-bold"><?= $avgRating ?>/5</span>
    </div>
  </div>

  <?php if ($reviews): ?>
    <?php foreach ($reviews as $rev): ?>
      <div class="border rounded p-3 mb-3 bg-white shadow-sm">
        <strong><?= htmlspecialchars($rev['name']) ?></strong>
        <p class="mb-1"><?= nl2br(htmlspecialchars($rev['feedback'])) ?></p>
        <small class="text-muted"><?= date('d M Y, h:i A', strtotime($rev['createdAt'])) ?></small>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p class="text-muted">No reviews yet. Be the first to review!</p>
  <?php endif; ?>

  <button class="btn btn-outline-dark mt-4" type="button" data-bs-toggle="collapse" data-bs-target="#reviewFormWrap">
    + Write a Review
  </button>

  <div class="collapse mt-3" id="reviewFormWrap">
    <form id="reviewForm" class="border p-4 rounded bg-light shadow-sm">
      <h5 class="mb-3">Your Review</h5>

      <div class="mb-3">
        <label class="form-label">Rating</label><br>
        <div class="rating-stars">
          <?php for ($i = 1; $i <= 5; $i++): ?>
            <span class="star" data-value="<?= $i ?>">&#9733;</span>
          <?php endfor; ?>
        </div>
        <input type="hidden" name="rating" id="rating" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Review</label>
        <textarea name="feedback" class="form-control" rows="3" required></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Your Name</label>
        <input type="text" name="name" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Mobile Number</label>
        <input type="text" name="phoneNo" class="form-control" required pattern="\d{10}">
      </div>

      <input type="hidden" name="productId" value="<?= $productId ?>">
      <button type="submit" class="btn btn-success">Submit Review</button>
      <div id="reviewMsg" class="mt-2 fw-bold"></div>
    </form>
  </div>
</div>




  </div>
</div>
<script>
document.querySelectorAll('.rating-stars .star').forEach(star => {
  star.addEventListener('click', () => {
    const value = star.getAttribute('data-value');
    document.getElementById('rating').value = value;
    document.querySelectorAll('.rating-stars .star').forEach(s => {
      s.classList.toggle('selected', s.getAttribute('data-value') <= value);
    });
  });
});

document.getElementById('reviewForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  const obj = Object.fromEntries(formData);

  const res = await fetch('../products/submit-review.php', {
    method: 'POST',
    body: formData
  });
  const data = await res.json();

  const msgBox = document.getElementById('reviewMsg');
  msgBox.textContent = data.message;
  msgBox.className = data.success ? 'text-success' : 'text-danger';

  if (data.success) {
    this.reset();
    document.querySelectorAll('.rating-stars .star').forEach(s => s.classList.remove('selected'));
  }
});
</script>

<?php include('../components/footer.php'); ?>
