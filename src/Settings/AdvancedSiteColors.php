<?php

declare(strict_types=1);

namespace Drupal\drupal8_w3css_theme\Settings;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Advanced color settings for theme regions.
 *
 * Three sections of the form: a Blueprint of the page, a reference of the CSS
 * classes, and the region fields. Each field sets #parents to its settings key,
 * and core builds the field's id from #parents, however deep it sits. The
 * Blueprint's anchors are built the same way.
 */
final class AdvancedSiteColors extends SettingsSection {

  /**
   * The vertical tabs holding the panes of the CSS class reference.
   */
  private const CLASSES_TABS = 'w3_colors_classes_tabs';

  /**
   * The vertical tabs holding the panes of the region fields.
   */
  private const REGIONS_TABS = 'w3_colors_regions_tabs';

  /**
   * The region fields: group, then rows as they sit on the page, then keys.
   *
   * Every region_colors key in the schema is in exactly one group.
   */
  private const REGION_GROUPS = [
    'page' => [
      ['region_colors.page_wrapper'],
      ['region_colors.header'],
      ['region_colors.primary_menu'],
      ['region_colors.welcome_text', 'region_colors.highlighted'],
    ],
    'top' => [
      ['region_colors.top_container'],
      ['region_colors.top_first', 'region_colors.top_second', 'region_colors.top_third'],
    ],
    'main' => [
      ['region_colors.page_title'],
      ['region_colors.main'],
      ['region_colors.breadcrumb'],
      ['region_colors.sidebar_first', 'region_colors.content', 'region_colors.sidebar_second'],
    ],
    'bottom' => [
      ['region_colors.bottom_container'],
      [
        'region_colors.bottom_first',
        'region_colors.bottom_second',
        'region_colors.bottom_third',
        'region_colors.bottom_fourth',
      ],
    ],
    'footer' => [
      ['region_colors.footer_container'],
      ['region_colors.footer_first', 'region_colors.footer_second', 'region_colors.footer_third'],
      ['region_colors.footer_menu'],
      ['region_colors.copyright'],
    ],
  ];

  /**
   * The color names each family of W3.CSS classes comes in.
   */
  private const COLORS = [
    'base' => [
      'red',
      'pink',
      'purple',
      'deep-purple',
      'indigo',
      'blue',
      'light-blue',
      'cyan',
      'aqua',
      'teal',
      'green',
      'light-green',
      'lime',
      'sand',
      'khaki',
      'yellow',
      'amber',
      'orange',
      'deep-orange',
      // cspell:disable-next-line
      'blue-grey',
      'brown',
      // cspell:disable-next-line
      'light-grey',
      // cspell:disable-next-line
      'grey',
      // cspell:disable-next-line
      'dark-grey',
      'black',
      'white',
      'pale-red',
      'pale-yellow',
      'pale-green',
      'pale-blue',
    ],
    'link' => [
      'red',
      'green',
      'blue',
      'yellow',
      'white',
      'black',
      // cspell:disable-next-line
      'grey',
      'amber',
      'aqua',
      'light-blue',
      'brown',
      'cyan',
      // cspell:disable-next-line
      'blue-grey',
      'blue-gray',
      'light-green',
      'indigo',
      'khaki',
      'lime',
      'orange',
      'deep-orange',
      'pink',
      'purple',
      'deep-purple',
      'sand',
      'teal',
      'light-gray',
      // cspell:disable-next-line
      'light-grey',
      'dark-gray',
      // cspell:disable-next-line
      'dark-grey',
      'pale-red',
      'pale-yellow',
      'pale-green',
      'pale-blue',
    ],
    'button' => [
      'red',
      'green',
      'blue',
      'yellow',
      'white',
      'black',
      'gray',
      // cspell:disable-next-line
      'grey',
      'amber',
      'aqua',
      'light-blue',
      'brown',
      'cyan',
      // cspell:disable-next-line
      'blue-grey',
      'blue-gray',
      'light-green',
      'indigo',
      'khaki',
      'lime',
      'orange',
      'deep-orange',
      'pink',
      'purple',
      'deep-purple',
      'sand',
      'teal',
      'light-gray',
      // cspell:disable-next-line
      'light-grey',
      'dark-gray',
      // cspell:disable-next-line
      'dark-grey',
      'pale-red',
      'pale-yellow',
      'pale-green',
      'pale-blue',
    ],
  ];

  /**
   * The kinds of class in the reference.
   */
  private const CLASS_KINDS = [
    'background' => [
      'prefix' => 'w3-',
      'colors' => 'base',
      'demo' => 'https://w3css.flashwebcenter.com/node/169',
    ],
    'background_hover' => [
      'prefix' => 'w3-hover-',
      'colors' => 'base',
      'demo' => 'https://w3css.flashwebcenter.com/node/168',
    ],
    'text' => [
      'prefix' => 'w3-text-',
      'colors' => 'base',
      'demo' => 'https://w3css.flashwebcenter.com/node/124',
    ],
    'text_hover' => [
      'prefix' => 'w3-hover-text-',
      'colors' => 'base',
      'demo' => 'https://w3css.flashwebcenter.com/node/143',
    ],
    'link_text' => [
      'prefix' => 'w3-link-',
      'colors' => 'base',
      'demo' => 'https://w3css.flashwebcenter.com/node/135',
    ],
    'link_background' => [
      'prefix' => 'w3-link-bg-',
      'colors' => 'link',
      'demo' => 'https://w3css.flashwebcenter.com/node/137',
    ],
    'link_background_hover' => [
      'prefix' => 'w3-link-hover-',
      'colors' => 'link',
      'demo' => 'https://w3css.flashwebcenter.com/node/136',
    ],
    'button' => [
      'prefix' => 'w3-button-',
      'colors' => 'button',
      'demo' => 'https://w3css.flashwebcenter.com/node/162',
    ],
    'button_hover' => [
      'prefix' => 'w3-button-hover-',
      'colors' => 'button',
      'demo' => 'https://w3css.flashwebcenter.com/node/161',
    ],
  ];

  /**
   * The Blueprint: the page's regions, in the order and nesting they render.
   *
   * Each row names its field. 'class' is the default class. 'label' and 'note'
   * are keys of the translated text in blueprintText(). 'rows' stack inside the
   * box and 'cells' sit side by side.
   */
  private const BLUEPRINT = [
    ['field' => 'region_colors.primary_menu', 'label' => 'menu_vertical', 'class' => 'w3-theme'],
    [
      'field' => 'region_colors.page_wrapper',
      'rows' => [
        ['field' => 'region_colors.header', 'class' => 'w3-theme-l4'],
        ['field' => 'region_colors.primary_menu', 'label' => 'menu_horizontal', 'class' => 'w3-theme'],
        ['field' => 'region_colors.welcome_text', 'class' => 'w3-theme-l5', 'note' => 'front_only'],
        ['field' => 'region_colors.highlighted', 'class' => 'w3-theme-l2'],
        [
          'field' => 'region_colors.top_container',
          'class' => 'w3-theme-l4',
          'note' => 'front_only',
          'cells' => [
            ['field' => 'region_colors.top_first', 'class' => 'l5'],
            ['field' => 'region_colors.top_second', 'class' => 'l5'],
            ['field' => 'region_colors.top_third', 'class' => 'l5'],
          ],
        ],
        ['field' => 'region_colors.page_title', 'class' => 'w3-theme-d5', 'note' => 'not_front'],
        [
          'field' => 'region_colors.main',
          'class' => 'w3-theme-l5',
          'note' => 'columns',
          'rows' => [
            ['field' => 'region_colors.breadcrumb'],
          ],
          'cells' => [
            ['field' => 'region_colors.sidebar_first'],
            ['field' => 'region_colors.content'],
            ['field' => 'region_colors.sidebar_second'],
          ],
        ],
        [
          'field' => 'region_colors.bottom_container',
          'class' => 'w3-theme-l2',
          'note' => 'front_only',
          'cells' => [
            ['field' => 'region_colors.bottom_first', 'class' => 'l4'],
            ['field' => 'region_colors.bottom_second', 'class' => 'l4'],
            ['field' => 'region_colors.bottom_third', 'class' => 'l4'],
            ['field' => 'region_colors.bottom_fourth', 'class' => 'l4'],
          ],
        ],
        [
          'field' => 'region_colors.footer_container',
          'class' => 'w3-theme-d3',
          'cells' => [
            ['field' => 'region_colors.footer_first', 'class' => 'l4'],
            ['field' => 'region_colors.footer_second', 'class' => 'l5'],
            ['field' => 'region_colors.footer_third', 'class' => 'l4'],
          ],
        ],
        ['field' => 'region_colors.footer_menu', 'class' => 'w3-theme-d4'],
        ['field' => 'region_colors.copyright', 'class' => 'w3-theme-d5'],
      ],
    ],
  ];

  /**
   * One box of the Blueprint.
   */
  private const BLUEPRINT_BOX_TEMPLATE = '<div class="{{ classes }}"><a href="#{{ anchor }}">{{ label }}</a>{% if theme_class or note %}<small>{{ theme_class }}{% if theme_class and note %} · {% endif %}{{ note }}</small>{% endif %}{{ rows }}{% if cells %}<div class="w3css-settings__bp-cells">{{ cells }}</div>{% endif %}</div>';

  /**
   * One pane of the CSS class reference.
   */
  private const CLASS_PANE_TEMPLATE = '<div class="w3css-settings__kind"><strong>{{ name }}</strong> <code>{{ pattern }}</code> <span class="w3css-settings__kind-count">{{ count }}</span> <a class="w3css-settings__kind-demo" href="{{ demo }}" target="_blank" rel="noopener">{{ demo_label }}</a></div><div class="w3css-settings__chips">{% for chip in chips %}<code class="w3css-settings__swatch w3css-settings__swatch--{{ chip }}">{{ chip }}</code>{% endfor %}</div>';

  /**
   * {@inheritdoc}
   */
  public function build(array &$form): void {
    $form['drupal8_w3css_theme_settings']['w3_colors_blueprint'] = $this->blueprint();
    $form['drupal8_w3css_theme_settings']['w3_colors_classes'] = $this->classReference();
    $form['drupal8_w3css_theme_settings']['w3_colors_regions'] = $this->regionFields();
  }

  /**
   * Builds the Blueprint of the page.
   *
   * @return array<string, mixed>
   *   The section.
   */
  private function blueprint(): array {
    $labels = $this->fieldLabels() + $this->blueprintText();
    $group = $this->section(
      $this->t('Blueprint'),
      $this->t('Where each region sits on the page'),
      $this->t('Select a name to go to its field. Cell labels give the default W3.CSS theme class. Regions are visible on all pages unless marked.'),
    );
    $group['w3_colors_blueprint_map'] = [
      '#type' => 'inline_template',
      '#template' => '<div class="w3css-settings__blueprint">{{ rows }}</div>',
      '#context' => ['rows' => $this->blueprintBoxes(self::BLUEPRINT, $labels)],
    ];
    return $group;
  }

  /**
   * Builds the boxes of one level of the Blueprint.
   *
   * The boxes sit in an inline template's context, so the form builder does
   * not walk them as form elements. Twig escapes every value.
   *
   * @param array<int, array<string, mixed>> $boxes
   *   The boxes, in the shape of self::BLUEPRINT.
   * @param array<string, \Drupal\Core\StringTranslation\TranslatableMarkup> $text
   *   The translated labels and notes.
   *
   * @return array<int, array<string, mixed>>
   *   One render array per box.
   */
  private function blueprintBoxes(array $boxes, array $text): array {
    $build = [];
    foreach ($boxes as $box) {
      $rows = $this->blueprintBoxes($box['rows'] ?? [], $text);
      $cells = $this->blueprintBoxes($box['cells'] ?? [], $text);
      $build[] = [
        '#type' => 'inline_template',
        '#template' => self::BLUEPRINT_BOX_TEMPLATE,
        '#context' => [
          'classes' => $rows || $cells ? 'w3css-settings__bp w3css-settings__bp--box' : 'w3css-settings__bp',
          'anchor' => 'edit-' . strtr($box['field'], ['_' => '-', '.' => '-']),
          'label' => $text[$box['label'] ?? $box['field']],
          'theme_class' => $box['class'] ?? '',
          'note' => isset($box['note']) ? $text[$box['note']] : '',
          'rows' => $rows,
          'cells' => $cells,
        ],
      ];
    }
    return $build;
  }

  /**
   * Builds the reference of CSS classes, one tab per kind of class.
   *
   * @return array<string, mixed>
   *   The section.
   */
  private function classReference(): array {
    $group = $this->section(
      $this->t('CSS classes'),
      $this->t('@count kinds of class', ['@count' => count(self::CLASS_KINDS)]),
      $this->t('To use these classes, the w3.css library must be active. Copy any class into a region field. Separate multiple classes with a space.'),
    );
    $group[self::CLASSES_TABS] = ['#type' => 'vertical_tabs'];
    $labels = $this->classKindLabels();
    foreach (self::CLASS_KINDS as $kind => $definition) {
      $colors = self::COLORS[$definition['colors']];
      $group['w3_colors_class_' . $kind] = [
        '#type' => 'details',
        '#title' => $labels[$kind]['tab'],
        '#group' => self::CLASSES_TABS,
        'w3_colors_class_' . $kind . '_list' => [
          '#type' => 'inline_template',
          '#template' => self::CLASS_PANE_TEMPLATE,
          '#context' => [
            'name' => $labels[$kind]['name'],
            'pattern' => $definition['prefix'] . '[color]',
            'count' => $this->formatPlural(count($colors), '1 class', '@count classes'),
            'demo' => $definition['demo'],
            'demo_label' => $this->t('Live demo'),
            'chips' => array_map(static fn (string $color): string => $definition['prefix'] . $color, $colors),
          ],
        ],
      ];
    }
    return $group;
  }

  /**
   * Builds the region fields, one tab per group of regions.
   *
   * @return array<string, mixed>
   *   The section.
   */
  private function regionFields(): array {
    $fields = array_merge(...array_merge(...array_values(self::REGION_GROUPS)));
    $group = $this->section(
      $this->t('Region fields'),
      $this->t('@fields fields in @groups groups', [
        '@fields' => count($fields),
        '@groups' => count(self::REGION_GROUPS),
      ]),
      $this->t('Each field adds its classes to that part of the page.'),
    );
    $group[self::REGIONS_TABS] = ['#type' => 'vertical_tabs'];
    $titles = $this->regionGroupLabels();
    $labels = $this->fieldLabels();
    foreach (self::REGION_GROUPS as $name => $rows) {
      $pane = [
        '#type' => 'details',
        '#title' => $titles[$name],
        '#group' => self::REGIONS_TABS,
      ];
      foreach ($rows as $index => $keys) {
        $row = [
          '#type' => 'container',
          '#attributes' => ['class' => ['w3css-settings__row']],
        ];
        foreach ($keys as $key) {
          $row[str_replace('.', '_', $key)] = [
            '#parents' => explode('.', $key),
            '#type' => 'textfield',
            '#title' => $labels[$key],
            '#default_value' => $this->settings->get($key),
            '#attributes' => ['spellcheck' => 'false'],
            '#element_validate' => [[self::class, 'validateClassList']],
          ];
        }
        $pane['w3_colors_region_' . $name . '_row_' . $index] = $row;
      }
      $group['w3_colors_region_' . $name] = $pane;
    }
    return $group;
  }

  /**
   * Accepts only CSS class names, separated by spaces.
   *
   * The value is printed into a class attribute, so anything else would only
   * produce broken or unexpected class tokens. Extra spaces are removed.
   *
   * @param array<string, mixed> $element
   *   The region field.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public static function validateClassList(array &$element, FormStateInterface $form_state): void {
    $value = trim((string) ($element['#value'] ?? ''));
    $tokens = $value === '' ? [] : preg_split('/\s+/', $value);
    foreach ($tokens as $token) {
      if (!preg_match('/^-?[A-Za-z_][A-Za-z0-9_:\/.-]*$/', $token)) {
        $form_state->setError($element, new TranslatableMarkup('%field accepts only CSS class names separated by spaces, for example %example. %token is not a class name.', [
          '%field' => $element['#title'],
          '%example' => 'w3-theme-d2 w3-text-white',
          '%token' => $token,
        ]));
        return;
      }
    }
    $form_state->setValueForElement($element, implode(' ', $tokens));
  }

  /**
   * Starts one of the three sections.
   *
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup $title
   *   The title.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup $hint
   *   The short hint shown in the navigation.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup $description
   *   The description.
   *
   * @return array<string, mixed>
   *   The details element.
   */
  private function section(TranslatableMarkup $title, TranslatableMarkup $hint, TranslatableMarkup $description): array {
    return [
      '#type' => 'details',
      '#title' => $title,
      '#description' => $description,
      self::HINT => $hint,
    ];
  }

  /**
   * Returns the label of each region field.
   *
   * The Blueprint shows these same labels, so the two cannot disagree.
   *
   * @return array<string, \Drupal\Core\StringTranslation\TranslatableMarkup>
   *   The labels, keyed by setting.
   */
  private function fieldLabels(): array {
    return [
      'region_colors.page_wrapper' => $this->t('Page Wrapper'),
      'region_colors.header' => $this->t('Header'),
      'region_colors.primary_menu' => $this->t('Main Menu'),
      'region_colors.welcome_text' => $this->t('Welcome Text'),
      'region_colors.highlighted' => $this->t('Highlighted'),
      'region_colors.top_container' => $this->t('Top Container Wrapper'),
      'region_colors.top_first' => $this->t('Top First'),
      'region_colors.top_second' => $this->t('Top Second'),
      'region_colors.top_third' => $this->t('Top Third'),
      'region_colors.page_title' => $this->t('Page Title'),
      'region_colors.main' => $this->t('Main Container Wrapper'),
      'region_colors.breadcrumb' => $this->t('Breadcrumb'),
      'region_colors.sidebar_first' => $this->t('Sidebar First'),
      'region_colors.content' => $this->t('W3CSS Content'),
      'region_colors.sidebar_second' => $this->t('Sidebar Second'),
      'region_colors.bottom_container' => $this->t('Bottom Container Wrapper'),
      'region_colors.bottom_first' => $this->t('Bottom First'),
      'region_colors.bottom_second' => $this->t('Bottom Second'),
      'region_colors.bottom_third' => $this->t('Bottom Third'),
      'region_colors.bottom_fourth' => $this->t('Bottom Fourth'),
      'region_colors.footer_container' => $this->t('Footer Container Wrapper'),
      'region_colors.footer_first' => $this->t('Footer First'),
      'region_colors.footer_second' => $this->t('Footer Second'),
      'region_colors.footer_third' => $this->t('Footer Third'),
      'region_colors.footer_menu' => $this->t('Footer Menu'),
      'region_colors.copyright' => $this->t('Copyright'),
    ];
  }

  /**
   * Returns the Blueprint's own labels and notes.
   *
   * @return array<string, \Drupal\Core\StringTranslation\TranslatableMarkup>
   *   The text, keyed as self::BLUEPRINT refers to it.
   */
  private function blueprintText(): array {
    return [
      'menu_vertical' => $this->t('Main Menu, vertical'),
      'menu_horizontal' => $this->t('Main Menu, horizontal'),
      'front_only' => $this->t('Front page only'),
      'not_front' => $this->t('All pages except front page'),
      'columns' => $this->t('Columns 25-50-25 or 33-33-33'),
    ];
  }

  /**
   * Returns the tab label and pane heading of each kind of class.
   *
   * @return array<string, array<string, \Drupal\Core\StringTranslation\TranslatableMarkup>>
   *   The labels, keyed by kind.
   */
  private function classKindLabels(): array {
    return [
      'background' => [
        'tab' => $this->t('Background'),
        'name' => $this->t('Background colors'),
      ],
      'background_hover' => [
        'tab' => $this->t('Background hover'),
        'name' => $this->t('Background hover colors'),
      ],
      'text' => [
        'tab' => $this->t('Text'),
        'name' => $this->t('Text colors'),
      ],
      'text_hover' => [
        'tab' => $this->t('Text hover'),
        'name' => $this->t('Text hover colors'),
      ],
      'link_text' => [
        'tab' => $this->t('Link text'),
        'name' => $this->t('Link text colors'),
      ],
      'link_background' => [
        'tab' => $this->t('Link background'),
        'name' => $this->t('Link background colors'),
      ],
      'link_background_hover' => [
        'tab' => $this->t('Link background hover'),
        'name' => $this->t('Link background hover colors'),
      ],
      'button' => [
        'tab' => $this->t('Button'),
        'name' => $this->t('Button colors'),
      ],
      'button_hover' => [
        'tab' => $this->t('Button hover'),
        'name' => $this->t('Button hover colors'),
      ],
    ];
  }

  /**
   * Returns the tab label of each group of regions.
   *
   * @return array<string, \Drupal\Core\StringTranslation\TranslatableMarkup>
   *   The labels, keyed by group.
   */
  private function regionGroupLabels(): array {
    return [
      'page' => $this->t('Page and Header'),
      'top' => $this->t('Top'),
      'main' => $this->t('Main'),
      'bottom' => $this->t('Bottom'),
      'footer' => $this->t('Footer'),
    ];
  }

}
