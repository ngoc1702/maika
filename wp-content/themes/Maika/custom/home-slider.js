(() => {
  document.querySelectorAll('[data-maika-slider]').forEach((slider) => {
    const slides = [...slider.querySelectorAll('.maika-slide')];
    if (slides.length < 2) return;
    const dots = [...slider.querySelectorAll('.maika-slider-dot')];
    const motion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let current = 0;
    let timer;
    let drag;
    let hovering = false;
    let focused = false;
    slides.forEach((slide) => { slide.querySelector('img').draggable = false; });
    const show = (index) => {
      current = (index + slides.length) % slides.length;
      slides.forEach((slide, i) => {
        slide.hidden = i !== current;
        if (i === current) slide.querySelector('img').loading = 'eager';
      });
      dots.forEach((dot, i) => dot.setAttribute('aria-pressed', String(i === current)));
      slides[(current + 1) % slides.length].querySelector('img').loading = 'eager';
    };
    const schedule = () => {
      window.clearInterval(timer);
      if (!motion.matches && !hovering && !focused && !drag && !document.hidden) {
        timer = window.setInterval(() => show(current + 1), 5000);
      }
    };
    const navigate = (index) => { show(index); schedule(); };
    const resetDrag = () => {
      drag = undefined;
      slider.classList.remove('is-dragging');
      schedule();
    };
    dots.forEach((dot, i) => dot.addEventListener('click', () => navigate(i)));
    slider.addEventListener('pointerenter', (event) => {
      if (event.pointerType === 'mouse') { hovering = true; schedule(); }
    });
    slider.addEventListener('pointerleave', (event) => {
      if (event.pointerType === 'mouse') { hovering = false; schedule(); }
    });
    slider.addEventListener('focusin', () => { focused = true; schedule(); });
    slider.addEventListener('focusout', (event) => {
      if (!slider.contains(event.relatedTarget)) { focused = false; schedule(); }
    });
    slider.addEventListener('keydown', (event) => {
      if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
        event.preventDefault();
        navigate(current + (event.key === 'ArrowRight' ? 1 : -1));
      }
    });
    slider.addEventListener('dragstart', (event) => event.preventDefault());
    slider.addEventListener('pointerdown', (event) => {
      if (!event.isPrimary || event.button !== 0 || event.target.closest('button')) return;
      drag = { id: event.pointerId, x: event.clientX, y: event.clientY };
      slider.setPointerCapture(event.pointerId);
      slider.classList.add('is-dragging');
      schedule();
    });
    slider.addEventListener('pointermove', (event) => {
      if (!drag || drag.id !== event.pointerId) return;
      const dx = Math.abs(event.clientX - drag.x);
      const dy = Math.abs(event.clientY - drag.y);
      if (dx > 8 && dx > dy * 1.2 && event.cancelable) event.preventDefault();
    }, { passive: false });
    slider.addEventListener('pointerup', (event) => {
      if (!drag || drag.id !== event.pointerId) return;
      const dx = event.clientX - drag.x;
      const dy = event.clientY - drag.y;
      const threshold = Math.min(60, Math.max(30, slider.clientWidth * 0.08));
      resetDrag();
      if (slider.hasPointerCapture(event.pointerId)) slider.releasePointerCapture(event.pointerId);
      if (Math.abs(dx) >= threshold && Math.abs(dx) > Math.abs(dy) * 1.2) {
        navigate(current + (dx < 0 ? 1 : -1));
      }
    });
    slider.addEventListener('pointercancel', resetDrag);
    slider.addEventListener('lostpointercapture', () => { if (drag) resetDrag(); });
    document.addEventListener('visibilitychange', schedule);
    motion.addEventListener('change', schedule);
    show(0);
    schedule();
  });
})();