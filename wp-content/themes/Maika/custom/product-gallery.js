(() => {
  document.querySelectorAll('[data-maika-product-gallery]').forEach((gallery) => {
    const slides = [...gallery.querySelectorAll('.maika-product-image')];
    const buttons = [...gallery.querySelectorAll('[data-product-image]')];
    if (buttons.length < 2) return;
    const track = gallery.querySelector('.maika-product-thumbnails');
    let drag;
    let suppressUntil = 0;
    track.addEventListener('dragstart', event => event.preventDefault());
    track.addEventListener('pointerdown', event => {
      if (event.pointerType === 'mouse' && event.button === 0) {
        drag = {id: event.pointerId, x: event.clientX, scroll: track.scrollLeft, moved: false};
      }
    });
    track.addEventListener('pointermove', event => {
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
      if (!drag) return;
      if (drag.moved) suppressUntil = Date.now() + 300;
      const id = drag.id;
      drag = undefined;
      track.classList.remove('is-dragging');
      if (track.hasPointerCapture(id)) track.releasePointerCapture(id);
    };
    track.addEventListener('pointerup', release);
    track.addEventListener('pointercancel', release);
    track.addEventListener('lostpointercapture', release);
    track.addEventListener('pointerleave', () => { if (drag && !drag.moved) release(); });
    track.addEventListener('click', event => {
      if (Date.now() < suppressUntil) { event.preventDefault(); event.stopPropagation(); }
    }, true);
    const show = (index) => {
      slides.forEach((slide, i) => { slide.hidden = i !== index; });
      buttons.forEach((button, i) => button.setAttribute('aria-pressed', String(i === index)));
      const image = slides[index].querySelector('img');
      if (image) image.loading = 'eager';
    };
    buttons.forEach((button, index) => {
      button.addEventListener('click', () => show(index));
      button.addEventListener('keydown', (event) => {
        if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') return;
        event.preventDefault();
        const next = (index + (event.key === 'ArrowRight' ? 1 : -1) + buttons.length) % buttons.length;
        show(next);
        buttons[next].focus();
      });
    });
  });
})();
