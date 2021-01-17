<?php

/**
 * @file
 * Drupal8 W3CSS Theme.theme.
 *
 * Filename:     drupal8_w3css_theme.theme
 * Website:      http://www.flashwebcenter.com
 * Description:  template
 * Author:       Alaa Haddad http://www.alaahaddad.com.
 */

/**
 * The theme settings.
 */

$form['drupal8_w3css_theme_settings']['w3_external_libraries'] = [
  '#type' => 'details',
  '#title' => t('Load External Libraries locally'),
  '#collapsible' => TRUE,
  '#collapsed' => FALSE,
  '#description'   => t('<p>Load font awesome 4.7 and google fonts locally.</p>'),
];
$form['drupal8_w3css_theme_settings']['w3_external_libraries']['d8w3css_font_awesome_4'] = [
  '#type' => 'checkbox',
  '#title' => t('Load font awesome 4.7 locally'),
  '#default_value' => theme_get_setting('d8w3css_font_awesome_4'),
];
$form['drupal8_w3css_theme_settings']['w3_external_libraries']['d8w3css_google_font'] = [
  '#type' => 'checkbox',
  '#title' => t('Load Google fonts locally'),
  '#default_value' => theme_get_setting('d8w3css_google_font'),
];
