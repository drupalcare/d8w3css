<?php

/**
 * @file
 * Copyright configuration.
 *
 * Filename:     copyright.inc
 * Website:      https://www.flashwebcenter.com
 * Description:  Copyright text settings.
 * Author:       Alaa Haddad https://www.alaahaddad.com.
 */

declare(strict_types=1);

use Drupal\Component\Utility\DeprecationHelper;
use Drupal\Core\Extension\ThemeSettingsProvider;

/**
 * Copyright theme settings.
 */
$form['drupal8_w3css_theme_settings']['copyright'] = [
  '#type' => 'details',
  '#title' => t('Copyright - W3CSS Theme'),
  '#collapsible' => TRUE,
  '#collapsed' => FALSE,
];
$form['drupal8_w3css_theme_settings']['copyright']['copyright_text'] = [
  '#type' => 'textfield',
  '#title' => t('Enter Copyright Text'),
  '#description' => t("Type %year% instead of an actual year number for dynamic copyright. (Copyright © %year% Company Name - All rights reserved)."),
  '#default_value' => DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(ThemeSettingsProvider::class)->getSetting('copyright_text'), fn() => theme_get_setting('copyright_text')),
];
