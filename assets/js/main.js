document.addEventListener("DOMContentLoaded", () => {
    // Example feature: Scroll to top button setup
  const scrollBtn = document.getElementById('scrollToTop');
  if (scrollBtn) {
    scrollBtn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
  }
});

