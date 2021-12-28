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
  '#title' => t('Google Fonts and Font awesome - D8W3CSS'),
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
    'none' => t('Default - Open Sans'),
    'd8w3css-alegreya-sans' => t('Alegreya Sans'),
    'd8w3css-alegreya-sans-sc' => t('Alegreya Sans SC'),
    'd8w3css-lato' => t('Lato'),
    'd8w3css-montserrat' => t('Montserrat'),
    'd8w3css-nunito' => t('nunito'),
    'd8w3css-oooh-baby' => t('Oooh Baby'),
    'd8w3css-oswald' => t('Oswald'),
    'd8w3css-playfair-display' => t('Playfair Display'),
    'd8w3css-roboto' => t('Robot'),
    'd8w3css-roboto-condensed' => t('Robot Condensed'),
  ],
  '#default_value' => theme_get_setting('d8w3css_site_gf'),
];
