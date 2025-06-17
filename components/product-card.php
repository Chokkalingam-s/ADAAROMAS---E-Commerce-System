<div class="col-6 col-sm-6 col-md-4 col-lg-3">
  <div class="product-card shadow position-relative overflow-hidden rounded">
    
    <!-- Image Container with badge and hover button -->
    <div class="product-image-wrapper position-relative">
      <!-- ✅ Discount Badge -->
      <?php if (isset($discount) && $discount > 0): ?>
        <div class="discount-badge bg-dark text-white px-2 py-1 position-absolute top-0 start-0 m-2">
          SAVE <?= $discount ?>%
        </div>
      <?php endif; ?>

      <!-- Product Image -->
      <img src="<?= $image ?>" class="img-fluid w-100 product-img" alt="<?= $title ?>">

      <!-- Hover Add to Cart Button -->
       <button 
  class="add-to-cart-btn btn btn-light fw-bold" 
  onclick='addToCart({
    title: "<?= addslashes($title) ?>",
    price: <?= $price ?>,
      image: "<?= "/adaaromas/" . ltrim($image, "/") ?>"
  })'>
  + Add to cart
</button>
     
    </div>

    <!-- Card Body -->
    <div class="text-center mt-3">
      <h5 class="mb-1"><?= $title ?></h5>
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
