<div class="col-6 col-sm-6 col-md-4 col-lg-3">
  <div class="product-card shadow position-relative overflow-hidden rounded">
    
    <!-- Image Container with badge and hover button -->
    <div class="position-relative overflow-hidden">
      <?php if (isset($discount) && $discount > 0): ?>
        <div class="discount-badge bg-dark text-white px-2 py-1 position-absolute top-0 start-0 m-2">
          SAVE <?= $discount ?>%
        </div>
      <?php endif; ?>

      <!-- Product Image (clickable) -->
      <a href="product.php?id=<?= $productId ?>">
        <img src="<?= $image ?>" class="img-fluid w-100 product-img" alt="<?= $title ?>">
      </a>

      <!-- Add to Cart / Out of Stock -->
      <?php
        $absImage = "/adaaromas/assets/images/" . basename($image); 
      ?>
      <?php if ($inStock): ?>
        <button 
          class="add-to-cart-btn btn btn-light fw-bold"
          onclick='addToCart({
            productId: <?= $productId ?>,
            title: "<?= addslashes($title) ?>",
            price: <?= $price ?>,
            mrp: <?= $mrp ?>,
            image: "<?= $absImage ?>"
          })'>
          + Add to cart
        </button>
      <?php else: ?>
        <button class="btn btn-secondary fw-bold" disabled style="cursor:not-allowed;">
          Out of stock
        </button>
      <?php endif; ?>
    </div>

    <!-- Card Body (clickable title) -->
    <div class="text-center mt-3">
      <h5 class="mb-1">
        <a href="product.php?id=<?= $productId ?>" class="text-decoration-none text-dark"><?= $title ?></a>
      </h5>
      <p class="mb-1 fs-6">
        Rs. <?= $price ?> 
        <span class="text-muted text-decoration-line-through fs-6">Rs. <?= $mrp ?></span>
      </p>
      <p class="text-warning mb-1">
        ⭐ <?= $rating ?> 
        <span class="text-primary">| <i class="bi bi-patch-check-fill"></i> (<?= $reviews ?> Reviews)</span>
      </p>
    </div>
  </div>
</div>
