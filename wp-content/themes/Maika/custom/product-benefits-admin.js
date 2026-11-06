(() => {
  document.addEventListener('click', (event) => {
    const upload = event.target.closest('[data-maika-benefit-upload]');
    const remove = event.target.closest('[data-maika-benefit-remove]');
    if (!upload && !remove) return;
    event.preventDefault();
    const editor = (upload || remove).closest('.maika-benefit-editor');
    const input = editor.querySelector('.maika-benefit-image-id');
    const preview = editor.querySelector('.maika-benefit-image-preview');
    const update = (id, url) => {
      input.value = id;
      preview.replaceChildren();
      if (url) {
        const image = document.createElement('img');
        image.src = url;
        image.alt = '';
        image.style.cssText = 'width:52px;height:52px;object-fit:contain';
        preview.append(image);
      }
      // Notify both the classic Widgets screen and the Customizer of changes.
      jQuery(input).trigger('change');
    };
    if (remove) { update('', ''); return; }
    const frame = wp.media({title: 'Chọn ảnh icon', button: {text: 'Dùng ảnh này'}, library: {type: 'image'}, multiple: false});
    frame.on('select', () => {
      const image = frame.state().get('selection').first().toJSON();
      update(image.id, image.sizes?.thumbnail?.url || image.url);
    });
    frame.open();
  });
})();
