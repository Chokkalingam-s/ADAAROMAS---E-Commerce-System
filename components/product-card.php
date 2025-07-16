<style>
  .product-card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid #f0f0f0;
    height: 100%;
    display: flex;
    flex-direction: column;
  }

  .product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    border-color: #e0e0e0;
  }

  .product-image-container {
    position: relative;
    overflow: hidden;
    aspect-ratio: 1;
    background: #fafafa;
  }

  .product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
  }

  .product-card:hover .product-image {
    transform: scale(1.05);
  }

  .discount-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: #2c2c2c;
    color: #fff;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    z-index: 2;
  }

  .new-arrival-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    background: #ff6b35;
    color: #fff;
    padding: 0.3rem 0.6rem;
    border-radius: 15px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .add-to-cart-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(transparent, rgba(0,0,0,0.7));
    padding: 2rem 1rem 1rem;
    transform: translateY(100%);
    transition: transform 0.3s ease;
  }

  .product-card:hover .add-to-cart-overlay {
    transform: translateY(0);
  }

  .add-to-cart-btn {
    width: 100%;
    background: #fff;
    color: #2c2c2c;
    border: none;
    padding: 0.75rem 1rem;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .add-to-cart-btn:hover {
    background: #2c2c2c;
    color: #fff;
    transform: translateY(-2px);
  }

  .add-to-cart-btn:disabled {
    background: #6c757d;
    color: #fff;
    cursor: not-allowed;
    transform: none;
  }

  .product-info {
    padding: 1.5rem;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
  }

  .product-title {
    font-size: 1rem;
    font-weight: 500;
    color: #2c2c2c;
    margin-bottom: 0.5rem;
    line-height: 1.4;
    text-decoration: none;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .product-title:hover {
    color: #666;
    text-decoration: none;
  }

  .product-pricing {
    margin-bottom: 0.75rem;
  }

  .current-price {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c2c2c;
    margin-right: 0.5rem;
  }

  .original-price {
    font-size: 0.9rem;
    color: #999;
    text-decoration: line-through;
  }

  .product-rating {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: auto;
    font-size: 0.875rem;
  }

  .rating-stars {
    color: #ffc107;
    font-weight: 500;
  }

  .rating-count {
    color: #6c757d;
  }

  .verified-badge {
    color: #28a745;
    font-size: 0.8rem;
  }

  @media (max-width: 576px) {
    .product-info {
      padding: 1rem;
    }
    
    .product-title {
      font-size: 0.9rem;
    }
    
    .current-price {
      font-size: 1rem;
    }
  }
</style>

<div class="col-6 col-sm-6 col-md-4 col-lg-3 product-card-wrapper">
  <div class="product-card">
    <div class="product-image-container">
      <?php if (isset($discount) && $discount > 0): ?>
        <div class="discount-badge">
          SAVE <?= $discount ?>%
        </div>
      <?php endif; ?>
      
      <?php
      // Detect if current page is inside products/ folder or not
      $prefix = (strpos($_SERVER['PHP_SELF'], '/products/') !== false) ? '' : 'products/';
      ?>
      
      <a href="<?= $prefix ?>product.php?id=<?= $productId ?>">
        <img src="<?= $image ?>" class="product-image" alt="<?= $title ?>">
      </a>

      <div class="add-to-cart-overlay">
        <?php
        $absImage = "/adaaromas/assets/images/" . basename($image);
        ?>
        <?php if ($inStock): ?>
          <button 
            class="add-to-cart-btn"
            onclick='addToCart({
              productId: <?= $productId ?>,
              title: "<?= addslashes($title) ?>",
              size: "<?= $size ?>",
              price: <?= $price ?>,
              mrp: <?= $mrp ?>,
              image: "<?= $absImage ?>"
            })'>
            + Add to cart
          </button>
        <?php else: ?>
          <button class="add-to-cart-btn" disabled>
            Out of stock
          </button>
        <?php endif; ?>
      </div>

            
    </div>

    <?php
    // Step 1: Get name & category for the given productId
    $infoStmt = $conn->prepare("SELECT name, category FROM products WHERE productId = ?");
    $infoStmt->execute([$productId]);
    $prodInfo = $infoStmt->fetch(PDO::FETCH_ASSOC);

    $rating = 0;
    $reviews = 0;

    if ($prodInfo) {
      // Step 2: Get all related productIds (same name and category)
      $relatedStmt = $conn->prepare("SELECT productId FROM products WHERE name = ? AND category = ?");
      $relatedStmt->execute([$prodInfo['name'], $prodInfo['category']]);
      $relatedIds = $relatedStmt->fetchAll(PDO::FETCH_COLUMN);
      
      if ($relatedIds) {
        // Step 3: Prepare IN clause
        $placeholders = implode(',', array_fill(0, count($relatedIds), '?'));
        
        // Get average rating from products table
        $avgStmt = $conn->prepare("SELECT ROUND(AVG(rating), 1) as avgRating FROM products WHERE productId IN ($placeholders)");
        $avgStmt->execute($relatedIds);
        $rating = $avgStmt->fetchColumn() ?: 0;
        
        // Count actual review entries from reviews table (distinct entries)
        $revStmt = $conn->prepare("SELECT COUNT(*) FROM reviews WHERE productId IN ($placeholders)");
        $revStmt->execute($relatedIds);
        $reviews = $revStmt->fetchColumn() ?: 0;
      }
    }
    ?>

    <div class="product-info">
      <h5 class="product-title">
        <a href="<?= $prefix ?>product.php?id=<?= $productId ?>" class="product-title">
          <?= $title ?>
        
      </h5>
      
      <div class="product-pricing">
        <span class="current-price">₹<?= $price ?><small class="text-muted ms-1">Onwards</small></span>
        <?php if ($mrp > $price): ?>
          <span class="original-price" style="text-decoration: line-through; color: red;">₹<?= $mrp ?></span>
        <?php endif; ?>
      </div>

      <div class="product-rating">
        <span class="rating-stars">Onwards
          ⭐ <?= number_format($rating, 1) ?>
        </span>
        <span class="verified-badge">✓</span>
        <span class="rating-count">(<?= $reviews ?> Reviews)</span>
      </div>
      </a>
    </div>
  </div>
</div>