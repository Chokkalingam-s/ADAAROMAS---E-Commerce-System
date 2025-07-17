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
  WHERE p.productId = ?");
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
  ORDER BY size ASC");
$sizeStmt->execute([$product['name'], $product['category']]);
$sizes = $sizeStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
.product-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem 1rem;
}

.product-image-section {
  position: sticky;
  top: 2rem;
  height: fit-content;
}

.product-main-image {
  width: 100%;
  height: 500px;
  object-fit: cover;
  border-radius: 12px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
}

.product-main-image:hover {
  transform: scale(1.02);
}

.product-details-section {
  padding-left: 2rem;
}

.product-title1 {
  font-size: 2.5rem;
  font-weight: 700;
  color: #2d3748;
  margin-bottom: 1rem;
  line-height: 1.2;
}

.product-description {
  font-size: 1.1rem;
  color: #718096;
  margin-bottom: 2rem;
  line-height: 1.6;
}

.price-section {
  margin-bottom: 2rem;
  padding: 1.5rem;
  background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
  border-radius: 12px;
  border-left: 4px solid #48bb78;
}

.current-price1 {
  font-size: 2rem;
  font-weight: 700;
  color: #48bb78;
  margin-right: 1rem;
}

.original-price1 {
  font-size: 1.2rem;
  color: #a0aec0;
  text-decoration: line-through;
}

.sizes-section {
  margin-bottom: 2rem;
}

.sizes-label {
  font-size: 1.2rem;
  font-weight: 600;
  color: #2d3748;
  margin-bottom: 1rem;
  display: block;
}

.size-option {
  background: white;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 1rem;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.size-option:hover {
  border-color: #48bb78;
  box-shadow: 0 4px 16px rgba(72, 187, 120, 0.1);
  transform: translateY(-2px);
}

.size-option.out-of-stock {
  background: #f7fafc;
  border-color: #e2e8f0;
  opacity: 0.6;
}

.size-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.size-name {
  font-weight: 600;
  color: #2d3748;
  font-size: 1.1rem;
}

.size-price {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.size-current-price {
  font-weight: 700;
  color: #48bb78;
  font-size: 1.1rem;
}

.size-original-price {
  color: #a0aec0;
  text-decoration: line-through;
  font-size: 0.9rem;
}

.add-to-cart-btn1 {
  background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
  border: none;
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(72, 187, 120, 0.3);
}

.add-to-cart-btn1:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(72, 187, 120, 0.4);
  color: white;
}

.out-of-stock-btn1 {
  background: #e2e8f0;
  color: #a0aec0;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: not-allowed;
}

.product-info-accordion {
  margin-top: 2rem;
}

.accordion-item {
  border: none;
  margin-bottom: 1rem;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.accordion-button {
  background: white;
  border: none;
  font-weight: 600;
  color: #2d3748;
  padding: 1.5rem;
}

.accordion-button:not(.collapsed) {
  background: #f7fafc;
  color: #48bb78;
}

.accordion-body {
  padding: 1.5rem;
  background: white;
  color: #718096;
  line-height: 1.6;
}

.reviews-section {
  margin-top: 4rem;
  padding-top: 2rem;
  border-top: 2px solid #e2e8f0;
}

.reviews-header {
  margin-bottom: 2rem;
}

.reviews-title {
  font-size: 2rem;
  font-weight: 700;
  color: #2d3748;
  margin-bottom: 1rem;
}

.rating-display {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1rem;
}

.stars {
  font-size: 1.5rem;
  color: #ffd700;
}

.rating-score {
  font-size: 1.2rem;
  font-weight: 600;
  color: #2d3748;
}

.review-card {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 1rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
}

.review-card:hover {
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
  transform: translateY(-2px);
}

.review-author {
  font-weight: 600;
  color: #2d3748;
  margin-bottom: 0.5rem;
}

.review-content {
  color: #718096;
  line-height: 1.6;
  margin-bottom: 0.5rem;
}

.review-date {
  color: #a0aec0;
  font-size: 0.9rem;
}

.write-review-btn {
  background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
  border: none;
  color: white;
  padding: 1rem 2rem;
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.3s ease;
  margin-top: 2rem;
}

.write-review-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(45, 55, 72, 0.3);
  color: white;
}

.review-form {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 2rem;
  margin-top: 1rem;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}

.review-form-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #2d3748;
  margin-bottom: 1.5rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-label {
  font-weight: 600;
  color: #2d3748;
  margin-bottom: 0.5rem;
  display: block;
}

.form-control {
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  padding: 0.75rem;
  transition: all 0.3s ease;
  font-size: 1rem;
}

.form-control:focus {
  border-color: #48bb78;
  box-shadow: 0 0 0 3px rgba(72, 187, 120, 0.1);
}

.rating-stars {
  display: flex;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.star {
  font-size: 2rem;
  color: #e2e8f0;
  cursor: pointer;
  transition: all 0.3s ease;
}

.star:hover,
.star.selected {
  color: #ffd700;
  transform: scale(1.1);
}

.submit-review-btn {
  background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
  border: none;
  color: white;
  padding: 1rem 2rem;
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.submit-review-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(72, 187, 120, 0.3);
}

@media (max-width: 768px) {
  .product-container {
    padding: 1rem;
  }
  
  .product-details-section {
    padding-left: 0;
    margin-top: 2rem;
  }
  
  .product-title1 {
    font-size: 2rem;
  }
  
  .product-main-image {
    height: 300px;
  }
  
  .size-info {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
  
  .current-price1 {
    font-size: 1.5rem;
  }
}
</style>

<div class="product-container">
  <div class="row">
    <!-- Product Image Section -->
    <div class="col-lg-6">
      <div class="product-image-section">
        <img src="<?= '../' . $product['image'] ?>" class="product-main-image" alt="<?= htmlspecialchars($product['name']) ?>">
      </div>
    </div>

    <!-- Product Details Section -->
    <div class="col-lg-6">
      <div class="product-details-section">
        <h1 class="product-title1"><?= htmlspecialchars($product['name']) ?></h1>
        <p class="product-description"><?= htmlspecialchars($product['description']) ?></p>
        
        <div class="price-section">
          <span class="current-price1">₹<?= number_format($product['asp']) ?></span>
          <span class="original-price1">₹<?= number_format($product['mrp']) ?></span>
        </div>

        <div class="sizes-section">
          <label class="sizes-label">Available Sizes & Variants:</label>
          
          <?php foreach ($sizes as $s): ?>
            <div class="size-option <?= $s['stockInHand'] <= 0 ? 'out-of-stock' : '' ?>">
              <div class="size-info">
                <div class="size-name">
                  <?= ($s['size'] === '0' || $s['size'] == 0 || empty($s['size'])) ? '1 Nos' : $s['size'] . 'ml' ?>
                </div>
                <div class="size-price">
                  <span class="size-current-price">₹<?= number_format($s['asp']) ?></span>
                  <span class="size-original-price">₹<?= number_format($s['mrp']) ?></span>
                </div>
              </div>
              
              <?php
              $absImage = "/adaaromas/assets/images/" . basename($product['image']);
              $displaySize = ($s['size'] === '0' || $s['size'] == 0 || empty($s['size'])) ? '1 Nos' : $s['size'] . 'ml';
              $cartSize = ($s['size'] === '0' || $s['size'] == 0 || empty($s['size'])) ? '1 Nos' : $s['size'];
              ?>
              
              <?php if ($s['stockInHand'] > 0): ?>
                <button 
                  class="add-to-cart-btn1"
                  onclick='addToCart({
                    productId: <?= (int)$s['productId'] ?>,
                    title: "<?= htmlspecialchars($product['name'], ENT_QUOTES) ?>",
                    size: "<?= $cartSize ?>",
                    price: <?= (float)$s['asp'] ?>,
                    mrp: <?= (float)$s['mrp'] ?>,
                    image: "<?= $absImage ?>"
                  })'>
                  Add to Cart
                </button>
              <?php else: ?>
                <button class="out-of-stock-btn1" disabled>Out of Stock</button>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Product Information Accordion -->
        <div class="accordion product-info-accordion" id="productInfoAccordion">
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
          
          <div class="accordion-item">
            <h2 class="accordion-header" id="heading2">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                Shipping Policy
              </button>
            </h2>
            <div id="collapse2" class="accordion-collapse collapse">
              <div class="accordion-body">
                Orders are shipped within <strong>24–48 hours</strong> and delivered in <strong>3-5 business days</strong>.<br>
              </div>
            </div>
          </div>
          
          <div class="accordion-item">
            <h2 class="accordion-header" id="heading3">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                Quality Guarantee
              </button>
            </h2>
            <div id="collapse3" class="accordion-collapse collapse">
              <div class="accordion-body">
                100% authentic products with quality guarantee. Contact us for any quality concerns.
              </div>
            </div>
          </div>
        </div>
      </div>
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
    WHERE productId IN (" . implode(',', array_fill(0, count($relatedIds), '?')) . ")");
  $avgStmt->execute($relatedIds);
  $avgRating = $avgStmt->fetchColumn();

  // Fetch reviews across all sizes
  $placeholders = implode(',', array_fill(0, count($relatedIds), '?'));
  $reviewStmt = $conn->prepare("
    SELECT u.name, r.feedback, r.createdAt
    FROM reviews r
    JOIN users u ON u.userId = r.userId
    WHERE r.productId IN ($placeholders)
    ORDER BY r.createdAt DESC");
  $reviewStmt->execute($relatedIds);
  $reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);
  ?>

  <!-- Reviews Section -->
  <div class="reviews-section" id="reviewSection">
    <div class="reviews-header">
      <h3 class="reviews-title">Customer Reviews</h3>
      <div class="rating-display">
        <div class="stars">
          <?php
          $fullStars = floor($avgRating);
          for ($i = 1; $i <= 5; $i++) {
            echo $i <= $fullStars ? '★' : '☆';
          }
          ?>
        </div>
        <span class="rating-score"><?= $avgRating ?>/5</span>
      </div>
    </div>

    <?php if ($reviews): ?>
      <?php foreach ($reviews as $rev): ?>
        <div class="review-card">
          <div class="review-author"><?= htmlspecialchars($rev['name']) ?></div>
          <div class="review-content"><?= nl2br(htmlspecialchars($rev['feedback'])) ?></div>
          <div class="review-date"><?= date('d M Y, h:i A', strtotime($rev['createdAt'])) ?></div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="review-card">
        <div class="review-content">No reviews yet. Be the first to review this product!</div>
      </div>
    <?php endif; ?>

    <button class="write-review-btn" type="button" data-bs-toggle="collapse" data-bs-target="#reviewFormWrap">
      Write a Review
    </button>

    <div class="collapse" id="reviewFormWrap">
      <form id="reviewForm" class="review-form">
        <h4 class="review-form-title">Share Your Experience</h4>
        
        <div class="form-group">
          <label class="form-label">Rating</label>
          <div class="rating-stars">
            <?php for ($i = 1; $i <= 5; $i++): ?>
              <span class="star" data-value="<?= $i ?>">★</span>
            <?php endfor; ?>
          </div>
          <input type="hidden" name="rating" id="rating" required>
        </div>
        
        <div class="form-group">
          <label class="form-label">Your Review</label>
          <textarea name="feedback" class="form-control" rows="4" placeholder="Share your thoughts about this product..." required></textarea>
        </div>
        
        <div class="form-group">
          <label class="form-label">Your Name</label>
          <input type="text" name="name" class="form-control" placeholder="Enter your name" required>
        </div>
        
        <div class="form-group">
          <label class="form-label">Mobile Number</label>
          <input type="text" name="phoneNo" class="form-control" placeholder="Enter your mobile number" required pattern="\d{10}">
        </div>
        
        <input type="hidden" name="productId" value="<?= $productId ?>">
        <button type="submit" class="submit-review-btn">Submit Review</button>
        <div id="reviewMsg" class="mt-3"></div>
      </form>
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
  
  try {
    const res = await fetch('../products/submit-review.php', {
      method: 'POST',
      body: formData
    });
    const data = await res.json();
    const msgBox = document.getElementById('reviewMsg');
    msgBox.textContent = data.message;
    msgBox.className = data.success ? 'text-success fw-bold' : 'text-danger fw-bold';
    
    if (data.success) {
      this.reset();
      document.querySelectorAll('.rating-stars .star').forEach(s => s.classList.remove('selected'));
      setTimeout(() => {
        location.reload();
      }, 2000);
    }
  } catch (error) {
    console.error('Error submitting review:', error);
    document.getElementById('reviewMsg').textContent = 'Error submitting review. Please try again.';
    document.getElementById('reviewMsg').className = 'text-danger fw-bold';
  }
});
</script>

<?php include('../components/footer.php'); ?>
