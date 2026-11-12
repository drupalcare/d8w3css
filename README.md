# W3CSS Theme

W3CSS Theme is a base theme built on the W3.CSS framework, with a focus on ease
of use and a fast load time. With the W3CSS Paragraphs module you can build
unlimited page designs.

- Project page:
  [drupal.org/project/d8w3css](https://www.drupal.org/project/d8w3css)
- Issue queue:
  [drupal.org/project/issues/d8w3css](https://www.drupal.org/project/issues/d8w3css)

W3.CSS:

- Smaller and faster than other CSS frameworks.
- Easier to learn, and easier to use than other CSS frameworks.
- Uses standard CSS only (no jQuery or JavaScript library).
- Speeds up and simplifies web development.
- Supports modern responsive design (mobile first) by default.
- Provides CSS equality for all modern browsers. Chrome, Firefox, Safari, and
  more.
- Provides CSS equality for all devices. PC, laptop, tablet, and mobile.
- Learn more in the [W3.CSS documentation](https://www.w3schools.com/w3css/).


## Requirements

- Drupal 11.3 or later, or Drupal 12.
- No modules are required, and no base theme.


## Installation

1. Install the theme. With Composer, from your project folder, run
   `composer require drupal/d8w3css`. Without Composer, download the theme and
   unpack it into `themes/contrib/` (or `themes/`). The folder name is
   `drupal8_w3css_theme`. Run `drush cr`, or clear caches at Configuration >
   Development > Performance.

1. Set it as the default theme. Go to /admin/appearance and scroll to "W3CSS
   Theme". Click "Install and set as default". Open the front page and check
   that the site now uses the theme.

1. Place your blocks. Go to /admin/structure/block and open the "W3CSS Theme"
   tab. The theme prints a region only when it holds a block, so an empty region
   leaves no gap. A good first set:
   - "Site branding" in "Header".
   - "Main navigation" in "Primary Menu" for a horizontal menu, or in "Primary
     Menu Vertical" for a side menu that slides in. You can use both.
   - "Page title" in "Page Title". The block is disabled by default: click
     "Enable".
   - Your own blocks in "Left Sidebar", "Content" and "Right Sidebar".

   The Top, Bottom and Welcome Text regions show on the front page only. Page
   Title does not show on the front page.

1. Open the theme settings. Go to
   /admin/appearance/settings/drupal8_w3css_theme. Settings has a navigation
   list with three groups: Site-wide, Colors, and Footer and social. Choose a
   name to jump to its section.

1. Set these first.
   - Colors > "Predefined color themes": choose a color theme by its picture.
   - Site-wide > "Global Site Settings": website width, main columns, login page
     layout, breadcrumb.
   - Site-wide > "Libraries and Fonts": site font, heading font, Font Awesome.
   - Footer and social > "Copyright and Credit": replace the sample copyright
     text ("Company Name") or the credit link if you do not want them.
   - Footer and social > "Social media links": the links that ship in the theme
     point to the maintainer's accounts. Replace them with yours, or turn off
     "Show social icons".

   Click "Save configuration" at the bottom of the page.

1. Check that it works.
   - The front page shows your header, menu and blocks, and the colors you
     chose.
   - Make the browser window narrower than 993 pixels. The horizontal menu
     collapses into a button. Click it to open the menu. A vertical menu shows a
     button in the header that opens the side menu, and a close button inside
     it.
   - Open the login page (/user/login) and the settings page again. Both should
     show without errors.
   - If a change does not show, clear caches and reload.


## What changed in 3.0

- Needs Drupal 11.3 or later, or Drupal 12.
- No jQuery. The theme's script is plain JavaScript.
- No .theme file and no theme-settings.php. The theme's hooks and the settings
  form sections are classes in src/.
- Semantic markup: landmarks, labeled regions, buttons for the menu toggles, and
  one heading structure.
- The theme's own classes and ids now use BEM names that start with w3css-
  (w3css-main-nav__close, for example). W3.CSS library classes (w3-*) are
  unchanged. The old class names stay on the markup, so existing sub-theme CSS
  keeps working for now.
- The settings page has a new layout: a navigation list, one section at a time,
  and color themes shown as pictures.
- The settings keys were renamed. Run drush updb after updating.
- Moving your own CSS, templates and stored configuration to the new names
  (classes, ids, CSS variables, JavaScript names and library ids): see
  docs/CLASS-MIGRATION.md, or docs/class-migration.html for a page you can open
  in a browser. They list the old and new names side by side. A Drush script
  does the renaming for you. Dry run first, with a report outside the web root:
  `drush php:script themes/contrib/drupal8_w3css_theme/scripts/migrate-classes.php -- mytheme --stored --content --report=w3css-report.md`.
  Then add `--apply` to write, and `--yml` to also rewrite library ids in your
  `*.info.yml` and `*.libraries.yml`.

The details for an existing site are in the next section.


## Upgrading from 2.0.x to 3.0

Back up the site, update the theme, then run the database updates and rebuild
caches: drush updb, then drush cr (or /update.php, then Configuration >
Development > Performance). The theme has new hook classes, and Drupal finds
them only after a cache rebuild. The update renames the stored settings (see the
second bullet); afterward export the configuration (drush cex) and commit it, so
other environments get the new names instead of the old ones.

- Needs Drupal 11.3 or later, or Drupal 12. Drupal 9, 10 and 11.0 to 11.2 stay
  on 2.0.x.
- The machine name and regions are unchanged, so block placement needs no
  change. The library ids are new (see Upgrade notes: library ids, below). The setting keys are renamed: a flat key such as
  color_page_wrapper is now region_colors.page_wrapper, and current_theme is
  color_scheme.predefined. The post-update (drupal8_w3css_theme_post_update_
  rename_settings, run by drush updb) moves every stored value to its new name
  in this theme and in each installed sub-theme, and keeps a backup of the old
  configuration. The full list of old and new names is the MAP constant in
  src/Upgrade/SettingsMap.php. Saving the settings form also moves any key the
  update left behind. Overrides of old keys in settings.php or a module, and
  code that reads an old key (theme_get_setting('color_header'), for example),
  are reported by the update but not changed: edit them by hand.
- The theme ships no .theme file, no theme-settings.php and no includes/ folder.
  Its hooks and the 12 settings sections are classes in src/. A sub-theme or
  site code that called a drupal8_w3css_theme_* function must move to a hook
  class (see the sub-theme steps below).
- jQuery is gone from the theme. A script of yours that uses jQuery must declare
  core/jquery in its own library. The one exception is the status report page,
  which still loads it through core/drupal.collapse when this is the admin
  theme.
- The theme's own classes and ids are renamed to BEM names that start with
  w3css- (for example .ul-parent is .w3css-menu__list and #main-navigation-v is
  #w3css-main-nav-vertical). The old names are no longer printed and no script
  adds them back, so sub-theme CSS, copied templates and JavaScript that use an
  old name stop matching after the update. Run the migrate-classes script
  (above) on your sub-theme before or right after updating: it rewrites every
  name in docs/CLASS-MIGRATION.md in your CSS, Twig, JavaScript and, with --yml,
  your library files. Names it cannot rewrite safely are listed in its report.
- Stored classes are not renamed by drush updb. Classes and ids saved in
  configuration (Block Class, Views, text formats, the theme's region color
  settings) and ids in body fields and menu links are rewritten only by the
  migrate-classes script with --stored and --content. It does not touch classes
  typed into body fields, menu link class attributes, Layout Builder section and
  component classes, or Paragraphs class fields: change those by hand.
- The JavaScript behaviors released in 2.0.x keep working under their old names
  as deprecated aliases: d8w3cssTheme is w3cssPage, d8w3cssMainNav is
  w3cssPageNav, d8w3cssMenuDepth is w3cssMenuDepth, d8w3cssResponsiveMenu is
  w3cssMenuResponsive and d8w3cssFullOpacity is w3cssFade. Replacing an old name
  replaces the new behavior. Deleting an old name removes only the alias: delete
  the new name to stop a behavior. The search code is now its own behavior,
  w3cssSearch, so replacing d8w3cssTheme no longer turns it off.
- The settings form was regrouped: every element has a new key and several
  sections moved into groups (layout, effects, libraries, copyright; the colors
  section is split into w3_colors_regions, w3_colors_blueprint and
  w3_colors_classes). A sub-theme form alter aimed at an old element path stops
  matching; rebuild it against the new form (the section classes in src/Settings/).
- Removed in 3.0: Font Awesome 4, 5 and 6 (the old library ids
  font-awesome-4-in, -4-ex, -5-ex and -6-ex are aliases of Font Awesome 7, and
  a stored choice of 4.7 loads 7), the old social icon images in images/icons/
  (the *-circle.svg files, rss.svg and twitter-circle-old.svg), and the PHP class
  Drupal\drupal8_w3css_theme\Hook\Drupal8W3cssThemeHooks, which is split into the
  hook classes in src/Hook/. Point sub-theme CSS and code at the new files and
  classes.
- Markup changes you may see in a copied template: the open and close side menu
  toggles are buttons, the site name is a heading on the front page only, and
  messages use one role (alert for errors and warnings, status otherwise).
- The theme no longer attaches Claro libraries, except the search results list
  when Claro is installed. A site that relied on Claro's indent for comments
  gets the theme's 40px indent.
- amun, amunet and anhur 3.0 and W3CSS Paragraphs 2.0 are built for this
  release. Composer refuses to install this theme next to amun, amunet or anhur
  older than 3.0 or W3CSS Paragraphs older than 2.0, so update them together:
  composer require 'drupal/d8w3css:^3.0' 'drupal/amun:^3.0' (and the others you
  use) -W.


### Upgrade notes: library ids

Every asset library has a new id in 3.0.0 (for example w3-css-theme-amber is
now scheme-golden-amber, d8w3css-gfex-lato is font-lato-google and
d8w3css-gfin-lato is font-lato-local). The list is in
libraries/w3css-libraries.json: the "id" is the new id, "was" the old one.

- The old ids still work through 3.x as deprecated aliases. Each alias holds no
  files and depends on its new library, and Drupal reports a use of one as a
  deprecation. They are removed in 4.0.0: change attach_library() calls,
  libraries and libraries-extend entries to the new ids.
- A sub-theme or module that uses libraries-override on an old id must switch to
  the new id. An alias has no files, so an override of it replaces nothing and
  the real library keeps loading. Use libraries-override on the new id.
- w3-css and d8w3css-global-styling are split into one library per file; see
  Upgrade notes: file paths, below.
- The scheme choice stored in the theme settings (color_scheme.predefined) is
  unchanged. The font choices (typography.site_font, heading_font and
  special_font) change from d8w3css-<name> to w3css-font-<name>; run
  `drush updb` (post update rename_typography_font_values) to rewrite them, and change any
  settings.php override or sub-theme code that sets them. Until then a font
  still stored under the old name loads as before.
- The old sub-theme library d8w3css-subtheme-global is now subtheme-global. A
  sub-theme copied from the starter kit has its own copy of that id and keeps
  working; rename it only if you want the new name.

### Upgrade notes: file paths

CSS and JS files have `w3css-` names in folders named for what they hold. A
sub-theme that overrides, copies or links a file by its old path must use the
new one. `w3-css` and `d8w3css-global-styling` are one library per file now;
both old ids stay as deprecated aliases that load every part, so attaching them
still works. Libraries-override on the old id has nothing to override: use the
new per-file id. Rules inside the files are unchanged.

| Old path | New path |
| --- | --- |
| `css/base/base.css` | `css/base/w3css-base.css` |
| `css/components/breadcrumb.css` | `css/components/w3css-breadcrumb.css` |
| `css/components/ckeditor.css` | `css/components/w3css-ckeditor.css` |
| `css/components/comments.css` | `css/components/w3css-comment.css` |
| `css/components/contact-form.css` | `css/components/w3css-contact-form.css` |
| `css/components/default-node.css` | `css/components/w3css-node.css` |
| `css/components/default-views.css` | `css/components/w3css-views-view.css` |
| `css/components/embedded-media.css` | `css/components/w3css-media.css` |
| `css/components/footer-menu.css` | `css/components/w3css-footer-menu.css` |
| `css/components/forms.css` | `css/components/w3css-form-element.css` |
| `css/components/global-menu.css` | `css/components/w3css-menu.css` |
| `css/components/layout-builder.css` | `css/components/w3css-layout-builder.css` |
| `css/components/maintenance-page.css` | `css/components/w3css-maintenance-page.css` |
| `css/components/page-title.css` | `css/components/w3css-page-title.css` |
| `css/components/pager.css` | `css/components/w3css-pager.css` |
| `css/components/search-block.css` | `css/components/w3css-search-block-form.css` |
| `css/components/search-page.css` | `css/components/w3css-search-result.css` |
| `css/components/site-branding.css` | `css/components/w3css-site-branding.css` |
| `css/components/sitemap.css` | `css/components/w3css-menu-sitemap.css` |
| `css/components/system-admin.css` | `css/components/w3css-system-admin.css` |
| `css/components/system-messages.css` | `css/components/w3css-status-messages.css` |
| `css/components/system-tabs.css` | `css/components/w3css-local-tasks.css` |
| `css/components/teaser-mode.css` | `css/components/w3css-node.css` |
| `css/components/user-menu.css` | `css/components/w3css-menu.css` |
| `css/components/v-menu.css` | `css/components/w3css-menu-responsive-v.css` |
| `css/components/w3css-tables.css` | `css/components/w3css-table.css` |
| `css/components/webform.css` | `css/components/w3css-webform.css` |
| `css/layout/layout-node-edit.css` | `css/layout/w3css-node-edit-form.css` |
| `css/layout/layout-regions.css` | `css/layout/w3css-page.css` |
| `css/layout/layout-user-login.css` | `css/layout/w3css-user-login.css` |
| `css/layout/layout-width.css` | `css/layout/w3css-page-width.css` |
| `css/theme/a-image.css` | `css/components/w3css-image.css` |
| `css/theme/border-animation.css` | `css/utilities/w3css-animate-border.css` |
| `css/theme/d8w3css-settings-form.css` | `css/admin/w3css-settings-form.css` |
| `css/theme/d8w3css-settings-schemes.css` | `css/admin/w3css-settings-schemes.css` |
| `css/theme/d8w3css-settings-search.css` | `css/admin/w3css-settings-search.css` |
| `css/theme/d8w3css-settings-swatches.css` | `css/admin/w3css-settings-swatches.css` |
| `css/theme/default-color-theme.css` | `css/predefined/w3css-pure-monochrome.css` |
| `css/theme/sitename-animation.css` | `css/components/w3css-site-branding-animation.css` |
| `css/theme/social-media-icon-size-l.css` | `css/components/w3css-social-media-size-l.css` |
| `css/theme/social-media-icon-size-m.css` | `css/components/w3css-social-media-size-m.css` |
| `css/theme/w3-ckeditor-iframe.css` | `css/admin/w3css-ckeditor-iframe.css` |
| `css/theme/w3-ckeditor5.css` | `css/admin/w3css-ckeditor5.css` |
| `css/theme/w3-css-theme-shared-custom-b.css` | `css/predefined/w3css-scheme-custom.css` |
| `css/theme/w3-css-theme-shared-custom-paint.css` | `css/predefined/w3css-scheme-custom-paint.css` |
| `css/theme/w3-css-theme-shared-custom.css` | `css/predefined/w3css-scheme-custom.css` |
| `css/theme/w3-css-theme-shared-keyframes.css` | `css/predefined/w3css-scheme-keyframes.css` |
| `css/theme/w3-css-theme-shared-palette.css` | `css/predefined/w3css-scheme-palette.css` |
| `css/w3-css/w3css-color-libraries-camouflage.css` | `css/utilities/w3css-color-camouflage.css` |
| `css/w3-css/w3css-color-libraries-european-signal.css` | `css/utilities/w3css-color-european-signal.css` |
| `css/w3-css/w3css-color-libraries-fashion.css` | `css/utilities/w3css-color-fashion.css` |
| `css/w3-css/w3css-color-libraries-flat-ui.css` | `css/utilities/w3css-color-flat-ui.css` |
| `css/w3-css/w3css-color-libraries-food.css` | `css/utilities/w3css-color-food.css` |
| `css/w3-css/w3css-color-libraries-ios.css` | `css/utilities/w3css-color-ios.css` |
| `css/w3-css/w3css-color-libraries-metro-ui.css` | `css/utilities/w3css-color-metro-ui.css` |
| `css/w3-css/w3css-color-libraries-us-highway.css` | `css/utilities/w3css-color-us-highway.css` |
| `css/w3-css/w3css-color-libraries-us-safety.css` | `css/utilities/w3css-color-us-safety.css` |
| `css/w3-css/w3css-color-libraries-vivid.css` | `css/utilities/w3css-color-vivid.css` |
| `css/w3-css/w3css-color-libraries-win8.css` | `css/utilities/w3css-color-win8.css` |
| `css/w3-css/w3css-drupal.css` | `css/base/w3css-drupal.css` |
| `css/w3-css/w3css-extended-classes.css` | `css/utilities/w3css-extended-classes.css` |
| `css/w3-css/w3css-layers.css` | `css/base/w3css-layers.css` |
| `drupal8_w3css_subtheme/css/d8w3css-subtheme-style.css` | `drupal8_w3css_subtheme/css/w3css-subtheme-style.css` |
| `drupal8_w3css_subtheme/js/d8w3css-subtheme-script.js` | `drupal8_w3css_subtheme/js/w3css-subtheme-script.js` |
| `js/d8w3css-settings-form.js` | `js/admin/w3css-settings-form.js` |
| `js/d8w3css-settings-schemes.js` | `js/admin/w3css-settings-schemes.js` |
| `js/d8w3css-settings-search.js` | `js/admin/w3css-settings-search.js` |
| `js/full.opacity.on.scroll.js` | `js/utilities/w3css-fade.js` |

Fonts: `fonts/google-font-<name>.css` is `fonts/w3css-font-<name>-local.css`,
`fonts/css/d8w3css-<name>.css` and `fonts/css/special/d8w3css-<name>.css` are
`fonts/w3css-font-<name>-google.css`, and `fonts/font-awesome-7.0.1.css` is
`fonts/w3css-font-awesome-7.css`. The font files themselves
(`fonts/fonts-files/`) did not move.

Not moved yet: `css/theme/max-width-600.css`, `max-width-767.css`,
`max-width-992.css`, `max-min-width-992-601.css`, `min-width-993.css`,
`js/custom.js`, `css/components/user-login.css` and `node-form.css`. They are
cut or merged in a later step and have temporary library ids.

### Upgrade notes: class, id, behavior and variable names

Theme classes and ids now follow the template they belong to (`w3css-<block>__<element>--<modifier>`). These names were new on the 3.0.x development branch only; a site coming from 2.x renames them with the migrate-classes script (`--stored --content` for saved configuration and content; drush updb does not), whose class map (`src/Upgrade/ClassMap.php`) points each 2.x name straight at the new one. A sub-theme or custom CSS that already used a 3.0 development name must switch to the new one. Names that amun, amunet and anhur select (for example `w3css-header`, `w3css-main-nav`, `w3css-site-name`, `w3css-page-wrapper`) are unchanged.

| Old | New |
|---|---|
| `.w3css-action-links` | `.w3css-local-actions` |
| `.w3css-admin-context` | `.w3css-page--admin` |
| `.w3css-admin-list` | `.w3css-admin-block-content__list` |
| `.w3css-bottom-box` | `.w3css-page__bottom` |
| `#w3css-bottom-region` | `#w3css-bottom-region` |
| `.w3css-comment-form__title` | `.w3css-field-comment__title` |
| `.w3css-comment-wrapper` | `.w3css-field-comment` |
| `.w3css-content-region` | `.w3css-page__content` |
| `#w3css-copyright` | `#w3css-copyright` |
| `.w3css-copyright__inner` | `.w3css-copyright__inner` |
| `.w3css-des-spacer` | `.w3css-file-upload-help__spacer` |
| `.w3css-description` | `.w3css-form-element__description` |
| `.w3css-details-wrapper` | `.w3css-details__wrapper` |
| `.w3css-element-container-wrapper` | `.w3css-container__wrapper` |
| `.w3css-field-prefix` | `.w3css-form-element__prefix` |
| `.w3css-field-suffix` | `.w3css-form-element__suffix` |
| `.w3css-fieldset-wrapper` | `.w3css-fieldset__wrapper` |
| `.w3css-filter-guidelines-item` | `.w3css-filter-guidelines__item` |
| `.w3css-first-bottom` | `.w3css-page__bottom-first` |
| `.w3css-first-footer` | `.w3css-page__footer-first` |
| `.w3css-first-sidebar` | `.w3css-page__sidebar-first` |
| `.w3css-first-top` | `.w3css-page__top-first` |
| `.w3css-footer-box` | `.w3css-page__footer` |
| `#w3css-footer-region` | `#w3css-footer-region` |
| `.w3css-form-checkboxes` | `.w3css-checkboxes` |
| `.w3css-form-radios` | `.w3css-radios` |
| `.w3css-form-required` | `.w3css-form-element__required` |
| `.w3css-form-wrapper` | `.w3css-container` |
| `.w3css-forth-bottom` | `.w3css-page__bottom-fourth` |
| `#w3css-highlighted` | `#w3css-highlighted` |
| `.w3css-icon` | `.w3css-settings-nav__icon` |
| `.w3css-image-link` | `.w3css-image__link` |
| `.w3css-label` | `.w3css-admin-block-content__label` |
| `.w3css-layout-node-form` | `.w3css-node-edit-form` |
| `.w3css-layout-region-node-footer` | `.w3css-node-edit-form__footer` |
| `.w3css-layout-region-node-footer__content` | `.w3css-node-edit-form__footer-content` |
| `.w3css-layout-region-node-main` | `.w3css-node-edit-form__main` |
| `.w3css-layout-region-node-secondary` | `.w3css-node-edit-form__secondary` |
| `#w3css-main-region` | `#w3css-main-region` |
| `.w3css-marker` | `.w3css-mark` |
| `.w3css-menu__link-toggle-vertical` | `.w3css-menu__link-toggle--vertical` |
| `.w3css-messages` | `.w3css-status-messages` |
| `.w3css-messages__item` | `.w3css-status-messages__item` |
| `.w3css-messages__list` | `.w3css-status-messages__list` |
| `.w3css-requirements` | `.w3css-status-report__requirements` |
| `.w3css-responsive-nav` | `.w3css-menu--responsive-h` |
| `.w3css-responsive-nav__toggle` | `.w3css-menu__responsive-toggle` |
| `.w3css-search-button-block` | `.w3css-search-block-form__button` |
| `.w3css-second-bottom` | `.w3css-page__bottom-second` |
| `.w3css-second-footer` | `.w3css-page__footer-second` |
| `.w3css-second-sidebar` | `.w3css-page__sidebar-second` |
| `.w3css-second-top` | `.w3css-page__top-second` |
| `.w3css-section-title` | `.w3css-settings__section-title` |
| `.w3css-sections` | `.w3css-settings__sections` |
| `.w3css-site-logo` | `.w3css-site-branding__logo` |
| `.w3css-system-status-general-info__item` | `.w3css-status-report-general-info__item` |
| `.w3css-system-status-general-info__items` | `.w3css-status-report-general-info__items` |
| `.w3css-system-status-general-info__run-cron` | `.w3css-status-report-general-info__run-cron` |
| `.w3css-system-status-report__entry-value` | `.w3css-status-report__entry-value` |
| `.w3css-system-status-report__requirements-group` | `.w3css-status-report__requirements-group` |
| `.w3css-system-status-report__status-icon` | `.w3css-status-report__status-icon` |
| `.w3css-system-status-report__status-title` | `.w3css-status-report__status-title` |
| `.w3css-third-bottom` | `.w3css-page__bottom-third` |
| `.w3css-third-footer` | `.w3css-page__footer-third` |
| `.w3css-third-top` | `.w3css-page__top-third` |
| `.w3css-top-box` | `.w3css-page__top` |
| `#w3css-top-region` | `#w3css-top-region` |
| `.w3css-vertical-nav` | `.w3css-menu--responsive-v` |
| `.w3css-view` | `.w3css-views-view` |
| `.w3css-view-filters` | `.w3css-views-view__filters` |
| `.w3css-view-header` | `.w3css-views-view__header` |
| `.w3css-views-ui-view-displays` | `.w3css-views-ui-views-listing-table__displays` |
| `#w3css-welcome-text` | `#w3css-welcome-text` |
| `.w3css-item-depth-<n>` | `.w3css-menu__item--depth-<n>` |
| `Drupal.behaviors.w3cssTheme` | `Drupal.behaviors.w3cssPage` and `Drupal.behaviors.w3cssSearch` |
| `Drupal.behaviors.w3cssMainNav` | `Drupal.behaviors.w3cssPageNav` |
| `Drupal.behaviors.w3cssResponsiveMenu` | `Drupal.behaviors.w3cssMenuResponsive` |
| `Drupal.behaviors.w3cssFullOpacity` | `Drupal.behaviors.w3cssFade` |
| `Drupal.behaviors.d8w3cssSettingsSearch` | `Drupal.behaviors.w3cssSettingsSearch` |
| `--w3css-theme-*` | `--w3css-scheme-*` |
| `--w3css-submenu-max`, `--w3css-submenu-shift`, `--w3css-arrow-open` | `--w3css-menu-submenu-max`, `--w3css-menu-submenu-shift`, `--w3css-menu-arrow-open` |

## Features

- W3CSS Theme is using the modern W3.CSS framework with built-in responsiveness

- W3CSS Theme has 22 regions. See the list in drupal8_w3css_theme.info.yml.

- W3CSS Theme has 12 settings sections, listed in the navigation on the left of
  the theme settings: Global Site Settings, Animation and Effects, Libraries and
  Fonts, Logo, Favicon, Theme settings (Logo, Favicon and Theme settings are
  Drupal's own), Predefined Color Themes, Region fields, Blueprint, CSS classes,
  Social Media Links, and Copyright and Credit. Under Colors, Region fields has
  26 fields, in 5 groups, for your CSS classes. CSS classes lists the W3.CSS
  color classes you can type into them, in 9 kinds (Background, Background
  hover, Text, Text hover, Link text, Link background, Link background hover,
  Button, Button hover).

- W3CSS Theme comes with ability to change the website width. Just insert any of
  the predefined classes into website width in theme settings.
  (w3-width-100-percent, w3-width-2560, w3-width-1920, w3-width-1600,
  w3-width-1360, w3-width-1280, w3-width-1024 and w3-width-800).

- W3CSS Theme comes with horizontal main menu or vertical side nav. Simply add
  the main menu to the horizontal region or the vertical region.

- W3CSS Theme comes with 101 fonts. 50 fonts can be applied to the global site,
  26 fonts for headings (H1, H2 and H3) and 25 special fonts can be applied to
  any HTML tag. Any uploaded font has a CSS class that can be applied to any
  HTML tag.

- W3CSS Theme comes with 24 CSS text animation effects that can be applied to
  site name, page title or use the CSS class to apply it to any HTML tag.

- W3CSS Theme comes with a custom page layout for user login/register/reset
  password.

- W3CSS Theme comes with a custom border animation for top regions, bottom
  regions and footer regions.

- W3CSS Theme comes with over 50 predefined color themes. Simply insert the name
  of any of the predefined themes. More will be added.

- W3CSS Theme comes with the ability to create your own private W3.CSS theme and
  apply it to your site.

- W3CSS Theme V1 comes with 6 color libraries. (US Highway, US Safety,
  Camouflage, Vivid, European Signal and Food).

- W3CSS Theme comes with 11 color libraries. (Camouflage, European Signal,
  Fashion, Flat UI, Food, IOS, Metro UI, US highway, US Safety, Vivid and Win8).
  You can create unlimited color themes.

- W3CSS Theme comes with 8 social media links that are easy to configure.

- W3CSS Theme has a top region that includes three flexible regions. (The total
  space in the top region will be divided by the number of regions you filled.)

- W3CSS Theme has a bottom region that includes four flexible regions. (The
  total space in the bottom region will be divided by the number of regions you
  filled.)

- W3CSS Theme has a footer region that includes three flexible regions. (The
  total space in the footer region will be divided by the number of regions you
  filled.)

- W3CSS Theme comes with Font Awesome 7, local or from a CDN. It is off by
  default; you can enable it in theme settings, under Libraries and Fonts.

- W3CSS Theme comes with match height. Example: If you have three blocks in the
  top area, they will always have matching height on the large screen size. The
  script was removed and it uses CSS Flexbox.

- W3CSS Theme comes with a responsive main menu and It is three level drop-down
  menu. The drop-down works on hover for big screen and on click for small
  screen.

- W3CSS Theme will hide completely (divs, wrappers, classes) any region you
  don't use. Nothing will be printed in the back.

- W3CSS Theme uses the minimum HTML structure to show the content. Example, You
  will not see div > div > div > div > div.

- W3CSS Theme comes with the ability to create a custom page template according
  to content type's name. If you have two content types article and video you
  can have page--article.html.twig and page--video.html.twig

- W3CSS Theme comes with the ability to create a custom page template for any
  view. Example if you have a view name "recent_video", then you can create a
  page template name page--recent-video.html.twig.

- W3CSS Theme comes with the ability to create a custom page template for any
  vocabulary. Example if you have a vocabulary name "Tags", then you can create
  a page template name page--taxonomy--tags.html.twig.

- W3CSS Theme comes with the ability to change the main container's columns
  layout to equal width. Change the columns from (25% 50% 25%) to (33% 33% 33%)

- W3CSS Theme comes with the ability to create a responsive horizontal three
  level secondary menu or a responsive vertical three level secondary menu.

- W3CSS Theme comes with customized Sub-themes. Each sub-theme has different
  layout/template.


## Configuration

The new version was added with all new features. If you are using V1 and you did
not override the page.html.twig, then you can update the theme however you
normally do. If you did override the page.html.twig, then you will need to clone
the new page.html.twig and update it.

You can build any website layout with the w3.css library. I try to add the most
well-known templates/layouts. I added some sub-themes to include different
layouts. If you see a w3css template that you want to have available in this
theme then let me know and I'll customize it and add it to the collection as a
sub-theme.

Note: All customized Sub-themes have an extra region in addition to the regions
in the parent theme for the sticky search block. If you click on the search
icon, you'll get a sticky search bar on the top in return.

What are drupal8 w3css Sub-themes? They are custom themes with different page
layouts.

What are drupal8 w3css predefined color themes? They are color skins that can be
applied to a W3CSS Theme or any of its sub-themes.


### How to change the logo

Go to your-drupal-site-name/admin/appearance/settings/drupal8_w3css_theme and
uncheck “Use the logo supplied by the theme” then upload your own website logo
they insert the path in logo text "Path to custom logo" box and click save. Logo
size is fixed. If you want to change the logo's size you will have to change it
in the sub theme.


### How to enable the page title

Go to your-drupal-site-name/admin/structure/block and scroll down until you see
Page Title's region. Page Title block is disabled by default, just click on
Enable to enable the page title


### How to enable the site slogan

Go to your-drupal-site-name/admin/structure/block and in header region. You
should see two blocks User account menu and Site branding. Click on configure
for Site branding and check the check box for Site Slogan.


### CSS text animation effects classes

Go to your-drupal-site-name/admin/appearance/settings/drupal8_w3css_theme and
open "Animation and Effects", then the "Text Animation" tab. You can choose the
animation for the site name or page title. The tab also lists the CSS text
animation effect classes that you can use with any HTML tag.


### How to enable/disable region border animation

Go to your-drupal-site-name/admin/appearance/settings/drupal8_w3css_theme and
open "Animation and Effects", then the "Border Animation" tab. There is a
checkbox to enable/disable the border animation for top regions, bottom regions
and footer regions.


### How to expand multi items field to 100%

By default, any Drupal field with multiple items (like taxonomy, file, user etc.
) will display those items in one line. If you want to change that, go to
your-drupal-site-name/admin/appearance/settings/drupal8_w3css_theme, open
"Region fields" under Colors, then add w3-expand-fields to the "Page Wrapper"
field.


### How to change the favicon

Go to www.your-drupal-site-name/admin/appearance/settings/drupal8_w3css_theme
and uncheck “Use the favicon supplied by the theme” then upload your own favicon
and click save.


### How to change website width

Go to your-drupal-site-name/admin/appearance/settings/drupal8_w3css_theme and
open "Global Site Settings", then the "Website's width" tab. Choose the width
from the list ("Choose the desired website's width") and click save.


### How to change website font or heading font

Go to your-drupal-site-name/admin/appearance/settings/drupal8_w3css_theme and
open "Libraries and Fonts", then the "Google Fonts" tab. You can choose the site
font or heading font.

Special fonts are not applied to any HTML tag by default. You just choose the
font then use the CSS class with any HTML tag.

Tip: Any font you choose for the site or heading will have a CSS class that you
can apply to any HTML tag.

Example: If I have chosen the font "Anton" for the heading and I want to apply
this font to a paragraph, here is how it's done;


### Color theme names (renamed in the next release)

Each color theme has a new name. Only the name shown on the settings page
changed; the stored value and the w3-theme-* class are the same, so a site keeps
the theme it picked.


| Machine name | Old name | New name |
|---|---|---|
| none | Default - Black Gray White | Pure Monochrome |
| w3-theme-amber | Amber | Golden Amber |
| w3-theme-black | Black | Jet Black |
| w3-theme-blue-grey | Blue Gray | Misty Blue Gray |
| w3-theme-blue | Blue | Royal Blue |
| w3-theme-brown | Brown | Walnut Brown |
| w3-theme-cyan | Cyan | Bright Cyan |
| w3-theme-dark-grey | Dark Gray | Charcoal Gray |
| w3-theme-deep-orange | Deep Orange | Burnt Orange |
| w3-theme-deep-purple | Deep Purple | Plum Purple |
| w3-theme-green | Green | Meadow Green |
| w3-theme-grey | Gray | Silver Gray |
| w3-theme-indigo | Indigo | Ink Indigo |
| w3-theme-khaki | Khaki | Desert Khaki |
| w3-theme-light-blue | Light Blue | Sky Blue |
| w3-theme-light-green | Light Green | Spring Green |
| w3-theme-lime | Lime | Fresh Lime |
| w3-theme-orange | Orange | Tangerine Orange |
| w3-theme-pink | Pink | Rose Pink |
| w3-theme-purple | Purple | Grape Purple |
| w3-theme-red | Red | Cherry Red |
| w3-theme-teal | Teal | Lagoon Teal |
| w3-theme-yellow | Yellow | Sun Yellow |
| w3-theme-00000 | 00000 - Plain No Colors | Quiet Slate |
| w3-theme-00001 | 00001 - Blue Gray | Harbor Steel |
| w3-theme-00002 | 00002 - Blue | Ocean Blue |
| w3-theme-00003 | 00003 - Green | Forest Green |
| w3-theme-00004 | 00004 - Purple | Orchid Purple |
| w3-theme-00005 | 00005 - Brown | Copper Brown |
| w3-theme-00006 | 00006 - Red | Crimson Red |
| w3-theme-00007 | 00007 - Indigo | Indigo Ink |
| w3-theme-00008 | 00008 - Red Turquoise | Indigo Lagoon |
| w3-theme-00009 | 00009 - DarkCyan | Deep Cyan |
| w3-theme-00010 | 00010 - Green Grass | Lime Grass |
| w3-theme-00011 | 00011 - Dark Brown | Espresso Brown |
| w3-theme-00012 | 00012 - Blue Red | Navy Ruby |
| w3-theme-00013 | 00013 - CadetBlue | Cadet Blue |
| w3-theme-00014 | 00014 - SlateGray | Harbor Blue |
| w3-theme-00015 | 00015 - CadetBlue SteelBlue | Cadet Steel |
| w3-theme-00016 | 00016 - BlueOrange | Slate Amber |
| w3-theme-00017 | 00017 - DarkOrange MidnightBlue | Midnight Ember |
| w3-theme-00018 | 00018 - Dark Theme - BlackGray | Dark Graphite |
| w3-theme-00019 | 00019 - Dark Theme - BlackGreen | Dark Moss |
| w3-theme-00020 | 00020 - Dark Theme - BlackBlue | Dark Ocean |
| w3-theme-00021 | 00021 - Dark Theme - BlackPurple | Dark Violet |
| w3-theme-00022 | 00022 - Dark Theme - BlackRed | Dark Ruby |
| w3-theme-00023 | 00023 - Dark Theme - BlackPink | Dark Rose |
| w3-theme-00024 | 00024 - Dark Theme - BlackLightGreen | Dark Emerald |
| w3-theme-00025 | 00025 - Dark Theme - BlackDarkPurple | Dark Lavender |
| w3-theme-00026 | 00026 - Dark Theme - BlackYellow | Dark Gold |
| w3-theme-00027 | 00027 - Dark Theme - BlackLightBlue | Dark Sky |
| w3-theme-00028 | 00028 - DarkRed LightBlue | Teal Coral |
| w3-theme-00029 | 00029 - Colorful Image Background | Photo Backdrop |


### How to use the predefined color themes

Go to your-drupal-site-name/admin/appearance/settings/drupal8_w3css_theme and
open "Predefined color themes" under Colors. Each color theme is shown as a
picture with its name. Select one and click save.  FYI: classes you type into
the "Region fields" section do not override the color theme because of the CSS
order.  Any predefined theme file is loaded after w3.css.


### How to change website colors

Go to your-drupal-site-name/admin/appearance/settings/drupal8_w3css_theme and
open "Region fields" under Colors in the navigation list.  Each section in the
website can be modified and click save. You can change the text color,
background color, padding, margin or borders. You can use any of the [W3.CSS
classes](https://www.w3schools.com/w3css/w3css_references.asp).


### How to change social media links

Go to www.your-drupal-site-name/admin/appearance/settings/drupal8_w3css_theme
and open "Social media links" under Footer and social.  Change any of the links
and click save.


### How to change copyright

Go to www.your-drupal-site-name/admin/appearance/settings/drupal8_w3css_theme
and open "Copyright and Credit", then the "Copyright" tab. Change the text there
and click save.


### How to disable the credit at the bottom

Go to www.your-drupal-site-name/admin/appearance/settings/drupal8_w3css_theme
and open "Copyright and Credit", then the "Credit" tab.  Uncheck the Show/Hide
Credit Text and click save.


### How to disable the whole page opacity animation

Go to your-drupal-site-name/admin/appearance/settings/drupal8_w3css_theme, open
"Region fields" under Colors, then insert w3-animate-opacity-disabled into the
"Page Wrapper" field.


### How to disable animate opacity on scroll down

Go to your-drupal-site-name/admin/appearance/settings/drupal8_w3css_theme and
open "Animation and Effects", then the "Full Opacity On Scroll" tab, and uncheck
"Animate the opacity on scroll down.".


### How to create a new custom predefined color theme

You must have a sub-theme to store the generated CSS.

Go to the [W3.CSS color
generator](https://www.w3schools.com/w3css/w3css_color_generator.asp) and create
your custom private theme. Follow the steps to generate a private theme.

Open `css/w3css-subtheme-style.css` in your sub-theme folder and paste the
generated CSS into it.

Clear caches and it is done.


### How to use the drupal8_w3css_subtheme

Inside the folder
your-drupal-site-directory/themes/drupal8_w3css_theme/drupal8_w3css_subtheme you
will have all the necessary files for start up sub-theme.


#### Steps for creating a new sub-theme for W3CSS Theme

1. Copy the sub-theme from the W3CSS Theme and paste it in your custom theme
   directory.

1. Re-name the copied folder to have the new sub-theme name. Example: my new
   sub-theme is w3css_subtheme.  I will rename the folder from
   "drupal8_w3css_subtheme" to "w3css_subtheme".

1. Open the new named sub-theme folder "w3css_subtheme" and change the following
   files' names from:
- `drupal8_w3css_subtheme.info.yml` to `w3css_subtheme.info.yml`
- `drupal8_w3css_subtheme.libraries.yml` to `w3css_subtheme.libraries.yml`

A sub-theme has no .theme file. Put hooks in a class at
`src/Hook/W3cssSubthemeHooks.php`, namespace `Drupal\w3css_subtheme\Hook`, with
a `#[Hook('preprocess_page')]` attribute on each method.

1. Open the file w3css_subtheme.info.yml in your text editor and edit the
   following:
    - Line 1: change "Drupal8 W3CSS Sub-theme" to "W3css Subtheme", the
      human-readable name.
    - Line 8: change "drupal8_w3css_subtheme/subtheme-global" to
      "w3css_subtheme/subtheme-global".

We are only changing the drupal8_w3css_subtheme to the new sub theme name. The
name "subtheme-global" is the library name and MUST match the name in
w3css_subtheme.libraries.yml.

If you would like to change the library name you MUST change it in both files.

1. Make sure the spaces in this file are not changed. The sub-theme now is ready
   to be enabled.


#### Steps for creating a new sub-theme of a sub-theme

I will be using the sub-theme Amun as an example and you can follow these steps
with any of the sub-themes.

1. Copy the amun_subtheme from the Amun Sub-Theme and paste it in your custom
   theme directory.

1. Re-name the copied folder to have the new sub-theme name. Example: my new
   sub-theme is amun_custom_subtheme.  I will rename the folder from
   "amun_subtheme" to "amun_custom_subtheme".

1. Open the new named sub-theme folder "amun_custom_subtheme" and change the
   following files' names from:
- `amun_subtheme.theme` to `amun_custom_subtheme.theme`
- `amun_subtheme.info.yml` to `amun_custom_subtheme.info.yml`
- `amun_subtheme.libraries.yml` to `amun_custom_subtheme.libraries.yml`

1. Open the file amun_custom_subtheme.info.yml in your text editor and edit the
   following:
    - Line 1: change "Amun Sub-Theme" to "Amun Custom Sub-theme", the
      human-readable name.
    - Line 8: change "amun_subtheme/amun-subtheme-global" to
      "amun_custom_subtheme/amun-subtheme-global".

1. Make sure the spaces in this file are not changed. The sub-theme now is ready
   to be enabled.


### How to change the main container's columns to equal width

Go to www.your-drupal-site-name/admin/appearance/settings/drupal8_w3css_theme
and open "Global Site Settings", then the "Main container columns' width" tab.
Check "Change the columns in the main container to (33% 33% 33%)" and click
save.


### How to create the main menu

(Drupal comes with a default main menu.)

1. You don't have to use the default main menu as your main menu. ANY menu can
   be the main menu.

1. If you want a horizontal menu just drop ANY menu in the Primary Menu region.

1. If you want a vertical menu just drop ANY menu in the Primary Menu Vertical
   region.

1. You don't have to create a special template for the vertical or horizontal
   main menu. menu--main.html.twig

1. You can create two main menus and use the two regions.

1. Note: A vertical main menu is different from a side menu in your website. See
   the [interior design
   template](https://www.w3schools.com/w3css/tryw3css_templates_interior_design.htm)
   for an example of a vertical menu.

A side menu is just a secondary menu you have on your website.  The secondary
menu can be vertical or horizontal and you can place it in left or right column.


### How to create a responsive horizontal secondary menu

Create a menu with any name. Example the new menu name is 'Titto'.  Add all the
links as usual in Drupal. The maximum number of menu levels to show is three.
This maximum number includes the initial level.  Level one is always visible on
screen resolution bigger than 992px. The two levels show on hover. On screen
resolution less than 992px all levels are hidden and they show by clicking. Go
to d8w3css/templates/navigation and clone menu--responsive-h.html.twig to your
sub theme folder and name it menu--titto.html.twig. Clear all caches and you are
done.


### How to create a responsive vertical secondary menu

Create a menu with any name.  Example the new menu name is 'Titto'. Add all the
links as usual in Drupal. The maximum number of menu levels to show is three.
This maximum number includes the initial level. Level one is always visible. The
two levels are hidden and you show them by clicking. Go to
d8w3css/templates/navigation and clone menu--responsive-v.html.twig to your sub
theme folder and name it menu--titto.html.twig. Clear all caches and you are
done.


### How to make the main vertical menu open on the right side

1. Create a sub-theme.
1. Copy `d8w3css/templates/layout/page.html.twig` to the sub-theme.
1. Open page.html.twig in the sub-theme and find the words "w3css-main-nav-vertical".
   On the same line, change "w3-animate-left" to "w3-animate-right".
1. Find the word "w3css-main-nav-open". On the same line, change "w3-left" to
   "w3-right".
1. Copy and paste the following CSS in the sub-theme style sheet:

```css
#w3css-main-nav-vertical.w3-sidebar {right: 0;}
#w3css-main-nav-open {margin: 0 0 0 10px !important;}
```


### How to remove background colors and use an image as a background

Create a sub-theme. Go to d8w3css/css/predefined and copy
w3css-photo-backdrop.css file inside your-sub-theme/css folder. Open
your-sub-theme.libraries.yml and  paste `css/w3css-photo-backdrop.css: {}` under
`css/w3css-subtheme-style.css: {}`. Note: Spacing must be aligned exactly
underneath. Create a folder inside the sub-theme and name it images. Add the
image you want to use as a background inside images folder. The image name must
be page-wrapper.jpg.


### How to use W3.CSS classes with views

Watch the video [Using W3.CSS classes with
Views](https://www.youtube.com/watch?v=wCObqmBn3Xk).

## Sub-themes and CSS layers

A sub-theme (the starter kit `drupal8_w3css_subtheme`, Amun, Amunet, Anhur or your own) writes its CSS in the `w3css.subtheme` layer. The layer order is declared once, in this theme's `css/base/w3css-layers.css`, which loads first on every page of a sub-theme; a sub-theme never repeats the `@layer` order statement. Every sub-theme ships a `css-layers.json` with `"ladder_parent": "drupal8_w3css_theme"` and a byte-identical copy of `scripts/css-layers-check.py`; the checker reads the order from this theme when it finds it next to the sub-theme and otherwise says "ladder taken from parent, not found here: skipped". A rule that must beat unlayered core, contrib or Paragraphs Bundles CSS goes in an unlayered tail of the file, behind a `:where()` root.
