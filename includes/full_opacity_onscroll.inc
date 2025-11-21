<?php

/**
 * @file
 * Full opacity on scroll configuration.
 *
 * Filename:     full_opacity_onscroll.inc
 * Website:      https://www.flashwebcenter.com
 * Description:  Opacity animation on scroll settings.
 * Author:       Alaa Haddad https://www.alaahaddad.com.
 */

declare(strict_types=1);

/**
 * Full opacity on scroll theme settings.
 */
$form['drupal8_w3css_theme_settings']['w3_full_opacity'] = [
  '#type' => 'details',
  '#title' => t("Enable Full Opacity On Scroll - W3CSS Theme"),
  '#collapsible' => TRUE,
  '#collapsed' => FALSE,
  '#description'   => t('<p>When you visit the page, the hidden blocks are invisible and they will start to show/animate when you scroll down. The default setting is disabled.</p>'),
];
$form['drupal8_w3css_theme_settings']['w3_full_opacity']['full_opacity_onscroll'] = [
  '#type' => 'checkbox',
  '#title' => t('Check this box to animate the opacity on scroll down.'),
  '#default_value' => theme_get_setting('full_opacity_onscroll'),
];
