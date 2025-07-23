<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ADA AROMAS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<style>
/* Professional Navbar Styles */
* {
  font-family: 'Inter', sans-serif;
}

.navbar {
  background: #ffffff !important;
  border-bottom: 1px solid #e8e9ea;
  padding: 1rem 0;
  transition: all 0.3s ease;
  box-shadow: 0 2px 4px rgba(0,0,0,0.04);
}

.navbar.scrolled {
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  border-bottom: 1px solid #dee2e6;
}

.navbar-brand {
  font-weight: 700 !important;
  font-size: 1.75rem !important;
  color: #2c3e50 !important;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  transition: all 0.3s ease;
}

.navbar-brand:hover {
  color: #8b4513 !important;
}

/* Mobile Navigation Controls */
.mobile-nav-controls {
  display: none;
  align-items: center;
  gap: 1rem;
}

.hamburger-btn {
  background: transparent !important;
  border: 1px solid #dee2e6 !important;
  padding: 0.5rem !important;
  border-radius: 6px !important;
  transition: all 0.3s ease !important;
  color: #495057 !important;
  width: 44px;
  height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.hamburger-btn:hover {
  background: #f8f9fa !important;
  border-color: #8b4513 !important;
  color: #8b4513 !important;
}

.hamburger-btn:focus {
  box-shadow: 0 0 0 0.2rem rgba(139, 69, 19, 0.25) !important;
}

.mobile-separator {
  width: 1px;
  height: 24px;
  background: #dee2e6;
}

.mobile-cart-btn {
  background: transparent !important;
  border: 1px solid #dee2e6 !important;
  padding: 0.5rem !important;
  border-radius: 6px !important;
  transition: all 0.3s ease !important;
  color: #495057 !important;
  width: 44px;
  height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

.mobile-cart-btn:hover {
  background: #f8f9fa !important;
  border-color: #8b4513 !important;
  color: #8b4513 !important;
}

.mobile-cart-btn .cart-count-badge {
  position: absolute;
  top: -2px;
  right: -2px;
  background: #8b4513 !important;
  color: white;
  font-size: 10px;
  font-weight: 600;
  width: 16px;
  height: 16px;
  display: flex;
  justify-content: center;
  align-items: center;
  border-radius: 50%;
  border: 2px solid #fff;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Desktop Navigation */
.navbar-toggler {
  border: 1px solid #dee2e6 !important;
  padding: 0.375rem 0.75rem;
  border-radius: 6px;
  transition: all 0.3s ease;
}

.navbar-toggler:focus {
  box-shadow: 0 0 0 0.2rem rgba(139, 69, 19, 0.25) !important;
}

.navbar-toggler-icon {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%2844, 62, 80, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='m4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
}

.navbar-nav {
  align-items: center;
  gap: 0.25rem;
}

.nav-link {
  color: #495057 !important;
  font-weight: 500 !important;
  font-size: 0.95rem !important;
  padding: 0.75rem 1.25rem !important;
  border-radius: 6px !important;
  transition: all 0.3s ease !important;
  position: relative;
  text-transform: capitalize;
}

.nav-link:hover {
  color: #8b4513 !important;
  background: #f8f9fa !important;
}

.nav-link.active {
  color: #8b4513 !important;
  background: #f8f9fa !important;
  font-weight: 600 !important;
}

/* Professional Dropdown */
.dropdown-menu {
  background: #ffffff !important;
  border: 1px solid #e9ecef !important;
  border-radius: 8px !important;
  box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
  padding: 0.5rem 0 !important;
  margin-top: 0.25rem !important;
  min-width: 180px;
}

.dropdown-item {
  padding: 0.75rem 1.25rem !important;
  font-weight: 500 !important;
  color: #495057 !important;
  transition: all 0.2s ease !important;
  font-size: 0.9rem;
}

.dropdown-item:hover {
  background: #f8f9fa !important;
  color: #8b4513 !important;
}

.dropdown-item:active {
  background: #f8f9fa !important;
  color: #8b4513 !important;
}

/* Professional Cart Icon */
.cart-icon-wrapper {
  position: relative;
  padding: 0.75rem 1.25rem !important;
  background: transparent;
  border-radius: 6px;
  transition: all 0.3s ease;
  border: 1px solid transparent;
}

.cart-icon-wrapper:hover {
  background: #f8f9fa !important;
  border-color: #e9ecef;
}

.cart-icon {
  font-size: 1.2rem !important;
  color: #495057 !important;
  transition: all 0.3s ease;
}

.cart-icon-wrapper:hover .cart-icon {
  color: #8b4513 !important;
}

.cart-count-badge {
  position: absolute;
  top: 8px;
  right: 8px;
  background: #8b4513 !important;
  color: white;
  font-size: 11px;
  font-weight: 600;
  width: 18px;
  height: 18px;
  display: flex;
  justify-content: center;
  align-items: center;
  border-radius: 50%;
  border: 2px solid #fff;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Navigation Sidebar */
.nav-sidebar {
  position: fixed;
  top: 0;
  left: -100%;
  width: 320px;
  height: 100vh;
  background: #ffffff;
  box-shadow: 4px 0 10px rgba(0,0,0,0.2);
  z-index: 1055;
  transition: left 0.35s ease-in-out;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
}

.nav-sidebar.open {
  left: 0;
}

.nav-sidebar-header {
  padding: 1.5rem;
  background: #ffffff;
  color: #2c3e50;
  border-bottom: 1px solid #e9ecef;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.nav-sidebar-title {
  font-weight: 700;
  font-size: 1.25rem;
  color: #2c3e50;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.nav-close-btn {
  background: #ffffff !important;
  border: 1px solid #e9ecef !important;
  border-radius: 8px !important;
  width: 40px !important;
  height: 40px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  transition: all 0.3s ease !important;
  color: #6c757d !important;
  cursor: pointer !important;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05) !important;
}

.nav-close-btn:hover {
  background: #f8f9fa !important;
  border-color: #dee2e6 !important;
  color: #343a40 !important;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
  transform: scale(1.05) !important;
}

.nav-sidebar-body {
  flex-grow: 1;
  padding: 1.5rem;
}

.nav-sidebar-menu {
  list-style: none;
  padding: 0;
  margin: 0;
}

.nav-sidebar-item {
  margin-bottom: 0.5rem;
}

.nav-sidebar-link {
  display: flex;
  align-items: center;
  padding: 1rem 1.25rem;
  color: #495057;
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.3s ease;
  font-weight: 500;
  font-size: 1rem;
  border: 1px solid transparent;
}

.nav-sidebar-link:hover {
  background: #f8f9fa;
  color: #8b4513;
  border-color: #e9ecef;
  text-decoration: none;
}

.nav-sidebar-link.active {
  background: #f8f9fa;
  color: #8b4513;
  border-color: #e9ecef;
  font-weight: 600;
}

.nav-sidebar-link i {
  margin-right: 0.75rem;
  font-size: 1.1rem;
  width: 20px;
  text-align: center;
}

/* Dropdown in sidebar */
.nav-sidebar-dropdown {
  margin-bottom: 0.5rem;
}

.nav-sidebar-dropdown-toggle {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 1.25rem;
  color: #495057;
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.3s ease;
  font-weight: 500;
  font-size: 1rem;
  border: 1px solid transparent;
  background: none;
  width: 100%;
  text-align: left;
}

.nav-sidebar-dropdown-toggle:hover {
  background: #f8f9fa;
  color: #8b4513;
  border-color: #e9ecef;
}

.nav-sidebar-dropdown-toggle i.dropdown-icon {
  transition: transform 0.3s ease;
}

.nav-sidebar-dropdown-toggle[aria-expanded="true"] i.dropdown-icon {
  transform: rotate(180deg);
}

.nav-sidebar-dropdown-menu {
  padding-left: 1rem;
  margin-top: 0.5rem;
  border-left: 2px solid #f8f9fa;
}

.nav-sidebar-dropdown-item {
  display: block;
  padding: 0.75rem 1.25rem;
  color: #6c757d;
  text-decoration: none;
  border-radius: 6px;
  transition: all 0.3s ease;
  font-size: 0.95rem;
  margin-bottom: 0.25rem;
}

.nav-sidebar-dropdown-item:hover {
  background: #f8f9fa;
  color: #8b4513;
  text-decoration: none;
}

/* Overlay */
.nav-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  z-index: 1050;
  opacity: 0;
  visibility: hidden;
  transition: all 0.3s ease;
}

.nav-overlay.show {
  opacity: 1;
  visibility: visible;
}

/* Professional Cart Header */
.cart-header {
  padding: 1.5rem;
  background: #ffffff;
  color: #2c3e50;
  border-bottom: 1px solid #e9ecef;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.cart-header-content {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.cart-header-icon {
  font-size: 1.25rem;
  color: #8b4513;
}

.cart-header-text {
  font-weight: 600;
  font-size: 1.1rem;
  color: #2c3e50;
}

.cart-header-count {
  background: #f8f9fa;
  color: #6c757d;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 500;
  border: 1px solid #e9ecef;
}

.cart-close-btn {
  background: #ffffff !important;
  border: 1px solid #e9ecef !important;
  border-radius: 8px !important;
  width: 40px !important;
  height: 40px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  transition: all 0.3s ease !important;
  color: #6c757d !important;
  cursor: pointer !important;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05) !important;
}

.cart-close-btn:hover {
  background: #f8f9fa !important;
  border-color: #dee2e6 !important;
  color: #343a40 !important;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
  transform: scale(1.05) !important;
}

.cart-close-btn:active {
  background: #e9ecef !important;
  transform: scale(0.98) !important;
  box-shadow: 0 1px 2px rgba(0,0,0,0.1) !important;
}

.cart-close-btn i {
  font-size: 16px !important;
  font-weight: 600 !important;
}

/* Mobile Responsiveness */
@media (max-width: 991.98px) {
  .mobile-nav-controls {
    display: flex;
  }
  
  .navbar-collapse {
    display: none !important;
  }
  
  .nav-sidebar {
    width: 280px;
  }
}

@media (max-width: 576px) {
  .navbar-brand {
    font-size: 1.5rem !important;
  }
  
  .nav-sidebar {
    width: 100%;
  }
  
  .cart-header {
    padding: 1rem;
  }
  
  .cart-header-text {
    font-size: 1rem;
  }
  
  .cart-sidebar {
    width: 100%;
  }
}

/* Keep all your existing cart sidebar styles */
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
  color: red;
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
  width: 155px;
  margin-right: -1px;
}

.custom-scroll-wrap .product-card {
  height: auto;
}
</style>
</head>

<body>
<!-- Navigation Overlay -->
<div id="navOverlay" class="nav-overlay" onclick="toggleNavSidebar()"></div>

<!-- Navigation Sidebar -->
<div id="navSidebar" class="nav-sidebar">
  <div class="nav-sidebar-header">
    <div class="nav-sidebar-title">ADA AROMAS</div>
    <button class="nav-close-btn" onclick="toggleNavSidebar()" aria-label="Close">
      <i class="bi bi-x-lg"></i>
    </button>
  </div>
  
  <div class="nav-sidebar-body">
    <ul class="nav-sidebar-menu">
      <li class="nav-sidebar-item">
        <a class="nav-sidebar-link <?= $currentPage === 'index.php' ? 'active' : '' ?>" href="/adaaromas/index.php">
          <i class="bi bi-house"></i>
          Home
        </a>
      </li>
      
      <li class="nav-sidebar-dropdown">
        <button class="nav-sidebar-dropdown-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#perfumeSubmenu" aria-expanded="false">
          <span><i class="bi bi-droplet"></i> Perfume</span>
          <i class="bi bi-chevron-down dropdown-icon"></i>
        </button>
        <div class="collapse nav-sidebar-dropdown-menu" id="perfumeSubmenu">
          <a class="nav-sidebar-dropdown-item" href="/adaaromas/products/perfume-men.php">For Him</a>
          <a class="nav-sidebar-dropdown-item" href="/adaaromas/products/perfume-women.php">For Her</a>
          <a class="nav-sidebar-dropdown-item" href="/adaaromas/products/perfume.php">All Collection</a>
        </div>
      </li>
      
      <li class="nav-sidebar-item">
        <a class="nav-sidebar-link <?= $currentPage === 'attar.php' ? 'active' : '' ?>" href="/adaaromas/products/attar.php">
          <i class="bi bi-flower1"></i>
          Attar
        </a>
      </li>
      
      <li class="nav-sidebar-item">
        <a class="nav-sidebar-link <?= $currentPage === 'Essenceoil.php' ? 'active' : '' ?>" href="/adaaromas/products/Essenceoil.php">
          <i class="bi bi-droplet-half"></i>
          Essence Oil
        </a>
      </li>
      
      <li class="nav-sidebar-item">
        <a class="nav-sidebar-link <?= $currentPage === 'diffuser.php' ? 'active' : '' ?>" href="/adaaromas/products/diffuser.php">
          <i class="bi bi-wind"></i>
          Diffuser
        </a>
      </li>
    </ul>
  </div>
</div>

<!-- Professional Navbar -->
<nav class="navbar navbar-expand-lg sticky-top" id="mainNavbar">
  <div class="container">
    <a class="navbar-brand" href="/adaaromas/index.php">ADA AROMAS</a>
    
    <!-- Mobile Navigation Controls -->
    <div class="mobile-nav-controls">
      <button class="hamburger-btn" onclick="toggleNavSidebar()" aria-label="Open Menu">
        <i class="bi bi-list"></i>
      </button>
      
      <div class="mobile-separator"></div>
      
      <button class="mobile-cart-btn" onclick="toggleCartSidebar()" aria-label="Open Cart">
        <i class="bi bi-bag"></i>
        <span class="cart-count-badge" id="mobileCartCount">0</span>
      </button>
    </div>
    
    <!-- Desktop Navigation -->
    <button class="navbar-toggler d-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?= in_array($currentPage, ['perfume-men.php', 'perfume-women.php']) ? 'active' : '' ?>"
             href="#" id="perfumeDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Perfume
          </a>
          <ul class="dropdown-menu" aria-labelledby="perfumeDropdown">
            <li><a class="dropdown-item" href="/adaaromas/products/perfume-men.php">For Him</a></li>
            <li><a class="dropdown-item" href="/adaaromas/products/perfume-women.php">For Her</a></li>
            <li><a class="dropdown-item" href="/adaaromas/products/perfume.php">All Collection</a></li>
          </ul>
        </li>
        
        <li class="nav-item">
          <a class="nav-link <?= $currentPage === 'attar.php' ? 'active' : '' ?>" href="/adaaromas/products/attar.php">
            Attar
          </a>
        </li>
        
        <li class="nav-item">
          <a class="nav-link <?= $currentPage === 'Essenceoil.php' ? 'active' : '' ?>" href="/adaaromas/products/Essenceoil.php">
            Essence Oil
          </a>
        </li>
        
        <li class="nav-item">
          <a class="nav-link <?= $currentPage === 'diffuser.php' ? 'active' : '' ?>" href="/adaaromas/products/diffuser.php">
            Diffuser
          </a>
        </li>
        
        <li class="nav-item">
          <a class="nav-link cart-icon-wrapper" href="javascript:void(0)" onclick="toggleCartSidebar()">
            <i class="bi bi-bag cart-icon"></i>
            <span class="cart-count-badge" id="cartCount">0</span>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Professional Cart Sidebar -->
<div id="cartSidebar" class="cart-sidebar">
  <div class="cart-header">
    <div class="cart-header-content">
      <i class="bi bi-bag cart-header-icon"></i>
      <span class="cart-header-text">Shopping Bag</span>
      <span class="cart-header-count" id="cartItemCount">0 items</span>
    </div>
    <button class="cart-close-btn" onclick="toggleCartSidebar()" aria-label="Close">
      <i class="bi bi-x-lg"></i>
    </button>
  </div>
  
  <div class="cart-body" id="cartItems">
    <p class="text-muted">No items in cart.</p>
  </div>
  
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Enhanced navbar scroll effect
window.addEventListener('scroll', function() {
  const navbar = document.getElementById('mainNavbar');
  if (window.scrollY > 50) {
    navbar.classList.add('scrolled');
  } else {
    navbar.classList.remove('scrolled');
  }
});

// Navigation Sidebar Functions
function toggleNavSidebar() {
  const navSidebar = document.getElementById('navSidebar');
  const navOverlay = document.getElementById('navOverlay');
  
  if (navSidebar.classList.contains('open')) {
    navSidebar.classList.remove('open');
    navOverlay.classList.remove('show');
    document.body.style.overflow = '';
  } else {
    navSidebar.classList.add('open');
    navOverlay.classList.add('show');
    document.body.style.overflow = 'hidden';
  }
}

// Cart Sidebar Functions (keep your existing cart functionality)
function toggleCartSidebar() {
  const cartSidebar = document.getElementById('cartSidebar');
  
  if (cartSidebar.classList.contains('open')) {
    cartSidebar.classList.remove('open');
    document.body.style.overflow = '';
  } else {
    cartSidebar.classList.add('open');
    document.body.style.overflow = 'hidden';
  }
}

// Close sidebars when clicking outside
document.addEventListener('click', function(event) {
  const navSidebar = document.getElementById('navSidebar');
  const cartSidebar = document.getElementById('cartSidebar');
  const hamburgerBtn = document.querySelector('.hamburger-btn');
  const cartBtn = document.querySelector('.mobile-cart-btn');
  const desktopCartBtn = document.querySelector('.cart-icon-wrapper');
  
  // Close nav sidebar if clicking outside
  if (!navSidebar.contains(event.target) && !hamburgerBtn.contains(event.target)) {
    if (navSidebar.classList.contains('open')) {
      toggleNavSidebar();
    }
  }
  
  // Close cart sidebar if clicking outside
  if (!cartSidebar.contains(event.target) && 
      !cartBtn?.contains(event.target) && 
      !desktopCartBtn?.contains(event.target)) {
    if (cartSidebar.classList.contains('open')) {
      toggleCartSidebar();
    }
  }
});

// Sync cart count between mobile and desktop
function updateCartCount(count) {
  document.getElementById('cartCount').textContent = count;
  document.getElementById('mobileCartCount').textContent = count;
  document.getElementById('cartItemCount').textContent = count + ' items';
}

// Handle window resize
window.addEventListener('resize', function() {
  if (window.innerWidth > 991) {
    const navSidebar = document.getElementById('navSidebar');
    const navOverlay = document.getElementById('navOverlay');
    
    if (navSidebar.classList.contains('open')) {
      navSidebar.classList.remove('open');
      navOverlay.classList.remove('show');
      document.body.style.overflow = '';
    }
  }
});
</script>
</body>
</html>
