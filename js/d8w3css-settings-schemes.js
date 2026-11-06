/**
 * @file
 * The color scheme picker: shows the selected scheme's name in text. Selection
 * itself is native radios and needs no script.
 */
((Drupal, once) => {
  /**
   * The name a radio's label shows.
   *
   * @param {HTMLInputElement} radio
   *   A radio of the picker.
   *
   * @return {string}
   *   The name.
   */
  const schemeName = (radio) => {
    const name =
      radio.labels && radio.labels[0]
        ? radio.labels[0].querySelector('.w3css-schemes__name')
        : null;
    return name ? name.textContent.trim() : '';
  };

  /**
   * Adds the line with the selected name and keeps it current.
   *
   * @param {HTMLElement} picker
   *   The picker's wrapper.
   */
  const setUpPicker = (picker) => {
    const strong = document.createElement('strong');
    const current = document.createElement('p');
    current.className = 'w3css-schemes__current';
    current.append(`${Drupal.t('Selected:')} `, strong);
    picker.prepend(current);

    const update = () => {
      const checked = picker.querySelector('input[type="radio"]:checked');
      strong.textContent = checked ? schemeName(checked) : Drupal.t('None');
    };
    picker.addEventListener('change', update);
    update();
  };

  Drupal.behaviors.w3cssSchemePicker = {
    attach(context) {
      once('w3css-scheme-picker', '.w3css-schemes', context).forEach(
        setUpPicker,
      );
    },
  };
})(Drupal, once);
