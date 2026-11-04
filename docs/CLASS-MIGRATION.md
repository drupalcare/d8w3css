# CSS class migration: W3CSS Theme 2 to W3CSS Theme 3

Generated from `src/Upgrade/ClassMap.php` by the lane tool `class-migration-doc.py --theme w3css`. Do not edit by hand.

W3CSS Theme 3 renames its own classes and ids to BEM names with the `w3css-` prefix. W3.CSS library classes (`w3-*`) do not change. A sub-theme or custom module that selects the old names stops matching until it is migrated. The theme ships a script that does the renaming for you; change the script path below if the theme is not in `themes/contrib`.

## Before you start: let Drush do the renaming

1. Run `drush updb` first, so the theme settings are in their new places.
2. Save a copy of your theme (or commit it to Git).
3. Dry run, with a report written outside the web root (run from the project folder):

   ```
   drush php:script themes/contrib/drupal8_w3css_theme/scripts/migrate-classes.php -- mytheme --stored --content --report=w3css-report.md
   ```

   Nothing is written. The target is a theme machine name or a folder inside the Drupal root (`web/themes/custom/mytheme`, `web/modules/custom/my_module`). Drupal core, the W3CSS Theme itself and the public files folder are refused, and symbolic links are never followed.
4. Read the report. Configuration and content come first, then each file and its changed lines, then the items to check by hand.
5. Apply: `drush php:script themes/contrib/drupal8_w3css_theme/scripts/migrate-classes.php -- mytheme --apply`. Add `--stored` to save the configuration changes, and `--content` to save id changes in content (each changed item becomes a new revision).
6. Clear the caches: `drush cr`, then check your pages.

What the script changes: `.css .scss .sass .less .twig .js` files are rewritten; `.theme .php .module .inc` files are only reported (change the PHP by hand). `--ext=css,js` narrows the file types. `.yml` and `*.min.*` files, `node_modules`, `vendor`, `dist` and `.git` are never read.

What `--stored` covers: the region colour class settings of this theme and its sub-themes, Block Class classes, Views CSS, row and field classes, and text format allowed tags with the matching CKEditor 5 settings. `--content` renames only ids, in formatted text fields and menu link attributes.

Report-only rules: a name that is one plain word, or is both a class and an id, is renamed only where the line plainly sets classes (or ids). State classes and generic words (`show`) are renamed only when chained to a `w3css-` selector in a stylesheet; elsewhere they are reported.

## Classes (77)

| 2.x | 3.x |
|---|---|
| `admin-context` | `w3css-admin-context` |
| `animate-border` | `w3css-animate-border` |
| `bottom-box` | `w3css-bottom-box` |
| `bottom-container-inner` | `w3css-bottom-region__inner` |
| `breadcrumb-found` | `w3css-main-box--breadcrumb-found` |
| `close-nav` | `w3css-main-nav__close` |
| `clickable-dropdown-menu-fa-down` | `w3css-menu__arrow-toggle` |
| `clickable-dropdown-menu-fa-down-h` | `w3css-menu__arrow-toggle` |
| `clickable-dropdown-menu-fa-down-v` | `w3css-menu__arrow-toggle` |
| `clickable-dropdown-menu-link` | `w3css-menu__link--enabled` |
| `clickable-dropdown-menu-link-h` | `w3css-menu__link--enabled` |
| `clickable-dropdown-menu-link-v` | `w3css-menu__link--enabled` |
| `comment__permalink` | `w3css-comment__permalink` |
| `copyright-inner` | `w3css-copyright__inner` |
| `d8-fade` | `w3css-fade` |
| `d8-has-image` | `w3css-image-link` |
| `des-spacer` | `w3css-des-spacer` |
| `dropdown-arrow` | `w3css-menu__arrow` |
| `element-container-wrapper` | `w3css-element-container-wrapper` |
| `first-bottom` | `w3css-first-bottom` |
| `first-footer` | `w3css-first-footer` |
| `first-sidebar` | `w3css-first-sidebar` |
| `first-top` | `w3css-first-top` |
| `footer-box` | `w3css-footer-box` |
| `footer-container-inner` | `w3css-footer-region__inner` |
| `footer-menu-inner` | `w3css-footer-menu__inner` |
| `forth-bottom` | `w3css-forth-bottom` |
| `header-inner` | `w3css-header__inner` |
| `highlighted-inner` | `w3css-highlighted__inner` |
| `layout-region-node-footer__content` | `w3css-layout-region-node-footer__content` |
| `li-expanded` | `w3css-menu__item--expanded` |
| `li-item` | `w3css-menu__item` |
| `li-item-{menu}` | `w3css-menu__item--menu-{menu}` |
| `li-link` | `w3css-menu__link` |
| `li-link-{menu}` | `w3css-menu__link--menu-{menu}` |
| `li-parent` | `w3css-menu__item--parent` |
| `li-{n}` | `w3css-item-depth-{n}` |
| `link-disabled` | `w3css-menu__link--disabled` |
| `link-enabled` | `w3css-menu__link--enabled` |
| `main-box` | `w3css-main-box` |
| `main-container-inner` | `w3css-main-region__inner` |
| `main-navigation-inner-h` | `w3css-main-nav__inner--horizontal` |
| `main-navigation-inner-v` | `w3css-main-nav__inner--vertical` |
| `main-navigation-wrapper` | `w3css-main-nav` |
| `mobile-nav` | `w3css-mobile-nav__toggle` |
| `mobile-nav-responsive` | `w3css-responsive-nav__toggle` |
| `node__author-info` | `w3css-node__author-info` |
| `page-title-inner` | `w3css-page-title__inner` |
| `page-wrapper` | `w3css-page-wrapper` |
| `right-arrow` | `w3css-menu__right-arrow` |
| `search-button-block` | `w3css-search-button-block` |
| `second-bottom` | `w3css-second-bottom` |
| `second-footer` | `w3css-second-footer` |
| `second-sidebar` | `w3css-second-sidebar` |
| `second-top` | `w3css-second-top` |
| `site-name-link` | `w3css-site-name__link` |
| `site-name-slogan` | `w3css-site-name-slogan` |
| `social-media` | `w3css-social-media` |
| `tMenu` | `w3css-menu__link-toggle` |
| `tMenu-v` | `w3css-menu__link-toggle-vertical` |
| `third-bottom` | `w3css-third-bottom` |
| `third-footer` | `w3css-third-footer` |
| `third-top` | `w3css-third-top` |
| `toggle-child` | `w3css-menu__toggle-child` |
| `toggle-parent` | `w3css-menu__toggle-parent` |
| `top-box` | `w3css-top-box` |
| `top-container-inner` | `w3css-top-region__inner` |
| `ul-child` | `w3css-menu__submenu` |
| `ul-child-{menu}` | `w3css-menu__submenu--menu-{menu}` |
| `ul-parent` | `w3css-menu__list` |
| `ul-parent-{menu}` | `w3css-menu__list--menu-{menu}` |
| `ul-responsive-h` | `w3css-responsive-nav` |
| `ul-responsive-v` | `w3css-vertical-nav` |
| `ul-{n}` | `w3css-list-depth-{n}` |
| `open-nav-inner` | `w3css-main-nav__open` |
| `w3css-content` | `w3css-content-region` |
| `welcome-text-inner` | `w3css-welcome-text__inner` |

## Ids (26)

| 2.x | 3.x |
|---|---|
| `bottom-container` | `w3css-bottom-region` |
| `bottom-container-inner` | `w3css-bottom-region-inner` |
| `close-nav` | `w3css-main-nav-close` |
| `copyright` | `w3css-copyright` |
| `copyright-inner` | `w3css-copyright-inner` |
| `footer-container` | `w3css-footer-region` |
| `footer-container-inner` | `w3css-footer-region-inner` |
| `footer-menu` | `w3css-footer-menu` |
| `footer-menu-inner` | `w3css-footer-menu-inner` |
| `header` | `w3css-header` |
| `header-inner` | `w3css-header-inner` |
| `highlighted` | `w3css-highlighted` |
| `highlighted-inner` | `w3css-highlighted-inner` |
| `main-container` | `w3css-main-region` |
| `main-container-inner` | `w3css-main-region-inner` |
| `main-navigation-h` | `w3css-main-nav-horizontal` |
| `main-navigation-inner-h` | `w3css-main-nav-inner-horizontal` |
| `main-navigation-inner-v` | `w3css-main-nav-inner-vertical` |
| `main-navigation-v` | `w3css-main-nav-vertical` |
| `open-nav-inner` | `w3css-main-nav-open` |
| `page-title` | `w3css-page-title` |
| `page-title-inner` | `w3css-page-title-inner` |
| `top-container` | `w3css-top-region` |
| `top-container-inner` | `w3css-top-region-inner` |
| `welcome-text` | `w3css-welcome-text` |
| `welcome-text-inner` | `w3css-welcome-text-inner` |

## State classes (set by the theme JavaScript) (1)

| 2.x | 3.x |
|---|---|
| `{size}-screen` | `w3css-is-{size}-screen` |

## Generic words (chained to a theme selector only) (1)

| 2.x | 3.x |
|---|---|
| `show` | `w3css-is-open` |

## Context-dependent (pick by hand) (1)

| 2.x | Use |
|---|---|
| `breadcrumb` | w3css-breadcrumb on the nav, w3css-breadcrumb__list on the ul or ol (2.x printed breadcrumb on both). |

## Removed (no 3.x element) (4)

| 2.x | Why |
|---|---|
| `visually_hidden` | Core label_display setting value compared in Twig, not a class; rewriting it would break the comparison. |
| `#open-search` | Id selected in CSS on released branches; no template prints it (needs checking in W2). |
| `search-slide-inner` | Selected in CSS on released branches; no template prints it (needs checking in W2). |
| `search-slide-wrapper` | Selected in CSS on released branches; no template prints it (needs checking in W2). |

## Core names kept (73)

These core or Classy names are still printed beside the theme's own name, so selectors that use them keep working: `action-links`, `admin-list`, `button`, `checkbox`, `comment`, `comment-form__title`, `comment-wrapper`, `comment__author`, `comment__content`, `comment__meta`, `comment__time`, `description`, `details-wrapper`, `error`, `field`, `field-prefix`, `field-suffix`, `field__item`, `field__items`, `field__label`, `fieldset-wrapper`, `filter-guidelines-item`, `form-checkboxes`, `form-item`, `form-item--error-message`, `form-radios`, `form-required`, `form-wrapper`, `indented`, `item-list`, `label`, `layout-node-form`, `layout-region-node-footer`, `layout-region-node-main`, `layout-region-node-secondary`, `links`, `maintenance-page`, `marker`, `messages`, `messages__item`, `messages__list`, `node`, `node--unpublished`, `node--view-mode-full`, `node--view-mode-teaser`, `node__author-image`, `node__content`, `node__header`, `node__meta`, `node__title`, `page-title`, `pager__item`, `pager__items`, `primary`, `requirements`, `secondary`, `site-logo`, `site-name`, `site-slogan`, `system-status-general-info__item`, `system-status-general-info__items`, `system-status-general-info__run-cron`, `system-status-report__entry__value`, `system-status-report__requirements-group`, `system-status-report__status-icon`, `system-status-report__status-title`, `tabs`, `text-format-wrapper`, `unpublished`, `view`, `view-filters`, `view-header`, `views-ui-view-displays`.

## Unchanged on purpose (175)

Font and animation value classes are public names and keep their spelling: `abel`, `abril-fatface`, `akronim`, `aladin`, `alegreya-aans`, `alegreya-aans-sc`, `amita`, `animate-name-01`, `animate-name-02`, `animate-name-03`, `animate-name-04`, `animate-name-05`, `animate-name-06`, `animate-name-07`, `animate-name-08`, `animate-name-09`, `animate-name-10`, `animate-name-11`, `animate-name-12`, `animate-name-13`, `animate-name-14`, `animate-name-15`, `animate-name-16`, `animate-name-17`, `animate-name-18`, `animate-name-19`, `animate-name-20`, `animate-name-21`, `animate-name-22`, `animate-name-23`, `animate-name-24`, `animate-text-01`, `animate-text-02`, `animate-text-03`, `animate-text-04`, `animate-text-05`, `animate-text-06`, `animate-text-07`, `animate-text-08`, `animate-text-09`, `animate-text-10`, `animate-text-11`, `animate-text-12`, `animate-text-13`, `animate-text-14`, `animate-text-15`, `animate-text-16`, `animate-text-17`, `animate-text-18`, `animate-text-19`, `animate-text-20`, `animate-text-21`, `animate-text-22`, `animate-text-23`, `animate-text-24`, `animate-title-01`, `animate-title-02`, `animate-title-03`, `animate-title-04`, `animate-title-05`, `animate-title-06`, `animate-title-07`, `animate-title-08`, `animate-title-09`, `animate-title-10`, `animate-title-11`, `animate-title-12`, `animate-title-13`, `animate-title-14`, `animate-title-15`, `animate-title-16`, `animate-title-17`, `animate-title-18`, `animate-title-19`, `animate-title-20`, `animate-title-21`, `animate-title-22`, `animate-title-23`, `animate-title-24`, `anton`, `archivo-black`, `archivo-narrow`, `arimo`, `arvo`, `barlow`, `barlow-semi-condensed`, `barriecito`, `bebas-neue`, `big-shoulders-stencil-display`, `big-shoulders-stencil-text`, `bitter`, `black-ops-one`, `bree-serif`, `bungee-shade`, `cairo`, `ceviche-one`, `cinzel`, `comfortaa`, `courgette`, `creepster`, `dancing-script`, `dm-serif-display`, `domine`, `doppio-one`, `dosis`, `exo-2`, `faster-one`, `finger-paint`, `fira-sans`, `fjalla-one`, `francois-one`, `fredericka-the-great`, `fredoka`, `fredoka-one`, `frijole`, `glegoo`, `hammersmith-one`, `hind-siliguri`, `ibm-plex-sans`, `inconsolata`, `indie-flower`, `irish-grover`, `josefin-sans`, `kalam`, `kanit`, `kavivanar`, `kumar-one`, `lato`, `libre-baskerville`, `lobster-two`, `londrina-outline`, `lora`, `megrim`, `merienda`, `merriweather`, `metal-mania`, `monoton`, `montserrat`, `moo-lah-lah`, `mukta`, `new-rocker`, `nosifer`, `nunito`, `open-sans`, `oranienbaum`, `oswald`, `oxygen`, `patrick-hand`, `pirata-one`, `playfair-display`, `poppins`, `prompt`, `prosto-one`, `pt-serif`, `quicksand`, `raleway`, `righteous`, `roboto`, `roboto-condensed`, `roboto-mono`, `ruslan-display`, `rye`, `sancreek`, `slackey`, `source-sans-3`, `source-sans-pro`, `spline-sans`, `staatliches`, `titillium-web`, `trade-winds`, `train-one`, `ubuntu`, `unifrakturmaguntia`, `work-sans`, `yanone-kaffeesatz`.

A name in braces is a pattern: `{n}` is a number, `{menu}` a cleaned menu name, `{size}` small, medium or large.
