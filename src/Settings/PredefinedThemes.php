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
      '#title' => $this->t('Predefined Color Themes - W3CSS Theme'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
      '#description'   => $this->t('<p>w3-theme-amber, w3-theme-black, w3-theme-blue-grey, w3-theme-blue, w3-theme-brown, w3-theme-cyan, w3-theme-dark-grey, w3-theme-deep-orange, w3-theme-deep-purple, w3-theme-green, w3-theme-grey, w3-theme-indigo, w3-theme-khaki, w3-theme-light-blue, w3-theme-light-green, w3-theme-lime, w3-theme-orange, w3-theme-pink, w3-theme-purple, w3-theme-red, w3-theme-teal, w3-theme-yellow, w3-theme-00000, w3-theme-00001, w3-theme-00002, w3-theme-00003, w3-theme-00004, w3-theme-00005, w3-theme-00006, w3-theme-00007, w3-theme-00008, w3-theme-00009, w3-theme-00010, w3-theme-00011, w3-theme-00012, w3-theme-00013, w3-theme-00014, w3-theme-00015, w3-theme-00016, w3-theme-00017, w3-theme-00018, w3-theme-00019, w3-theme-00020, w3-theme-00021, w3-theme-00022, w3-theme-00023, w3-theme-00024, w3-theme-00025, w3-theme-00026, w3-theme-00027, w3-theme-00028 and w3-theme-00029. <strong><a target="_blank" href="https://w3css.flashwebcenter.com/w3css-predefined-color-themes">Demo Site has an example for all color themes</a>.</strong></p>'),
    ];
    // Server-rendered radios: selection needs no script, and arrow keys, screen
    // readers and forced colors come with the native input. The script only adds
    // the selected name.
    $picker = [
      '#parents' => ['color_scheme', 'predefined'],
      '#type' => 'radios',
      '#title' => $this->t("Choose the desired predefined theme"),
      '#options' => [
        'none' => $this->t('Default - Black Gray White'),
        'w3-theme-amber' => $this->t('Amber'),
        'w3-theme-black' => $this->t('Black'),
        'w3-theme-blue-grey' => $this->t('Blue Gray'),
        'w3-theme-blue' => $this->t('Blue'),
        'w3-theme-brown' => $this->t('Brown'),
        'w3-theme-cyan' => $this->t('Cyan'),
        'w3-theme-dark-grey' => $this->t('Dark Gray'),
        'w3-theme-deep-orange' => $this->t('Deep Orange'),
        'w3-theme-deep-purple' => $this->t('Deep Purple'),
        'w3-theme-green' => $this->t('Green'),
        'w3-theme-grey' => $this->t('Gray'),
        'w3-theme-indigo' => $this->t('Indigo'),
        'w3-theme-khaki' => $this->t('Khaki'),
        'w3-theme-light-blue' => $this->t('Light Blue'),
        'w3-theme-light-green' => $this->t('Light Green'),
        'w3-theme-lime' => $this->t('Lime'),
        'w3-theme-orange' => $this->t('Orange'),
        'w3-theme-pink' => $this->t('Pink'),
        'w3-theme-purple' => $this->t('Purple'),
        'w3-theme-red' => $this->t('Red'),
        'w3-theme-teal' => $this->t('Teal'),
        'w3-theme-yellow' => $this->t('Yellow'),
        'w3-theme-00000' => $this->t('00000 - Plain No Colors'),
        'w3-theme-00001' => $this->t('00001 - Blue Gray'),
        'w3-theme-00002' => $this->t('00002 - Blue'),
        'w3-theme-00003' => $this->t('00003 - Green'),
        'w3-theme-00004' => $this->t('00004 - Purple'),
        'w3-theme-00005' => $this->t('00005 - Brown'),
        'w3-theme-00006' => $this->t('00006 - Red'),
        'w3-theme-00007' => $this->t('00007 - Indigo'),
        'w3-theme-00008' => $this->t('00008 - Red Turquoise'),
        'w3-theme-00009' => $this->t('00009 - DarkCyan'),
        'w3-theme-00010' => $this->t('00010 - Green Grass'),
        'w3-theme-00011' => $this->t('00011 - Dark Brown'),
        'w3-theme-00012' => $this->t('00012 - Blue Red'),
        'w3-theme-00013' => $this->t('00013 - CadetBlue'),
        'w3-theme-00014' => $this->t('00014 - SlateGray'),
        'w3-theme-00015' => $this->t('00015 - CadetBlue SteelBlue'),
        'w3-theme-00016' => $this->t('00016 - BlueOrange'),
        'w3-theme-00017' => $this->t('00017 - DarkOrange MidnightBlue'),
        'w3-theme-00018' => $this->t('00018 - Dark Theme - BlackGray'),
        'w3-theme-00019' => $this->t('00019 - Dark Theme - BlackGreen'),
        'w3-theme-00020' => $this->t('00020 - Dark Theme - BlackBlue'),
        'w3-theme-00021' => $this->t('00021 - Dark Theme - BlackPurple'),
        'w3-theme-00022' => $this->t('00022 - Dark Theme - BlackRed'),
        'w3-theme-00023' => $this->t('00023 - Dark Theme - BlackPink'),
        'w3-theme-00024' => $this->t('00024 - Dark Theme - BlackLightGreen'),
        'w3-theme-00025' => $this->t('00025 - Dark Theme - BlackDarkPurple'),
        'w3-theme-00026' => $this->t('00026 - Dark Theme - BlackYellow'),
        'w3-theme-00027' => $this->t('00027 - Dark Theme - BlackLightBlue'),
        'w3-theme-00028' => $this->t('00028 - DarkRed LightBlue'),
        'w3-theme-00029' => $this->t('00029 - Colorfull Image Background'),
      ],
      '#default_value' => $this->settings->get('color_scheme.predefined') ?: 'none',
      '#process' => [[Radios::class, 'processRadios'], [self::class, 'arrangeThumbnails']],
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
   * Places the radios in one grid.
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
    $grid = [
      '#type' => 'container',
      '#attributes' => ['class' => ['w3css-schemes__grid']],
    ];
    foreach (array_keys($element['#options']) as $value) {
      $grid[$value] = $element[$value];
      unset($element[$value]);
    }
    $element['grid'] = $grid;
    return $element;
  }

}
