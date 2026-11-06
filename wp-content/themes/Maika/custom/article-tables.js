(() => {
  document.querySelectorAll('.single-post .entry-content table, .maika-about-page .entry-content table, .single-product .entry-content table').forEach((table) => {
    let viewport = table.parentElement;
    if (!viewport.matches('.wp-block-table, .maika-table-scroll')) {
      viewport = document.createElement('div');
      table.before(viewport);
      viewport.append(table);
    }
    viewport.classList.add('maika-table-scroll');
    viewport.setAttribute('role', 'region');
    viewport.setAttribute('aria-label', table.caption?.textContent.trim() || 'Bảng thông tin, cuộn ngang để xem đầy đủ');
    const update = () => {
      if (viewport.scrollWidth > viewport.clientWidth + 1) viewport.tabIndex = 0;
      else viewport.removeAttribute('tabindex');
    };
    new ResizeObserver(update).observe(viewport);
    update();
  });
})();
