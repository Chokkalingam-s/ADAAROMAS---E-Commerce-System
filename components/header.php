<?php
$currentPage = basename($_SERVER['PHP_SELF']); // gets current file name like 'index.php'
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
</head>
<body>

<!-- Navbar -->
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="/index.php">ADA Aromas</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">

        <!-- Perfume Dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?= in_array($currentPage, ['perfume-men.php', 'perfume-women.php']) ? 'active fw-bold text-primary' : '' ?>" 
             href="#" id="perfumeDropdown" 
             role="button" 
             data-bs-toggle="dropdown" 
             aria-expanded="false"
             onclick="return false;"> <!-- Prevent clicking -->
            Perfume
          </a>
          <ul class="dropdown-menu" aria-labelledby="perfumeDropdown">
            <li><a class="dropdown-item" href="/adaaromas/products/perfume-men.php">For Him</a></li>
            <li><a class="dropdown-item" href="/adaaromas/products/perfume-women.php">For Her</a></li>
          </ul>
        </li>

        <!-- Other Items -->
        <li class="nav-item">
          <a class="nav-link <?= $currentPage === 'attar.php' ? 'active fw-bold text-primary' : '' ?>" href="/adaaromas/products/attar.php">Attar</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $currentPage === 'oud.php' ? 'active fw-bold text-primary' : '' ?>" href="/adaaromas/products/oud.php">Oud</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $currentPage === 'about.php' ? 'active fw-bold text-primary' : '' ?>" href="/adaaromas/about.php">About</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $currentPage === 'contact.php' ? 'active fw-bold text-primary' : '' ?>" href="/adaaromas/contact.php">Contact</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $currentPage === 'cart.php' ? 'active fw-bold text-primary' : '' ?>" href="/adaaromas/cart.php">Cart</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

