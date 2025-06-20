<?php include "components/header.php"; ?>

  <!-- Banner -->
  
    <div class="banner">
      <img src="assets/images/banner.png" class="img-fluid w-100 banner" alt="Banner" />
    </div>
  

  <!-- Featured Products -->
  <section class="py-5">
    <div class="container">
      <h2 class="text-center mb-4">Best Sellers</h2>
      <div class="row g-4">

        <?php
$products = [
  [
    "title" => "Ombre Nomade",
    "image" => "assets/images/image.png",
    "price" => 849,
    "mrp" => 1999,
    "rating" => "5.0",
    "reviews" => 1,
    "inStock" => true
  ],
  [
    "title" => "Aventus",
    "image" => "assets/images/image.png",
    "price" => 999,
    "mrp" => 2499,
    "rating" => "4.8",
    "reviews" => 5,
    "inStock" => true
  ],
  [
    "title" => "Creed Viking",
    "image" => "assets/images/image.png",
    "price" => 1299,
    "mrp" => 2999,
    "rating" => "4.9",
    "reviews" => 3,
    "inStock" => false

  ],
  [
    "title" => "Tom Ford Noir",
    "image" => "assets/images/image.png",
    "price" => 899,
    "mrp" => 1999,
    "rating" => "4.7",
    "reviews" => 2,
    "inStock" => true
  ]
];

foreach ($products as $p) {
  $discount = round((($p["mrp"] - $p["price"]) / $p["mrp"]) * 100);
  extract($p);
  include "components/product-card.php";
}
?>
      </div>
    </div>
  </section>

<?php include "components/footer.php"; ?>
