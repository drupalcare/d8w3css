<?php

declare(strict_types=1);

namespace Drupal\drupal8_w3css_theme;

use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * The color families of the predefined color themes.
 *
 * A family groups the themes by their main color, so a site builder finds a
 * theme by the color they want. The dark themes join the family of their
 * accent color. Every predefined theme, and "none", is in one family.
 */
final class ColorThemeFamilies {

  /**
   * The themes of each family, by option value.
   */
  public const array FAMILIES = [
    'blue' => [
      'w3-theme-blue',
      'w3-theme-light-blue',
      'w3-theme-indigo',
      'w3-theme-00002',
      'w3-theme-00007',
      'w3-theme-00014',
      'w3-theme-00015',
      'w3-theme-00016',
      'w3-theme-00017',
      'w3-theme-00020',
      'w3-theme-00027',
    ],
    'teal' => [
      'w3-theme-cyan',
      'w3-theme-teal',
      'w3-theme-00008',
      'w3-theme-00009',
      'w3-theme-00013',
    ],
    'green' => [
      'w3-theme-green',
      'w3-theme-light-green',
      'w3-theme-lime',
      'w3-theme-00003',
      'w3-theme-00010',
      'w3-theme-00019',
      'w3-theme-00024',
    ],
    'red' => [
      'w3-theme-red',
      'w3-theme-pink',
      'w3-theme-00006',
      'w3-theme-00012',
      'w3-theme-00022',
      'w3-theme-00023',
      'w3-theme-00028',
    ],
    'purple' => [
      'w3-theme-deep-purple',
      'w3-theme-purple',
      'w3-theme-00004',
      'w3-theme-00021',
      'w3-theme-00025',
    ],
    'warm' => [
      'w3-theme-deep-orange',
      'w3-theme-orange',
      'w3-theme-amber',
      'w3-theme-yellow',
      'w3-theme-khaki',
      'w3-theme-brown',
      'w3-theme-00005',
      'w3-theme-00011',
      'w3-theme-00026',
    ],
    'neutral' => [
      'none',
      'w3-theme-black',
      'w3-theme-dark-grey',
      'w3-theme-grey',
      'w3-theme-blue-grey',
      'w3-theme-00000',
      'w3-theme-00001',
      'w3-theme-00018',
      'w3-theme-00029',
    ],
  ];

  /**
   * The family names, in the order they are shown.
   *
   * @return array<string, \Drupal\Core\StringTranslation\TranslatableMarkup>
   *   Family key => its name.
   */
  public static function labels(): array {
    return [
      'blue' => new TranslatableMarkup('Blue'),
      'teal' => new TranslatableMarkup('Teal'),
      'green' => new TranslatableMarkup('Green'),
      'red' => new TranslatableMarkup('Red'),
      'purple' => new TranslatableMarkup('Purple'),
      'warm' => new TranslatableMarkup('Warm tones'),
      'neutral' => new TranslatableMarkup('Neutral'),
    ];
  }

  /**
   * The family of a predefined theme; an unknown value is neutral.
   *
   * @param string $value
   *   The option value, for example w3-theme-blue.
   *
   * @return string
   *   A key of FAMILIES.
   */
  public static function family(string $value): string {
    foreach (self::FAMILIES as $family => $values) {
      if (in_array($value, $values, TRUE)) {
        return $family;
      }
    }
    return 'neutral';
  }

}
