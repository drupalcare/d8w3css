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
- Moving your own CSS, templates and stored configuration to the new class
  names: see docs/CLASS-MIGRATION.md. It has the old and new names side by side
  and a Drush script that does the renaming for you.

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
- The machine name, regions and library names are unchanged, so block placement
  needs no change. The setting keys are renamed: a flat key such as
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
- Menus, regions and several components gained BEM class names that start with
  w3css- . The old class names stay on the markup, so sub-theme CSS written
  against them keeps working. A copied page or menu template that prints only
  the old names still works: the theme's script adds the new name to those
  elements when the page loads.
- The theme's own CSS no longer lists the old menu names (.ul-parent, .ul-child,
  .tMenu, .mobile-nav-responsive, .ul-responsive-h, .dropdown-arrow and the
  clickable-dropdown-menu-fa-down classes) beside the new ones. Old markup is
  styled once the script has added the new name.
- Markup changes you may see in a copied template: the open and close side menu
  toggles are buttons, the site name is a heading on the front page only, and
  messages use one role (alert for errors and warnings, status otherwise).
- The theme no longer attaches Claro libraries, except the search results list
  when Claro is installed. A site that relied on Claro's indent for comments
  gets the theme's 40px indent.
- amun, amunet and anhur build on this theme and are released separately. Test
  each against 3.0 before updating a site that uses one.


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

Open `css/d8w3css-subtheme-style.css` in your sub-theme folder and paste the
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
    - Line 8: change "drupal8_w3css_subtheme/d8w3css-subtheme-global" to
      "w3css_subtheme/d8w3css-subtheme-global".

We are only changing the drupal8_w3css_subtheme to the new sub theme name. The
name "d8w3css-subtheme-global" is the library name and MUST match the name in
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
1. Open page.html.twig in the sub-theme and find the words "main-navigation-v".
   On the same line, change "w3-animate-left" to "w3-animate-right".
1. Find the word "open-nav-inner". On the same line, change "w3-left" to
   "w3-right".
1. Copy and paste the following CSS in the sub-theme style sheet:

```css
#main-navigation-v.w3-sidebar {right: 0;}
#open-nav-inner {margin: 0 0 0 10px !important;}
```


### How to remove background colors and use an image as a background

Create a sub-theme. Go to d8w3css/css/w3-css-theme-custom and copy
w3-theme-00029.css file inside your-sub-theme/css folder. Open
your-sub-theme.libraries.yml and  paste `css/w3-theme-00029.css: {}` under
`css/d8w3css-subtheme-style.css: {}`. Note: Spacing must be aligned exactly
underneath. Create a folder inside the sub-theme and name it images. Add the
image you want to use as a background inside images folder. The image name must
be page-wrapper.jpg.


### How to use W3.CSS classes with views

Watch the video [Using W3.CSS classes with
Views](https://www.youtube.com/watch?v=wCObqmBn3Xk).
