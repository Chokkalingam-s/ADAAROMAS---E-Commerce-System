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
  <link href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.0/nouislider.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/style.css"/>
  <style>
    .cart-sidebar {
      position: fixed;
      top: 0;
      right: -400px;
      width: 350px;
      height: 100vh;
      background: #fff;
      box-shadow: -2px 0 10px rgba(0,0,0,0.1);
      z-index: 1050;
      transition: right 0.3s ease-in-out;
      overflow-y: auto;
    }
    .cart-sidebar.open {
      right: 0;
    }
    .cart-sidebar-header {
      padding: 1rem;
      border-bottom: 1px solid #ccc;
    }
    .cart-sidebar-body {
      padding: 1rem;
    }
    .cart-item {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 1rem;
    }
    .cart-item img {
      width: 60px;
      height: 60px;
      object-fit: cover;
    }
    .cart-count-badge {
      position: absolute;
      top: 0;
      right: -5px;
      background: red;
      color: white;
      border-radius: 50%;
      font-size: 12px;
      width: 18px;
      height: 18px;
      display: flex;
      align-items: center;
      justify-content: center;
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

        <!-- Perfume Dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?= in_array($currentPage, ['perfume-men.php', 'perfume-women.php']) ? 'active fw-bold text-primary' : '' ?>" 
             href="#" id="perfumeDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Perfume
          </a>
          <ul class="dropdown-menu" aria-labelledby="perfumeDropdown">
            <li><a class="dropdown-item" href="/adaaromas/products/perfume-men.php">For Him</a></li>
            <li><a class="dropdown-item" href="/adaaromas/products/perfume-women.php">For Her</a></li>
          </ul>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= $currentPage === 'attar.php' ? 'active fw-bold text-primary' : '' ?>" href="/adaaromas/products/attar.php">Attar</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $currentPage === 'oud.php' ? 'active fw-bold text-primary' : '' ?>" href="/adaaromas/products/oud.php">Oud</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $currentPage === 'contact.php' ? 'active fw-bold text-primary' : '' ?>" href="/adaaromas/contact.php">Contact</a>
        </li>
        <li class="nav-item position-relative">
          <a class="nav-link <?= $currentPage === 'cart.php' ? 'active fw-bold text-primary' : '' ?>" href="#" onclick="toggleCartSidebar()">
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
  <div class="cart-sidebar-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Your Cart</h5>
    <button class="btn-close" onclick="toggleCartSidebar()"></button>
  </div>
  <div class="cart-sidebar-body" id="cartItems">
    <p class="text-muted">No items in cart.</p>
  </div>
 <div class="p-3 border-top">
  <a href="/adaaromas/checkout.php" class="btn btn-primary w-100">Go to Checkout</a>
</div>

</div>


