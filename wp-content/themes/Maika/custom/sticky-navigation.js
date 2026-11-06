(() => {
  const original = document.querySelector('.maika-home-hero');
  if (!original) return;
  const home = !original.classList.contains('maika-inner-navigation');
  const marker = document.createElement('div');
  marker.className = 'maika-navigation-marker';
  original.before(marker);
  let dock = original;
  let slots = [];
  let fixed = false;
  let frame = 0;
  if (home) {
    dock = document.createElement('nav');
    dock.className = 'maika-home-hero maika-inner-navigation wrap maika-sticky-dock';
    dock.setAttribute('aria-label', 'Danh mục và menu chính');
    dock.hidden = true;
    document.body.append(dock);
  }
  const closeCategory = () => {
    const widget = dock.querySelector('.widget_product_categories');
    widget?.classList.remove('is-open');
    widget?.querySelector('.maika-category-toggle')?.setAttribute('aria-expanded', 'false');
  };
  const update = () => {
    frame = 0;
    const bar = document.querySelector('#wpadminbar');
    const top = bar ? Math.max(0, bar.getBoundingClientRect().bottom) : 0;
    const shouldFix = window.matchMedia('(min-width: 768px)').matches && marker.getBoundingClientRect().top < top;
    if (shouldFix !== fixed) {
      if (shouldFix) {
        if (home) {
          slots = [...original.children].filter(el => el.matches('.widget_nav_menu, .widget_product_categories')).map(widget => {
            const placeholder = document.createElement('div');
            placeholder.className = widget.className + ' maika-navigation-placeholder';
            placeholder.style.height = `${widget.getBoundingClientRect().height}px`;
            widget.before(placeholder);
            dock.append(widget);
            return {widget, placeholder};
          });
          dock.hidden = false;
        } else {
          marker.style.height = `${original.getBoundingClientRect().height}px`;
        }
        dock.classList.add('maika-navigation-fixed');
      } else {
        closeCategory();
        dock.classList.remove('maika-navigation-fixed');
        if (home) {
          slots.forEach(({widget, placeholder}) => placeholder.replaceWith(widget));
          slots = [];
          dock.hidden = true;
        } else {
          marker.style.height = '';
        }
      }
      fixed = shouldFix;
    }
    if (fixed) dock.style.setProperty('--maika-navigation-top', `${top}px`);
  };
  const schedule = () => { if (!frame) frame = requestAnimationFrame(update); };
  window.addEventListener('scroll', schedule, {passive: true});
  window.addEventListener('resize', schedule);
  window.addEventListener('maika-navigation-before-mobile-layout', update);
  update();
})();
