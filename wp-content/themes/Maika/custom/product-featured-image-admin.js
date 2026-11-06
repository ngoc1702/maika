/* Keep the selected product image in sync while WordPress loads its preview. */
jQuery(() => {
  const form = document.querySelector('form#post');
  const box = document.querySelector('#postimagediv');
  const featuredImage = window.wp?.media?.featuredImage;
  if (!form || !box || !featuredImage || typeof featuredImage.set !== 'function') return;
  let selected = null;
  const sync = (beforeSubmit = false) => {
    if (selected === null) return;
    let fields = [...form.querySelectorAll('input[name="_thumbnail_id"]')];
    if (!fields.length) {
      const field = document.createElement('input');
      field.type = 'hidden';
      field.name = '_thumbnail_id';
      field.id = '_thumbnail_id';
      form.prepend(field);
      fields = [field];
    }
    fields.forEach(field => { field.value = String(selected); });
    // Place the selected image early in large product forms as well.
    if (beforeSubmit) form.prepend(fields[0]);
  };
  const nativeSet = featuredImage.set;
  featuredImage.set = function (id, ...args) {
    const imageId = Number(id);
    if (Number.isInteger(imageId) && (imageId > 0 || imageId === -1)) {
      selected = imageId;
      sync();
    }
    const result = nativeSet.call(this, id, ...args);
    sync();
    return result;
  };
  // A slower preview response for an earlier selection must not reset the ID.
  new MutationObserver(() => sync()).observe(box, {childList: true, subtree: true});
  form.addEventListener('submit', () => sync(true), true);
});
