<?php

/**
 * @file
 * Breadcrumb page title configuration.
 *
 * Filename:     breadcrumb_page_title.inc
 * Website:      https://www.flashwebcenter.com
 * Description:  Breadcrumb page title display settings.
 * Author:       Alaa Haddad https://www.alaahaddad.com.
 */

declare(strict_types=1);

/**
 * Breadcrumb page title theme settings.
 */
$form['drupal8_w3css_theme_settings']['w3_breadcrumb_page_title'] = [
  '#type' => 'details',
  '#title' => t("Remove Page Title from Breadcrumb"),
  '#collapsible' => TRUE,
  '#collapsed' => FALSE,
  '#description'   => t('<p>By default, the theme adds the page title as the last value to the breadcrumb.</p>'),
];
$form['drupal8_w3css_theme_settings']['w3_breadcrumb_page_title']['breadcrumb_page_title'] = [
  '#type' => 'checkbox',
  '#title' => t('Check this box to remove the page title from breadcrumb.'),
  '#default_value' => theme_get_setting('breadcrumb_page_title'),
];
