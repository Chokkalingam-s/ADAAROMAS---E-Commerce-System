// main.js for future DOM operations

document.addEventListener("DOMContentLoaded", () => {
  console.log("ADA Aromas site loaded!");

  // Example feature: Scroll to top button setup
  const scrollBtn = document.getElementById('scrollToTop');
  if (scrollBtn) {
    scrollBtn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
  }
});

const range = document.getElementById("priceRange");
const minPrice = document.getElementById("minPrice");
const maxPrice = document.getElementById("maxPrice");

if (range) {
  range.addEventListener("input", () => {
    const val = range.value;
    minPrice.value = 0;
    maxPrice.value = val;
  });
}

