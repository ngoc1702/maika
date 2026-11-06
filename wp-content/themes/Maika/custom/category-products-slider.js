(() => {
  document.querySelectorAll('[data-maika-category-track]').forEach((track) => {
    const motion = matchMedia('(prefers-reduced-motion: reduce)');
    let drag;
    let suppressUntil = 0;
    let timer;
    let visible = false;
    let hovered = false;
    let focused = false;
    let interacting = false;
    const step = () => {
      const card = track.querySelector('.maika-product-card');
      return card ? card.getBoundingClientRect().width + parseFloat(getComputedStyle(track).columnGap) : 0;
    };
    const schedule = () => {
      clearTimeout(timer);
      if (motion.matches || document.hidden || !visible || hovered || focused || interacting || track.scrollWidth <= track.clientWidth + 1) return;
      timer = setTimeout(() => {
        const end = track.scrollWidth - track.clientWidth;
        const next = track.scrollLeft >= end - 2 ? 0 : Math.min(end, track.scrollLeft + step());
        track.scrollTo({left: next, behavior: 'smooth'});
        schedule();
      }, 5000);
    };
    track.addEventListener('dragstart', event => event.preventDefault());
    track.addEventListener('pointerenter', event => {
      if (event.pointerType === 'mouse') { hovered = true; schedule(); }
    });
    track.addEventListener('pointerleave', () => {
      hovered = false;
      if (drag && !drag.moved) release();
      schedule();
    });
    track.addEventListener('focusin', () => { focused = true; schedule(); });
    track.addEventListener('focusout', event => {
      if (!track.contains(event.relatedTarget)) { focused = false; schedule(); }
    });
    track.addEventListener('pointerdown', (event) => {
      if (!event.isPrimary || event.button !== 0) return;
      interacting = true;
      schedule();
      if (event.pointerType === 'mouse') {
        drag = {id: event.pointerId, x: event.clientX, scroll: track.scrollLeft, moved: false};
      }
    });
    track.addEventListener('pointermove', (event) => {
      if (!drag || event.pointerId !== drag.id) return;
      const delta = event.clientX - drag.x;
      if (!drag.moved && Math.abs(delta) > 6) {
        drag.moved = true;
        track.classList.add('is-dragging');
        track.setPointerCapture(event.pointerId);
      }
      if (drag.moved) {
        event.preventDefault();
        track.scrollLeft = drag.scroll - delta;
      }
    });
    const release = () => {
      interacting = false;
      if (drag) {
        if (drag.moved) suppressUntil = Date.now() + 300;
        const id = drag.id;
        drag = undefined;
        track.classList.remove('is-dragging');
        if (track.hasPointerCapture(id)) track.releasePointerCapture(id);
      }
      schedule();
    };
    track.addEventListener('pointerup', release);
    track.addEventListener('pointercancel', release);
    track.addEventListener('lostpointercapture', release);
    track.addEventListener('wheel', schedule, {passive: true});
    track.addEventListener('click', (event) => {
      if (Date.now() < suppressUntil) { event.preventDefault(); event.stopPropagation(); }
    }, true);
    track.addEventListener('keydown', (event) => {
      if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') return;
      event.preventDefault();
      track.scrollBy({left: event.key === 'ArrowRight' ? step() : -step(), behavior: motion.matches ? 'instant' : 'smooth'});
      schedule();
    });
    document.addEventListener('visibilitychange', schedule);
    motion.addEventListener('change', schedule);
    new ResizeObserver(schedule).observe(track);
    new IntersectionObserver(([entry]) => {
      visible = entry.isIntersecting;
      schedule();
    }, {threshold: 0.1}).observe(track);
  });
})();
