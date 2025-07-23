<?php 
$currentPage = basename($_SERVER['PHP_SELF']); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ADA AROMAS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css"/>
<style>
/* Keep all your existing CSS and add these updates for cart items */
.cart-sidebar {
  position: fixed;
  top: 0;
  right: -100%;
  width: 380px;
  height: 100vh;
  background: #fff;
  box-shadow: -4px 0 10px rgba(0,0,0,0.2);
  z-index: 1055;
  transition: right 0.35s ease-in-out;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
}

.cart-sidebar.open {
  right: 0;
}

.cart-header {
  padding: 1rem;
  font-size: 16px;
  border-bottom: 1px solid #eee;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.cart-body {
  flex-grow: 1;
  padding: 1rem;
}

/* Updated cart item styles to match reference */
.cart-item {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  border-bottom: 1px solid #f0f0f0;
  padding: 16px 0;
  margin-bottom: 8px;
}

.cart-item:last-child {
  border-bottom: none;
  margin-bottom: 0;
}

.cart-item img {
  width: 70px;
  height: 70px;
  object-fit: cover;
  border-radius: 8px;
  flex-shrink: 0;
}

.cart-info {
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.cart-item-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  width: 100%;
}

.cart-item-left {
  flex-grow: 1;
}

.cart-title {
  font-weight: 600;
  font-size: 15px;
  color: #333;
  margin-bottom: 2px;
  line-height: 1.3;
}

.cart-size {
  font-size: 13px;
  color: #666;
  margin-bottom: 4px;
}

.cart-item-right {
  text-align: right;
  flex-shrink: 0;
}

.cart-current-price {
  font-weight: 600;
  font-size: 16px;
  color: #333;
  margin-bottom: 2px;
}

.cart-original-price {
  font-size: 13px;
  color: #999;
  text-decoration: line-through;
}

.cart-item-controls {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 4px;
}

.qty-controls {
  display: flex;
  align-items: center;
  gap: 0;
  border: 1px solid #ddd;
  border-radius: 6px;
  background: #fff;
}

.qty-btn {
  background: none;
  border: none;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  color: #666;
  cursor: pointer;
  transition: all 0.2s ease;
}

.qty-btn:hover {
  background: #f5f5f5;
  color: #333;
}

.qty-btn:first-child {
  border-radius: 5px 0 0 5px;
}

.qty-btn:last-child {
  border-radius: 0 5px 5px 0;
}

.qty-display {
  min-width: 40px;
  text-align: center;
  font-weight: 500;
  font-size: 14px;
  padding: 0 8px;
  border-left: 1px solid #ddd;
  border-right: 1px solid #ddd;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.remove-btn {
  font-size: 13px;
  color: #dc3545;
  background: none;
  border: none;
  cursor: pointer;
  padding: 4px 8px;
  border-radius: 4px;
  transition: all 0.2s ease;
  font-weight: 500;
}

.remove-btn:hover {
  background: #ffeaea;
  color: #c82333;
}

/* Keep all your existing styles for other elements */
.cart-footer {
  padding: 1rem;
  border-top: 1px solid #eee;
}

.recommendations {
  border-top: 1px solid #eee;
  padding: 1rem;
}

.recommend-title {
  font-size: 13px;
  font-weight: 600;
  margin-bottom: 8px;
  text-transform: uppercase;
}

.cart-count-badge {
  position: absolute;
  top: -2px;
  right: -6px;
  background: #dc3545;
  color: white;
  font-size: 12px;
  width: 18px;
  height: 18px;
  display: flex;
  justify-content: center;
  align-items: center;
  border-radius: 50%;
}

@media(max-width: 420px){
  .cart-sidebar {
    width: 100%;
  }
  
  .cart-item {
    gap: 10px;
  }
  
  .cart-item img {
    width: 60px;
    height: 60px;
  }
  
  .cart-title {
    font-size: 14px;
  }
  
  .cart-current-price {
    font-size: 15px;
  }
}

.scroll-row {
  display: flex;
  overflow-x: auto;
  gap: 12px;
  padding-bottom: 8px;
  scrollbar-width: none;
}

.scroll-row::-webkit-scrollbar {
  display: none;
}

html, body {
  height: 100%;
  scroll-behavior: smooth;
}

body {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.page-wrapper {
  flex: 1;
}

.custom-scroll-wrap .product-card-wrapper {
  flex: 0 0 auto;
  width: 155px; /* or 180px, adjust as needed */
  margin-right: -1px;
}

.custom-scroll-wrap .product-card {
  height: auto; /* optional if height restriction causes cutoff */
}
</style>

</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="/adaaromas/index.php">ADA AROMAS</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?= in_array($currentPage, ['perfume-men.php', 'perfume-women.php']) ? 'active fw-bold text-primary' : '' ?>" 
             href="/adaaromas/products/perfume.php" id="perfumeDropdown" data-bs-toggle="dropdown">
            Perfume
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="/adaaromas/products/perfume-men.php">For Him</a></li>
            <li><a class="dropdown-item" href="/adaaromas/products/perfume-women.php">For Her</a></li>
            <li><a class="dropdown-item" href="/adaaromas/products/perfume.php">All Collection</a></li>
          </ul>
        </li>
        <li class="nav-item"><a class="nav-link <?= $currentPage === 'attar.php' ? 'active fw-bold text-primary' : '' ?>" href="/adaaromas/products/attar.php">Attar</a></li>
        <li class="nav-item"><a class="nav-link <?= $currentPage === 'Essenceoil.php' ? 'active fw-bold text-primary' : '' ?>" href="/adaaromas/products/Essenceoil.php">Essence Oil</a></li>
        <li class="nav-item"><a class="nav-link <?= $currentPage === 'diffuser.php' ? 'active fw-bold text-primary' : '' ?>" href="/adaaromas/products/diffuser.php">Diffuser</a></li>
        <!-- <li class="nav-item"><a class="nav-link <?= $currentPage === 'contact.php' ? 'active fw-bold text-primary' : '' ?>" href="/adaaromas/contact.php">Contact</a></li> -->
        <li class="nav-item position-relative">
          <a class="nav-link" href="javascript:void(0)" onclick="toggleCartSidebar()">
            <i class="bi bi-cart3 fs-5"></i>
            <span class="cart-count-badge" id="cartCount">0</span>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Cart Sidebar -->
<div id="cartSidebar" class="cart-sidebar">
  <div class="cart-header">
    <span><i class="bi bi-bag me-2"></i><span id="cartItemCount">0 items</span></span>
    <button class="btn-close" onclick="toggleCartSidebar()"></button>
  </div>

  <div class="cart-body" id="cartItems"><p class="text-muted">No items in cart.</p></div>

<div class="recommendations custom-scroll-wrap">
  <div class="recommend-title">You May Also Like</div>
  <div id="recommendationBox" class="scroll-row"></div>
</div>

<div id="hiddenRecommendations" style="display: none;">
<?php 
include __DIR__ . '/../config/db.php';

try {
  $stmt = $conn->prepare("
    SELECT 
      p.productId, p.name, p.asp, p.mrp, p.rating, p.reviewCount, 
      p.image, p.createdAt,
      MIN(ps.size) as size,
      MIN(ps.stockInHand) as stockInHand
    FROM products p
    INNER JOIN product_stock ps ON p.productId = ps.productId
    WHERE ps.stockInHand > 0
    GROUP BY p.productId, p.name, p.asp, p.mrp, p.rating, p.reviewCount, p.image, p.createdAt
    ORDER BY p.rating DESC, p.reviewCount DESC
    LIMIT 10
  ");
  $stmt->execute();
  $cartProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

  foreach ($cartProducts as $product) {
    $productId = $product['productId'];
    $title = $product['name'];
    $price = $product['asp'];
    $mrp = $product['mrp'];
  $basePath = (substr_count($_SERVER['PHP_SELF'], '/') > 2) ? "../" : "";
$image = $basePath . $product['image'];

    $size = $product['size'];
    $inStock = $product['stockInHand'] > 0;
    $createdAt = $product['createdAt'];

    $discount = ($mrp > $price) ? round((($mrp - $price) / $mrp) * 100) : 0;

include __DIR__ . '/../components/product-card.php';

  }

} catch (Exception $e) {
  echo "<div style='color:red;'>Error: " . $e->getMessage() . "</div>";
}
?>
</div>



  <div class="cart-footer">
    <a href="/adaaromas/checkout.php" class="btn btn-dark w-100">
      <i class="bi bi-lock"></i> Checkout • <span id="cartTotal">₹0</span>
    </a>
  </div>
</div>
<div class="page-wrapper">

