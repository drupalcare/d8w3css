<?php

declare(strict_types=1);

namespace Drupal\drupal8_w3css_theme\Settings;

use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Render\Markup;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;

/**
 * Arranges the built sections: one navigation, the chosen section beside it.
 *
 * Runs after every section has built, and only moves elements around. Each
 * field sets its own #parents, so where it sits does not change its settings
 * key. Core saves a settings group whole, so all of a group's fields must stay
 * on the form.
 * A vertical_tabs element has no #input, and core marks its __active_tab field
 * as a clean value key, so the tabs add nothing to the saved config either.
 *
 * With no script every section stays open, one after another, and the
 * navigation is a list of jump links. The script then shows one at a time.
 */
final class SettingsLayout {
  use StringTranslationTrait;

  /**
   * The form key that holds every section of this theme.
   */
  private const ROOT = 'drupal8_w3css_theme_settings';

  /**
   * Every section in page order, with its navigation group and icon.
   *
   * The one home of the sections and the navigation. Icons are files in this
   * theme's images/icons/settings; see the SOURCE.md there. Core's three
   * sections join by #group, never a moved array, because core reads
   * $form['logo']['settings']['logo_upload'] by path.
   */
  private const NAV = [
    'w3_settings_global' => ['group' => 'site', 'icon' => 'tune'],
    'w3_settings_effects' => ['group' => 'site', 'icon' => 'crop_free'],
    'w3_settings_libraries' => ['group' => 'site', 'icon' => 'font_download'],
    'logo' => ['group' => 'site', 'icon' => 'branding_watermark'],
    'favicon' => ['group' => 'site', 'icon' => 'tab'],
    'theme_settings' => ['group' => 'site', 'icon' => 'toggle_on'],
    'w3_theme_current' => ['group' => 'colors', 'icon' => 'palette'],
    'w3_colors_regions' => ['group' => 'colors', 'icon' => 'view_column'],
    'w3_colors_blueprint' => ['group' => 'colors', 'icon' => 'view_sidebar'],
    'w3_colors_classes' => ['group' => 'colors', 'icon' => 'list'],
    'social_icon' => ['group' => 'footer', 'icon' => 'share'],
    'w3_settings_credit_copyright' => ['group' => 'footer', 'icon' => 'copyright'],
  ];

  /**
   * The icon of each navigation group and of the parts the template names.
   */
  private const PART_ICON = [
    'site' => 'language',
    'colors' => 'palette',
    'footer' => 'vertical_align_bottom',
    'other' => 'extension',
    'sections' => 'toc',
    'block_layout' => 'dashboard',
  ];

  /**
   * The navigation. Twig escapes every value except the icons, which are files.
   */
  private const NAV_TEMPLATE = <<<'TWIG'
<nav class="w3css-nav" aria-label="{{ nav_label }}">
  <details class="w3css-nav__menu" open>
    <summary class="w3css-nav__toggle">{{ icons.sections }}{{ nav_label }}<span class="w3css-nav__current"></span></summary>
    {% for group in groups %}
      <h2 class="w3css-nav__group">{{ group.icon }}{{ group.title }}</h2>
      <ul class="w3css-nav__list">
        {% for row in group.rows %}
          <li class="w3css-nav__item">
            <a class="w3css-nav__link" href="#{{ row.target }}">
              {{ row.icon }}
              <span class="w3css-nav__name">{{ row.label }}</span>
              {% if row.detail %}<small class="w3css-nav__detail">{{ row.detail }}</small>{% endif %}
            </a>
          </li>
        {% endfor %}
      </ul>
    {% endfor %}
    {% if block_layout_url %}<p class="w3css-nav__note"><a href="{{ block_layout_url }}">{{ icons.block_layout }}{{ block_layout }}</a></p>{% endif %}
    <template data-w3css-icon="other">{{ icons.other }}</template>
  </details>
</nav>
TWIG;

  /**
   * The inline icons read so far, by file name, so each is read once a request.
   *
   * @var array<string, \Drupal\Component\Render\MarkupInterface|string>
   */
  private array $iconMarkup = [];

  /**
   * Applies the layout to the theme settings form.
   *
   * @param array<string, mixed> $form
   *   The form, with every section already built.
   */
  public function arrange(array &$form): void {
    $form['#attributes']['class'][] = 'w3css-settings';
    $form['#attached']['library'][] = 'drupal8_w3css_theme/d8w3css-settings-form';

    $built = $form[self::ROOT];
    // The navigation sorts first, then a stale keys warning, then the sections,
    // then anything another module adds, then core's actions at weight 100.
    $form[self::ROOT] = [
      '#type' => 'container',
      '#weight' => -90,
      '#attributes' => ['class' => ['w3css-sections']],
    ];

    $titles = $this->navGroups();
    $rows = array_fill_keys(array_keys($titles), []);
    $weight = 0;
    foreach (self::NAV as $key => $nav) {
      $weight++;
      // Core's sections stay where core reads them. Each is absent when its
      // feature is switched off for the theme.
      $in_root = !isset($form[$key]);
      if ($in_root) {
        $form[self::ROOT][$key] = $this->section($key, $built);
        $section = &$form[self::ROOT][$key];
      }
      else {
        $form[$key]['#group'] = self::ROOT;
        $section = &$form[$key];
      }
      if ($section === [] || ($section['#access'] ?? TRUE) === FALSE) {
        if ($in_root) {
          unset($form[self::ROOT][$key]);
        }
        unset($section);
        continue;
      }
      $section['#weight'] = $weight;
      $section['#open'] = TRUE;
      $section['#id'] = self::sectionId($key);
      $section['#attributes']['data-w3css-section'] = $key;
      $rows[$nav['group']][] = [
        'icon' => $this->icon($nav['icon']),
        'label' => (string) ($section['#title'] ?? ''),
        'target' => $section['#id'],
        'detail' => $section[SettingsSection::HINT] ?? NULL,
      ];
      unset($section);
    }

    $form['d8w3css_nav'] = $this->navigation($form, $titles, $rows);
  }

  /**
   * The id of a section's details element, the value core would generate.
   *
   * @param string $key
   *   The section's form key.
   */
  private static function sectionId(string $key): string {
    return 'edit-' . str_replace('_', '-', $key);
  }

  /**
   * The navigation groups, in order.
   *
   * @return array<string, \Drupal\Core\StringTranslation\TranslatableMarkup>
   *   The heading of each group, keyed as in NAV.
   */
  private function navGroups(): array {
    return [
      'site' => $this->t('Site-wide'),
      'colors' => $this->t('Colors'),
      'footer' => $this->t('Footer and social'),
    ];
  }

  /**
   * Builds the navigation element.
   *
   * @param array<string, mixed> $form
   *   The form.
   * @param array<string, \Drupal\Core\StringTranslation\TranslatableMarkup> $titles
   *   The heading of each group.
   * @param array<string, list<array<string, mixed>>> $rows
   *   The items of each group.
   *
   * @return array<string, mixed>
   *   The inline template.
   */
  private function navigation(array $form, array $titles, array $rows): array {
    $groups = [];
    foreach ($titles as $group => $title) {
      if ($rows[$group]) {
        $groups[] = [
          'title' => $title,
          'icon' => $this->icon(self::PART_ICON[$group]),
          'rows' => $rows[$group],
        ];
      }
    }
    return [
      '#type' => 'inline_template',
      '#weight' => -110,
      '#template' => self::NAV_TEMPLATE,
      '#context' => [
        'nav_label' => $this->t('Sections'),
        'block_layout' => $this->t('Block layout'),
        'block_layout_url' => $this->blockLayoutUrl($form),
        'groups' => $groups,
        'icons' => [
          'sections' => $this->icon(self::PART_ICON['sections']),
          'block_layout' => $this->icon(self::PART_ICON['block_layout']),
          'other' => $this->icon(self::PART_ICON['other']),
        ],
      ],
      // The Block layout link depends on the person's permissions.
      '#cache' => ['contexts' => ['user.permissions']],
    ];
  }

  /**
   * The Block layout page of the edited theme, when this person may open it.
   *
   * The access check also answers FALSE when the Block module is off, so the
   * route is never turned into a URL without existing.
   *
   * @param array<string, mixed> $form
   *   The form.
   *
   * @return string|null
   *   The URL, or NULL when there is no link to show.
   */
  private function blockLayoutUrl(array $form): ?string {
    $config = $form['config_key']['#value'] ?? NULL;
    if (!is_string($config) || !str_ends_with($config, '.settings')) {
      return NULL;
    }
    $url = Url::fromRoute('block.admin_display_theme', [
      'theme' => substr($config, 0, -strlen('.settings')),
    ]);
    return $url->access() ? $url->toString() : NULL;
  }

  /**
   * The inline icon of a navigation part, hidden from assistive technology.
   *
   * The name comes only from this class's constants and the file only from this
   * theme's folder, never from input or settings, which is why it may be printed
   * unescaped. Fill follows the text around the icon, from the stylesheet.
   *
   * @param string $name
   *   The file name, without its extension.
   *
   * @return \Drupal\Component\Render\MarkupInterface|string
   *   The SVG, or an empty string when the file is missing or is not an SVG.
   */
  private function icon(string $name): MarkupInterface|string {
    if (!isset($this->iconMarkup[$name])) {
      $file = dirname(__DIR__, 2) . '/images/icons/settings/' . $name . '.svg';
      $svg = is_file($file) ? (string) file_get_contents($file) : '';
      $this->iconMarkup[$name] = str_starts_with($svg, '<svg ')
        ? Markup::create('<svg class="w3css-icon" aria-hidden="true" focusable="false" ' . substr($svg, 5))
        : '';
    }
    return $this->iconMarkup[$name];
  }

  /**
   * Builds one section of this theme from the sections as they were built.
   *
   * @param string $key
   *   A key of NAV.
   * @param array<string, array<string, mixed>> $built
   *   The sections as they were built.
   *
   * @return array<string, mixed>
   *   The section, empty when it does not exist.
   */
  private function section(string $key, array $built): array {
    return match ($key) {
      'w3_settings_global' => $this->tabbedGroup($built, $this->t('Global Site Settings'), $this->t('Width, columns, login pages, breadcrumb'), [
        'w3_website_width' => $this->t("Website's Width"),
        'w3_equal_width' => $this->t("Main Container Columns' Width"),
        'w3_layout_user' => $this->t('Login Pages Layout'),
        'w3_breadcrumb_page_title' => $this->t('Breadcrumb'),
      ]),
      'w3_settings_effects' => $this->tabbedGroup($built, $this->t('Animation and Effects'), $this->t('Border, opacity on scroll, text'), [
        'w3_border_animation' => $this->t('Border Animation'),
        'w3_full_opacity' => $this->t('Full Opacity On Scroll'),
        'w3_sitename_animation' => $this->t('Text Animation'),
      ]),
      'w3_settings_libraries' => $this->tabbedGroup($built, $this->t('Libraries and Fonts'), $this->t('Font Awesome, Google Fonts'), [
        'w3_fontawesome_libraries' => $this->t('Font Awesome'),
        'w3_external_libraries' => $this->t('Google Fonts'),
      ]),
      'w3_theme_current' => $this->titled($built['w3_theme_current'], $this->t('Predefined Color Themes'), $this->t('One palette for the whole site')),
      'social_icon' => $this->socialSection($built['social_icon']),
      'w3_settings_credit_copyright' => $this->tabbedGroup($built, $this->t('Copyright and Credit'), $this->t('Copyright line, credit link'), [
        'copyright' => $this->t('Copyright'),
        'credit_link' => $this->t('Credit'),
      ]),
      default => $built[$key] ?? [],
    };
  }

  /**
   * Builds a section whose panes are existing sections.
   *
   * @param array<string, array<string, mixed>> $sections
   *   The sections as they were built.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup $title
   *   The title of the group.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup $hint
   *   The short hint shown in the navigation.
   * @param array<string, \Drupal\Core\StringTranslation\TranslatableMarkup> $panes
   *   The tab label of each section in the group, keyed by section key.
   *
   * @return array<string, mixed>
   *   The group's render array.
   */
  private function tabbedGroup(array $sections, TranslatableMarkup $title, TranslatableMarkup $hint, array $panes): array {
    $tabs_key = array_key_first($panes) . '_tabs';
    $group = $this->titled(['#type' => 'details'], $title, $hint);
    $group[$tabs_key] = ['#type' => 'vertical_tabs'];
    foreach ($panes as $key => $label) {
      $group[$key] = [
        '#title' => $label,
        '#group' => $tabs_key,
      ] + $sections[$key];
    }
    return $group;
  }

  /**
   * Sets a section's title and the hint the navigation shows under its name.
   *
   * @param array<string, mixed> $element
   *   The details element.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup $title
   *   The new title.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup $hint
   *   The short hint shown in the navigation.
   *
   * @return array<string, mixed>
   *   The element.
   */
  private function titled(array $element, TranslatableMarkup $title, TranslatableMarkup $hint): array {
    $element['#title'] = $title;
    $element[SettingsSection::HINT] = $hint;
    return $element;
  }

  /**
   * Splits the social section into a display tab and a links tab.
   *
   * @param array<string, mixed> $social
   *   The social links section.
   *
   * @return array<string, mixed>
   *   The section with its fields inside two tabs.
   */
  private function socialSection(array $social): array {
    $social = $this->titled($social, $this->t('Social Media Links'), $this->t('Display, platform links'));
    $display = [
      '#type' => 'details',
      '#title' => $this->t('Display'),
      '#group' => 'social_icon_tabs',
    ];
    $links = [
      '#type' => 'details',
      '#title' => $this->t('Platform Links'),
      '#group' => 'social_icon_tabs',
    ];
    foreach (Element::children($social) as $key) {
      if (str_starts_with($key, 'social_icons_links_')) {
        $links[$key] = $social[$key];
      }
      else {
        $display[$key] = $social[$key];
      }
      unset($social[$key]);
    }
    $social['social_icon_tabs'] = ['#type' => 'vertical_tabs'];
    $social['social_icon_display'] = $display;
    $social['social_icon_links'] = $links;
    return $social;
  }

}
