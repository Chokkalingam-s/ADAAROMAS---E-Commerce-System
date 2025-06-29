<?php 
$currentPage = basename($_SERVER['PHP_SELF']); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ADA Aromas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/style.css"/>
  <style>
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
    .cart-item {
     display: flex;
  align-items: flex-start;
  gap: 10px;
  border-bottom: 1px solid #eee;
  padding-bottom: 15px;
    }
    .cart-item img {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 5px;
    }
    .cart-info {
      flex-grow: 1;
    }
    .cart-title {
      font-weight: 500;
      font-size: 14px;
      margin-bottom: 4px;
    }
    .cart-price {
      font-size: 14px;
      display: flex;
      gap: 6px;
      align-items: center;
    }
    .original-price {
      text-decoration: line-through;
      color: #999;
      font-size: 13px;
    }
    .qty-controls {
      display: flex;
      align-items: center;
      gap: 6px;
      margin-top: 6px;
    }
    .qty-controls button {
      padding: 2px 6px;
      font-size: 12px;
    }
    .remove-btn {
      font-size: 14px;
      color: #dc3545;
      background: none;
      border: none;
      margin-top: 4px;
      padding: 0;
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
    .rec-item {
      display: flex;
      gap: 10px;
      margin-bottom: 12px;
      align-items: center;
    }
    .rec-item img {
      width: 50px;
      height: 50px;
      object-fit: cover;
    }
    .rec-item .title {
      font-size: 13px;
      font-weight: 500;
      margin: 0;
    }
    .rec-item .price {
      font-size: 13px;
    }
    .rec-item .old {
      text-decoration: line-through;
      font-size: 12px;
      color: gray;
    }
    .rec-item .star {
      color: #ffc107;
      font-size: 12px;
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
.rec-card {
  flex: 0 0 160px;
  border: 1px solid #eee;
  border-radius: 8px;
  padding: 8px;
  background: #f9f9f9;
}
.rec-card img {
  width: 100%;
  height: 100px;
  object-fit: cover;
  border-radius: 5px;
}
.rec-title {
  font-size: 13px;
  font-weight: 600;
  margin: 6px 0 4px;
}
.rec-price {
  font-size: 13px;
}
.rec-old {
  text-decoration: line-through;
  color: gray;
  font-size: 12px;
}
.rec-star {
  font-size: 12px;
  color: #ffc107;
}
.rec-add {
  font-size: 12px;
  color: #007bff;
  cursor: pointer;
}

.rating-stars .star {
  font-size: 2rem;
  color: #ccc;
  cursor: pointer;
  transition: color 0.3s;
}
.rating-stars .star:hover,
.rating-stars .star.selected {
  color: #ffc107;
}


  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="/adaaromas/index.php">ADA Aromas</a>
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
        <li class="nav-item"><a class="nav-link <?= $currentPage === 'contact.php' ? 'active fw-bold text-primary' : '' ?>" href="/adaaromas/contact.php">Contact</a></li>
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

<div class="recommendations">
  <div class="recommend-title">You May Also Like</div>
  <div id="recommendationBox" class="scroll-row"></div>
</div>

  <div class="cart-footer">
    <a href="/adaaromas/checkout.php" class="btn btn-dark w-100">
      <i class="bi bi-lock"></i> Checkout • <span id="cartTotal">₹0</span>
    </a>
  </div>
</div>


