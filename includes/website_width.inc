<?php

/**
 * @file
 * Website width configuration.
 *
 * Filename:     website_width.inc
 * Website:      https://www.flashwebcenter.com
 * Description:  Website maximum width settings.
 * Author:       Alaa Haddad https://www.alaahaddad.com.
 */

declare(strict_types=1);

use Drupal\Component\Utility\DeprecationHelper;
use Drupal\Core\Extension\ThemeSettingsProvider;

/**
 * Website width theme settings.
 */
$form['drupal8_w3css_theme_settings']['w3_website_width'] = [
  '#type' => 'details',
  '#title' => t("Website's Width - W3CSS Theme"),
  '#collapsible' => TRUE,
  '#collapsed' => FALSE,
  '#description' => t("<p><strong>You can change the whole website's width by selecting any of the desired width in pixels.</strong></p><p>w3-width-100-percent, w3-width-2560, w3-width-1920, w3-width-1360, w3-width-1280, w3-width-1024 and w3-width-800. The default width is 1600 pixels.</p>"),
];

$form['drupal8_w3css_theme_settings']['w3_website_width']['website_width'] = [
  '#type' => 'select',
  '#title' => t("Choose the desired website's width"),
  '#options' => [
    'none' => t('Default - 1600 Pixels'),
    'w3-width-2560' => t('2560 Pixels'),
    'w3-width-1920' => t('1920 Pixels'),
    'w3-width-1360' => t('1360 Pixels'),
    'w3-width-1280' => t('1280 Pixels'),
    'w3-width-1024' => t('1024 Pixels'),
    'w3-width-800' => t('800 Pixels'),
    'w3-width-100-percent' => t('100%'),
  ],
  '#default_value' => DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(ThemeSettingsProvider::class)->getSetting('website_width'), fn() => theme_get_setting('website_width')),
];
