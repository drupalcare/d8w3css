Drupal8 W3CSS Sub-theme is a start up theme for your customization.

CSS layers
----------
css/w3css-subtheme-style.css is written inside CSS layers. The layer order is
owned by the W3CSS Theme (css/base/w3css-layers.css); do not declare it in the
sub-theme. Put your rules in the w3css.subtheme layer (it beats the W3CSS
Theme's layers whatever the selector specificity); custom properties and color
values go in w3css.tokens, bare element selectors in w3css.base. Check with:
python3 scripts/css-layers-check.py (manifest: css-layers.json; the script is
the W3CSS Theme's own copy).
