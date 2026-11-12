# CSS class migration: W3CSS Theme 2 to W3CSS Theme 3

Generated from `src/Upgrade/ClassMap.php` and `src/Upgrade/w3css-rename-map.json` by `scripts/class-list.py`. Do not edit by hand. A readable copy for site builders is `docs/class-migration.html`.

W3CSS Theme 3 renames its own classes, ids, CSS variables, JavaScript names, library ids and files to names with the `w3css-` prefix. W3.CSS library names (`w3-*`, `--w3-*`) do not change. A sub-theme or custom module that uses the old names stops matching until it is migrated. The theme ships a script that does the renaming for you; change the script path below if the theme is not in `themes/contrib`.

## Before you start: let Drush do the renaming

1. Run `drush updb` first, so the theme settings are in their new places.
2. Save a copy of your theme (or commit it to Git).
3. Dry run, with a report written outside the web root (run from the project folder):

   ```
   drush php:script themes/contrib/drupal8_w3css_theme/scripts/migrate-classes.php -- mytheme --stored --content --report=w3css-report.md
   ```

   Nothing is written. The target is a theme machine name or a folder inside the Drupal root (`web/themes/custom/mytheme`, `web/modules/custom/my_module`). Drupal core, the W3CSS Theme itself and the public files folder are refused, and symbolic links are never followed.
4. Read the report. Configuration and content come first, then each file and its changed lines, then the items to check by hand.
5. Apply: `drush php:script themes/contrib/drupal8_w3css_theme/scripts/migrate-classes.php -- mytheme --apply`. Add `--stored` to save the configuration changes, `--content` to save id changes in content (see how `--content` saves, below), and `--yml` to rewrite library ids in `*.info.yml` and `*.libraries.yml`.
6. Clear the caches: `drush cr`, then check your pages.

What the script changes: `.css .scss .sass .less .twig .js` files are rewritten; `.theme .php .module .inc` files are only reported (change the PHP by hand). `--ext=css,js` narrows the file types. `*.min.*` files, `node_modules`, `vendor`, `dist` and `.git` are never read.

YAML files: unless `--ext` is given, `.yml` files are read for library ids and moved file paths. They are reported. Library ids in `*.info.yml` and `*.libraries.yml` are rewritten only with `--apply --yml`. The old library ids keep working as deprecated aliases for one major version, so a site that does not migrate them still loads.

What `--stored` covers: the region color class settings of this theme and its sub-themes, Block Class classes, Views CSS, row and field classes, and text format allowed tags with the matching CKEditor 5 settings. `--content` renames only ids, in formatted text fields and menu link attributes.

How `--content` saves: a node, term, reusable block or menu link becomes a new revision, so the old text stays in its history. A paragraph, or a Layout Builder inline block, is saved in place on the revision its page already points at, with no new revision: its page stores that revision number, so a new revision would never be shown. The report marks those rows `(in place, no new revision)`; the report's old and new values are the record of what changed, so take a database backup before `--apply --content`. Only the current (default) revision is rewritten; a pending draft keeps the old ids.

Links: only a link to the same page (`href="#header"`) is renamed. A link that names another page or site before the `#` (`href="/about#header"`, `href="https://example.org/#header"`) is never changed, because that page may not use the theme; the report lists it as `other-page-link` with the whole address, to check by hand.

Report-only rules: a name that is one plain word, or is both a class and an id, is renamed only where the line plainly sets classes (or ids). State classes and generic words (`show`) are renamed only when chained to a `w3css-` selector in a stylesheet; elsewhere they are reported. A once id is rewritten in `once()` calls and `data-once` selectors only. A data attribute read through `dataset` is reported, because its spelling differs.

## Classes (79)

| 2.x | 3.x |
|---|---|
| `admin-context` | `w3css-page--admin` |
| `animate-border` | `w3css-animate-border` |
| `bottom-box` | `w3css-page__bottom` |
| `bottom-container-inner` | `w3css-page__bottom-inner` |
| `breadcrumb-found` | `w3css-page__main--breadcrumb-found` |
| `close-nav` | `w3css-page__nav-close` |
| `clickable-dropdown-menu-fa-down` | `w3css-menu__arrow-toggle` |
| `clickable-dropdown-menu-fa-down-h` | `w3css-menu__arrow-toggle` |
| `clickable-dropdown-menu-fa-down-v` | `w3css-menu__arrow-toggle` |
| `clickable-dropdown-menu-fa-right` | `w3css-menu__right-arrow` |
| `clickable-dropdown-menu-fa-right-h` | `w3css-menu__right-arrow` |
| `clickable-dropdown-menu-link` | `w3css-menu__link--enabled` |
| `clickable-dropdown-menu-link-h` | `w3css-menu__link--enabled` |
| `clickable-dropdown-menu-link-v` | `w3css-menu__link--enabled` |
| `comment__permalink` | `w3css-comment__permalink` |
| `copyright-inner` | `w3css-copyright__inner` |
| `d8-fade` | `w3css-fade` |
| `d8-has-image` | `w3css-image__link` |
| `des-spacer` | `w3css-file-upload-help__spacer` |
| `dropdown-arrow` | `w3css-menu__arrow` |
| `element-container-wrapper` | `w3css-container__wrapper` |
| `first-bottom` | `w3css-page__bottom-first` |
| `first-footer` | `w3css-page__footer-first` |
| `first-sidebar` | `w3css-page__sidebar-first` |
| `first-top` | `w3css-page__top-first` |
| `footer-box` | `w3css-page__footer` |
| `footer-container-inner` | `w3css-page__footer-inner` |
| `footer-menu-inner` | `w3css-page__footer-menu-inner` |
| `forth-bottom` | `w3css-page__bottom-fourth` |
| `header-inner` | `w3css-page__header-inner` |
| `highlighted-inner` | `w3css-highlighted__inner` |
| `layout-region-node-footer__content` | `w3css-node-edit-form__footer-content` |
| `li-expanded` | `w3css-menu__item--expanded` |
| `li-item` | `w3css-menu__item` |
| `li-item-{menu}` | `w3css-menu__item--menu-{menu}` |
| `li-link` | `w3css-menu__link` |
| `li-link-{menu}` | `w3css-menu__link--menu-{menu}` |
| `li-parent` | `w3css-menu__item--parent` |
| `li-{n}` | `w3css-menu__item--depth-{n}` |
| `link-disabled` | `w3css-menu__link--disabled` |
| `link-enabled` | `w3css-menu__link--enabled` |
| `main-box` | `w3css-page__main` |
| `main-container-inner` | `w3css-main-region__inner` |
| `main-navigation-inner-h` | `w3css-page__nav-inner--horizontal` |
| `main-navigation-inner-v` | `w3css-page__nav-inner--vertical` |
| `main-navigation-wrapper` | `w3css-page__nav` |
| `mobile-nav` | `w3css-page__nav-toggle` |
| `mobile-nav-responsive` | `w3css-menu__responsive-toggle` |
| `node__author-info` | `w3css-node__author-info` |
| `page-title-inner` | `w3css-page-title__inner` |
| `page-wrapper` | `w3css-page` |
| `right-arrow` | `w3css-menu__right-arrow` |
| `search-button-block` | `w3css-search-block-form__button` |
| `second-bottom` | `w3css-page__bottom-second` |
| `second-footer` | `w3css-page__footer-second` |
| `second-sidebar` | `w3css-page__sidebar-second` |
| `second-top` | `w3css-page__top-second` |
| `site-name-link` | `w3css-site-branding__name-link` |
| `site-name-slogan` | `w3css-site-branding__name-slogan` |
| `social-media` | `w3css-social-media` |
| `tMenu` | `w3css-menu__link-toggle` |
| `tMenu-v` | `w3css-menu__link-toggle--vertical` |
| `third-bottom` | `w3css-page__bottom-third` |
| `third-footer` | `w3css-page__footer-third` |
| `third-top` | `w3css-page__top-third` |
| `toggle-child` | `w3css-menu__toggle-child` |
| `toggle-parent` | `w3css-menu__toggle-parent` |
| `top-box` | `w3css-page__top` |
| `top-container-inner` | `w3css-page__top-inner` |
| `ul-child` | `w3css-menu__submenu` |
| `ul-child-{menu}` | `w3css-menu__submenu--menu-{menu}` |
| `ul-parent` | `w3css-menu__list` |
| `ul-parent-{menu}` | `w3css-menu__list--menu-{menu}` |
| `ul-responsive-h` | `w3css-menu--responsive-h` |
| `ul-responsive-v` | `w3css-menu--responsive-v` |
| `ul-{n}` | `w3css-list-depth-{n}` |
| `open-nav-inner` | `w3css-page__nav-open` |
| `w3css-content` | `w3css-page__content` |
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

## Other renames: variables, JavaScript, library ids and files

Each table lists the rows that change. The last columns say what the script does with them: `Rewritten` (the script edits the file), `Rewritten with --yml` (only in info and libraries YAML files), `Reported, change by hand`, `Saved by drush updb (setting values) or by the script with --stored (classes in configuration)`, or `Not needed: this name never shipped` (a name that only existed on the 3.0 development branch; the 2.x names above already go straight to the final name). Rows that keep their name are counted, not listed; `docs/class-migration.html` shows them all.

### CSS variables (60)

| Old | New | Action | What the script does | Note |
|---|---|---|---|---|
| --w3css-custom-* (122 names, set by the 31 custom schemes) | --r-<role> for the 15 region roles + --w3css-scheme-<part> for the rest | rename | Reported, change by hand | names like --w3css-custom-bottom-region-bottom-box-and-12-more-border-color are generator output: they list selectors, not meaning. Owner decision D7 (2026-11-10) says region colors are --r-<role>. Needs checking: a one-to-one table of the 122 names to the 15 roles; only the generator knows which selectors each covers. |
| --w3css-theme-* (38 names, set by the 22 W3 schemes) | --w3css-scheme-* (shade ladder: l5..d5, light, dark, base, accent, hover; each -text / -background) | rename | Rewritten | one family prefix --w3css-scheme- for both scheme kinds avoids the two-names-for-one-job split (theme vs custom) |
| --d8s-* (15 names, settings form) | --w3css-settings-*  | rename | Rewritten | d8s is a third abbreviation of the old product name |
| --w3css-submenu-max, --w3css-submenu-shift, --w3css-arrow-open | --w3css-menu-submenu-max, --w3css-menu-submenu-shift, --w3css-menu-arrow-open | rename | Rewritten | component-private values: --w3css-<component>-<prop> |
| `--w-main-inner` | `--w3css-scheme-main-text` | rename | Rewritten | token set by every ready-made color scheme file in 2.x |
| `--w-main-main-b` | `--w3css-scheme-main-background` | rename | Rewritten | token set by every ready-made color scheme file in 2.x |
| `--w-l5` | `--w3css-scheme-l5-text` | rename | Rewritten | token set by every ready-made color scheme file in 2.x |
| `--w-l4` | `--w3css-scheme-l4-text` | rename | Rewritten | token set by every ready-made color scheme file in 2.x |
| `--w-l3` | `--w3css-scheme-l3-text` | rename | Rewritten | token set by every ready-made color scheme file in 2.x |
| `--w-l2` | `--w3css-scheme-l2-text` | rename | Rewritten | token set by every ready-made color scheme file in 2.x |
| `--w-l1` | `--w3css-scheme-l1-text` | rename | Rewritten | token set by every ready-made color scheme file in 2.x |
| `--w-d1` | `--w3css-scheme-d1-text` | rename | Rewritten | token set by every ready-made color scheme file in 2.x |
| `--w-d2` | `--w3css-scheme-d2-text` | rename | Rewritten | token set by every ready-made color scheme file in 2.x |
| `--w-d3` | `--w3css-scheme-d3-text` | rename | Rewritten | token set by every ready-made color scheme file in 2.x |
| `--w-d4` | `--w3css-scheme-d4-text` | rename | Rewritten | token set by every ready-made color scheme file in 2.x |
| `--w-d5` | `--w3css-scheme-d5-text` | rename | Rewritten | token set by every ready-made color scheme file in 2.x |
| `--w-l5-b` | `--w3css-scheme-l5-background` | rename | Rewritten | token set by every ready-made color scheme file in 2.x |
| `--w-l4-b` | `--w3css-scheme-l4-background` | rename | Rewritten | token set by every ready-made color scheme file in 2.x |
| `--w-l3-b` | `--w3css-scheme-l3-background` | rename | Rewritten | token set by every ready-made color scheme file in 2.x |
| `--w-l2-b` | `--w3css-scheme-l2-background` | rename | Rewritten | token set by every ready-made color scheme file in 2.x |
| `--w-l1-b` | `--w3css-scheme-l1-background` | rename | Rewritten | token set by every ready-made color scheme file in 2.x |
| `--w-d1-b` | `--w3css-scheme-d1-background` | rename | Rewritten | token set by every ready-made color scheme file in 2.x |
| `--w-d2-b` | `--w3css-scheme-d2-background` | rename | Rewritten | token set by every ready-made color scheme file in 2.x |
| `--w-d3-b` | `--w3css-scheme-d3-background` | rename | Rewritten | token set by every ready-made color scheme file in 2.x |
| `--w-d4-b` | `--w3css-scheme-d4-background` | rename | Rewritten | token set by every ready-made color scheme file in 2.x |
| `--w-d5-b` | `--w3css-scheme-d5-background` | rename | Rewritten | token set by every ready-made color scheme file in 2.x |
| `--w-light-b` | `--w3css-scheme-light-background` | rename | Rewritten | token set by every ready-made color scheme file in 2.x |
| `--w-dark-b` | `--w3css-scheme-dark-background` | rename | Rewritten | token set by every ready-made color scheme file in 2.x |
| `--w-theme` | `--w3css-scheme-base-text` | rename | Rewritten | token set by every ready-made color scheme file in 2.x |
| `--w-theme-b` | `--w3css-scheme-base-background` | rename | Rewritten | token set by every ready-made color scheme file in 2.x |
| `--w-text-theme` | `--w3css-scheme-accent-text` | rename | Rewritten | token set by every ready-made color scheme file in 2.x |
| `--w-border-the-r` | `--w3css-scheme-border` | rename | Rewritten | token set by every ready-made color scheme file in 2.x |
| `--w-hover-them` | `--w3css-scheme-hover-text` | rename | Rewritten | token set by every ready-made color scheme file in 2.x |
| `--w-hover-them-b` | `--w3css-scheme-hover-background` | rename | Rewritten | token set by every ready-made color scheme file in 2.x |
| `--w-kbtn` | `--w3css-scheme-keyframe-button` | rename | Rewritten | token set by every ready-made color scheme file in 2.x |
| `--w-kbd` | `--w3css-scheme-keyframe-border` | rename | Rewritten | token set by every ready-made color scheme file in 2.x |
| `--w-html` | (decision pending: --r-<role> or --w3css-scheme-<part>) | rename | Reported, change by hand | token set by the custom color scheme files in 2.x; the 3.0 name is not decided, so the tool only reports it |
| `--w-h1` | (decision pending: --r-<role> or --w3css-scheme-<part>) | rename | Reported, change by hand | token set by the custom color scheme files in 2.x; the 3.0 name is not decided, so the tool only reports it |
| `--w-pt` | (decision pending: --r-<role> or --w3css-scheme-<part>) | rename | Reported, change by hand | token set by the custom color scheme files in 2.x; the 3.0 name is not decided, so the tool only reports it |
| `--w-fm-p` | (decision pending: --r-<role> or --w3css-scheme-<part>) | rename | Reported, change by hand | token set by the custom color scheme files in 2.x; the 3.0 name is not decided, so the tool only reports it |
| `--w-fm-a` | (decision pending: --r-<role> or --w3css-scheme-<part>) | rename | Reported, change by hand | token set by the custom color scheme files in 2.x; the 3.0 name is not decided, so the tool only reports it |
| `--w-cke-top-r` | (decision pending: --r-<role> or --w3css-scheme-<part>) | rename | Reported, change by hand | token set by the custom color scheme files in 2.x; the 3.0 name is not decided, so the tool only reports it |
| `--w-hd-a` | (decision pending: --r-<role> or --w3css-scheme-<part>) | rename | Reported, change by hand | token set by the custom color scheme files in 2.x; the 3.0 name is not decided, so the tool only reports it |
| `--w-pt-b` | (decision pending: --r-<role> or --w3css-scheme-<part>) | rename | Reported, change by hand | token set by the custom color scheme files in 2.x; the 3.0 name is not decided, so the tool only reports it |
| `--w-hd-b` | (decision pending: --r-<role> or --w3css-scheme-<part>) | rename | Reported, change by hand | token set by the custom color scheme files in 2.x; the 3.0 name is not decided, so the tool only reports it |
| `--w-hd-p` | (decision pending: --r-<role> or --w3css-scheme-<part>) | rename | Reported, change by hand | token set by the custom color scheme files in 2.x; the 3.0 name is not decided, so the tool only reports it |
| `--w-open-searc` | (decision pending: --r-<role> or --w3css-scheme-<part>) | rename | Reported, change by hand | token set by the custom color scheme files in 2.x; the 3.0 name is not decided, so the tool only reports it |
| `--w-open-searc-b` | (decision pending: --r-<role> or --w3css-scheme-<part>) | rename | Reported, change by hand | token set by the custom color scheme files in 2.x; the 3.0 name is not decided, so the tool only reports it |
| `--w-bt-b` | (decision pending: --r-<role> or --w3css-scheme-<part>) | rename | Reported, change by hand | token set by the custom color scheme files in 2.x; the 3.0 name is not decided, so the tool only reports it |
| `--w-fm-inner-f-v` | (decision pending: --r-<role> or --w3css-scheme-<part>) | rename | Reported, change by hand | token set by the custom color scheme files in 2.x; the 3.0 name is not decided, so the tool only reports it |
| `--w-cr-b` | (decision pending: --r-<role> or --w3css-scheme-<part>) | rename | Reported, change by hand | token set by the custom color scheme files in 2.x; the 3.0 name is not decided, so the tool only reports it |
| `--w-input-focu-s` | (decision pending: --r-<role> or --w3css-scheme-<part>) | rename | Reported, change by hand | token set by the custom color scheme files in 2.x; the 3.0 name is not decided, so the tool only reports it |
| `--w-top-top-bo` | (decision pending: --r-<role> or --w3css-scheme-<part>) | rename | Reported, change by hand | token set by the custom color scheme files in 2.x; the 3.0 name is not decided, so the tool only reports it |
| `--w-hd` | (decision pending: --r-<role> or --w3css-scheme-<part>) | rename | Reported, change by hand | token set by the custom color scheme files in 2.x; the 3.0 name is not decided, so the tool only reports it |
| `--w-cr` | (decision pending: --r-<role> or --w3css-scheme-<part>) | rename | Reported, change by hand | token set by the custom color scheme files in 2.x; the 3.0 name is not decided, so the tool only reports it |
| `--w-hd-h1` | (decision pending: --r-<role> or --w3css-scheme-<part>) | rename | Reported, change by hand | token set by the custom color scheme files in 2.x; the 3.0 name is not decided, so the tool only reports it |
| `--w-top-top-bo-b` | (decision pending: --r-<role> or --w3css-scheme-<part>) | rename | Reported, change by hand | token set by the custom color scheme files in 2.x; the 3.0 name is not decided, so the tool only reports it |
| `--w-main-b` | (decision pending: --r-<role> or --w3css-scheme-<part>) | rename | Reported, change by hand | token set by the custom color scheme files in 2.x; the 3.0 name is not decided, so the tool only reports it |

2 more row(s) keep their name.

### JavaScript behaviors (14)

| Old | New | Action | What the script does | Note |
|---|---|---|---|---|
| `Drupal.behaviors.d8w3cssSettingsSearch` | `Drupal.behaviors.w3cssSettingsSearch` | rename | Rewritten | names follow w3css<Component> |
| `Drupal.behaviors.w3cssFullOpacity` | `Drupal.behaviors.w3cssFade` | rename | Rewritten | names follow w3css<Component> |
| `Drupal.behaviors.w3cssMainNav` | `Drupal.behaviors.w3cssPageNav` | rename | Rewritten | names follow w3css<Component> |
| `Drupal.behaviors.w3cssResponsiveMenu` | `Drupal.behaviors.w3cssMenuResponsive` | rename | Rewritten | names follow w3css<Component> |
| `Drupal.behaviors.w3cssTheme` | `Drupal.behaviors.w3cssPage` | rename | Rewritten | names follow w3css<Component>; the search code moved to Drupal.behaviors.w3cssSearch |
| `Drupal.behaviors.d8w3cssMenuDepth` | `Drupal.behaviors.w3cssMenuDepth` | rename | Rewritten | released in 2.0.18; the old name stays a deprecated alias in 3.0 |
| `Drupal.behaviors.d8w3cssResponsiveMenu` | `Drupal.behaviors.w3cssMenuResponsive` | rename | Rewritten | released in 2.0.18; the old name stays a deprecated alias in 3.0 |
| `Drupal.behaviors.d8w3cssMainNav` | `Drupal.behaviors.w3cssPageNav` | rename | Rewritten | released in 2.0.18; the old name stays a deprecated alias in 3.0 |
| `Drupal.behaviors.d8w3cssTheme` | `Drupal.behaviors.w3cssPage` | rename | Rewritten | released in 2.0.18; the old name stays a deprecated alias in 3.0; the search code moved to Drupal.behaviors.w3cssSearch |
| `Drupal.behaviors.d8w3cssFullOpacity` | `Drupal.behaviors.w3cssFade` | rename | Rewritten | released in 2.0.18; the old name stays a deprecated alias in 3.0 |

4 more row(s) keep their name.

### Once ids (data-once) (30)

| Old | New | Action | What the script does | Note |
|---|---|---|---|---|
| `d8w3css-settings-search` | `w3css-settings-search` | rename | Rewritten | once ids follow w3css-<component>-<what>; they sit in the data-once attribute of elements, so a sub-theme that runs once() with the same id would collide: keep unique per component |
| `w3css-arrow-toggle` | `w3css-menu-arrow-toggle` | rename | Rewritten | once ids follow w3css-<component>-<what>; they sit in the data-once attribute of elements, so a sub-theme that runs once() with the same id would collide: keep unique per component |
| `w3css-footer-form` | `w3css-search-footer-form` | rename | Rewritten | once ids follow w3css-<component>-<what>; they sit in the data-once attribute of elements, so a sub-theme that runs once() with the same id would collide: keep unique per component |
| `w3css-link-toggle` | `w3css-menu-link-toggle` | rename | Rewritten | once ids follow w3css-<component>-<what>; they sit in the data-once attribute of elements, so a sub-theme that runs once() with the same id would collide: keep unique per component |
| `w3css-link-toggle-vertical` | `w3css-menu-link-toggle-vertical` | rename | Rewritten | once ids follow w3css-<component>-<what>; they sit in the data-once attribute of elements, so a sub-theme that runs once() with the same id would collide: keep unique per component |
| `w3css-main-box` | `w3css-page-main` | rename | Rewritten | once ids follow w3css-<component>-<what>; they sit in the data-once attribute of elements, so a sub-theme that runs once() with the same id would collide: keep unique per component |
| `w3css-main-nav-close` | `w3css-page-nav-close` | rename | Rewritten | once ids follow w3css-<component>-<what>; they sit in the data-once attribute of elements, so a sub-theme that runs once() with the same id would collide: keep unique per component |
| `w3css-main-nav-open` | `w3css-page-nav-open` | rename | Rewritten | once ids follow w3css-<component>-<what>; they sit in the data-once attribute of elements, so a sub-theme that runs once() with the same id would collide: keep unique per component |
| `w3css-mobile-nav-toggle` | `w3css-page-nav-toggle` | rename | Rewritten | once ids follow w3css-<component>-<what>; they sit in the data-once attribute of elements, so a sub-theme that runs once() with the same id would collide: keep unique per component |
| `w3css-responsive-nav-toggle` | `w3css-menu-responsive-toggle` | rename | Rewritten | once ids follow w3css-<component>-<what>; they sit in the data-once attribute of elements, so a sub-theme that runs once() with the same id would collide: keep unique per component |
| `w3css-tabledrag-toggle` | `w3css-table-tabledrag-toggle` | rename | Rewritten | once ids follow w3css-<component>-<what>; they sit in the data-once attribute of elements, so a sub-theme that runs once() with the same id would collide: keep unique per component |
| `w3css-toolbar-search` | `w3css-search-toolbar` | rename | Rewritten | once ids follow w3css-<component>-<what>; they sit in the data-once attribute of elements, so a sub-theme that runs once() with the same id would collide: keep unique per component |
| `w3css-window-listeners` | `w3css-page-window-listeners` | rename | Rewritten | once ids follow w3css-<component>-<what>; they sit in the data-once attribute of elements, so a sub-theme that runs once() with the same id would collide: keep unique per component |

17 more row(s) keep their name.

### Data attributes (3)

| Old | New | Action | What the script does | Note |
|---|---|---|---|---|
| `data-w3css-section` | `data-w3css-settings-section` | rename | Rewritten | admin settings form |
| `data-w3css-icon` | `data-w3css-settings-icon` | rename | Rewritten | admin settings form template element |

1 more row(s) keep their name.

### drupalSettings keys (1)

1 more row(s) keep their name.

### Browser storage keys (1)

1 more row(s) keep their name.

### Library ids (251)

| Old | New | Action | What the script does | Note |
|---|---|---|---|---|
| `drupal8_w3css_subtheme/d8w3css-subtheme-global` | `drupal8_w3css_subtheme/subtheme-global` | rename | Rewritten with --yml | library id inside the starter kit; keep the pattern <layer>-<name> |
| `drupal8_w3css_theme/w3-css` | base-w3css-drupal (+ base-w3css-layers, utilities-w3css-extended-classes, 11 utilities-w3css-color-<name>) | split | Reported, change by hand; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). |
| `drupal8_w3css_theme/d8w3css-global-styling` | one library per CSS file, see css_file rows (base-w3css-base, layout-w3css-page, component-menu, ...) and js: component-menu, base-page | split | Reported, change by hand; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). |
| `drupal8_w3css_theme/d8w3css-full-opacity` | `drupal8_w3css_theme/utility-fade` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). |
| `drupal8_w3css_theme/d8w3css-user-login-custom` | `drupal8_w3css_theme/layout-user-login` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). |
| `drupal8_w3css_theme/d8w3css-node-edit-form` | `drupal8_w3css_theme/layout-node-edit-form` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). |
| `drupal8_w3css_theme/social-media-icon-size-m` | `drupal8_w3css_theme/component-social-media-size-m` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). |
| `drupal8_w3css_theme/social-media-icon-size-l` | `drupal8_w3css_theme/component-social-media-size-l` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). |
| `drupal8_w3css_theme/font-awesome-7-in` | `drupal8_w3css_theme/font-awesome-7-local` | rename | Rewritten with --yml | new in 3.0.x-dev. Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/font-awesome-7-ex` | `drupal8_w3css_theme/font-awesome-7-cdn` | rename | Rewritten with --yml | new in 3.0.x-dev. Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-alegreya-sans` | `drupal8_w3css_theme/font-alegreya-sans-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-alegreya-sans` | `drupal8_w3css_theme/font-alegreya-sans-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-alegreya-sans-sc` | `drupal8_w3css_theme/font-alegreya-sans-sc-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-alegreya-sans-sc` | `drupal8_w3css_theme/font-alegreya-sans-sc-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-lato` | `drupal8_w3css_theme/font-lato-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-lato` | `drupal8_w3css_theme/font-lato-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-montserrat` | `drupal8_w3css_theme/font-montserrat-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-montserrat` | `drupal8_w3css_theme/font-montserrat-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-nunito` | `drupal8_w3css_theme/font-nunito-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-nunito` | `drupal8_w3css_theme/font-nunito-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-kavivanar` | `drupal8_w3css_theme/font-kavivanar-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-kavivanar` | `drupal8_w3css_theme/font-kavivanar-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-open-sans` | `drupal8_w3css_theme/font-open-sans-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-open-sans` | `drupal8_w3css_theme/font-open-sans-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-oswald` | `drupal8_w3css_theme/font-oswald-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-oswald` | `drupal8_w3css_theme/font-oswald-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-playfair-display` | `drupal8_w3css_theme/font-playfair-display-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-playfair-display` | `drupal8_w3css_theme/font-playfair-display-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-roboto` | `drupal8_w3css_theme/font-roboto-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-roboto` | `drupal8_w3css_theme/font-roboto-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-roboto-condensed` | `drupal8_w3css_theme/font-roboto-condensed-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-roboto-condensed` | `drupal8_w3css_theme/font-roboto-condensed-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-roboto-mono` | `drupal8_w3css_theme/font-roboto-mono-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-roboto-mono` | `drupal8_w3css_theme/font-roboto-mono-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-source-sans-3` | `drupal8_w3css_theme/font-source-sans-3-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-source-sans-3` | `drupal8_w3css_theme/font-source-sans-3-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-domine` | `drupal8_w3css_theme/font-domine-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-domine` | `drupal8_w3css_theme/font-domine-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-glegoo` | `drupal8_w3css_theme/font-glegoo-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-glegoo` | `drupal8_w3css_theme/font-glegoo-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-amita` | `drupal8_w3css_theme/font-amita-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-amita` | `drupal8_w3css_theme/font-amita-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-oranienbaum` | `drupal8_w3css_theme/font-oranienbaum-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-oranienbaum` | `drupal8_w3css_theme/font-oranienbaum-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-abel` | `drupal8_w3css_theme/font-abel-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-abel` | `drupal8_w3css_theme/font-abel-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-archivo-narrow` | `drupal8_w3css_theme/font-archivo-narrow-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-archivo-narrow` | `drupal8_w3css_theme/font-archivo-narrow-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-arimo` | `drupal8_w3css_theme/font-arimo-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-arimo` | `drupal8_w3css_theme/font-arimo-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-arvo` | `drupal8_w3css_theme/font-arvo-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-arvo` | `drupal8_w3css_theme/font-arvo-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-barlow` | `drupal8_w3css_theme/font-barlow-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-barlow` | `drupal8_w3css_theme/font-barlow-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-barlow-semi-condensed` | `drupal8_w3css_theme/font-barlow-semi-condensed-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-barlow-semi-condensed` | `drupal8_w3css_theme/font-barlow-semi-condensed-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-bitter` | `drupal8_w3css_theme/font-bitter-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-bitter` | `drupal8_w3css_theme/font-bitter-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-cairo` | `drupal8_w3css_theme/font-cairo-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-cairo` | `drupal8_w3css_theme/font-cairo-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-comfortaa` | `drupal8_w3css_theme/font-comfortaa-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-comfortaa` | `drupal8_w3css_theme/font-comfortaa-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-courgette` | `drupal8_w3css_theme/font-courgette-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-courgette` | `drupal8_w3css_theme/font-courgette-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-dosis` | `drupal8_w3css_theme/font-dosis-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-dosis` | `drupal8_w3css_theme/font-dosis-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-exo-2` | `drupal8_w3css_theme/font-exo-2-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-exo-2` | `drupal8_w3css_theme/font-exo-2-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-fira-sans` | `drupal8_w3css_theme/font-fira-sans-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-fira-sans` | `drupal8_w3css_theme/font-fira-sans-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-hind-siliguri` | `drupal8_w3css_theme/font-hind-siliguri-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-hind-siliguri` | `drupal8_w3css_theme/font-hind-siliguri-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-ibm-plex-sans` | `drupal8_w3css_theme/font-ibm-plex-sans-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-ibm-plex-sans` | `drupal8_w3css_theme/font-ibm-plex-sans-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-inconsolata` | `drupal8_w3css_theme/font-inconsolata-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-inconsolata` | `drupal8_w3css_theme/font-inconsolata-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-josefin-sans` | `drupal8_w3css_theme/font-josefin-sans-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-josefin-sans` | `drupal8_w3css_theme/font-josefin-sans-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-kanit` | `drupal8_w3css_theme/font-kanit-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-kanit` | `drupal8_w3css_theme/font-kanit-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-libre-baskerville` | `drupal8_w3css_theme/font-libre-baskerville-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-libre-baskerville` | `drupal8_w3css_theme/font-libre-baskerville-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-lora` | `drupal8_w3css_theme/font-lora-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-lora` | `drupal8_w3css_theme/font-lora-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-merienda` | `drupal8_w3css_theme/font-merienda-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-merienda` | `drupal8_w3css_theme/font-merienda-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-merriweather` | `drupal8_w3css_theme/font-merriweather-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-merriweather` | `drupal8_w3css_theme/font-merriweather-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-mukta` | `drupal8_w3css_theme/font-mukta-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-mukta` | `drupal8_w3css_theme/font-mukta-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-oxygen` | `drupal8_w3css_theme/font-oxygen-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-oxygen` | `drupal8_w3css_theme/font-oxygen-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-poppins` | `drupal8_w3css_theme/font-poppins-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-poppins` | `drupal8_w3css_theme/font-poppins-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-prompt` | `drupal8_w3css_theme/font-prompt-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-prompt` | `drupal8_w3css_theme/font-prompt-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-pt-serif` | `drupal8_w3css_theme/font-pt-serif-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-pt-serif` | `drupal8_w3css_theme/font-pt-serif-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-quicksand` | `drupal8_w3css_theme/font-quicksand-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-quicksand` | `drupal8_w3css_theme/font-quicksand-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-raleway` | `drupal8_w3css_theme/font-raleway-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-raleway` | `drupal8_w3css_theme/font-raleway-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-spline-sans` | `drupal8_w3css_theme/font-spline-sans-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-spline-sans` | `drupal8_w3css_theme/font-spline-sans-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-titillium-web` | `drupal8_w3css_theme/font-titillium-web-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-titillium-web` | `drupal8_w3css_theme/font-titillium-web-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-ubuntu` | `drupal8_w3css_theme/font-ubuntu-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-ubuntu` | `drupal8_w3css_theme/font-ubuntu-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-work-sans` | `drupal8_w3css_theme/font-work-sans-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-work-sans` | `drupal8_w3css_theme/font-work-sans-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-abril-fatface` | `drupal8_w3css_theme/font-abril-fatface-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-abril-fatface` | `drupal8_w3css_theme/font-abril-fatface-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-aladin` | `drupal8_w3css_theme/font-aladin-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-aladin` | `drupal8_w3css_theme/font-aladin-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-anton` | `drupal8_w3css_theme/font-anton-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-anton` | `drupal8_w3css_theme/font-anton-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-archivo-black` | `drupal8_w3css_theme/font-archivo-black-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-archivo-black` | `drupal8_w3css_theme/font-archivo-black-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-bebas-neue` | `drupal8_w3css_theme/font-bebas-neue-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-bebas-neue` | `drupal8_w3css_theme/font-bebas-neue-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-big-shoulders-stencil-display` | `drupal8_w3css_theme/font-big-shoulders-stencil-display-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-big-shoulders-stencil-display` | `drupal8_w3css_theme/font-big-shoulders-stencil-display-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-big-shoulders-stencil-text` | `drupal8_w3css_theme/font-big-shoulders-stencil-text-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-big-shoulders-stencil-text` | `drupal8_w3css_theme/font-big-shoulders-stencil-text-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-black-ops-one` | `drupal8_w3css_theme/font-black-ops-one-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-black-ops-one` | `drupal8_w3css_theme/font-black-ops-one-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-bree-serif` | `drupal8_w3css_theme/font-bree-serif-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-bree-serif` | `drupal8_w3css_theme/font-bree-serif-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-cinzel` | `drupal8_w3css_theme/font-cinzel-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-cinzel` | `drupal8_w3css_theme/font-cinzel-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-dancing-script` | `drupal8_w3css_theme/font-dancing-script-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-dancing-script` | `drupal8_w3css_theme/font-dancing-script-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-dm-serif-display` | `drupal8_w3css_theme/font-dm-serif-display-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-dm-serif-display` | `drupal8_w3css_theme/font-dm-serif-display-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-doppio-one` | `drupal8_w3css_theme/font-doppio-one-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-doppio-one` | `drupal8_w3css_theme/font-doppio-one-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-fjalla-one` | `drupal8_w3css_theme/font-fjalla-one-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-fjalla-one` | `drupal8_w3css_theme/font-fjalla-one-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-francois-one` | `drupal8_w3css_theme/font-francois-one-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-francois-one` | `drupal8_w3css_theme/font-francois-one-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-fredericka-the-great` | `drupal8_w3css_theme/font-fredericka-the-great-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-fredericka-the-great` | `drupal8_w3css_theme/font-fredericka-the-great-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-fredoka` | `drupal8_w3css_theme/font-fredoka-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-fredoka` | `drupal8_w3css_theme/font-fredoka-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-hammersmith-one` | `drupal8_w3css_theme/font-hammersmith-one-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-hammersmith-one` | `drupal8_w3css_theme/font-hammersmith-one-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-indie-flower` | `drupal8_w3css_theme/font-indie-flower-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-indie-flower` | `drupal8_w3css_theme/font-indie-flower-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-kalam` | `drupal8_w3css_theme/font-kalam-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-kalam` | `drupal8_w3css_theme/font-kalam-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-lobster-two` | `drupal8_w3css_theme/font-lobster-two-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-lobster-two` | `drupal8_w3css_theme/font-lobster-two-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-patrick-hand` | `drupal8_w3css_theme/font-patrick-hand-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-patrick-hand` | `drupal8_w3css_theme/font-patrick-hand-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-prosto-one` | `drupal8_w3css_theme/font-prosto-one-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-prosto-one` | `drupal8_w3css_theme/font-prosto-one-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-righteous` | `drupal8_w3css_theme/font-righteous-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-righteous` | `drupal8_w3css_theme/font-righteous-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-staatliches` | `drupal8_w3css_theme/font-staatliches-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-staatliches` | `drupal8_w3css_theme/font-staatliches-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-yanone-kaffeesatz` | `drupal8_w3css_theme/font-yanone-kaffeesatz-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfin-yanone-kaffeesatz` | `drupal8_w3css_theme/font-yanone-kaffeesatz-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-metal-mania` | `drupal8_w3css_theme/font-metal-mania-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-train-one` | `drupal8_w3css_theme/font-train-one-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-barriecito` | `drupal8_w3css_theme/font-barriecito-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-sancreek` | `drupal8_w3css_theme/font-sancreek-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-new-rocker` | `drupal8_w3css_theme/font-new-rocker-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-nosifer` | `drupal8_w3css_theme/font-nosifer-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-kumar-one` | `drupal8_w3css_theme/font-kumar-one-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-londrina-outline` | `drupal8_w3css_theme/font-londrina-outline-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-trade-winds` | `drupal8_w3css_theme/font-trade-winds-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-slackey` | `drupal8_w3css_theme/font-slackey-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-frijole` | `drupal8_w3css_theme/font-frijole-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-ruslan-display` | `drupal8_w3css_theme/font-ruslan-display-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-faster-one` | `drupal8_w3css_theme/font-faster-one-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-akronim` | `drupal8_w3css_theme/font-akronim-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-finger-paint` | `drupal8_w3css_theme/font-finger-paint-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-ceviche-one` | `drupal8_w3css_theme/font-ceviche-one-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-unifrakturmaguntia` | `drupal8_w3css_theme/font-unifrakturmaguntia-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-megrim` | `drupal8_w3css_theme/font-megrim-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-bungee-shade` | `drupal8_w3css_theme/font-bungee-shade-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-irish-grover` | `drupal8_w3css_theme/font-irish-grover-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-pirata-one` | `drupal8_w3css_theme/font-pirata-one-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-rye` | `drupal8_w3css_theme/font-rye-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-creepster` | `drupal8_w3css_theme/font-creepster-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-moo-lah-lah` | `drupal8_w3css_theme/font-moo-lah-lah-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-gfex-monoton` | `drupal8_w3css_theme/font-monoton-google` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3css-default-theme` | `drupal8_w3css_theme/scheme-pure-monochrome` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-amber` | `drupal8_w3css_theme/scheme-golden-amber` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-blue` | `drupal8_w3css_theme/scheme-royal-blue` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-blue-grey` | `drupal8_w3css_theme/scheme-misty-blue-gray` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-brown` | `drupal8_w3css_theme/scheme-walnut-brown` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-cyan` | `drupal8_w3css_theme/scheme-bright-cyan` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-deep-orange` | `drupal8_w3css_theme/scheme-burnt-orange` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-deep-purple` | `drupal8_w3css_theme/scheme-plum-purple` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-green` | `drupal8_w3css_theme/scheme-meadow-green` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-indigo` | `drupal8_w3css_theme/scheme-ink-indigo` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-khaki` | `drupal8_w3css_theme/scheme-desert-khaki` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-light-blue` | `drupal8_w3css_theme/scheme-sky-blue` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-light-green` | `drupal8_w3css_theme/scheme-spring-green` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-lime` | `drupal8_w3css_theme/scheme-fresh-lime` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-orange` | `drupal8_w3css_theme/scheme-tangerine-orange` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-pink` | `drupal8_w3css_theme/scheme-rose-pink` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-purple` | `drupal8_w3css_theme/scheme-grape-purple` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-red` | `drupal8_w3css_theme/scheme-cherry-red` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-teal` | `drupal8_w3css_theme/scheme-lagoon-teal` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-yellow` | `drupal8_w3css_theme/scheme-sun-yellow` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-black` | `drupal8_w3css_theme/scheme-jet-black` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-dark-grey` | `drupal8_w3css_theme/scheme-charcoal-gray` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-grey` | `drupal8_w3css_theme/scheme-silver-gray` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-custom` | `drupal8_w3css_theme/scheme-custom` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-00000` | `drupal8_w3css_theme/scheme-quiet-slate` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-00001` | `drupal8_w3css_theme/scheme-harbor-steel` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-00002` | `drupal8_w3css_theme/scheme-ocean-blue` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-00003` | `drupal8_w3css_theme/scheme-forest-green` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-00004` | `drupal8_w3css_theme/scheme-orchid-purple` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-00005` | `drupal8_w3css_theme/scheme-copper-brown` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-00006` | `drupal8_w3css_theme/scheme-crimson-red` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-00007` | `drupal8_w3css_theme/scheme-indigo-ink` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-00008` | `drupal8_w3css_theme/scheme-indigo-lagoon` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-00009` | `drupal8_w3css_theme/scheme-deep-cyan` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-00010` | `drupal8_w3css_theme/scheme-lime-grass` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-00011` | `drupal8_w3css_theme/scheme-espresso-brown` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-00012` | `drupal8_w3css_theme/scheme-navy-ruby` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-00013` | `drupal8_w3css_theme/scheme-cadet-blue` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-00014` | `drupal8_w3css_theme/scheme-harbor-blue` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-00015` | `drupal8_w3css_theme/scheme-cadet-steel` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-00016` | `drupal8_w3css_theme/scheme-slate-amber` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-00017` | `drupal8_w3css_theme/scheme-midnight-ember` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-00018` | `drupal8_w3css_theme/scheme-dark-graphite` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-00019` | `drupal8_w3css_theme/scheme-dark-moss` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-00020` | `drupal8_w3css_theme/scheme-dark-ocean` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-00021` | `drupal8_w3css_theme/scheme-dark-violet` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-00022` | `drupal8_w3css_theme/scheme-dark-ruby` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-00023` | `drupal8_w3css_theme/scheme-dark-rose` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-00024` | `drupal8_w3css_theme/scheme-dark-emerald` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-00025` | `drupal8_w3css_theme/scheme-dark-lavender` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-00026` | `drupal8_w3css_theme/scheme-dark-gold` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-00027` | `drupal8_w3css_theme/scheme-dark-sky` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-00028` | `drupal8_w3css_theme/scheme-teal-coral` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/w3-css-theme-00029` | `drupal8_w3css_theme/scheme-photo-backdrop` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | existed in 2.0.18 (git show 2.0.18:drupal8_w3css_theme.libraries.yml). Attached by name built in PHP (src/Hook/AttachmentHooks.php:45-67 scheme and font ids), so a rename must change that code in the same commit. |
| `drupal8_w3css_theme/d8w3css-settings-form` | `drupal8_w3css_theme/admin-settings-form` | rename | Rewritten with --yml | new in 3.0.x-dev. |
| `drupal8_w3css_theme/w3-css-theme-shared-keyframes` | `drupal8_w3css_theme/scheme-keyframes` | rename | Rewritten with --yml | new in 3.0.x-dev. |
| `drupal8_w3css_theme/w3-css-theme-shared-palette` | `drupal8_w3css_theme/scheme-palette` | rename | Rewritten with --yml | new in 3.0.x-dev. |
| `drupal8_w3css_theme/w3-css-theme-shared-custom` | `drupal8_w3css_theme/scheme-custom` | rename | Rewritten with --yml | new in 3.0.x-dev. |
| `drupal8_w3css_theme/w3-css-theme-shared-custom-b` | `drupal8_w3css_theme/scheme-custom-b` | rename | Rewritten with --yml | new in 3.0.x-dev. |
| `drupal8_w3css_theme/w3-css-theme-shared-custom-paint` | `drupal8_w3css_theme/scheme-custom-paint` | rename | Rewritten with --yml | new in 3.0.x-dev. |
| `drupal8_w3css_theme/font-awesome-4-in` | `drupal8_w3css_theme/font-awesome-7-local` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | Font Awesome 4, 5 and 6 were removed in 3.0; the old id is a deprecated alias of Font Awesome 7 |
| `drupal8_w3css_theme/font-awesome-4-ex` | `drupal8_w3css_theme/font-awesome-7-cdn` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | Font Awesome 4, 5 and 6 were removed in 3.0; the old id is a deprecated alias of Font Awesome 7 |
| `drupal8_w3css_theme/font-awesome-5-ex` | `drupal8_w3css_theme/font-awesome-7-cdn` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | Font Awesome 4, 5 and 6 were removed in 3.0; the old id is a deprecated alias of Font Awesome 7 |
| `drupal8_w3css_theme/font-awesome-6-ex` | `drupal8_w3css_theme/font-awesome-7-cdn` | rename | Rewritten with --yml; the old id keeps working as a deprecated alias | Font Awesome 4, 5 and 6 were removed in 3.0; the old id is a deprecated alias of Font Awesome 7 |

### CSS files (359)

| Old | New | Action | What the script does | Note |
|---|---|---|---|---|
| `css/base/base.css` | `css/base/w3css-base.css` | rename | Reported, change by hand | base layer: element rules |
| `css/components/breadcrumb.css` | `css/components/w3css-breadcrumb.css` | rename | Reported, change by hand | navigation/breadcrumb.html.twig |
| `css/components/ckeditor.css` | `css/components/w3css-ckeditor.css` | rename | Reported, change by hand | no template (rich text on the front end); component name |
| `css/components/comments.css` | `css/components/w3css-comment.css` | rename | Reported, change by hand | content/comment.html.twig |
| `css/components/contact-form.css` | `css/components/w3css-contact-form.css` | rename | Reported, change by hand | 8 lines, no template; needs checking whether it can merge into w3css-form-element.css |
| `css/components/default-node.css` | `css/components/w3css-node.css` | merge | Reported, change by hand | content/node.html.twig; merge teaser-mode.css (26 lines, node--view-mode-teaser) into it |
| `css/components/default-views.css` | `css/components/w3css-views-view.css` | rename | Reported, change by hand | views/views-view.html.twig; needs checking whether to SPLIT per views-view-* template (table, grid, list, summary) |
| `css/components/embedded-media.css` | `css/components/w3css-media.css` | rename | Reported, change by hand | content/media.html.twig |
| `css/components/footer-menu.css` | `css/components/w3css-footer-menu.css` | rename | Reported, change by hand | footer menu region of page.html.twig |
| `css/components/forms.css` | `css/components/w3css-form-element.css` | split | Reported, change by hand | 97 lines across form/*.html.twig (form-element, fieldset, details, checkboxes, radios, input, select, textarea): needs checking which selectors belong to which template; proposed pieces w3css-form-element, w3css-fieldset, w3css-details, w3css-input |
| `css/components/global-menu.css` | `css/components/w3css-menu.css` | rename | Reported, change by hand | navigation/menu.html.twig and menu--responsive-h (shared w3css-menu__*) |
| `css/components/layout-builder.css` | `css/components/w3css-layout-builder.css` | split | Reported, change by hand | no template; module-override rules move to the unlayered css/components/w3css-module-overrides.css (plan themes-layers-plan-w3css section 2) |
| `css/components/maintenance-page.css` | `css/components/w3css-maintenance-page.css` | rename | Reported, change by hand | layout/maintenance-page.html.twig |
| `css/components/node-form.css` | `css/layout/w3css-node-edit-form.css` | merge | Reported, change by hand | see layout-node-edit.css |
| `css/components/page-title.css` | `css/components/w3css-page-title.css` | rename | Reported, change by hand | content/page-title.html.twig |
| `css/components/pager.css` | `css/components/w3css-pager.css` | rename | Reported, change by hand | navigation/pager.html.twig and views-mini-pager |
| `css/components/search-block.css` | `css/components/w3css-search-block-form.css` | rename | Reported, change by hand | form/form--search-block-form.html.twig |
| `css/components/search-page.css` | `css/components/w3css-search-result.css` | rename | Reported, change by hand | content/search-result.html.twig; needs checking (selectors may target the search page form) |
| `css/components/site-branding.css` | `css/components/w3css-site-branding.css` | rename | Reported, change by hand | block/block--system-branding-block.html.twig |
| `css/components/sitemap.css` | `css/components/w3css-menu-sitemap.css` | rename | Reported, change by hand | navigation/menu--sitemap.html.twig; 3 lines |
| `css/components/system-admin.css` | `css/components/w3css-system-admin.css` | split | Reported, change by hand | 271 lines; the status report part belongs to admin/status-report-*.html.twig: proposed w3css-status-report.css (needs checking) |
| `css/components/system-messages.css` | `css/components/w3css-status-messages.css` | rename | Reported, change by hand | misc/status-messages.html.twig |
| `css/components/system-tabs.css` | `css/components/w3css-local-tasks.css` | rename | Reported, change by hand | navigation/menu-local-tasks.html.twig |
| `css/components/teaser-mode.css` | `css/components/w3css-node.css` | merge | Reported, change by hand | teaser is a node view mode |
| `css/components/user-login.css` | `css/layout/w3css-user-login.css` | merge | Reported, change by hand | 12 lines; merge with layout/layout-user-login.css, same page |
| `css/components/user-menu.css` | `css/components/w3css-menu.css` | merge | Reported, change by hand | 26 lines for the account menu, a menu.html.twig variant (w3css-menu__list--menu-account) |
| `css/components/v-menu.css` | `css/components/w3css-menu-responsive-v.css` | rename | Reported, change by hand | navigation/menu--responsive-v.html.twig |
| `css/components/w3css-tables.css` | `css/components/w3css-table.css` | rename | Reported, change by hand | dataset/table.html.twig |
| `css/components/webform.css` | `css/components/w3css-webform.css` | rename | Reported, change by hand | contrib module styling, no template; its module-override rules go to w3css-module-overrides.css |
| `css/layout/layout-node-edit.css` | `css/layout/w3css-node-edit-form.css` | merge | Reported, change by hand | merge with components/node-form.css (297 lines): both style templates/admin/node-edit-form.html.twig; 534 lines together, owner may prefer to keep two |
| `css/layout/layout-regions.css` | `css/layout/w3css-page.css` | rename | Reported, change by hand | regions are printed by page.html.twig |
| `css/layout/layout-user-login.css` | `css/layout/w3css-user-login.css` | merge | Reported, change by hand | merge with components/user-login.css (12 lines): one page, one file |
| `css/layout/layout-width.css` | `css/layout/w3css-page-width.css` | rename | Reported, change by hand | width presets of the page; no template of its own |
| `css/theme/a-image.css` | `css/components/w3css-image.css` | rename | Reported, change by hand | field/image.html.twig and the w3css-image-link class |
| `css/theme/border-animation.css` | `css/utilities/w3css-animate-border.css` | rename | Reported, change by hand | single-purpose class w3css-animate-border |
| `css/theme/d8w3css-settings-form.css` | `css/admin/w3css-settings-form.css` | rename | Reported, change by hand | admin: theme settings form |
| `css/theme/d8w3css-settings-schemes.css` | `css/admin/w3css-settings-schemes.css` | rename | Reported, change by hand | admin: scheme picker |
| `css/theme/d8w3css-settings-search.css` | `css/admin/w3css-settings-search.css` | rename | Reported, change by hand | admin |
| `css/theme/d8w3css-settings-swatches.css` | `css/admin/w3css-settings-swatches.css` | rename | Reported, change by hand | admin |
| `css/theme/default-color-theme.css` | `css/predefined/w3css-pure-monochrome.css` | rename | Reported, change by hand | the scheme labeled Pure Monochrome (option value none) |
| `css/theme/max-min-width-992-601.css` | (split into component files) | split | Reported, change by hand | as above |
| `css/theme/max-width-600.css` | (split into component files) | split | Reported, change by hand | as above |
| `css/theme/max-width-767.css` | (split into component files) | split | Reported, change by hand | as above |
| `css/theme/max-width-992.css` | (split into component files) | split | Reported, change by hand | as above |
| `css/theme/min-width-993.css` | (split into component files) | split | Reported, change by hand | breakpoint file: its rules belong to the components they style, each inside an @media block; owner decision O3 |
| `css/theme/sitename-animation.css` | `css/components/w3css-site-branding-animation.css` | rename | Reported, change by hand | site name animation of block--system-branding-block (323 lines, 241 gradient colors become tokens) |
| `css/theme/social-media-icon-size-l.css` | `css/components/w3css-social-media-size-l.css` | rename | Reported, change by hand | social media size variant |
| `css/theme/social-media-icon-size-m.css` | `css/components/w3css-social-media-size-m.css` | rename | Reported, change by hand | social media size variant |
| `css/theme/w3-ckeditor-iframe.css` | `css/admin/w3css-ckeditor-iframe.css` | rename | Reported, change by hand | named in info.yml ckeditor_stylesheets |
| `css/theme/w3-ckeditor5.css` | `css/admin/w3css-ckeditor5.css` | rename | Reported, change by hand | named in info.yml ckeditor5-stylesheets |
| `css/theme/w3-css-theme-shared-custom-b.css` | `css/predefined/w3css-scheme-custom.css` | merge | Reported, change by hand | see -custom.css |
| `css/theme/w3-css-theme-shared-custom-paint.css` | `css/predefined/w3css-scheme-custom-paint.css` | rename | Reported, change by hand | kept as an empty file and library; each scheme file now carries its own paint rules (scripts/build-scheme-paint.py) |
| `css/theme/w3-css-theme-shared-custom.css` | `css/predefined/w3css-scheme-custom.css` | merge | Reported, change by hand | merge with -custom-b.css (16 lines); the "-b" suffix says nothing |
| `css/theme/w3-css-theme-shared-keyframes.css` | `css/predefined/w3css-scheme-keyframes.css` | rename | Reported, change by hand | keyframes shared by all schemes |
| `css/theme/w3-css-theme-shared-palette.css` | `css/predefined/w3css-scheme-palette.css` | rename | Reported, change by hand | rules shared by the 22 W3 schemes |
| `css/w3-css/w3css-color-libraries-camouflage.css` | `css/utilities/w3css-color-camouflage.css` | rename | Reported, change by hand | colour library; "libraries" in the name collides with Drupal libraries |
| `css/w3-css/w3css-color-libraries-european-signal.css` | `css/utilities/w3css-color-european-signal.css` | rename | Reported, change by hand | colour library; "libraries" in the name collides with Drupal libraries |
| `css/w3-css/w3css-color-libraries-fashion.css` | `css/utilities/w3css-color-fashion.css` | rename | Reported, change by hand | colour library; "libraries" in the name collides with Drupal libraries |
| `css/w3-css/w3css-color-libraries-flat-ui.css` | `css/utilities/w3css-color-flat-ui.css` | rename | Reported, change by hand | colour library; "libraries" in the name collides with Drupal libraries |
| `css/w3-css/w3css-color-libraries-food.css` | `css/utilities/w3css-color-food.css` | rename | Reported, change by hand | colour library; "libraries" in the name collides with Drupal libraries |
| `css/w3-css/w3css-color-libraries-ios.css` | `css/utilities/w3css-color-ios.css` | rename | Reported, change by hand | colour library; "libraries" in the name collides with Drupal libraries |
| `css/w3-css/w3css-color-libraries-metro-ui.css` | `css/utilities/w3css-color-metro-ui.css` | rename | Reported, change by hand | colour library; "libraries" in the name collides with Drupal libraries |
| `css/w3-css/w3css-color-libraries-us-highway.css` | `css/utilities/w3css-color-us-highway.css` | rename | Reported, change by hand | colour library; "libraries" in the name collides with Drupal libraries |
| `css/w3-css/w3css-color-libraries-us-safety.css` | `css/utilities/w3css-color-us-safety.css` | rename | Reported, change by hand | colour library; "libraries" in the name collides with Drupal libraries |
| `css/w3-css/w3css-color-libraries-vivid.css` | `css/utilities/w3css-color-vivid.css` | rename | Reported, change by hand | colour library; "libraries" in the name collides with Drupal libraries |
| `css/w3-css/w3css-color-libraries-win8.css` | `css/utilities/w3css-color-win8.css` | rename | Reported, change by hand | colour library; "libraries" in the name collides with Drupal libraries |
| `css/w3-css/w3css-drupal.css` | `css/base/w3css-drupal.css` | rename | Reported, change by hand | our upgraded library; layers inside (reset, tokens, base, layout, library, utilities) |
| `css/w3-css/w3css-extended-classes.css` | `css/utilities/w3css-extended-classes.css` | rename | Reported, change by hand | single-purpose classes |
| `css/w3-css/w3css-layers.css` | `css/base/w3css-layers.css` | rename | Reported, change by hand | ladder statement, moves to base; library base-w3css-layers at weight -1000 |
| `fonts/font-awesome-7.0.1.css` | `fonts/w3css-font-awesome-7.css` | rename | Reported, change by hand | vendored; version stays in the library `version:` key not in the file name |
| `fonts/google-font-<font>.css` | `fonts/w3css-font-<font>-local.css` | rename | Reported, change by hand | local copy of each Google font (76 files); JSON rows repeat per font |
| `fonts/css/d8w3css-<font>.css` | `fonts/w3css-font-<font>-google.css` | rename | Reported, change by hand | class rule beside the external Google import, global and heading fonts; these set the frozen class names such as .lato |
| `fonts/css/special/d8w3css-<font>.css` | `fonts/w3css-font-<font>-google.css` | rename | Reported, change by hand | special fonts, always external |
| `drupal8_w3css_subtheme/css/d8w3css-subtheme-style.css` | `drupal8_w3css_subtheme/css/w3css-subtheme-style.css` | rename | Reported, change by hand | starter kit; template comment should show @layer w3css.custom |
| `fonts/font-awesome.4.7.0.css` | `fonts/w3css-font-awesome-7.css` | rename | Reported, change by hand | 2.0.18 library font-awesome-4-in is now font-awesome-7-local |
| `fonts/css/d8w3css-alegreya-sans.css` | `fonts/w3css-font-alegreya-sans-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-alegreya-sans is now font-alegreya-sans-google |
| `fonts/google-font-alegreya-sans.css` | `fonts/w3css-font-alegreya-sans-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-alegreya-sans is now font-alegreya-sans-local |
| `fonts/css/d8w3css-alegreya-sans-sc.css` | `fonts/w3css-font-alegreya-sans-sc-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-alegreya-sans-sc is now font-alegreya-sans-sc-google |
| `fonts/google-font-alegreya-sans-sc.css` | `fonts/w3css-font-alegreya-sans-sc-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-alegreya-sans-sc is now font-alegreya-sans-sc-local |
| `fonts/css/d8w3css-lato.css` | `fonts/w3css-font-lato-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-lato is now font-lato-google |
| `fonts/google-font-lato.css` | `fonts/w3css-font-lato-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-lato is now font-lato-local |
| `fonts/css/d8w3css-montserrat.css` | `fonts/w3css-font-montserrat-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-montserrat is now font-montserrat-google |
| `fonts/google-font-montserrat.css` | `fonts/w3css-font-montserrat-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-montserrat is now font-montserrat-local |
| `fonts/css/d8w3css-nunito.css` | `fonts/w3css-font-nunito-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-nunito is now font-nunito-google |
| `fonts/google-font-nunito.css` | `fonts/w3css-font-nunito-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-nunito is now font-nunito-local |
| `fonts/css/d8w3css-kavivanar.css` | `fonts/w3css-font-kavivanar-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-kavivanar is now font-kavivanar-google |
| `fonts/google-font-kavivanar.css` | `fonts/w3css-font-kavivanar-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-kavivanar is now font-kavivanar-local |
| `fonts/css/d8w3css-open-sans.css` | `fonts/w3css-font-open-sans-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-open-sans is now font-open-sans-google |
| `fonts/google-font-open-sans.css` | `fonts/w3css-font-open-sans-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-open-sans is now font-open-sans-local |
| `fonts/css/d8w3css-oswald.css` | `fonts/w3css-font-oswald-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-oswald is now font-oswald-google |
| `fonts/google-font-oswald.css` | `fonts/w3css-font-oswald-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-oswald is now font-oswald-local |
| `fonts/css/d8w3css-playfair-display.css` | `fonts/w3css-font-playfair-display-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-playfair-display is now font-playfair-display-google |
| `fonts/google-font-playfair-display.css` | `fonts/w3css-font-playfair-display-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-playfair-display is now font-playfair-display-local |
| `fonts/css/d8w3css-roboto.css` | `fonts/w3css-font-roboto-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-roboto is now font-roboto-google |
| `fonts/google-font-roboto.css` | `fonts/w3css-font-roboto-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-roboto is now font-roboto-local |
| `fonts/css/d8w3css-roboto-condensed.css` | `fonts/w3css-font-roboto-condensed-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-roboto-condensed is now font-roboto-condensed-google |
| `fonts/google-font-roboto-condensed.css` | `fonts/w3css-font-roboto-condensed-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-roboto-condensed is now font-roboto-condensed-local |
| `fonts/css/d8w3css-roboto-mono.css` | `fonts/w3css-font-roboto-mono-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-roboto-mono is now font-roboto-mono-google |
| `fonts/google-font-roboto-mono.css` | `fonts/w3css-font-roboto-mono-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-roboto-mono is now font-roboto-mono-local |
| `fonts/css/d8w3css-source-sans-3.css` | `fonts/w3css-font-source-sans-3-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-source-sans-3 is now font-source-sans-3-google |
| `fonts/google-font-source-sans-3.css` | `fonts/w3css-font-source-sans-3-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-source-sans-3 is now font-source-sans-3-local |
| `fonts/css/d8w3css-domine.css` | `fonts/w3css-font-domine-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-domine is now font-domine-google |
| `fonts/google-font-domine.css` | `fonts/w3css-font-domine-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-domine is now font-domine-local |
| `fonts/css/d8w3css-glegoo.css` | `fonts/w3css-font-glegoo-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-glegoo is now font-glegoo-google |
| `fonts/google-font-glegoo.css` | `fonts/w3css-font-glegoo-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-glegoo is now font-glegoo-local |
| `fonts/css/d8w3css-amita.css` | `fonts/w3css-font-amita-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-amita is now font-amita-google |
| `fonts/google-font-amita.css` | `fonts/w3css-font-amita-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-amita is now font-amita-local |
| `fonts/css/d8w3css-oranienbaum.css` | `fonts/w3css-font-oranienbaum-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-oranienbaum is now font-oranienbaum-google |
| `fonts/google-font-oranienbaum.css` | `fonts/w3css-font-oranienbaum-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-oranienbaum is now font-oranienbaum-local |
| `fonts/css/d8w3css-abel.css` | `fonts/w3css-font-abel-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-abel is now font-abel-google |
| `fonts/google-font-abel.css` | `fonts/w3css-font-abel-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-abel is now font-abel-local |
| `fonts/css/d8w3css-archivo-narrow.css` | `fonts/w3css-font-archivo-narrow-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-archivo-narrow is now font-archivo-narrow-google |
| `fonts/google-font-archivo-narrow.css` | `fonts/w3css-font-archivo-narrow-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-archivo-narrow is now font-archivo-narrow-local |
| `fonts/css/d8w3css-arimo.css` | `fonts/w3css-font-arimo-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-arimo is now font-arimo-google |
| `fonts/google-font-arimo.css` | `fonts/w3css-font-arimo-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-arimo is now font-arimo-local |
| `fonts/css/d8w3css-arvo.css` | `fonts/w3css-font-arvo-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-arvo is now font-arvo-google |
| `fonts/google-font-arvo.css` | `fonts/w3css-font-arvo-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-arvo is now font-arvo-local |
| `fonts/css/d8w3css-barlow.css` | `fonts/w3css-font-barlow-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-barlow is now font-barlow-google |
| `fonts/google-font-barlow.css` | `fonts/w3css-font-barlow-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-barlow is now font-barlow-local |
| `fonts/css/d8w3css-barlow-semi-condensed.css` | `fonts/w3css-font-barlow-semi-condensed-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-barlow-semi-condensed is now font-barlow-semi-condensed-google |
| `fonts/google-font-barlow-semi-condensed.css` | `fonts/w3css-font-barlow-semi-condensed-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-barlow-semi-condensed is now font-barlow-semi-condensed-local |
| `fonts/css/d8w3css-bitter.css` | `fonts/w3css-font-bitter-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-bitter is now font-bitter-google |
| `fonts/google-font-bitter.css` | `fonts/w3css-font-bitter-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-bitter is now font-bitter-local |
| `fonts/css/d8w3css-cairo.css` | `fonts/w3css-font-cairo-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-cairo is now font-cairo-google |
| `fonts/google-font-cairo.css` | `fonts/w3css-font-cairo-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-cairo is now font-cairo-local |
| `fonts/css/d8w3css-comfortaa.css` | `fonts/w3css-font-comfortaa-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-comfortaa is now font-comfortaa-google |
| `fonts/google-font-comfortaa.css` | `fonts/w3css-font-comfortaa-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-comfortaa is now font-comfortaa-local |
| `fonts/css/d8w3css-courgette.css` | `fonts/w3css-font-courgette-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-courgette is now font-courgette-google |
| `fonts/google-font-courgette.css` | `fonts/w3css-font-courgette-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-courgette is now font-courgette-local |
| `fonts/css/d8w3css-dosis.css` | `fonts/w3css-font-dosis-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-dosis is now font-dosis-google |
| `fonts/google-font-dosis.css` | `fonts/w3css-font-dosis-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-dosis is now font-dosis-local |
| `fonts/css/d8w3css-exo-2.css` | `fonts/w3css-font-exo-2-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-exo-2 is now font-exo-2-google |
| `fonts/google-font-exo-2.css` | `fonts/w3css-font-exo-2-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-exo-2 is now font-exo-2-local |
| `fonts/css/d8w3css-fira-sans.css` | `fonts/w3css-font-fira-sans-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-fira-sans is now font-fira-sans-google |
| `fonts/google-font-fira-sans.css` | `fonts/w3css-font-fira-sans-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-fira-sans is now font-fira-sans-local |
| `fonts/css/d8w3css-hind-siliguri.css` | `fonts/w3css-font-hind-siliguri-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-hind-siliguri is now font-hind-siliguri-google |
| `fonts/google-font-hind-siliguri.css` | `fonts/w3css-font-hind-siliguri-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-hind-siliguri is now font-hind-siliguri-local |
| `fonts/css/d8w3css-ibm-plex-sans.css` | `fonts/w3css-font-ibm-plex-sans-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-ibm-plex-sans is now font-ibm-plex-sans-google |
| `fonts/google-font-ibm-plex-sans.css` | `fonts/w3css-font-ibm-plex-sans-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-ibm-plex-sans is now font-ibm-plex-sans-local |
| `fonts/css/d8w3css-inconsolata.css` | `fonts/w3css-font-inconsolata-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-inconsolata is now font-inconsolata-google |
| `fonts/google-font-inconsolata.css` | `fonts/w3css-font-inconsolata-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-inconsolata is now font-inconsolata-local |
| `fonts/css/d8w3css-josefin-sans.css` | `fonts/w3css-font-josefin-sans-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-josefin-sans is now font-josefin-sans-google |
| `fonts/google-font-josefin-sans.css` | `fonts/w3css-font-josefin-sans-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-josefin-sans is now font-josefin-sans-local |
| `fonts/css/d8w3css-kanit.css` | `fonts/w3css-font-kanit-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-kanit is now font-kanit-google |
| `fonts/google-font-kanit.css` | `fonts/w3css-font-kanit-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-kanit is now font-kanit-local |
| `fonts/css/d8w3css-libre-baskerville.css` | `fonts/w3css-font-libre-baskerville-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-libre-baskerville is now font-libre-baskerville-google |
| `fonts/google-font-libre-baskerville.css` | `fonts/w3css-font-libre-baskerville-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-libre-baskerville is now font-libre-baskerville-local |
| `fonts/css/d8w3css-lora.css` | `fonts/w3css-font-lora-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-lora is now font-lora-google |
| `fonts/google-font-lora.css` | `fonts/w3css-font-lora-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-lora is now font-lora-local |
| `fonts/css/d8w3css-merienda.css` | `fonts/w3css-font-merienda-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-merienda is now font-merienda-google |
| `fonts/google-font-merienda.css` | `fonts/w3css-font-merienda-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-merienda is now font-merienda-local |
| `fonts/css/d8w3css-merriweather.css` | `fonts/w3css-font-merriweather-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-merriweather is now font-merriweather-google |
| `fonts/google-font-merriweather.css` | `fonts/w3css-font-merriweather-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-merriweather is now font-merriweather-local |
| `fonts/css/d8w3css-mukta.css` | `fonts/w3css-font-mukta-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-mukta is now font-mukta-google |
| `fonts/google-font-mukta.css` | `fonts/w3css-font-mukta-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-mukta is now font-mukta-local |
| `fonts/css/d8w3css-oxygen.css` | `fonts/w3css-font-oxygen-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-oxygen is now font-oxygen-google |
| `fonts/google-font-oxygen.css` | `fonts/w3css-font-oxygen-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-oxygen is now font-oxygen-local |
| `fonts/css/d8w3css-poppins.css` | `fonts/w3css-font-poppins-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-poppins is now font-poppins-google |
| `fonts/google-font-poppins.css` | `fonts/w3css-font-poppins-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-poppins is now font-poppins-local |
| `fonts/css/d8w3css-prompt.css` | `fonts/w3css-font-prompt-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-prompt is now font-prompt-google |
| `fonts/google-font-prompt.css` | `fonts/w3css-font-prompt-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-prompt is now font-prompt-local |
| `fonts/css/d8w3css-pt-serif.css` | `fonts/w3css-font-pt-serif-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-pt-serif is now font-pt-serif-google |
| `fonts/google-font-pt-serif.css` | `fonts/w3css-font-pt-serif-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-pt-serif is now font-pt-serif-local |
| `fonts/css/d8w3css-quicksand.css` | `fonts/w3css-font-quicksand-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-quicksand is now font-quicksand-google |
| `fonts/google-font-quicksand.css` | `fonts/w3css-font-quicksand-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-quicksand is now font-quicksand-local |
| `fonts/css/d8w3css-raleway.css` | `fonts/w3css-font-raleway-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-raleway is now font-raleway-google |
| `fonts/google-font-raleway.css` | `fonts/w3css-font-raleway-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-raleway is now font-raleway-local |
| `fonts/css/d8w3css-spline-sans.css` | `fonts/w3css-font-spline-sans-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-spline-sans is now font-spline-sans-google |
| `fonts/google-font-spline-sans.css` | `fonts/w3css-font-spline-sans-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-spline-sans is now font-spline-sans-local |
| `fonts/css/d8w3css-titillium-web.css` | `fonts/w3css-font-titillium-web-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-titillium-web is now font-titillium-web-google |
| `fonts/google-font-titillium-web.css` | `fonts/w3css-font-titillium-web-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-titillium-web is now font-titillium-web-local |
| `fonts/css/d8w3css-ubuntu.css` | `fonts/w3css-font-ubuntu-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-ubuntu is now font-ubuntu-google |
| `fonts/google-font-ubuntu.css` | `fonts/w3css-font-ubuntu-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-ubuntu is now font-ubuntu-local |
| `fonts/css/d8w3css-work-sans.css` | `fonts/w3css-font-work-sans-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-work-sans is now font-work-sans-google |
| `fonts/google-font-work-sans.css` | `fonts/w3css-font-work-sans-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-work-sans is now font-work-sans-local |
| `fonts/css/d8w3css-abril-fatface.css` | `fonts/w3css-font-abril-fatface-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-abril-fatface is now font-abril-fatface-google |
| `fonts/google-font-abril-fatface.css` | `fonts/w3css-font-abril-fatface-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-abril-fatface is now font-abril-fatface-local |
| `fonts/css/d8w3css-aladin.css` | `fonts/w3css-font-aladin-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-aladin is now font-aladin-google |
| `fonts/google-font-aladin.css` | `fonts/w3css-font-aladin-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-aladin is now font-aladin-local |
| `fonts/css/d8w3css-anton.css` | `fonts/w3css-font-anton-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-anton is now font-anton-google |
| `fonts/google-font-anton.css` | `fonts/w3css-font-anton-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-anton is now font-anton-local |
| `fonts/css/d8w3css-archivo-black.css` | `fonts/w3css-font-archivo-black-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-archivo-black is now font-archivo-black-google |
| `fonts/google-font-archivo-black.css` | `fonts/w3css-font-archivo-black-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-archivo-black is now font-archivo-black-local |
| `fonts/css/d8w3css-bebas-neue.css` | `fonts/w3css-font-bebas-neue-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-bebas-neue is now font-bebas-neue-google |
| `fonts/google-font-bebas-neue.css` | `fonts/w3css-font-bebas-neue-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-bebas-neue is now font-bebas-neue-local |
| `fonts/css/d8w3css-big-shoulders-stencil-display.css` | `fonts/w3css-font-big-shoulders-stencil-display-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-big-shoulders-stencil-display is now font-big-shoulders-stencil-display-google |
| `fonts/google-font-big-shoulders-stencil-display.css` | `fonts/w3css-font-big-shoulders-stencil-display-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-big-shoulders-stencil-display is now font-big-shoulders-stencil-display-local |
| `fonts/css/d8w3css-big-shoulders-stencil-text.css` | `fonts/w3css-font-big-shoulders-stencil-text-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-big-shoulders-stencil-text is now font-big-shoulders-stencil-text-google |
| `fonts/google-font-big-shoulders-stencil-text.css` | `fonts/w3css-font-big-shoulders-stencil-text-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-big-shoulders-stencil-text is now font-big-shoulders-stencil-text-local |
| `fonts/css/d8w3css-black-ops-one.css` | `fonts/w3css-font-black-ops-one-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-black-ops-one is now font-black-ops-one-google |
| `fonts/google-font-black-ops-one.css` | `fonts/w3css-font-black-ops-one-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-black-ops-one is now font-black-ops-one-local |
| `fonts/css/d8w3css-bree-serif.css` | `fonts/w3css-font-bree-serif-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-bree-serif is now font-bree-serif-google |
| `fonts/google-font-bree-serif.css` | `fonts/w3css-font-bree-serif-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-bree-serif is now font-bree-serif-local |
| `fonts/css/d8w3css-cinzel.css` | `fonts/w3css-font-cinzel-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-cinzel is now font-cinzel-google |
| `fonts/google-font-cinzel.css` | `fonts/w3css-font-cinzel-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-cinzel is now font-cinzel-local |
| `fonts/css/d8w3css-dancing-script.css` | `fonts/w3css-font-dancing-script-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-dancing-script is now font-dancing-script-google |
| `fonts/google-font-dancing-script.css` | `fonts/w3css-font-dancing-script-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-dancing-script is now font-dancing-script-local |
| `fonts/css/d8w3css-dm-serif-display.css` | `fonts/w3css-font-dm-serif-display-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-dm-serif-display is now font-dm-serif-display-google |
| `fonts/google-font-dm-serif-display.css` | `fonts/w3css-font-dm-serif-display-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-dm-serif-display is now font-dm-serif-display-local |
| `fonts/css/d8w3css-doppio-one.css` | `fonts/w3css-font-doppio-one-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-doppio-one is now font-doppio-one-google |
| `fonts/google-font-doppio-one.css` | `fonts/w3css-font-doppio-one-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-doppio-one is now font-doppio-one-local |
| `fonts/css/d8w3css-fjalla-one.css` | `fonts/w3css-font-fjalla-one-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-fjalla-one is now font-fjalla-one-google |
| `fonts/google-font-fjalla-one.css` | `fonts/w3css-font-fjalla-one-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-fjalla-one is now font-fjalla-one-local |
| `fonts/css/d8w3css-francois-one.css` | `fonts/w3css-font-francois-one-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-francois-one is now font-francois-one-google |
| `fonts/google-font-francois-one.css` | `fonts/w3css-font-francois-one-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-francois-one is now font-francois-one-local |
| `fonts/css/d8w3css-fredericka-the-great.css` | `fonts/w3css-font-fredericka-the-great-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-fredericka-the-great is now font-fredericka-the-great-google |
| `fonts/google-font-fredericka-the-great.css` | `fonts/w3css-font-fredericka-the-great-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-fredericka-the-great is now font-fredericka-the-great-local |
| `fonts/css/d8w3css-fredoka.css` | `fonts/w3css-font-fredoka-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-fredoka is now font-fredoka-google |
| `fonts/google-font-fredoka.css` | `fonts/w3css-font-fredoka-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-fredoka is now font-fredoka-local |
| `fonts/css/d8w3css-hammersmith-one.css` | `fonts/w3css-font-hammersmith-one-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-hammersmith-one is now font-hammersmith-one-google |
| `fonts/google-font-hammersmith-one.css` | `fonts/w3css-font-hammersmith-one-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-hammersmith-one is now font-hammersmith-one-local |
| `fonts/css/d8w3css-indie-flower.css` | `fonts/w3css-font-indie-flower-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-indie-flower is now font-indie-flower-google |
| `fonts/google-font-indie-flower.css` | `fonts/w3css-font-indie-flower-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-indie-flower is now font-indie-flower-local |
| `fonts/css/d8w3css-kalam.css` | `fonts/w3css-font-kalam-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-kalam is now font-kalam-google |
| `fonts/google-font-kalam.css` | `fonts/w3css-font-kalam-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-kalam is now font-kalam-local |
| `fonts/css/d8w3css-lobster-two.css` | `fonts/w3css-font-lobster-two-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-lobster-two is now font-lobster-two-google |
| `fonts/google-font-lobster-two.css` | `fonts/w3css-font-lobster-two-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-lobster-two is now font-lobster-two-local |
| `fonts/css/d8w3css-patrick-hand.css` | `fonts/w3css-font-patrick-hand-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-patrick-hand is now font-patrick-hand-google |
| `fonts/google-font-patrick-hand.css` | `fonts/w3css-font-patrick-hand-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-patrick-hand is now font-patrick-hand-local |
| `fonts/css/d8w3css-prosto-one.css` | `fonts/w3css-font-prosto-one-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-prosto-one is now font-prosto-one-google |
| `fonts/google-font-prosto-one.css` | `fonts/w3css-font-prosto-one-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-prosto-one is now font-prosto-one-local |
| `fonts/css/d8w3css-righteous.css` | `fonts/w3css-font-righteous-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-righteous is now font-righteous-google |
| `fonts/google-font-righteous.css` | `fonts/w3css-font-righteous-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-righteous is now font-righteous-local |
| `fonts/css/d8w3css-staatliches.css` | `fonts/w3css-font-staatliches-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-staatliches is now font-staatliches-google |
| `fonts/google-font-staatliches.css` | `fonts/w3css-font-staatliches-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-staatliches is now font-staatliches-local |
| `fonts/css/d8w3css-yanone-kaffeesatz.css` | `fonts/w3css-font-yanone-kaffeesatz-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-yanone-kaffeesatz is now font-yanone-kaffeesatz-google |
| `fonts/google-font-yanone-kaffeesatz.css` | `fonts/w3css-font-yanone-kaffeesatz-local.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfin-yanone-kaffeesatz is now font-yanone-kaffeesatz-local |
| `fonts/css/special/d8w3css-metal-mania.css` | `fonts/w3css-font-metal-mania-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-metal-mania is now font-metal-mania-google |
| `fonts/css/special/d8w3css-train-one.css` | `fonts/w3css-font-train-one-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-train-one is now font-train-one-google |
| `fonts/css/special/d8w3css-barriecito.css` | `fonts/w3css-font-barriecito-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-barriecito is now font-barriecito-google |
| `fonts/css/special/d8w3css-sancreek.css` | `fonts/w3css-font-sancreek-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-sancreek is now font-sancreek-google |
| `fonts/css/special/d8w3css-new-rocker.css` | `fonts/w3css-font-new-rocker-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-new-rocker is now font-new-rocker-google |
| `fonts/css/special/d8w3css-nosifer.css` | `fonts/w3css-font-nosifer-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-nosifer is now font-nosifer-google |
| `fonts/css/special/d8w3css-kumar-one.css` | `fonts/w3css-font-kumar-one-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-kumar-one is now font-kumar-one-google |
| `fonts/css/special/d8w3css-londrina-outline.css` | `fonts/w3css-font-londrina-outline-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-londrina-outline is now font-londrina-outline-google |
| `fonts/css/special/d8w3css-trade-winds.css` | `fonts/w3css-font-trade-winds-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-trade-winds is now font-trade-winds-google |
| `fonts/css/special/d8w3css-slackey.css` | `fonts/w3css-font-slackey-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-slackey is now font-slackey-google |
| `fonts/css/special/d8w3css-frijole.css` | `fonts/w3css-font-frijole-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-frijole is now font-frijole-google |
| `fonts/css/special/d8w3css-ruslan-display.css` | `fonts/w3css-font-ruslan-display-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-ruslan-display is now font-ruslan-display-google |
| `fonts/css/special/d8w3css-faster-one.css` | `fonts/w3css-font-faster-one-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-faster-one is now font-faster-one-google |
| `fonts/css/special/d8w3css-akronim.css` | `fonts/w3css-font-akronim-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-akronim is now font-akronim-google |
| `fonts/css/special/d8w3css-finger-paint.css` | `fonts/w3css-font-finger-paint-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-finger-paint is now font-finger-paint-google |
| `fonts/css/special/d8w3css-ceviche-one.css` | `fonts/w3css-font-ceviche-one-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-ceviche-one is now font-ceviche-one-google |
| `fonts/css/special/d8w3css-unifrakturmaguntia.css` | `fonts/w3css-font-unifrakturmaguntia-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-unifrakturmaguntia is now font-unifrakturmaguntia-google |
| `fonts/css/special/d8w3css-megrim.css` | `fonts/w3css-font-megrim-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-megrim is now font-megrim-google |
| `fonts/css/special/d8w3css-bungee-shade.css` | `fonts/w3css-font-bungee-shade-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-bungee-shade is now font-bungee-shade-google |
| `fonts/css/special/d8w3css-irish-grover.css` | `fonts/w3css-font-irish-grover-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-irish-grover is now font-irish-grover-google |
| `fonts/css/special/d8w3css-pirata-one.css` | `fonts/w3css-font-pirata-one-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-pirata-one is now font-pirata-one-google |
| `fonts/css/special/d8w3css-rye.css` | `fonts/w3css-font-rye-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-rye is now font-rye-google |
| `fonts/css/special/d8w3css-creepster.css` | `fonts/w3css-font-creepster-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-creepster is now font-creepster-google |
| `fonts/css/special/d8w3css-moo-lah-lah.css` | `fonts/w3css-font-moo-lah-lah-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-moo-lah-lah is now font-moo-lah-lah-google |
| `fonts/css/special/d8w3css-monoton.css` | `fonts/w3css-font-monoton-google.css` | rename | Reported, change by hand | 2.0.18 library d8w3css-gfex-monoton is now font-monoton-google |
| `css/w3-css-theme/w3-theme-amber.css` | `css/predefined/w3css-golden-amber.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-amber is now scheme-golden-amber |
| `css/w3-css-theme/w3-theme-blue.css` | `css/predefined/w3css-royal-blue.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-blue is now scheme-royal-blue |
| `css/w3-css-theme/w3-theme-blue-grey.css` | `css/predefined/w3css-misty-blue-gray.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-blue-grey is now scheme-misty-blue-gray |
| `css/w3-css-theme/w3-theme-brown.css` | `css/predefined/w3css-walnut-brown.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-brown is now scheme-walnut-brown |
| `css/w3-css-theme/w3-theme-cyan.css` | `css/predefined/w3css-bright-cyan.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-cyan is now scheme-bright-cyan |
| `css/w3-css-theme/w3-theme-deep-orange.css` | `css/predefined/w3css-burnt-orange.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-deep-orange is now scheme-burnt-orange |
| `css/w3-css-theme/w3-theme-deep-purple.css` | `css/predefined/w3css-plum-purple.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-deep-purple is now scheme-plum-purple |
| `css/w3-css-theme/w3-theme-green.css` | `css/predefined/w3css-meadow-green.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-green is now scheme-meadow-green |
| `css/w3-css-theme/w3-theme-indigo.css` | `css/predefined/w3css-ink-indigo.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-indigo is now scheme-ink-indigo |
| `css/w3-css-theme/w3-theme-khaki.css` | `css/predefined/w3css-desert-khaki.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-khaki is now scheme-desert-khaki |
| `css/w3-css-theme/w3-theme-light-blue.css` | `css/predefined/w3css-sky-blue.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-light-blue is now scheme-sky-blue |
| `css/w3-css-theme/w3-theme-light-green.css` | `css/predefined/w3css-spring-green.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-light-green is now scheme-spring-green |
| `css/w3-css-theme/w3-theme-lime.css` | `css/predefined/w3css-fresh-lime.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-lime is now scheme-fresh-lime |
| `css/w3-css-theme/w3-theme-orange.css` | `css/predefined/w3css-tangerine-orange.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-orange is now scheme-tangerine-orange |
| `css/w3-css-theme/w3-theme-pink.css` | `css/predefined/w3css-rose-pink.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-pink is now scheme-rose-pink |
| `css/w3-css-theme/w3-theme-purple.css` | `css/predefined/w3css-grape-purple.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-purple is now scheme-grape-purple |
| `css/w3-css-theme/w3-theme-red.css` | `css/predefined/w3css-cherry-red.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-red is now scheme-cherry-red |
| `css/w3-css-theme/w3-theme-teal.css` | `css/predefined/w3css-lagoon-teal.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-teal is now scheme-lagoon-teal |
| `css/w3-css-theme/w3-theme-yellow.css` | `css/predefined/w3css-sun-yellow.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-yellow is now scheme-sun-yellow |
| `css/w3-css-theme/w3-theme-black.css` | `css/predefined/w3css-jet-black.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-black is now scheme-jet-black |
| `css/w3-css-theme/w3-theme-dark-grey.css` | `css/predefined/w3css-charcoal-gray.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-dark-grey is now scheme-charcoal-gray |
| `css/w3-css-theme/w3-theme-grey.css` | `css/predefined/w3css-silver-gray.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-grey is now scheme-silver-gray |
| `css/w3-css-theme-custom/w3-theme-custom.css` | `css/predefined/w3css-custom.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-custom is now scheme-custom |
| `css/w3-css-theme-custom/w3-theme-00000.css` | `css/predefined/w3css-quiet-slate.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-00000 is now scheme-quiet-slate |
| `css/w3-css-theme-custom/w3-theme-00001.css` | `css/predefined/w3css-harbor-steel.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-00001 is now scheme-harbor-steel |
| `css/w3-css-theme-custom/w3-theme-00002.css` | `css/predefined/w3css-ocean-blue.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-00002 is now scheme-ocean-blue |
| `css/w3-css-theme-custom/w3-theme-00003.css` | `css/predefined/w3css-forest-green.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-00003 is now scheme-forest-green |
| `css/w3-css-theme-custom/w3-theme-00004.css` | `css/predefined/w3css-orchid-purple.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-00004 is now scheme-orchid-purple |
| `css/w3-css-theme-custom/w3-theme-00005.css` | `css/predefined/w3css-copper-brown.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-00005 is now scheme-copper-brown |
| `css/w3-css-theme-custom/w3-theme-00006.css` | `css/predefined/w3css-crimson-red.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-00006 is now scheme-crimson-red |
| `css/w3-css-theme-custom/w3-theme-00007.css` | `css/predefined/w3css-indigo-ink.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-00007 is now scheme-indigo-ink |
| `css/w3-css-theme-custom/w3-theme-00008.css` | `css/predefined/w3css-indigo-lagoon.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-00008 is now scheme-indigo-lagoon |
| `css/w3-css-theme-custom/w3-theme-00009.css` | `css/predefined/w3css-deep-cyan.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-00009 is now scheme-deep-cyan |
| `css/w3-css-theme-custom/w3-theme-00010.css` | `css/predefined/w3css-lime-grass.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-00010 is now scheme-lime-grass |
| `css/w3-css-theme-custom/w3-theme-00011.css` | `css/predefined/w3css-espresso-brown.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-00011 is now scheme-espresso-brown |
| `css/w3-css-theme-custom/w3-theme-00012.css` | `css/predefined/w3css-navy-ruby.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-00012 is now scheme-navy-ruby |
| `css/w3-css-theme-custom/w3-theme-00013.css` | `css/predefined/w3css-cadet-blue.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-00013 is now scheme-cadet-blue |
| `css/w3-css-theme-custom/w3-theme-00014.css` | `css/predefined/w3css-harbor-blue.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-00014 is now scheme-harbor-blue |
| `css/w3-css-theme-custom/w3-theme-00015.css` | `css/predefined/w3css-cadet-steel.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-00015 is now scheme-cadet-steel |
| `css/w3-css-theme-custom/w3-theme-00016.css` | `css/predefined/w3css-slate-amber.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-00016 is now scheme-slate-amber |
| `css/w3-css-theme-custom/w3-theme-00017.css` | `css/predefined/w3css-midnight-ember.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-00017 is now scheme-midnight-ember |
| `css/w3-css-theme-custom/w3-theme-00018.css` | `css/predefined/w3css-dark-graphite.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-00018 is now scheme-dark-graphite |
| `css/w3-css-theme-custom/w3-theme-00019.css` | `css/predefined/w3css-dark-moss.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-00019 is now scheme-dark-moss |
| `css/w3-css-theme-custom/w3-theme-00020.css` | `css/predefined/w3css-dark-ocean.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-00020 is now scheme-dark-ocean |
| `css/w3-css-theme-custom/w3-theme-00021.css` | `css/predefined/w3css-dark-violet.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-00021 is now scheme-dark-violet |
| `css/w3-css-theme-custom/w3-theme-00022.css` | `css/predefined/w3css-dark-ruby.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-00022 is now scheme-dark-ruby |
| `css/w3-css-theme-custom/w3-theme-00023.css` | `css/predefined/w3css-dark-rose.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-00023 is now scheme-dark-rose |
| `css/w3-css-theme-custom/w3-theme-00024.css` | `css/predefined/w3css-dark-emerald.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-00024 is now scheme-dark-emerald |
| `css/w3-css-theme-custom/w3-theme-00025.css` | `css/predefined/w3css-dark-lavender.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-00025 is now scheme-dark-lavender |
| `css/w3-css-theme-custom/w3-theme-00026.css` | `css/predefined/w3css-dark-gold.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-00026 is now scheme-dark-gold |
| `css/w3-css-theme-custom/w3-theme-00027.css` | `css/predefined/w3css-dark-sky.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-00027 is now scheme-dark-sky |
| `css/w3-css-theme-custom/w3-theme-00028.css` | `css/predefined/w3css-teal-coral.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-00028 is now scheme-teal-coral |
| `css/w3-css-theme-custom/w3-theme-00029.css` | `css/predefined/w3css-photo-backdrop.css` | rename | Reported, change by hand | 2.0.18 library w3-css-theme-00029 is now scheme-photo-backdrop |

54 more row(s) keep their name.

### JavaScript files (6)

| Old | New | Action | What the script does | Note |
|---|---|---|---|---|
| `js/custom.js` | js/components/w3css-menu.js + js/base/w3css-page.js + js/components/w3css-search.js | split | Reported, change by hand | 1,215 lines, 5 behaviors. Menu behaviors (w3cssMenuDepth, w3cssResponsiveMenu, w3cssMainNav, w3cssMenuState, lines 903-1100) and their helpers (lines 8-900) are one menu component; w3cssTheme (lines 1106-1215) mixes page, search, settings-page and image code: needs checking before the split, boundaries not measured |
| `js/full.opacity.on.scroll.js` | `js/utilities/w3css-fade.js` | rename | Reported, change by hand | behavior w3cssFullOpacity, class w3css-fade |
| `js/d8w3css-settings-form.js` | `js/admin/w3css-settings-form.js` | rename | Reported, change by hand | admin |
| `js/d8w3css-settings-schemes.js` | `js/admin/w3css-settings-schemes.js` | rename | Reported, change by hand | admin |
| `js/d8w3css-settings-search.js` | `js/admin/w3css-settings-search.js` | rename | Reported, change by hand | admin |
| `drupal8_w3css_subtheme/js/d8w3css-subtheme-script.js` | `drupal8_w3css_subtheme/js/w3css-subtheme-script.js` | rename | Reported, change by hand | starter kit: sites copy it and rename it; the prefix d8w3css is the old product name |

### Stored values (6)

| Old | New | Action | What the script does | Note |
|---|---|---|---|---|
| typography.site_font / heading_font / special_font = d8w3css-<font> (101 option values) | `w3css-font-<font>` | rename | Saved by drush updb (setting values) or by the script with --stored (classes in configuration) | stored in every site that picked a font, released in 2.x. The value is the stem of the library id, so one table value -> slug keeps PHP from building ids by string surgery (AttachmentHooks.php:98). |
| region color class settings, Block Class classes, Views CSS, field classes, CKEditor allowed tags | rewritten by the existing class migration | rename | Saved by drush updb (setting values) or by the script with --stored (classes in configuration) | covered by docs/CLASS-MIGRATION.md; every class row marked "class map row" above lands here |

4 more row(s) keep their name.

### Classes of the 3.0 development branch (218)

| Old | New | Action | What the script does | Note |
|---|---|---|---|---|
| `w3css-action-links` | `w3css-local-actions` | rename | Not needed: this name never shipped |  |
| `w3css-admin-context` | `w3css-page--admin` | rename | Not needed: this name never shipped |  |
| `w3css-admin-list` | `w3css-admin-block-content__list` | rename | Not needed: this name never shipped |  |
| `w3css-bottom-box` | `w3css-page__bottom` | rename | Not needed: this name never shipped |  |
| `w3css-bottom-region__inner` | `w3css-page__bottom-inner` | rename | Not needed: this name never shipped |  |
| `w3css-comment-form__title` | `w3css-field-comment__title` | rename | Not needed: this name never shipped |  |
| `w3css-comment-wrapper` | `w3css-field-comment` | rename | Not needed: this name never shipped |  |
| `w3css-content-region` | `w3css-page__content` | rename | Not needed: this name never shipped |  |
| `w3css-des-spacer` | `w3css-file-upload-help__spacer` | rename | Not needed: this name never shipped |  |
| `w3css-description` | `w3css-form-element__description` | rename | Not needed: this name never shipped |  |
| `w3css-details-wrapper` | `w3css-details__wrapper` | rename | Not needed: this name never shipped |  |
| `w3css-element-container-wrapper` | `w3css-container__wrapper` | rename | Not needed: this name never shipped |  |
| `w3css-field-prefix` | `w3css-form-element__prefix` | rename | Not needed: this name never shipped |  |
| `w3css-field-suffix` | `w3css-form-element__suffix` | rename | Not needed: this name never shipped |  |
| `w3css-fieldset-wrapper` | `w3css-fieldset__wrapper` | rename | Not needed: this name never shipped |  |
| `w3css-filter-guidelines-item` | `w3css-filter-guidelines__item` | rename | Not needed: this name never shipped |  |
| `w3css-first-bottom` | `w3css-page__bottom-first` | rename | Not needed: this name never shipped |  |
| `w3css-first-footer` | `w3css-page__footer-first` | rename | Not needed: this name never shipped |  |
| `w3css-first-sidebar` | `w3css-page__sidebar-first` | rename | Not needed: this name never shipped |  |
| `w3css-first-top` | `w3css-page__top-first` | rename | Not needed: this name never shipped |  |
| `w3css-footer-box` | `w3css-page__footer` | rename | Not needed: this name never shipped |  |
| `w3css-footer-menu__inner` | `w3css-page__footer-menu-inner` | rename | Not needed: this name never shipped |  |
| `w3css-footer-region__inner` | `w3css-page__footer-inner` | rename | Not needed: this name never shipped |  |
| `w3css-form-checkboxes` | `w3css-checkboxes` | rename | Not needed: this name never shipped |  |
| `w3css-form-item` | `w3css-form-element` | rename | Not needed: this name never shipped |  |
| `w3css-form-item--error-message` | `w3css-form-element--error` | rename | Not needed: this name never shipped |  |
| `w3css-form-radios` | `w3css-radios` | rename | Not needed: this name never shipped |  |
| `w3css-form-required` | `w3css-form-element__required` | rename | Not needed: this name never shipped |  |
| `w3css-form-wrapper` | `w3css-container` | rename | Not needed: this name never shipped | printed by container.html.twig AND fieldset.html.twig: a shared class; proposal names the container block, fieldset keeps w3css-fieldset__wrapper; needs checking in form/*.html.twig |
| `w3css-forth-bottom` | `w3css-page__bottom-fourth` | rename | Not needed: this name never shipped |  |
| `w3css-header__inner` | `w3css-page__header-inner` | rename | Not needed: this name never shipped |  |
| `w3css-highlighted-inner` | (id dropped) | drop | Not needed: this name never shipped | an id duplicating a class; CSS and JS move to the class. Needs checking whether a sub-theme or scheme file selects the id. |
| `w3css-icon` | `w3css-settings-nav__icon` | rename | Not needed: this name never shipped |  |
| `w3css-image-link` | `w3css-image__link` | rename | Not needed: this name never shipped |  |
| `w3css-item-depth-1` | `w3css-menu__item--depth-1` | rename | Not needed: this name never shipped |  |
| `w3css-label` | `w3css-admin-block-content__label` | rename | Not needed: this name never shipped |  |
| `w3css-layout-node-form` | `w3css-node-edit-form` | rename | Not needed: this name never shipped |  |
| `w3css-layout-region-node-footer` | `w3css-node-edit-form__footer` | rename | Not needed: this name never shipped |  |
| `w3css-layout-region-node-footer__content` | `w3css-node-edit-form__footer-content` | rename | Not needed: this name never shipped |  |
| `w3css-layout-region-node-main` | `w3css-node-edit-form__main` | rename | Not needed: this name never shipped |  |
| `w3css-layout-region-node-secondary` | `w3css-node-edit-form__secondary` | rename | Not needed: this name never shipped |  |
| `w3css-main-box` | `w3css-page__main` | rename | Not needed: this name never shipped |  |
| `w3css-main-box--breadcrumb-found` | `w3css-page__main--breadcrumb-found` | rename | Not needed: this name never shipped |  |
| `w3css-main-nav` | `w3css-page__nav` | rename | Not needed: this name never shipped |  |
| `w3css-main-nav--horizontal` | `w3css-page__nav--horizontal` | rename | Not needed: this name never shipped |  |
| `w3css-main-nav--vertical` | `w3css-page__nav--vertical` | rename | Not needed: this name never shipped |  |
| `w3css-main-nav-inner-horizontal` | (id dropped) | drop | Not needed: this name never shipped | an id duplicating a class; CSS and JS move to the class. Needs checking whether a sub-theme or scheme file selects the id. |
| `w3css-main-nav-inner-vertical` | (id dropped) | drop | Not needed: this name never shipped | an id duplicating a class; CSS and JS move to the class. Needs checking whether a sub-theme or scheme file selects the id. |
| `w3css-main-nav__close` | `w3css-page__nav-close` | rename | Not needed: this name never shipped |  |
| `w3css-main-nav__inner--horizontal` | `w3css-page__nav-inner--horizontal` | rename | Not needed: this name never shipped |  |
| `w3css-main-nav__inner--vertical` | `w3css-page__nav-inner--vertical` | rename | Not needed: this name never shipped |  |
| `w3css-main-nav__open` | `w3css-page__nav-open` | rename | Not needed: this name never shipped |  |
| `w3css-marker` | `w3css-mark` | rename | Not needed: this name never shipped | Drupal mark.html.twig prints a marker; "mark" is the template name |
| `w3css-menu__link-toggle-vertical` | `w3css-menu__link-toggle--vertical` | rename | Not needed: this name never shipped |  |
| `w3css-messages` | `w3css-status-messages` | rename | Not needed: this name never shipped |  |
| `w3css-messages__item` | `w3css-status-messages__item` | rename | Not needed: this name never shipped |  |
| `w3css-messages__list` | `w3css-status-messages__list` | rename | Not needed: this name never shipped |  |
| `w3css-mobile-nav__toggle` | `w3css-page__nav-toggle` | rename | Not needed: this name never shipped |  |
| `w3css-nav` | `w3css-settings-nav` | rename | Not needed: this name never shipped | admin settings left navigation; the name collides in meaning with page navigation |
| `w3css-nav__count` | `w3css-settings-nav__count` | rename | Not needed: this name never shipped | admin settings left navigation; the name collides in meaning with page navigation |
| `w3css-nav__current` | `w3css-settings-nav__current` | rename | Not needed: this name never shipped | admin settings left navigation; the name collides in meaning with page navigation |
| `w3css-nav__detail` | `w3css-settings-nav__detail` | rename | Not needed: this name never shipped | admin settings left navigation; the name collides in meaning with page navigation |
| `w3css-nav__error` | `w3css-settings-nav__error` | rename | Not needed: this name never shipped | admin settings left navigation; the name collides in meaning with page navigation |
| `w3css-nav__group` | `w3css-settings-nav__group` | rename | Not needed: this name never shipped | admin settings left navigation; the name collides in meaning with page navigation |
| `w3css-nav__item` | `w3css-settings-nav__item` | rename | Not needed: this name never shipped | admin settings left navigation; the name collides in meaning with page navigation |
| `w3css-nav__link` | `w3css-settings-nav__link` | rename | Not needed: this name never shipped | admin settings left navigation; the name collides in meaning with page navigation |
| `w3css-nav__list` | `w3css-settings-nav__list` | rename | Not needed: this name never shipped | admin settings left navigation; the name collides in meaning with page navigation |
| `w3css-nav__menu` | `w3css-settings-nav__menu` | rename | Not needed: this name never shipped | admin settings left navigation; the name collides in meaning with page navigation |
| `w3css-nav__name` | `w3css-settings-nav__name` | rename | Not needed: this name never shipped | admin settings left navigation; the name collides in meaning with page navigation |
| `w3css-nav__note` | `w3css-settings-nav__note` | rename | Not needed: this name never shipped | admin settings left navigation; the name collides in meaning with page navigation |
| `w3css-nav__search` | `w3css-settings-nav__search` | rename | Not needed: this name never shipped | admin settings left navigation; the name collides in meaning with page navigation |
| `w3css-nav__searchbox` | `w3css-settings-nav__searchbox` | rename | Not needed: this name never shipped | admin settings left navigation; the name collides in meaning with page navigation |
| `w3css-nav__toggle` | `w3css-settings-nav__toggle` | rename | Not needed: this name never shipped | admin settings left navigation; the name collides in meaning with page navigation |
| `w3css-page-footer` | `w3css-page__footer-block` | rename | Not needed: this name never shipped |  |
| `w3css-page-title-inner` | (id dropped) | drop | Not needed: this name never shipped | an id duplicating a class; CSS and JS move to the class. Needs checking whether a sub-theme or scheme file selects the id. |
| `w3css-page-wrapper` | `w3css-page` | rename | Not needed: this name never shipped |  |
| `w3css-requirements` | `w3css-status-report__requirements` | rename | Not needed: this name never shipped |  |
| `w3css-responsive-nav` | `w3css-menu--responsive-h` | rename | Not needed: this name never shipped |  |
| `w3css-responsive-nav__toggle` | `w3css-menu__responsive-toggle` | rename | Not needed: this name never shipped |  |
| `w3css-schemes` | `w3css-scheme-picker` | rename | Not needed: this name never shipped | admin scheme picker (src/Settings/PredefinedThemes.php) |
| `w3css-schemes-panel` | `w3css-scheme-picker-panel` | rename | Not needed: this name never shipped | admin scheme picker (src/Settings/PredefinedThemes.php) |
| `w3css-schemes-tab` | `w3css-scheme-picker-tab` | rename | Not needed: this name never shipped | admin scheme picker (src/Settings/PredefinedThemes.php) |
| `w3css-schemes__current` | `w3css-scheme-picker__current` | rename | Not needed: this name never shipped | admin scheme picker (src/Settings/PredefinedThemes.php) |
| `w3css-schemes__grid` | `w3css-scheme-picker__grid` | rename | Not needed: this name never shipped | admin scheme picker (src/Settings/PredefinedThemes.php) |
| `w3css-schemes__group` | `w3css-scheme-picker__group` | rename | Not needed: this name never shipped | admin scheme picker (src/Settings/PredefinedThemes.php) |
| `w3css-schemes__group-title` | `w3css-scheme-picker__group-title` | rename | Not needed: this name never shipped | admin scheme picker (src/Settings/PredefinedThemes.php) |
| `w3css-schemes__image` | `w3css-scheme-picker__image` | rename | Not needed: this name never shipped | admin scheme picker (src/Settings/PredefinedThemes.php) |
| `w3css-schemes__name` | `w3css-scheme-picker__name` | rename | Not needed: this name never shipped | admin scheme picker (src/Settings/PredefinedThemes.php) |
| `w3css-schemes__tab` | `w3css-scheme-picker__tab` | rename | Not needed: this name never shipped | admin scheme picker (src/Settings/PredefinedThemes.php) |
| `w3css-schemes__tab-mark` | `w3css-scheme-picker__tab-mark` | rename | Not needed: this name never shipped | admin scheme picker (src/Settings/PredefinedThemes.php) |
| `w3css-schemes__tabs` | `w3css-scheme-picker__tabs` | rename | Not needed: this name never shipped | admin scheme picker (src/Settings/PredefinedThemes.php) |
| `w3css-search-button-block` | `w3css-search-block-form__button` | rename | Not needed: this name never shipped |  |
| `w3css-second-bottom` | `w3css-page__bottom-second` | rename | Not needed: this name never shipped |  |
| `w3css-second-footer` | `w3css-page__footer-second` | rename | Not needed: this name never shipped |  |
| `w3css-second-sidebar` | `w3css-page__sidebar-second` | rename | Not needed: this name never shipped |  |
| `w3css-second-top` | `w3css-page__top-second` | rename | Not needed: this name never shipped |  |
| `w3css-section-title` | `w3css-settings__section-title` | rename | Not needed: this name never shipped |  |
| `w3css-sections` | `w3css-settings__sections` | rename | Not needed: this name never shipped |  |
| `w3css-site-logo` | `w3css-site-branding__logo` | rename | Not needed: this name never shipped |  |
| `w3css-site-name` | `w3css-site-branding__name` | rename | Not needed: this name never shipped |  |
| `w3css-site-name-slogan` | `w3css-site-branding__name-slogan` | rename | Not needed: this name never shipped |  |
| `w3css-site-name__link` | `w3css-site-branding__name-link` | rename | Not needed: this name never shipped |  |
| `w3css-site-slogan` | `w3css-site-branding__slogan` | rename | Not needed: this name never shipped |  |
| `w3css-system-status-general-info__item` | `w3css-status-report-general-info__item` | rename | Not needed: this name never shipped |  |
| `w3css-system-status-general-info__items` | `w3css-status-report-general-info__items` | rename | Not needed: this name never shipped |  |
| `w3css-system-status-general-info__run-cron` | `w3css-status-report-general-info__run-cron` | rename | Not needed: this name never shipped |  |
| `w3css-system-status-report__entry-value` | `w3css-status-report__entry-value` | rename | Not needed: this name never shipped |  |
| `w3css-system-status-report__requirements-group` | `w3css-status-report__requirements-group` | rename | Not needed: this name never shipped |  |
| `w3css-system-status-report__status-icon` | `w3css-status-report__status-icon` | rename | Not needed: this name never shipped |  |
| `w3css-system-status-report__status-title` | `w3css-status-report__status-title` | rename | Not needed: this name never shipped |  |
| `w3css-third-bottom` | `w3css-page__bottom-third` | rename | Not needed: this name never shipped |  |
| `w3css-third-footer` | `w3css-page__footer-third` | rename | Not needed: this name never shipped |  |
| `w3css-third-top` | `w3css-page__top-third` | rename | Not needed: this name never shipped |  |
| `w3css-top-box` | `w3css-page__top` | rename | Not needed: this name never shipped |  |
| `w3css-top-region__inner` | `w3css-page__top-inner` | rename | Not needed: this name never shipped |  |
| `w3css-unpublished` | `w3css-node--unpublished` | rename | Not needed: this name never shipped |  |
| `w3css-vertical-nav` | `w3css-menu--responsive-v` | rename | Not needed: this name never shipped |  |
| `w3css-view` | `w3css-views-view` | rename | Not needed: this name never shipped |  |
| `w3css-view-filters` | `w3css-views-view__filters` | rename | Not needed: this name never shipped |  |
| `w3css-view-header` | `w3css-views-view__header` | rename | Not needed: this name never shipped |  |
| `w3css-views-ui-view-displays` | `w3css-views-ui-views-listing-table__displays` | rename | Not needed: this name never shipped |  |
| `w3css-welcome-text-inner` | (id dropped) | drop | Not needed: this name never shipped | an id duplicating a class; CSS and JS move to the class. Needs checking whether a sub-theme or scheme file selects the id. |

96 more row(s) keep their name.

### Ids of the 3.0 development branch (20)

20 more row(s) keep their name.

### Framework names kept (1)

1 more row(s) keep their name.

A name in braces is a pattern: `{n}` is a number, `{menu}` a cleaned menu name, `{size}` small, medium or large.
