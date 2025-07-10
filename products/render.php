<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.js"></script>

<style>
  .product-listing-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 1rem;
  }

  .page-title {
    font-size: 1.75rem;
    font-weight: 300;
    text-align: center;
    margin-bottom: 2rem;
    color: #2c2c2c;
    letter-spacing: 0.5px;
  }

  /* Mobile Filter/Sort Buttons */
  .mobile-controls {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
  }

  .mobile-control-btn {
    flex: 1;
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    font-size: 0.95rem;
    color: #2c2c2c;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .mobile-control-btn:hover {
    background: #f8f9fa;
    border-color: #ccc;
  }

  .mobile-control-btn svg {
    width: 16px;
    height: 16px;
  }

  /* Desktop Sidebar */
  .filters-sidebar {
    background: #fff;
    border-radius: 8px;
    padding: 1.5rem;
    height: fit-content;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    border: 1px solid #f0f0f0;
  }

  .filters-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: #2c2c2c;
    border-bottom: 2px solid #f8f9fa;
    padding-bottom: 0.5rem;
  }

  .filter-section {
    margin-bottom: 1.25rem;
    border-bottom: 1px solid #f0f0f0;
    padding-bottom: 1rem;
  }

  .filter-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
  }

  .filter-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.75rem;
    cursor: pointer;
    padding: 0.25rem 0;
  }

  .filter-label {
    font-weight: 600;
    color: #2c2c2c;
    font-size: 1rem;
  }

  .chevron {
    font-size: 0.8rem;
    color: #666;
    transition: transform 0.3s ease;
  }

  .chevron.rotated {
    transform: rotate(180deg);
  }

  .filter-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
  }

  .filter-content.open {
    max-height: 150px;
  }

  .filter-tags {
    margin-bottom: 1rem;
  }

  .filter-tag {
    display: inline-flex;
    align-items: center;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 20px;
    padding: 0.4rem 0.8rem;
    font-size: 0.875rem;
    color: #495057;
    margin-right: 0.5rem;
    margin-bottom: 0.5rem;
  }

  .filter-tag-close {
    margin-left: 0.5rem;
    color: #6c757d;
    text-decoration: none;
    font-weight: bold;
    font-size: 1.1rem;
  }

  .filter-tag-close:hover {
    color: #dc3545;
  }

  .form-check-label {
    font-size: 0.95rem;
    color: #495057;
    cursor: pointer;
    padding: 0.3rem 0;
    display: flex;
    align-items: center;
  }

  .form-check-input {
    margin-right: 0.75rem;
    border-radius: 3px;
  }

  .stock-count {
    color: #6c757d;
    font-size: 0.875rem;
    margin-left: auto;
  }

  #priceSlider, #mobilePriceSlider {
    margin: 0.75rem 0;
  }

  .noUi-connect {
    background: #2c2c2c;
  }

  .noUi-handle {
    border: 2px solid #2c2c2c;
    background: #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
  }

  .price-display {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 0.75rem;
  }

  .price-box {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    padding: 0.4rem 0.6rem;
    font-weight: 500;
    color: #2c2c2c;
    min-width: 70px;
    text-align: center;
    font-size: 0.9rem;
  }

  /* Products Grid - FIXED: Proper spacing without affecting card size */
  .products-grid {
    margin: 0 -0.5rem; /* Negative margin to offset column padding */
  }

  .products-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f0f0f0;
  }

  .products-count {
    color: #6c757d;
    font-size: 1rem;
    text-align: center;
    margin-bottom: 1.5rem;
  }

  .sort-dropdown {
    border: 1px solid #e9ecef;
    border-radius: 6px;
    padding: 0.5rem 1rem;
    background: #fff;
    color: #495057;
    font-size: 0.95rem;
  }

  /* Bottom Modal */
  .modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
  }

  .modal-overlay.active {
    opacity: 1;
    visibility: visible;
  }

  .bottom-modal {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #fff;
    border-radius: 16px 16px 0 0;
    padding: 1.25rem;
    transform: translateY(100%);
    transition: transform 0.3s ease;
    z-index: 1001;
    max-height: 70vh;
    overflow-y: auto;
  }

  .bottom-modal.active {
    transform: translateY(0);
  }

  .modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #f0f0f0;
  }

  .modal-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #2c2c2c;
  }

  .modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: #666;
    cursor: pointer;
    padding: 0.25rem;
  }

  .sort-option {
    padding: 0.75rem 0;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #2c2c2c;
  }

  .sort-option:last-child {
    border-bottom: none;
  }

  .sort-option:hover {
    background: #f8f9fa;
    margin: 0 -1rem;
    padding-left: 1rem;
    padding-right: 1rem;
  }

  .sort-option.active {
    color: #007bff;
    font-weight: 500;
  }

  .checkmark {
    color: #007bff;
    font-weight: bold;
  }

  /* Responsive Design */
  @media (min-width: 768px) {
    .mobile-controls {
      display: none;
    }
    
    .products-count {
      text-align: left;
      margin-bottom: 0;
    }
    
    .products-header {
      display: flex;
    }
  }

  @media (max-width: 767px) {
    .filters-sidebar {
      display: none;
    }
    
    .products-header {
      display: none;
    }
    
    .page-title {
      font-size: 1.5rem;
      margin-bottom: 1.5rem;
    }
    
    .product-listing-container {
      padding: 0.5rem;
    }

    /* Mobile filter content spacing */
    .bottom-modal .filter-section {
      margin-bottom: 1rem;
      padding-bottom: 0.75rem;
    }

    .bottom-modal .filter-header {
      margin-bottom: 0.5rem;
    }

    .bottom-modal .form-check-label {
      padding: 0.25rem 0;
    }

    .bottom-modal .filter-content.open {
      max-height: 120px;
    }
  }

  /* PROFESSIONAL Price Slider Styles */
#priceSlider, #mobilePriceSlider {
  margin: 1.5rem 0;
  height: 6px;
}

/* Slider Container */
.noUi-target {
  background: #e8e9ea;
  border-radius: 10px;
  border: none;
  box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
  height: 6px;
  position: relative;
}

/* Active Range */
.noUi-connect {
  background: linear-gradient(90deg, #2c2c2c 0%, #404040 100%);
  border-radius: 10px;
  box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

/* Professional Handles */
.noUi-handle {
  width: 22px;
  height: 22px;
  border-radius: 50%;
  background: #ffffff;
  border: 3px solid #2c2c2c;
  box-shadow: 0 2px 8px rgba(0,0,0,0.15), 0 0 0 1px rgba(255,255,255,0.8);
  cursor: grab;
  outline: none;
  top: -8px;
  right: -11px;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Remove default handle styling */
.noUi-handle:before,
.noUi-handle:after {
  display: none;
}

/* Handle Hover State */
.noUi-handle:hover {
  transform: scale(1.1);
  box-shadow: 0 4px 12px rgba(0,0,0,0.2), 0 0 0 2px rgba(44,44,44,0.1);
  border-color: #1a1a1a;
}

/* Handle Active State */
.noUi-handle:active {
  cursor: grabbing;
  transform: scale(1.05);
  box-shadow: 0 2px 8px rgba(0,0,0,0.25), 0 0 0 3px rgba(44,44,44,0.15);
}

/* Handle Focus State */
.noUi-handle:focus {
  box-shadow: 0 2px 8px rgba(0,0,0,0.15), 0 0 0 3px rgba(44,44,44,0.2);
}

/* Professional Price Display */
.price-display {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 1.25rem;
  gap: 1rem;
}

.price-box {
  background: #ffffff;
  border: 2px solid #e8e9ea;
  border-radius: 12px;
  padding: 0.75rem 1rem;
  font-weight: 600;
  color: #2c2c2c;
  min-width: 85px;
  text-align: center;
  font-size: 0.95rem;
  transition: all 0.2s ease;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.price-box:hover {
  border-color: #2c2c2c;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

.price-box:focus-within {
  border-color: #2c2c2c;
  box-shadow: 0 0 0 3px rgba(44,44,44,0.1);
}

/* "to" text styling */
.price-display > span {
  color: #6c757d;
  font-size: 0.9rem;
  font-weight: 500;
  text-transform: lowercase;
}

/* Responsive Design */
@media (max-width: 767px) {
  #mobilePriceSlider {
    margin: 1rem 0;
  }
  
  .price-display {
    margin-top: 1rem;
    gap: 0.75rem;
  }
  
  .price-box {
    min-width: 75px;
    padding: 0.6rem 0.8rem;
    font-size: 0.9rem;
    border-radius: 10px;
  }
  
  /* Mobile handle adjustments */
  #mobilePriceSlider .noUi-handle {
    width: 24px;
    height: 24px;
    top: -9px;
    right: -12px;
  }
}

/* Additional Polish */
.noUi-target * {
  -webkit-touch-callout: none;
  -webkit-tap-highlight-color: rgba(0,0,0,0);
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
  box-sizing: border-box;
}

/* Smooth animations */
.noUi-state-tap .noUi-connect,
.noUi-state-tap .noUi-origin {
  transition: transform 0.3s;
}

/* Handle positioning fix for different states */
.noUi-horizontal .noUi-handle {
  width: 22px;
  height: 22px;
  right: -11px;
  top: -8px;
}

/* Ensure handles don't overlap */
.noUi-handle.noUi-handle-lower {
  z-index: 2;
}

.noUi-handle.noUi-handle-upper {
  z-index: 2;
}
</style>

<div class="product-listing-container">
  <h2 class="page-title"><?= $pageTitle ?></h2>
  
  <!-- Mobile Controls -->
  <div class="mobile-controls d-md-none">
    <button class="mobile-control-btn" onclick="openFilterModal()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="4" y1="21" x2="4" y2="14"></line>
        <line x1="4" y1="10" x2="4" y2="3"></line>
        <line x1="12" y1="21" x2="12" y2="12"></line>
        <line x1="12" y1="8" x2="12" y2="3"></line>
        <line x1="20" y1="21" x2="20" y2="16"></line>
        <line x1="20" y1="12" x2="20" y2="3"></line>
        <line x1="1" y1="14" x2="7" y2="14"></line>
        <line x1="9" y1="8" x2="15" y2="8"></line>
        <line x1="17" y1="16" x2="23" y2="16"></line>
      </svg>
      Filters
    </button>
    <button class="mobile-control-btn" onclick="openSortModal()">
      Sort by
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <polyline points="6,9 12,15 18,9"></polyline>
      </svg>
    </button>
  </div>


  
  <div class="row">
    <!-- Desktop Sidebar -->
    <div class="col-lg-3 col-md-4 mb-4 d-none d-md-block">
      <div class="filters-sidebar">
        <h4 class="filters-title">Filters</h4>
        
        <div class="filter-tags">
          <?php if ($min > 0 || $max < 2000): ?>
            <span class="filter-tag">
              Price: ₹<?= $min ?> – ₹<?= $max ?>
              <a href="?inStock=<?= $inStock ?>&outOfStock=<?= $outOfStock ?>" class="filter-tag-close" title="Remove filter">&times;</a>
            </span>
          <?php endif; ?>
        </div>

        <form method="GET" id="filterForm">
          <div class="filter-section">
            <div class="filter-header" onclick="toggleFilter('availability')">
              <span class="filter-label">Availability</span>
              <span class="chevron" id="availability-chevron">&#9660;</span>
            </div>
            
            <div class="filter-content open" id="availability-content">
              <label class="form-check-label">
                <input class="form-check-input" type="checkbox" name="inStock" value="1" <?= $inStock == '1' ? 'checked' : '' ?>>
                In stock
                <span class="stock-count">(<?= $stockInHand ?>)</span>
              </label>
              
              <label class="form-check-label">
                <input class="form-check-input" type="checkbox" name="outOfStock" value="1" <?= $outOfStock == '1' ? 'checked' : '' ?>>
                Out of stock
                <span class="stock-count">(<?= $stockOutOfHand ?>)</span>
              </label>
            </div>
          </div>

          <div class="filter-section">
            <div class="filter-header" onclick="toggleFilter('price')">
              <span class="filter-label">Price</span>
              <span class="chevron" id="price-chevron">&#9660;</span>
            </div>
            
            <div class="filter-content open" id="price-content">
              <div id="priceSlider"></div>
              
              <div class="price-display">
                <div class="price-box">₹ <span id="minVal"><?= $min ?></span></div>
                <span style="color: #6c757d;">to</span>
                <div class="price-box">₹ <span id="maxVal"><?= $max ?></span></div>
              </div>
              
              <input type="hidden" id="min" name="min" value="<?= $min ?>">
              <input type="hidden" id="max" name="max" value="<?= $max ?>">
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Products Grid -->
    <div class="col-lg-9 col-md-8">
      <!-- Desktop Header -->
      <div class="products-header d-none d-md-flex">
        <div class="products-count">
          <?= count($filteredProducts) ?> products
        </div>
        <select class="sort-dropdown" onchange="window.location.href='?sort=' + this.value + '&inStock=<?= $inStock ?>&outOfStock=<?= $outOfStock ?>&min=<?= $min ?>&max=<?= $max ?>'">
          <option value="best" <?= ($_GET['sort'] ?? 'best') === 'best' ? 'selected' : '' ?>>Best selling</option>
          <option value="new" <?= ($_GET['sort'] ?? '') === 'new' ? 'selected' : '' ?>>Date, new to old</option>
          <option value="old" <?= ($_GET['sort'] ?? '') === 'old' ? 'selected' : '' ?>>Date, old to new</option>
          <option value="low" <?= ($_GET['sort'] ?? '') === 'low' ? 'selected' : '' ?>>Price, low to high</option>
          <option value="high" <?= ($_GET['sort'] ?? '') === 'high' ? 'selected' : '' ?>>Price, high to low</option>
          <option value="az" <?= ($_GET['sort'] ?? '') === 'az' ? 'selected' : '' ?>>Alphabetically, A-Z</option>
          <option value="za" <?= ($_GET['sort'] ?? '') === 'za' ? 'selected' : '' ?>>Alphabetically, Z-A</option>
        </select>
      </div>

      <!-- FIXED: Using Bootstrap's native grid system that works with your product-card.php -->
      <div class="row products-grid" id="productGrid">
        <?php
        $sortOrder = $_GET['sort'] ?? 'best';
        if ($sortOrder === 'low') usort($filteredProducts, fn($a, $b) => $a['price'] <=> $b['price']);
        if ($sortOrder === 'high') usort($filteredProducts, fn($a, $b) => $b['price'] <=> $a['price']);
        if ($sortOrder === 'az') usort($filteredProducts, fn($a, $b) => strcmp($a['title'], $b['title']));
        if ($sortOrder === 'za') usort($filteredProducts, fn($a, $b) => strcmp($b['title'], $a['title']));
        if ($sortOrder === 'old') usort($filteredProducts, fn($a, $b) => strtotime($a['date']) <=> strtotime($b['date']));
        if ($sortOrder === 'new') usort($filteredProducts, fn($a, $b) => strtotime($b['date']) <=> strtotime($a['date']));

        foreach ($filteredProducts as $p):
          extract($p);
          $discount = round((($mrp - $price) / $mrp) * 100);
          $inStock = $p['stock'];
          // Your original product-card.php will handle the column classes properly
          include "../components/product-card.php";
        endforeach;
        ?>
      </div>
    </div>
  </div>
</div>

<!-- Filter Modal -->
<div class="modal-overlay" id="filterModal" onclick="closeModal('filterModal')">
  <div class="bottom-modal" onclick="event.stopPropagation()">
    <div class="modal-header">
      <h3 class="modal-title">Filters</h3>
      <button class="modal-close" onclick="closeModal('filterModal')">&times;</button>
    </div>
    
    <form method="GET" id="mobileFilterForm">
      <div class="filter-section">
        <div class="filter-header" onclick="toggleMobileFilter('mobile-availability')">
          <span class="filter-label">Availability</span>
          <span class="chevron" id="mobile-availability-chevron">&#9660;</span>
        </div>
        
        <div class="filter-content open" id="mobile-availability-content">
          <label class="form-check-label">
            <input class="form-check-input" type="checkbox" name="inStock" value="1" <?= $inStock == '1' ? 'checked' : '' ?>>
            In stock
            <span class="stock-count">(<?= $stockInHand ?>)</span>
          </label>
          
          <label class="form-check-label">
            <input class="form-check-input" type="checkbox" name="outOfStock" value="1" <?= $outOfStock == '1' ? 'checked' : '' ?>>
            Out of stock
            <span class="stock-count">(<?= $stockOutOfHand ?>)</span>
          </label>
        </div>
      </div>

      <div class="filter-section">
        <div class="filter-header" onclick="toggleMobileFilter('mobile-price')">
          <span class="filter-label">Price</span>
          <span class="chevron" id="mobile-price-chevron">&#9660;</span>
        </div>
        
        <div class="filter-content open" id="mobile-price-content">
          <div id="mobilePriceSlider"></div>
          
          <div class="price-display">
            <div class="price-box">₹ <span id="mobileMinVal"><?= $min ?></span></div>
            <span style="color: #6c757d;">to</span>
            <div class="price-box">₹ <span id="mobileMaxVal"><?= $max ?></span></div>
          </div>
          
          <input type="hidden" id="mobileMin" name="min" value="<?= $min ?>">
          <input type="hidden" id="mobileMax" name="max" value="<?= $max ?>">
        </div>
      </div>
      
      <button type="submit" style="width: 100%; background: #2c2c2c; color: white; border: none; padding: 1rem; border-radius: 8px; margin-top: 1rem; font-weight: 600;">Apply Filters</button>
    </form>
  </div>
</div>

<!-- Sort Modal -->
<div class="modal-overlay" id="sortModal" onclick="closeModal('sortModal')">
  <div class="bottom-modal" onclick="event.stopPropagation()">
    <div class="modal-header">
      <h3 class="modal-title">Sort by</h3>
      <button class="modal-close" onclick="closeModal('sortModal')">&times;</button>
    </div>
    
    <div class="sort-option <?= ($_GET['sort'] ?? 'best') === 'best' ? 'active' : '' ?>" onclick="applySortAndClose('best')">
      <span>Best selling</span>
      <?php if (($_GET['sort'] ?? 'best') === 'best'): ?>
        <span class="checkmark">✓</span>
      <?php endif; ?>
    </div>
    
    <div class="sort-option <?= ($_GET['sort'] ?? '') === 'new' ? 'active' : '' ?>" onclick="applySortAndClose('new')">
      <span>Date, new to old</span>
      <?php if (($_GET['sort'] ?? '') === 'new'): ?>
        <span class="checkmark">✓</span>
      <?php endif; ?>
    </div>
    
    <div class="sort-option <?= ($_GET['sort'] ?? '') === 'old' ? 'active' : '' ?>" onclick="applySortAndClose('old')">
      <span>Date, old to new</span>
      <?php if (($_GET['sort'] ?? '') === 'old'): ?>
        <span class="checkmark">✓</span>
      <?php endif; ?>
    </div>
    
    <div class="sort-option <?= ($_GET['sort'] ?? '') === 'low' ? 'active' : '' ?>" onclick="applySortAndClose('low')">
      <span>Price, low to high</span>
      <?php if (($_GET['sort'] ?? '') === 'low'): ?>
        <span class="checkmark">✓</span>
      <?php endif; ?>
    </div>
    
    <div class="sort-option <?= ($_GET['sort'] ?? '') === 'high' ? 'active' : '' ?>" onclick="applySortAndClose('high')">
      <span>Price, high to low</span>
      <?php if (($_GET['sort'] ?? '') === 'high'): ?>
        <span class="checkmark">✓</span>
      <?php endif; ?>
    </div>
    
    <div class="sort-option <?= ($_GET['sort'] ?? '') === 'az' ? 'active' : '' ?>" onclick="applySortAndClose('az')">
      <span>Alphabetically, A-Z</span>
      <?php if (($_GET['sort'] ?? '') === 'az'): ?>
        <span class="checkmark">✓</span>
      <?php endif; ?>
    </div>
    
    <div class="sort-option <?= ($_GET['sort'] ?? '') === 'za' ? 'active' : '' ?>" onclick="applySortAndClose('za')">
      <span>Alphabetically, Z-A</span>
      <?php if (($_GET['sort'] ?? '') === 'za'): ?>
        <span class="checkmark">✓</span>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
  // Desktop Price Slider
  const minVal = document.getElementById('minVal');
  const maxVal = document.getElementById('maxVal');
  const minInput = document.getElementById('min');
  const maxInput = document.getElementById('max');
  const slider = document.getElementById('priceSlider');
  const form = document.getElementById('filterForm');

  if (slider) {
    noUiSlider.create(slider, {
      start: [parseInt(minInput.value), parseInt(maxInput.value)],
      connect: true,
      step: 10,
      range: {
        min: 0,
        max: 2000
      },
      tooltips: false,
      format: {
        to: value => Math.round(value),
        from: value => Number(value)
      }
    });

    slider.noUiSlider.on('update', (values, handle) => {
      const [minValNum, maxValNum] = values;
      minVal.innerText = minValNum;
      maxVal.innerText = maxValNum;
      minInput.value = minValNum;
      maxInput.value = maxValNum;
    });

    slider.noUiSlider.on('change', () => form.submit());
  }

  // Mobile Price Slider
  const mobileMinVal = document.getElementById('mobileMinVal');
  const mobileMaxVal = document.getElementById('mobileMaxVal');
  const mobileMinInput = document.getElementById('mobileMin');
  const mobileMaxInput = document.getElementById('mobileMax');
  const mobileSlider = document.getElementById('mobilePriceSlider');

  if (mobileSlider) {
    noUiSlider.create(mobileSlider, {
      start: [parseInt(mobileMinInput.value), parseInt(mobileMaxInput.value)],
      connect: true,
      step: 10,
      range: {
        min: 0,
        max: 2000
      },
      tooltips: false,
      format: {
        to: value => Math.round(value),
        from: value => Number(value)
      }
    });

    mobileSlider.noUiSlider.on('update', (values, handle) => {
      const [minValNum, maxValNum] = values;
      mobileMinVal.innerText = minValNum;
      mobileMaxVal.innerText = maxValNum;
      mobileMinInput.value = minValNum;
      mobileMaxInput.value = maxValNum;
    });
  }

  // Desktop checkbox handlers
  document.querySelectorAll('#filterForm input[type="checkbox"]').forEach(cb => {
    cb.addEventListener('change', () => form.submit());
  });

  // Desktop filter toggle
  function toggleFilter(filterId) {
    const content = document.getElementById(filterId + '-content');
    const chevron = document.getElementById(filterId + '-chevron');
    
    if (content.classList.contains('open')) {
      content.classList.remove('open');
      chevron.classList.add('rotated');
    } else {
      content.classList.add('open');
      chevron.classList.remove('rotated');
    }
  }

  // Mobile filter toggle
  function toggleMobileFilter(filterId) {
    const content = document.getElementById(filterId + '-content');
    const chevron = document.getElementById(filterId + '-chevron');
    
    if (content.classList.contains('open')) {
      content.classList.remove('open');
      chevron.classList.add('rotated');
    } else {
      content.classList.add('open');
      chevron.classList.remove('rotated');
    }
  }

  // Modal functions
  function openFilterModal() {
    document.getElementById('filterModal').classList.add('active');
    document.querySelector('#filterModal .bottom-modal').classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function openSortModal() {
    document.getElementById('sortModal').classList.add('active');
    document.querySelector('#sortModal .bottom-modal').classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
    document.querySelector('#' + modalId + ' .bottom-modal').classList.remove('active');
    document.body.style.overflow = 'auto';
  }

  function applySortAndClose(sortValue) {
    const currentParams = new URLSearchParams(window.location.search);
    currentParams.set('sort', sortValue);
    window.location.href = '?' + currentParams.toString();
  }

  // Close modal on escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeModal('filterModal');
      closeModal('sortModal');
    }
  });
</script>