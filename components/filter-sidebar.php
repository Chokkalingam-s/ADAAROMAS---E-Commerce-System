<div class="col-md-3 mb-4 mb-md-0">
  <h5>Filters</h5>
  <div class="border rounded p-3 bg-light">

    <!-- Availability -->
    <div class="mb-3">
      <h6 class="mb-2">Availability</h6>
      <div>
        <input type="checkbox" id="inStock" checked>
        <label for="inStock">In stock (<?= $availableCount ?? 0 ?>)</label>
      </div>
      <div>
        <input type="checkbox" id="outOfStock">
        <label for="outOfStock">Out of stock (<?= $outOfStockCount ?? 0 ?>)</label>
      </div>
    </div>

    <!-- Price Range -->
    <div>
      <h6 class="mb-2">Price</h6>
      <input type="range" class="form-range" min="0" max="2000" id="priceRange" step="10">
      <div class="d-flex justify-content-between mt-2">
        <input type="text" id="minPrice" class="form-control w-45" value="0" readonly>
        <span class="mx-2">to</span>
        <input type="text" id="maxPrice" class="form-control w-45" value="2000" readonly>
      </div>
    </div>
  </div>
</div>
