<?php

declare(strict_types=1);

namespace Drupal\drupal8_w3css_theme\Settings;

use Drupal\Component\Render\MarkupInterface;
use Drupal\Component\Utility\Html;
use Drupal\Core\Extension\ThemeExtensionList;
use Drupal\Core\Extension\ThemeSettingsProvider;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\Radios;
use Drupal\Core\Render\Markup;
use Drupal\drupal8_w3css_theme\ColorThemeFamilies;

/**
 * W3.CSS predefined color themes settings.
 */
final class PredefinedThemes extends SettingsSection {

  public function __construct(
    ThemeSettingsProvider $themeSettings,
    private readonly ThemeExtensionList $themeList,
  ) {
    parent::__construct($themeSettings);
  }

  /**
   * {@inheritdoc}
   */
  public function build(array &$form): void {
    $form['drupal8_w3css_theme_settings']['w3_theme_current'] = [
      '#type' => 'details',
      '#title' => $this->t('Predefined color themes - W3CSS Theme'),
      '#description'   => $this->t('<p>Pick a color theme by its picture. <strong><a href=":demo" target="_blank" rel="noopener noreferrer">View an example of every color theme on the demo site (opens in a new tab)</a>.</strong></p>', [':demo' => 'https://w3css.flashwebcenter.com/w3css-predefined-color-themes']),
    ];
    // Server-rendered radios: selection needs no script, and arrow keys, screen
    // readers and forced colors come with the native input. The script only adds
    // the selected name.
    $picker = [
      '#parents' => ['color_scheme', 'predefined'],
      '#type' => 'radios',
      '#title' => $this->t("Choose the desired predefined theme"),
      '#options' => [
        'none' => $this->t('Pure Monochrome'),
        'w3-theme-amber' => $this->t('Golden Amber'),
        'w3-theme-black' => $this->t('Jet Black'),
        // cspell:disable-next-line
        'w3-theme-blue-grey' => $this->t('Misty Blue Gray'),
        'w3-theme-blue' => $this->t('Royal Blue'),
        'w3-theme-brown' => $this->t('Walnut Brown'),
        'w3-theme-cyan' => $this->t('Bright Cyan'),
        // cspell:disable-next-line
        'w3-theme-dark-grey' => $this->t('Charcoal Gray'),
        'w3-theme-deep-orange' => $this->t('Burnt Orange'),
        'w3-theme-deep-purple' => $this->t('Plum Purple'),
        'w3-theme-green' => $this->t('Meadow Green'),
        // cspell:disable-next-line
        'w3-theme-grey' => $this->t('Silver Gray'),
        'w3-theme-indigo' => $this->t('Ink Indigo'),
        'w3-theme-khaki' => $this->t('Desert Khaki'),
        'w3-theme-light-blue' => $this->t('Sky Blue'),
        'w3-theme-light-green' => $this->t('Spring Green'),
        'w3-theme-lime' => $this->t('Fresh Lime'),
        'w3-theme-orange' => $this->t('Tangerine Orange'),
        'w3-theme-pink' => $this->t('Rose Pink'),
        'w3-theme-purple' => $this->t('Grape Purple'),
        'w3-theme-red' => $this->t('Cherry Red'),
        'w3-theme-teal' => $this->t('Lagoon Teal'),
        'w3-theme-yellow' => $this->t('Sun Yellow'),
        'w3-theme-00000' => $this->t('Quiet Slate'),
        'w3-theme-00001' => $this->t('Harbor Steel'),
        'w3-theme-00002' => $this->t('Ocean Blue'),
        'w3-theme-00003' => $this->t('Forest Green'),
        'w3-theme-00004' => $this->t('Orchid Purple'),
        'w3-theme-00005' => $this->t('Copper Brown'),
        'w3-theme-00006' => $this->t('Crimson Red'),
        'w3-theme-00007' => $this->t('Indigo Ink'),
        'w3-theme-00008' => $this->t('Indigo Lagoon'),
        'w3-theme-00009' => $this->t('Deep Cyan'),
        'w3-theme-00010' => $this->t('Lime Grass'),
        'w3-theme-00011' => $this->t('Espresso Brown'),
        'w3-theme-00012' => $this->t('Navy Ruby'),
        'w3-theme-00013' => $this->t('Cadet Blue'),
        'w3-theme-00014' => $this->t('Harbor Blue'),
        'w3-theme-00015' => $this->t('Cadet Steel'),
        'w3-theme-00016' => $this->t('Slate Amber'),
        'w3-theme-00017' => $this->t('Midnight Ember'),
        'w3-theme-00018' => $this->t('Dark Graphite'),
        'w3-theme-00019' => $this->t('Dark Moss'),
        'w3-theme-00020' => $this->t('Dark Ocean'),
        'w3-theme-00021' => $this->t('Dark Violet'),
        'w3-theme-00022' => $this->t('Dark Ruby'),
        'w3-theme-00023' => $this->t('Dark Rose'),
        'w3-theme-00024' => $this->t('Dark Emerald'),
        'w3-theme-00025' => $this->t('Dark Lavender'),
        'w3-theme-00026' => $this->t('Dark Gold'),
        'w3-theme-00027' => $this->t('Dark Sky'),
        'w3-theme-00028' => $this->t('Teal Coral'),
        'w3-theme-00029' => $this->t('Photo Backdrop'),
      ],
      '#default_value' => $this->settings->get('color_scheme.predefined') ?: 'none',
      '#process' => [[Radios::class, 'processRadios'], [self::class, 'arrangeThumbnails']],
      '#w3css_scheme_families' => ColorThemeFamilies::labels(),
      '#prefix' => '<div class="w3css-schemes">',
      '#suffix' => '</div>',
    ];
    foreach ($picker['#options'] as $value => $label) {
      $picker[$value]['#title'] = $this->thumbnail((string) $value, (string) $label);
    }
    $form['drupal8_w3css_theme_settings']['w3_theme_current']['color_scheme_predefined'] = $picker;
  }

  /**
   * The label of one scheme: its image, and its name for the tag and for a11y.
   *
   * @param string $value
   *   The option value.
   * @param string $label
   *   The translated name.
   *
   * @return \Drupal\Component\Render\MarkupInterface
   *   The markup of the label. The image is decorative: the name labels the input.
   */
  private function thumbnail(string $value, string $label): MarkupInterface {
    $src = base_path() . $this->themeList->getPath('drupal8_w3css_theme') . '/images/schemes/' . $value . '.webp';
    return Markup::create('<img class="w3css-schemes__image" src="' . Html::escape($src) . '" alt="" width="120" height="160" loading="lazy" decoding="async"><span class="w3css-schemes__name">' . Html::escape($label) . '</span>');
  }

  /**
   * Places each radio in a titled grid of its color family.
   *
   * This is the shape without script as well: one heading and grid per family.
   * The script turns each family into a tab panel.
   *
   * @param array<string, mixed> $element
   *   The radios element, after core made one radio per option.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   * @param array<string, mixed> $complete_form
   *   The form.
   *
   * @return array<string, mixed>
   *   The element.
   */
  public static function arrangeThumbnails(array $element, FormStateInterface $form_state, array &$complete_form): array {
    foreach ($element['#w3css_scheme_families'] as $family => $title) {
      $group = [
        '#type' => 'container',
        '#attributes' => ['class' => ['w3css-schemes__group']],
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'h4',
          '#value' => $title,
          '#attributes' => ['class' => ['w3css-schemes__group-title']],
        ],
        'grid' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['w3css-schemes__grid']],
        ],
      ];
      foreach (array_keys($element['#options']) as $value) {
        if (isset($element[$value]) && ColorThemeFamilies::family((string) $value) === $family) {
          $group['grid'][$value] = $element[$value];
          unset($element[$value]);
        }
      }
      $element['group_' . $family] = $group;
    }
    return $element;
  }

}
