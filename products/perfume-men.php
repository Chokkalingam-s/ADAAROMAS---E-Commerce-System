<?php
$category = "Perfume - Men";
$pageTitle = "Men's Perfume Collection";
include "../components/header.php"; // Contains opening HTML tags and navbar
?>

<div class="container-fluid mt-4 mb-5">
  <h2 class="text-center mb-4"><?= $pageTitle ?></h2>

  <div class="row">
    <?php include "../components/filter-sidebar.php"; ?>

    <!-- Products Grid -->
    <div class="col-md-9">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <span><strong>42 products</strong></span>
        <?php include "../components/sort-dropdown.php"; ?>
      </div>

      <div class="row g-4">
        <?php
        $products = [
          [
            "title" => "Ombre Nomade",
            "image" => "../assets/images/image.png",
            "price" => 849,
            "mrp" => 1999,
            "discount" => 58,
            "rating" => "5.0",
            "reviews" => 1
          ],
          [
            "title" => "Flora",
            "image" => "../assets/images/image.png",
            "price" => 849,
            "mrp" => 1999,
            "discount" => 58,
            "rating" => "4.04",
            "reviews" => 90
          ],
          // Add more...
        ];

        foreach ($products as $p) {
          extract($p);
          include "../components/product-card.php";
        }
        ?>
      </div>
    </div>
  </div>
</div>

<?php include "../components/footer.php"; ?>
