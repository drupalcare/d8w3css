<?php

/**
 * @file
 * Credit link configuration.
 *
 * Filename:     credit.inc
 * Website:      https://www.flashwebcenter.com
 * Description:  Credit link display settings.
 * Author:       Alaa Haddad https://www.alaahaddad.com.
 */

declare(strict_types=1);

use Drupal\Component\Utility\DeprecationHelper;
use Drupal\Core\Extension\ThemeSettingsProvider;

/**
 * Credit link theme settings.
 */
$form['drupal8_w3css_theme_settings']['credit_link'] = [
  '#type' => 'details',
  '#title' => t('Credit - W3CSS Theme'),
  '#collapsible' => TRUE,
  '#collapsed' => FALSE,
];
$form['drupal8_w3css_theme_settings']['credit_link']['show_credit_link'] = [
  '#type' => 'checkbox',
  '#title' => t('Show/Hide Credit Text'),
  '#default_value' => DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(ThemeSettingsProvider::class)->getSetting('show_credit_link'), fn() => theme_get_setting('show_credit_link')),
  '#description'   => t('Please display credit in footer'),
];
