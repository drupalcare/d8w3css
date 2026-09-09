<?php

/**
 * @file
 * Equal width columns configuration.
 *
 * Filename:     equal_width.inc
 * Website:      https://www.flashwebcenter.com
 * Description:  Main container column width settings.
 * Author:       Alaa Haddad https://www.alaahaddad.com.
 */

declare(strict_types=1);

use Drupal\Component\Utility\DeprecationHelper;
use Drupal\Core\Extension\ThemeSettingsProvider;

/**
 * Equal width columns theme settings.
 */
$form['drupal8_w3css_theme_settings']['w3_equal_width'] = [
  '#type' => 'details',
  '#title' => t("Main Container Columns' Width - W3CSS Theme"),
  '#collapsible' => TRUE,
  '#collapsed' => FALSE,
  '#description'   => t('<p>The default settings for the columns in the main containers are <strong>(25% 50% 25%)</strong></p>'),
];
$form['drupal8_w3css_theme_settings']['w3_equal_width']['equal_width'] = [
  '#type' => 'checkbox',
  '#title' => t('Change the columns in the main container to (33% 33% 33%)'),
  '#default_value' => DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(ThemeSettingsProvider::class)->getSetting('equal_width'), fn() => theme_get_setting('equal_width')),
];
