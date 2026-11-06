(() => {
  document.querySelectorAll('[data-maika-partners]').forEach((viewport) => {
    const track = viewport.querySelector('.maika-partners-track');
    const group = track.querySelector('.maika-partners-group');
    const motion = matchMedia('(prefers-reduced-motion: reduce)');
    let distance = 0;
    let offset = 0;
    let dragging = false;
    let hovered = false;
    let focused = false;
    let visible = false;
    let pointerX = 0;
    let lastTime = 0;
    let frame = 0;
    const draw = () => {
      if (!distance) return;
      offset = ((offset % distance) + distance) % distance;
      track.style.transform = `translate3d(${-offset}px, 0, 0)`;
    };
    const tick = (time) => {
      frame = 0;
      if (lastTime) offset += Math.min(time - lastTime, 64) * 0.032;
      lastTime = time;
      draw();
      frame = requestAnimationFrame(tick);
    };
    const sync = () => {
      cancelAnimationFrame(frame);
      frame = 0;
      lastTime = 0;
      if (!motion.matches && !dragging && !hovered && !focused && !document.hidden && visible && distance) {
        frame = requestAnimationFrame(tick);
      }
    };
    const measure = () => {
      track.querySelectorAll('[data-partner-copy]').forEach((copy) => copy.remove());
      distance = group.getBoundingClientRect().width;
      if (!distance) return;
      const copies = Math.ceil(viewport.clientWidth / distance) + 1;
      for (let i = 0; i < copies; i++) {
        const copy = group.cloneNode(true);
        copy.dataset.partnerCopy = '';
        copy.setAttribute('aria-hidden', 'true');
        track.appendChild(copy);
      }
      draw();
      sync();
    };
    viewport.addEventListener('pointerenter', (event) => {
      if (event.pointerType === 'mouse') { hovered = true; sync(); }
    });
    viewport.addEventListener('pointerleave', () => { hovered = false; sync(); });
    viewport.addEventListener('focusin', () => { focused = true; sync(); });
    viewport.addEventListener('focusout', () => { focused = false; sync(); });
    viewport.addEventListener('pointerdown', (event) => {
      if (!event.isPrimary || event.button !== 0) return;
      dragging = true;
      pointerX = event.clientX;
      viewport.setPointerCapture(event.pointerId);
      viewport.classList.add('is-dragging');
      sync();
    });
    viewport.addEventListener('pointermove', (event) => {
      if (!dragging) return;
      offset += pointerX - event.clientX;
      pointerX = event.clientX;
      draw();
    });
    const release = () => {
      dragging = false;
      viewport.classList.remove('is-dragging');
      sync();
    };
    viewport.addEventListener('pointerup', release);
    viewport.addEventListener('pointercancel', release);
    viewport.addEventListener('lostpointercapture', release);
    viewport.addEventListener('keydown', (event) => {
      if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') return;
      event.preventDefault();
      offset += (event.key === 'ArrowRight' ? 1 : -1) * group.firstElementChild.getBoundingClientRect().width;
      draw();
    });
    document.addEventListener('visibilitychange', sync);
    motion.addEventListener('change', sync);
    new IntersectionObserver(([entry]) => {
      visible = entry.isIntersecting;
      sync();
    }).observe(viewport);
    new ResizeObserver(measure).observe(viewport);
    measure();
  });
})();
