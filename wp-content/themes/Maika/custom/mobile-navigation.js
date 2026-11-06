(() => {
  const header = document.querySelector('.site-header .header-widget-area');
  const navigation = document.querySelector('.maika-home-hero:not(.maika-sticky-dock)');
  if (!header || !navigation) return;
  const search = header.querySelector('.widget_search');
  const searchInput = search?.querySelector('input[type=search]');
  if (searchInput && !searchInput.hasAttribute('placeholder')) searchInput.setAttribute('placeholder', 'Tìm kiếm sản phẩm...');
  const menu = document.querySelector('.maika-home-hero .widget_nav_menu');
  const categories = document.querySelector('.maika-home-hero .widget_product_categories');
  const categoryButton = categories?.querySelector('.maika-category-toggle');
  if (categoryButton && !categoryButton.querySelector('.maika-category-chevron')) {
    categoryButton.insertAdjacentHTML('beforeend', '<svg class="maika-category-chevron" aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>');
  }
  const media = matchMedia('(max-width: 767px)');
  const toggle = document.createElement('button');
  toggle.type = 'button';
  toggle.className = 'maika-mobile-menu-toggle';
  toggle.setAttribute('aria-label', 'Mở menu và tìm kiếm');
  toggle.setAttribute('aria-controls', 'maika-mobile-menu');
  toggle.setAttribute('aria-expanded', 'false');
  toggle.innerHTML = '<svg aria-hidden="true" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg>';
  header.append(toggle);
  const dialog = document.createElement('dialog');
  dialog.id = 'maika-mobile-menu';
  dialog.className = 'maika-mobile-dialog';
  dialog.setAttribute('aria-labelledby', 'maika-mobile-menu-title');
  dialog.innerHTML = '<div class="maika-mobile-dialog-heading"><h2 id="maika-mobile-menu-title">Menu</h2><button type="button" class="maika-mobile-menu-close" aria-label="Đóng menu"><svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="m6 6 12 12M18 6 6 18"/></svg></button></div><div class="maika-mobile-dialog-body"><div class="maika-mobile-search"></div><nav class="maika-mobile-navigation" aria-label="Menu chính"></nav></div>';
  document.body.append(dialog);
  let mounted = false;
  let slots = [];
  const close = () => { if (dialog.open) dialog.close(); };
  dialog.addEventListener('close', () => {
    document.body.classList.remove('maika-mobile-menu-open');
    toggle.setAttribute('aria-expanded', 'false');
    toggle.focus({preventScroll: true});
  });
  toggle.addEventListener('click', () => {
    if (dialog.open) { close(); return; }
    if (!media.matches) return;
    dialog.showModal();
    document.body.classList.add('maika-mobile-menu-open');
    toggle.setAttribute('aria-expanded', 'true');
    dialog.querySelector('.maika-mobile-menu-close').focus();
  });
  dialog.querySelector('.maika-mobile-menu-close').addEventListener('click', close);
  dialog.addEventListener('click', (event) => {
    if (event.target !== dialog) return;
    const rect = dialog.getBoundingClientRect();
    if (event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom) close();
  });
  const apply = () => {
    if (media.matches === mounted) return;
    if (media.matches) {
      slots = [[search, '.maika-mobile-search'], [menu, '.maika-mobile-navigation']].filter(([node]) => node).map(([node, target]) => {
        const anchor = document.createComment('mobile menu slot');
        node.before(anchor);
        dialog.querySelector(target).append(node);
        return {node, anchor};
      });
      categories?.classList.remove('is-open');
      categories?.querySelector('.maika-category-toggle')?.setAttribute('aria-expanded', 'false');
      dialog.querySelectorAll('.menu-item-has-children').forEach((item, index) => {
        const submenu = [...item.children].find(child => child.classList.contains('sub-menu'));
        if (!submenu) return;
        if (!submenu.id) submenu.id = `maika-mobile-submenu-${index}`;
        submenu.hidden = true;
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'maika-mobile-submenu-toggle';
        const label = item.querySelector('a')?.textContent.trim() || '';
        button.setAttribute('aria-label', `Mở menu con ${label}`);
        button.setAttribute('aria-controls', submenu.id);
        button.setAttribute('aria-expanded', 'false');
        button.innerHTML = '<svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>';
        button.addEventListener('click', () => {
          submenu.hidden = !submenu.hidden;
          button.setAttribute('aria-expanded', String(!submenu.hidden));
          button.setAttribute('aria-label', `${submenu.hidden ? 'Mở' : 'Đóng'} menu con ${label}`);
        });
        item.querySelector('a').after(button);
      });
    } else {
      close();
      dialog.querySelectorAll('.maika-mobile-submenu-toggle').forEach(button => button.remove());
      dialog.querySelectorAll('.sub-menu').forEach(submenu => submenu.removeAttribute('hidden'));
      slots.forEach(({node, anchor}) => anchor.replaceWith(node));
      slots = [];
    }
    mounted = media.matches;
  };
  // Release any pinned desktop widgets before placing them in the mobile modal.
  window.addEventListener('resize', () => {
    if (media.matches) window.dispatchEvent(new Event('maika-navigation-before-mobile-layout'));
    apply();
  });
  apply();

  // Pin the compact header and category bar without moving their content.
  const siteHeader = header.closest('.site-header');
  const headerSpace = document.createElement('div');
  headerSpace.className = 'maika-mobile-header-space';
  siteHeader.before(headerSpace);
  const categorySpace = document.createElement('div');
  categorySpace.className = 'maika-mobile-category-space';
  let pinned = false;
  let pinFrame = 0;
  const unpin = () => {
    siteHeader.classList.remove('maika-mobile-header-fixed');
    navigation.classList.remove('maika-mobile-category-pinned');
    categories?.classList.remove('maika-mobile-category-fixed');
    headerSpace.style.height = '';
    categorySpace.remove();
    pinned = false;
  };
  const syncPin = () => {
    pinFrame = 0;
    if (!media.matches) { if (pinned) unpin(); return; }
    const adminBar = document.querySelector('#wpadminbar');
    const top = adminBar ? Math.max(0, adminBar.getBoundingClientRect().bottom) : 0;
    const shouldPin = headerSpace.getBoundingClientRect().top < top;
    if (!shouldPin) { if (pinned) unpin(); return; }
    const headerHeight = siteHeader.getBoundingClientRect().height;
    if (!pinned) {
      headerSpace.style.height = `${headerHeight}px`;
      if (categories) {
        categorySpace.style.height = `${categories.getBoundingClientRect().height}px`;
        categories.before(categorySpace);
      }
      siteHeader.classList.add('maika-mobile-header-fixed');
      navigation.classList.add('maika-mobile-category-pinned');
      categories?.classList.add('maika-mobile-category-fixed');
      pinned = true;
    }
    siteHeader.style.setProperty('--maika-mobile-pin-top', `${top}px`);
    if (categories) {
      const rect = navigation.getBoundingClientRect();
      categories.style.setProperty('--maika-mobile-category-top', `${top + headerHeight}px`);
      categories.style.setProperty('--maika-mobile-category-left', `${rect.left}px`);
      categories.style.setProperty('--maika-mobile-category-width', `${rect.width}px`);
    }
  };
  const schedulePin = () => { if (!pinFrame) pinFrame = requestAnimationFrame(syncPin); };
  window.addEventListener('scroll', schedulePin, {passive: true});
  window.addEventListener('resize', () => {
    if (!media.matches && pinned) unpin();
    schedulePin();
  });
  syncPin();
})();
