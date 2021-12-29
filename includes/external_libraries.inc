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

$form['drupal8_w3css_theme_settings']['w3_external_libraries'] = [
  '#type' => 'details',
  '#title' => t('Google Fonts and Font awesome - W3CSS Theme'),
  '#collapsible' => TRUE,
  '#collapsed' => FALSE,
  '#description'   => t('<p>Choose the fonts and load it locally. The default for all libraries to load from Google.</p>'),
];
$form['drupal8_w3css_theme_settings']['w3_external_libraries']['d8w3css_font_awesome_4'] = [
  '#type' => 'checkbox',
  '#title' => t('Load font awesome 4.7 locally. This is for use with the W3CSS Theme'),
  '#default_value' => theme_get_setting('d8w3css_font_awesome_4'),
];

$form['drupal8_w3css_theme_settings']['w3_external_libraries']['d8w3css_google_font_local'] = [
  '#type' => 'checkbox',
  '#title' => t('Load Google fonts locally. This is for use with the W3CSS Theme'),
  '#default_value' => theme_get_setting('d8w3css_google_font_local'),
];

$form['drupal8_w3css_theme_settings']['w3_external_libraries']['d8w3css_site_gf'] = [
  '#type' => 'select',
  '#title' => t('Choose google fonts - The fonts will be applied to the whole website.'),
  '#options' => [
    'none' => t('Default - Open Sans - sans-serif'),
    'd8w3css-amita' => t('Amita - cursive'),
    'd8w3css-kavivanar' => t('Kavivanar - cursive'),
    'd8w3css-domine' => t('Domine - serif'),
    'd8w3css-glegoo' => t('Glegoo - serif'),
    'd8w3css-oranienbaum' => t('Oranienbaum - Serif'),
    'd8w3css-alegreya-sans' => t('Alegreya Sans - sans-serif'),
    'd8w3css-alegreya-sans-sc' => t('Alegreya Sans SC - sans-serif'),
    'd8w3css-lato' => t('Lato - sans-serif'),
    'd8w3css-montserrat' => t('Montserrat - sans-serif'),
    'd8w3css-nunito' => t('Nunito - sans-serif'),
    'd8w3css-oswald' => t('Oswald - sans-serif'),
    'd8w3css-playfair-display' => t('Playfair Display - sans-serif'),
    'd8w3css-roboto' => t('Roboto - sans-serif'),
    'd8w3css-roboto-condensed' => t('Roboto Condensed - sans-serif'),
    'd8w3css-source-sans-pro' => t('Source Sans Pro - sans-serif'),
  ],
  '#default_value' => theme_get_setting('d8w3css_site_gf'),
];
