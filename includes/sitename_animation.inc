<?php

/**
 * @file
 * W3CSS Theme.theme.
 *
 * Filename:     drupal8_w3css_theme.theme
 * Website:      http://www.flashwebcenter.com
 * Description:  template
 * Author:       Alaa Haddad http://www.alaahaddad.com.
 */

/**
 * The theme settings.
 */

$form['drupal8_w3css_theme_settings']['w3_sitename_animation'] = [
  '#type' => 'details',
  '#title' => t("Enable animation for the site name"),
  '#collapsible' => TRUE,
  '#collapsed' => FALSE,
  '#description'   => t("<p><strong>You can also use these classes for any text on the site.</strong></p><p>animate-text-00, animate-text-00, animate-text-01, animate-text-02, animate-text-03, animate-text-04, animate-text-05, animate-text-06, animate-text-07, animate-text-08, animate-text-09, animate-text-10, animate-text-11, animate-text-12, animate-text-13, animate-text-14.</p>
    "),
];

$form['drupal8_w3css_theme_settings']['w3_sitename_animation']['sitename_animation'] = [
  '#type' => 'select',
  '#title' => t("Choose the desired site name animation"),
  '#options' => [
    'none' => t('Default set to none'),
    'animate-name-00' => t('Style 00 (animate-text-00)'),
    'animate-name-01' => t('Style 01 (animate-text-01)'),
    'animate-name-02' => t('Style 02 (animate-text-02)'),
    'animate-name-03' => t('Style 03 (animate-text-03)'),
    'animate-name-04' => t('Style 04 (animate-text-04)'),
    'animate-name-05' => t('Style 05 (animate-text-05)'),
    'animate-name-06' => t('Style 06 (animate-text-06)'),
    'animate-name-07' => t('Style 07 (animate-text-07)'),
    'animate-name-08' => t('Style 08 (animate-text-08)'),
    'animate-name-09' => t('Style 09 (animate-text-09)'),
    'animate-name-10' => t('Style 10 (animate-text-10)'),
    'animate-name-11' => t('Style 11 (animate-text-11)'),
    'animate-name-12' => t('Style 12 (animate-text-12)'),
    'animate-name-13' => t('Style 13 (animate-text-13)'),
    'animate-name-14' => t('Style 14 (animate-text-14)'),
  ],
  '#default_value' => theme_get_setting('sitename_animation'),
];
