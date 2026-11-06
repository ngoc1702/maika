(() => {
  const navigation = document.querySelector('.maika-home-hero');
  if (!navigation) return;
  const button = navigation.querySelector('.maika-category-toggle');
  const widget = navigation.querySelector('.widget_product_categories');
  const list = widget?.querySelector('.product-categories');
  if (!button || !list) return;
  list.id = button.getAttribute('aria-controls');
  const setOpen = (open) => {
    widget.classList.toggle('is-open', open);
    button.setAttribute('aria-expanded', String(open));
  };
  button.addEventListener('click', () => setOpen(button.getAttribute('aria-expanded') !== 'true'));
  widget.addEventListener('pointerenter', (event) => { if (event.pointerType === 'mouse') setOpen(true); });
  widget.addEventListener('pointerleave', (event) => { if (event.pointerType === 'mouse' && !widget.contains(document.activeElement)) setOpen(false); });
  widget.addEventListener('focusout', (event) => { if (!widget.contains(event.relatedTarget)) setOpen(false); });
  document.addEventListener('click', (event) => { if (!widget.contains(event.target)) setOpen(false); });
  widget.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') { setOpen(false); button.focus(); }
  });
})();