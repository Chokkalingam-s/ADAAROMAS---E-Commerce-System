<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ADA Aromas - Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css"/>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm sticky-top">
    <div class="container">
      <a class="navbar-brand fw-bold" href="index.html">ADA Aromas</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="products/perfume-men.html">Perfume</a></li>
          <li class="nav-item"><a class="nav-link" href="products/attar.html">Attar</a></li>
          <li class="nav-item"><a class="nav-link" href="products/oud.html">Oud</a></li>
          <li class="nav-item"><a class="nav-link" href="about.html">About</a></li>
          <li class="nav-item"><a class="nav-link" href="contact.html">Contact</a></li>
          <li class="nav-item"><a class="nav-link" href="cart.html">Cart</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Banner -->
  <div class="py-1 container-fluid px-0">
    <div class="banner">
      <img src="assets/images/banner.png" class="img-fluid w-100 banner" alt="Banner" />
    </div>
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
    "reviews" => 1
  ],
  [
    "title" => "Aventus",
    "image" => "assets/images/image.png",
    "price" => 999,
    "mrp" => 2499,
    "rating" => "4.8",
    "reviews" => 5
  ],
  [
    "title" => "Creed Viking",
    "image" => "assets/images/image.png",
    "price" => 1299,
    "mrp" => 2999,
    "rating" => "4.9",
    "reviews" => 3
  ],
  [
    "title" => "Tom Ford Noir",
    "image" => "assets/images/image.png",
    "price" => 899,
    "mrp" => 1999,
    "rating" => "4.7",
    "reviews" => 2
  ]
];

foreach ($products as $p) {
  $discount = round((($p["mrp"] - $p["price"]) / $p["mrp"]) * 100);
  extract($p);
  include "product-card.php";
}
?>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-dark text-light text-center py-3">
    <p class="mb-0">© 2025 ADA Aromas. All Rights Reserved.</p>
  </footer>
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js"></script>
 
</body>
</html>
