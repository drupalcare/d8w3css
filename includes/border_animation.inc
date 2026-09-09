<?php

/**
 * @file
 * Border animation configuration.
 *
 * Filename:     border_animation.inc
 * Website:      https://www.flashwebcenter.com
 * Description:  Region border animation settings.
 * Author:       Alaa Haddad https://www.alaahaddad.com.
 */

declare(strict_types=1);

use Drupal\Component\Utility\DeprecationHelper;
use Drupal\Core\Extension\ThemeSettingsProvider;

/**
 * Border animation theme settings.
 */
$form['drupal8_w3css_theme_settings']['w3_border_animation'] = [
  '#type' => 'details',
  '#title' => t("Enable Border Animation - W3CSS Theme"),
  '#collapsible' => TRUE,
  '#collapsed' => FALSE,
  '#description'   => t("<p><strong>You can enable/disable the border animation for the top regions, bottom regions and footer regions.</strong></p>"),
];

$form['drupal8_w3css_theme_settings']['w3_border_animation']['border_animation'] = [
  '#type' => 'checkbox',
  '#title' => t("Check this box to animate the region's border."),
  '#default_value' => DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(ThemeSettingsProvider::class)->getSetting('border_animation'), fn() => theme_get_setting('border_animation')),
];
