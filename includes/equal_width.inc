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
  '#default_value' => theme_get_setting('equal_width'),
];
