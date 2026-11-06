(() => {
  document.querySelector('.maika-back-to-top')?.addEventListener('click', () => {
    window.scrollTo({
      top: 0,
      behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth',
    });
  });
})();
