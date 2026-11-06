(() => {
  document.querySelectorAll('[data-maika-reviews]').forEach((carousel) => {
    const viewport = carousel.querySelector('.maika-reviews-viewport');
    const cards = [...viewport.querySelectorAll('.maika-review-card')];
    const controls = carousel.querySelector('.maika-review-controls');
    const dots = carousel.querySelector('.maika-review-dots');
    const status = carousel.querySelector('.maika-review-status');
    const mobile = window.matchMedia('(max-width: 767px)');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let starts = [];
    let current = 0;
    let frame;
    const position = (index) => cards[index].getBoundingClientRect().left - viewport.getBoundingClientRect().left + viewport.scrollLeft;
    const update = () => {
      [...dots.children].forEach((dot, index) => dot.setAttribute('aria-pressed', String(index === current)));
    };
    const go = (page, smooth = true) => {
      current = (page + starts.length) % starts.length;
      viewport.scrollTo({ left: position(starts[current]), behavior: smooth && !reducedMotion.matches ? 'smooth' : 'instant' });
      update();
      status.textContent = `Trang ${current + 1} / ${starts.length}`;
    };
    const setup = () => {
      const perView = mobile.matches ? 1 : 2;
      const total = Math.ceil(cards.length / perView);
      starts = Array.from({ length: total }, (_, i) => Math.min(i * perView, Math.max(0, cards.length - perView)));
      controls.hidden = cards.length <= perView;
      dots.replaceChildren();
      starts.forEach((_, index) => {
        const dot = document.createElement('button');
        dot.type = 'button';
        dot.setAttribute('aria-label', `Xem trang nhận xét ${index + 1}`);
        dot.addEventListener('click', () => go(index));
        dots.appendChild(dot);
      });
      go(Math.min(current, total - 1), false);
    };
    carousel.querySelector('.maika-review-prev').addEventListener('click', () => go(current - 1));
    carousel.querySelector('.maika-review-next').addEventListener('click', () => go(current + 1));
    viewport.addEventListener('keydown', (event) => {
      if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
        event.preventDefault();
        go(current + (event.key === 'ArrowRight' ? 1 : -1));
      }
    });
    viewport.addEventListener('scroll', () => {
      cancelAnimationFrame(frame);
      frame = requestAnimationFrame(() => {
        let closest = 0;
        starts.forEach((start, i) => {
          if (Math.abs(position(start) - viewport.scrollLeft) < Math.abs(position(starts[closest]) - viewport.scrollLeft)) closest = i;
        });
        current = closest;
        update();
      });
    }, { passive: true });
    mobile.addEventListener('change', setup);
    setup();
  });
})();