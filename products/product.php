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
  ORDER BY CAST(size AS UNSIGNED) ASC
");

$sizeStmt->execute([$product['name'], $product['category']]);
$sizes = $sizeStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all related productIds for reviews
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

// Pagination for reviews
$reviewsPerPage = 5;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $reviewsPerPage;

// Count total reviews
$placeholders = implode(',', array_fill(0, count($relatedIds), '?'));
$countStmt = $conn->prepare("
  SELECT COUNT(*) as total
  FROM reviews r
  WHERE r.productId IN ($placeholders)");
$countStmt->execute($relatedIds);
$totalReviews = $countStmt->fetchColumn();
$totalPages = ceil($totalReviews / $reviewsPerPage);

// Fetch reviews with pagination
$reviewStmt = $conn->prepare("
  SELECT u.name, r.feedback, r.createdAt
  FROM reviews r
  JOIN users u ON u.userId = r.userId
  WHERE r.productId IN ($placeholders)
  ORDER BY r.createdAt DESC
  LIMIT $reviewsPerPage OFFSET $offset");
$reviewStmt->execute($relatedIds);
$reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);


?>

<style>
.product-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 1rem;
}

.product-image-section {
  position: sticky;
  top: 1rem;
  height: fit-content;
}

.product-main-image {
  width: 100%;
  height: 475px;
  object-fit: cover;
  border-radius: 8px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
}

.giftIcon{
  font-size: 1.2rem;
}
.product-title-wrapper {
  position: relative;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 0.2rem;
  flex-wrap: nowrap;
}

.gift-sticker {
  background: #ffe066;
  color: #2d3748;
  font-size: 0.75rem;
  font-weight: 600;
  padding: 0.3rem 0.7rem;
  border-radius: 999px;
  display: flex;
  align-items: center;
  gap: 0.3rem;
  animation: blink 1.5s infinite;
  white-space: nowrap;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
  margin-left: 0.5rem;
}

/* Sticky behavior for mobile */
@media (max-width: 768px) {
  .product-title-wrapper {
    position: sticky;
    top: 0;
    background: white;
    z-index: 10;
    padding-right: 1rem;
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
  }

  .gift-sticker {
    font-size: 0.65rem;
    padding: 0.25rem 0.6rem;
    line-height: 1;
  }
}

@keyframes blink {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.4; }
}




.product-main-image:hover {
  transform: scale(1.02);
}

.image-rating-section {
  background: white;
  border-radius: 8px;
  padding: 0rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  text-align: center;
}

.image-rating-title {
  font-size: 0.9rem;
  font-weight: 600;
  color: #4a5568;
  margin-bottom: 0.5rem;
}

.image-rating-display {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.image-stars {
  font-size: 1.2rem;
  color: #ffd700;
}

.image-rating-score {
  font-size: 1rem;
  font-weight: 600;
  color: #2d3748;
}

.product-details-section {
  padding-left: 1.5rem;
  max-height: 100vh;
  overflow-y: auto;
}

.product-title1 {
  font-size: 1.8rem;
  font-weight: 700;
  color: #2d3748;
  margin-bottom: 0.5rem;
  line-height: 1.2;
}

.product-description {
  font-size: 0.95rem;
  color: #718096;
  margin-bottom: 1rem;
  line-height: 1.4;
}
.product-disclaimer {
  color: #e63946; /* red */
  font-size: 0.85rem;
  font-style: italic;
  margin-top: 0.75rem;
  text-align: center;
}

.price-section {
  margin-bottom: 1rem;
  padding: 1rem;
  background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
  border-radius: 8px;
  border-left: 3px solid #48bb78;
}

.current-price1 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #48bb78;
  margin-right: 0.5rem;
}

.original-price {
  font-size: 1rem;
  color: #a0aec0;
  text-decoration: line-through;
}

.sizes-section {
  margin-bottom: 1rem;
}

.sizes-label {
  font-size: 1rem;
  font-weight: 600;
  color: #2d3748;
  margin-bottom: 0.75rem;
  display: block;
}

.sizes-container {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  align-items: stretch;
}

.size-option {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 0.75rem;
  transition: all 0.3s ease;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
  flex: 1;
  min-width: 140px;
  max-width: 200px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.size-option:hover {
  border-color: #48bb78;
  box-shadow: 0 2px 8px rgba(72, 187, 120, 0.1);
  transform: translateY(-1px);
}

.size-option.out-of-stock {
  background: #f7fafc;
  border-color: #e2e8f0;
  opacity: 0.6;
}

.size-info {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  margin-bottom: 0.5rem;
  gap: 0.25rem;
}

.size-name {
  font-weight: 600;
  color: #2d3748;
  font-size: 0.95rem;
}

.size-price {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.1rem;
}

.size-current-price {
  font-weight: 700;
  color: #48bb78;
  font-size: 0.9rem;
}

.size-original-price {
  color: #a0aec0;
  text-decoration: line-through;
  font-size: 0.75em;
}

.add-to-cart-btn1 {
  background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
  border: none;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  font-weight: 600;
  font-size: 0.85rem;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(72, 187, 120, 0.3);
  width: 100%;
}

.add-to-cart-btn1:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(72, 187, 120, 0.4);
  color: white;
}

.out-of-stock-btn1 {
  background: #e2e8f0;
  color: #a0aec0;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  font-weight: 600;
  font-size: 0.85rem;
  cursor: not-allowed;
  width: 100%;
}

.product-info-accordion {
  margin-top: 1rem;
}

.accordion-item {
  border: none;
  margin-bottom: 0.5rem;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
}

.accordion-button {
  background: white;
  border: none;
  font-weight: 600;
  color: #2d3748;
  padding: 0.75rem 1rem;
  font-size: 0.9rem;
}

.accordion-button:not(.collapsed) {
  background: #f7fafc;
  color: #48bb78;
}

.accordion-body {
  padding: 1rem;
  background: white;
  color: #718096;
  line-height: 1.5;
  font-size: 0.9rem;
}

.reviews-section {
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 2px solid #e2e8f0;
}

.reviews-header {
  margin-bottom: 1.5rem;
}

.reviews-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #2d3748;
  margin-bottom: 0.5rem;
}

.rating-display {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.stars {
  font-size: 1.3rem;
  color: #ffd700;
}

.rating-score {
  font-size: 1.1rem;
  font-weight: 600;
  color: #2d3748;
}

.review-card {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 1rem;
  margin-bottom: 0.75rem;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
}

.review-card:hover {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transform: translateY(-1px);
}

.review-author {
  font-weight: 600;
  color: #2d3748;
  margin-bottom: 0.25rem;
  font-size: 0.95rem;
}

.review-content {
  color: #718096;
  line-height: 1.5;
  margin-bottom: 0.25rem;
  font-size: 0.9rem;
}

.review-date {
  color: #a0aec0;
  font-size: 0.8rem;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0.5rem;
  margin: 1.5rem 0;
}

.pagination-btn {
  background: white;
  border: 1px solid #e2e8f0;
  color: #4a5568;
  padding: 0.5rem 0.75rem;
  border-radius: 6px;
  text-decoration: none;
  font-size: 0.9rem;
  transition: all 0.3s ease;
}

.pagination-btn:hover {
  background: #48bb78;
  color: white;
  border-color: #48bb78;
  text-decoration: none;
}

.pagination-btn.active {
  background: #48bb78;
  color: white;
  border-color: #48bb78;
}

.pagination-btn:disabled {
  background: #f7fafc;
  color: #a0aec0;
  cursor: not-allowed;
}

.write-review-btn {
  background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
  border: none;
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 6px;
  font-weight: 600;
  transition: all 0.3s ease;
  margin-top: 1rem;
  font-size: 0.9rem;
}

.write-review-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(45, 55, 72, 0.3);
  color: white;
}

.review-form {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 1.5rem;
  margin-top: 1rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.review-form-title {
  font-size: 1.2rem;
  font-weight: 600;
  color: #2d3748;
  margin-bottom: 1rem;
}

.form-group {
  margin-bottom: 1rem;
}

.form-label {
  font-weight: 600;
  color: #2d3748;
  margin-bottom: 0.25rem;
  display: block;
  font-size: 0.9rem;
}

.form-control {
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  padding: 0.5rem;
  transition: all 0.3s ease;
  font-size: 0.9rem;
  width: 100%;
}

.form-control:focus {
  border-color: #48bb78;
  box-shadow: 0 0 0 2px rgba(72, 187, 120, 0.1);
  outline: none;
}

.rating-stars {
  display: flex;
  gap: 0.25rem;
  margin-top: 0.25rem;
}

.star {
  font-size: 1.5rem;
  color: #e2e8f0;
  cursor: pointer;
  transition: all 0.3s ease;
}

.star:hover,
.star.selected {
  color: #ffd700;
  transform: scale(1.05);
}

.submit-review-btn {
  background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
  border: none;
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 6px;
  font-weight: 600;
  transition: all 0.3s ease;
  font-size: 0.9rem;
}

.submit-review-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(72, 187, 120, 0.3);
}

@media (max-width: 768px) {
  .product-container {
    padding: 0.5rem;
  }
  
  .product-details-section {
    padding-left: 0;
    margin-top: 1rem;
    max-height: none;
  }

  .product-disclaimer {
  color: #e63946; /* red */
  font-size: 0.70rem;
  font-style: italic;
  margin-top: 0.75rem;
  text-align: center;
}
  
  .product-title1 {
    font-size: 1.5rem;
  }

    .add-to-cart-btn {
    width: 100%;
    background: #fff;
    color: #2c2c2c;
    border: none;
    padding: 0.5rem 0.75rem;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.65rem;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.1px;
  }
  
  .product-main-image {
    height: 450px;
  }
  
  .size-info {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.25rem;
  }
  
  .current-price1 {
    font-size: 1.3rem;
  }
  
  .pagination {
    flex-wrap: wrap;
  }
}

@media (max-width: 480px) {
  .sizes-container {
      display: flex;
  flex-direction: row;
  justify-content: space-between;
  }

  .product-main-image {
  width: 100%;
  height: 50vh;
  object-fit: cover;
  border-radius: 8px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
}
  
  .size-option {
    min-width: auto;
    max-width: none;
  }
  
  .size-info {
    flex-direction: row;
    justify-content: space-between;
    text-align: left;
  }
  
  .size-price {
    align-items: flex-end;
  }

  @media (max-width: 768px) {
    .carousel-item img {
        max-height: 380px;
    }
}
</style>

<div class="product-container">
  <div class="row">
    <!-- Product Image Section -->
    <div class="col-lg-5">
<div class="product-image-section">

<div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-inner">

    <!-- First image -->
    <div class="carousel-item active">
      <img src="<?= '../' . $product['image'] ?>" class="d-block w-100 product-main-image" alt="<?= htmlspecialchars($product['name']) ?>">
    </div>

    <!-- Second image -->
    <?php if (!empty($product['backImage'])): ?>
      <div class="carousel-item">
        <img src="<?= '../' . $product['backImage'] ?>" class="d-block w-100 product-main-image" alt="<?= htmlspecialchars($product['name']) ?> - Back View">
      </div>
    <?php endif; ?>

    <!-- Third image (static) -->
    <div class="carousel-item">
      <img src="staticImage.png" class="d-block w-100 product-main-image" alt="Static Image">
    </div>

  </div>

  <!-- Controls -->
  <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>

  <!-- Indicators -->
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
    <?php if (!empty($product['backImage'])): ?>
      <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
      <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
    <?php else: ?>
      <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
    <?php endif; ?>
  </div>
</div>

  <!-- Disclaimer -->
  <p class="product-disclaimer">
    Disclaimer: The Actual Design of Bottle may Vary from the one shown.
  </p>


  <!-- Rating Display Below Images -->
  <div class="image-rating-section">
    <div class="image-rating-title">Customer Rating</div>
    <div class="image-rating-display">
      <div class="image-stars">
        <?php
        $fullStars = floor($avgRating);
        for ($i = 1; $i <= 5; $i++) {
          echo $i <= $fullStars ? '★' : '☆';
        }
        ?>
      </div>
      <span class="image-rating-score"><?= $avgRating ?>/5</span>
      <span style="font-size: 0.8rem; color: #a0aec0;">(<?= $totalReviews ?> reviews)</span>
    </div>
  </div>
</div>

    </div>

    <!-- Product Details Section -->
    <div class="col-lg-7">
      <div class="product-details-section">
        <div class="product-title-wrapper">
  <h1 class="product-title1"><?= htmlspecialchars($product['name']) ?></h1>
  <div class="gift-sticker">
    <span class="giftIcon" >🎁</span> <span>Freebies & Gifts <br>On Every Order</span>
  </div>
</div>

        <p class="product-description"><?= htmlspecialchars($product['description']) ?></p>
        
        <!-- <div class="price-section">
          <span class="current-price1">₹<?= number_format($product['asp']) ?></span>
          <span class="original-price">₹<?= number_format($product['mrp']) ?></span>
        </div> -->

        <div class="sizes-section">
          <label class="sizes-label">Available Sizes & Variants:</label>
          
          <div class="sizes-container">
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
                    class="add-to-cart-btn"
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
                  <button class="out-of-stock-btn" disabled>Out of Stock</button>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Product Information Accordion -->
<div class="accordion product-info-accordion" id="productInfoAccordion">
  <div class="accordion-item">
    <h2 class="accordion-header" id="heading1">
      <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
        Product Details
      </button>
    </h2>
    <div id="collapse1" class="accordion-collapse collapse show" data-bs-parent="#productInfoAccordion">
<div class="accordion-body">
  <?php if (!empty($product['topNode']) || !empty($product['middleNode']) || !empty($product['baseNode'])): ?>
    
    <?php if (!empty($product['topNode'])): ?>
      <p><strong>Top Notes:</strong> <?= htmlspecialchars($product['topNode']) ?></p>
    <?php endif; ?>

    <?php if (!empty($product['middleNode'])): ?>
      <p><strong>Middle Notes:</strong> <?= htmlspecialchars($product['middleNode']) ?></p>
    <?php endif; ?>

    <?php if (!empty($product['baseNode'])): ?>
      <p><strong>Base Notes:</strong> <?= htmlspecialchars($product['baseNode']) ?></p>
    <?php endif; ?>

  <?php else: ?>
    <?= $product['description'] ?? 'Detailed description will be added soon.' ?>
  <?php endif; ?>
</div>

    </div>
  </div>
  
  <div class="accordion-item">
    <h2 class="accordion-header" id="heading2">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
        Shipping Policy
      </button>
    </h2>
    <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#productInfoAccordion">
      <div class="accordion-body">
        Orders are shipped within <strong>24–48 hours</strong> and delivered in <strong>3-5 business days</strong>.
      </div>
    </div>
  </div>
  
  <div class="accordion-item">
    <h2 class="accordion-header" id="heading3">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
        Quality Guarantee
      </button>
    </h2>
    <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#productInfoAccordion">
      <div class="accordion-body">
        100% authentic products with quality guarantee. Contact us for any quality concerns.
      </div>
    </div>
  </div>
</div>

      </div>
    </div>
  </div>

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
        <span style="font-size: 0.9rem; color: #718096;">Based on <?= $totalReviews ?> reviews</span>
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
      
      <!-- Pagination -->
      <?php if ($totalPages > 1): ?>
        <div class="pagination">
          <?php if ($currentPage > 1): ?>
            <a href="?id=<?= $productId ?>&page=<?= $currentPage - 1 ?>" class="pagination-btn">← Previous</a>
          <?php endif; ?>
          
          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?id=<?= $productId ?>&page=<?= $i ?>" 
               class="pagination-btn <?= $i == $currentPage ? 'active' : '' ?>">
              <?= $i ?>
            </a>
          <?php endfor; ?>
          
          <?php if ($currentPage < $totalPages): ?>
            <a href="?id=<?= $productId ?>&page=<?= $currentPage + 1 ?>" class="pagination-btn">Next →</a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
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
    <textarea name="feedback" class="form-control" rows="3" placeholder="Share your thoughts about this product..." required></textarea>
  </div>

  <div class="form-group">
    <label class="form-label">Mobile Number</label>
    <input type="text" name="phoneNo" class="form-control" placeholder="Enter your registered mobile number" required pattern="\d{10}">
  </div>

  <div class="form-group">
    <label class="form-label">Order ID</label>
    <input type="text" name="orderId" class="form-control" placeholder="Enter your Order ID" required>
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
