<?php

declare(strict_types=1);

namespace Drupal\drupal8_w3css_theme\Settings;

/**
 * Font Awesome and Google Fonts settings.
 */
final class ExternalLibraries extends SettingsSection {

  /**
   * {@inheritdoc}
   */
  public function build(array &$form): void {
    $form['drupal8_w3css_theme_settings']['w3_fontawesome_libraries'] = [
      '#type' => 'details',
      '#title' => $this->t('Font awesome - W3CSS Theme'),
      '#description'   => $this->t("<p>Font awesome is now not enabled by default. All the default theme's icons were replaced by svg images. You can enable this library if you are using any font awesome icons in your site.</p>"),
    ];

    $form['drupal8_w3css_theme_settings']['w3_fontawesome_libraries']['libraries_font_awesome'] = [
      '#parents' => ['libraries', 'font_awesome'],
      '#type' => 'radios',
      '#title' => $this->t('Select Font Awesome library'),
      '#default_value' => $this->settings->get('libraries.font_awesome') ?? 0,
      '#options' => [
        0 => $this->t('None'),
        1 => $this->t('Load Font Awesome 4.7 locally.'),
        2 => $this->t('Load Font Awesome 4.7 from the CDN.'),
        3 => $this->t('Load Font Awesome 5.15.4 from the CDN.'),
        4 => $this->t('Load Font Awesome 6.1.1 from the CDN.'),
      ],
    ];

    $form['drupal8_w3css_theme_settings']['w3_external_libraries'] = [
      '#type' => 'details',
      '#title' => $this->t('Google Fonts - W3CSS Theme'),
      '#description'   => $this->t('<p>Any font you choose for the site, heading or special will have a CSS class that you can apply to any html tag. <a href=":docs" target="_blank" rel="noopener noreferrer">Read the font documentation on drupal.org (opens in a new tab)</a>.</p>', [':docs' => 'https://www.drupal.org/node/2866181#change-font']),
    ];

    $form['drupal8_w3css_theme_settings']['w3_external_libraries']['typography_load_fonts_locally'] = [
      '#parents' => ['typography', 'load_fonts_locally'],
      '#type' => 'checkbox',
      '#title' => $this->t('Check this box if you would like to load Google Fonts (global site fonts and heading fonts only) locally. This is for use with the W3CSS Theme'),
      '#default_value' => $this->settings->get('typography.load_fonts_locally'),
    ];

    $form['drupal8_w3css_theme_settings']['w3_external_libraries']['typography_site_font'] = [
      '#parents' => ['typography', 'site_font'],
      '#type' => 'select',
      '#title' => $this->t('50 global site fonts: choose the site font - the font will be applied to the whole website'),
      '#description'   => $this->t('<p><a href=":demo" target="_blank" rel="noopener noreferrer">View a live demo of all global site fonts (opens in a new tab)</a>.</p><p>Global Site Font CSS Classes: open-sans, abel, alegreya-sans, alegreya-sans-sc, amita, archivo-narrow, arimo, arvo, barlow, barlow-semi-condensed
, bitter, cairo, comfortaa, courgette, domine, dosis, exo-2, fira-sans, glegoo, hind-siliguri, ibm-plex-sans, inconsolata, josefin-sans, kanit, kavivanar, lato, libre-baskerville, lora, merienda, merriweather, montserrat, mukta, nunito, oranienbaum, oswald, oxygen, playfair-display, poppins, prompt, pt-serif, quicksand, raleway, roboto, roboto-condensed, roboto-mono, spline-sans, source-sans-3, titillium-web, ubuntu, work-sans. </p>', [':demo' => 'https://w3css.flashwebcenter.com/node/105']),
      '#options' => [
        'none' => $this->t('None'),
        'd8w3css-abel' => $this->t('Abel - sans-serif'),
        'd8w3css-alegreya-sans' => $this->t('Alegreya Sans - sans-serif'),
        'd8w3css-alegreya-sans-sc' => $this->t('Alegreya Sans SC - sans-serif'),
        'd8w3css-amita' => $this->t('Amita - cursive'),
        'd8w3css-archivo-narrow' => $this->t('Archivo Narrow - sans-serif'),
        'd8w3css-arimo' => $this->t('Arimo - sans-serif'),
        'd8w3css-arvo' => $this->t('Arvo - serif'),
        'd8w3css-barlow' => $this->t('Barlow - sans-serif'),
        'd8w3css-barlow-semi-condensed' => $this->t('Barlow Semi Condensed - sans-serif'),
        'd8w3css-bitter' => $this->t('Bitter - serif'),
        'd8w3css-cairo' => $this->t('Cairo - sans-serif'),
        'd8w3css-comfortaa' => $this->t('Comfortaa - cursive'),
        'd8w3css-courgette' => $this->t('Courgette - cursive'),
        'd8w3css-domine' => $this->t('Domine - serif'),
        'd8w3css-dosis' => $this->t('Dosis - sans-serif'),
        'd8w3css-exo-2' => $this->t('EXO 2 - sans-serif'),
        'd8w3css-fira-sans' => $this->t('Fira Sans - sans-serif'),
        'd8w3css-glegoo' => $this->t('Glegoo - serif'),
        'd8w3css-hind-siliguri' => $this->t('Hind Siliguri - sans-serif'),
        'd8w3css-ibm-plex-sans' => $this->t('IBM Plex Sans - sans-serif'),
        'd8w3css-inconsolata' => $this->t('Inconsolata - monospace'),
        'd8w3css-josefin-sans' => $this->t('Josefin Sans  - sans-serif'),
        'd8w3css-kanit' => $this->t('Kanit - sans-serif'),
        'd8w3css-kavivanar' => $this->t('Kavivanar - cursive'),
        'd8w3css-lato' => $this->t('Lato - sans-serif'),
        'd8w3css-libre-baskerville' => $this->t('Libre Baskerville  - serif'),
        'd8w3css-lora' => $this->t('Lora - serif'),
        'd8w3css-merienda' => $this->t('Merienda - cursive'),
        'd8w3css-merriweather' => $this->t('Merriweather  - serif'),
        'd8w3css-montserrat' => $this->t('Montserrat - sans-serif'),
        'd8w3css-mukta' => $this->t('Mukta - sans-serif'),
        'd8w3css-nunito' => $this->t('Nunito - sans-serif'),
        'd8w3css-open-sans' => $this->t('Open Sans - sans-serif'),
        'd8w3css-oranienbaum' => $this->t('Oranienbaum - serif'),
        'd8w3css-oswald' => $this->t('Oswald - sans-serif'),
        'd8w3css-oxygen' => $this->t('Oxygen - sans-serif'),
        'd8w3css-playfair-display' => $this->t('Playfair Display - serif'),
        'd8w3css-poppins' => $this->t('Poppins - sans-serif'),
        'd8w3css-prompt' => $this->t('Prompt - sans-serif'),
        'd8w3css-pt-serif' => $this->t('PT Serif - serif'),
        'd8w3css-quicksand' => $this->t('Quicksand - sans-serif'),
        'd8w3css-raleway' => $this->t('Raleway - sans-serif'),
        'd8w3css-roboto' => $this->t('Roboto - sans-serif'),
        'd8w3css-roboto-condensed' => $this->t('Roboto Condensed - sans-serif'),
        'd8w3css-roboto-mono' => $this->t('Roboto Mono - monospace'),
        'd8w3css-spline-sans' => $this->t('Spline Sans - sans-serif'),
        'd8w3css-source-sans-3' => $this->t('Source Sans 3 - sans-serif'),
        'd8w3css-titillium-web' => $this->t('Titillium Web - sans-serif'),
        'd8w3css-ubuntu' => $this->t('Ubuntu - sans-serif'),
        'd8w3css-work-sans' => $this->t('Work Sans - sans-serif'),

      ],
      '#default_value' => $this->settings->get('typography.site_font'),
    ];

    $form['drupal8_w3css_theme_settings']['w3_external_libraries']['typography_heading_font'] = [
      '#parents' => ['typography', 'heading_font'],
      '#type' => 'select',
      '#title' => $this->t('26 heading fonts: choose the heading font - the font will be applied to h1, h2 and h3 only'),
      '#description'   => $this->t('<p><a href=":demo" target="_blank" rel="noopener noreferrer">View a live demo of all heading fonts (opens in a new tab)</a>.</p><p>Heading Font CSS Classes: abril-fatface, aladin, anton, archivo-black, bebas-neue, big-shoulders-stencil-display, big-shoulders-stencil-text, black-ops-one, bree-serif, cinzel, dancing-script, dm-serif-display, doppio-one, fjalla-one, francois-one, fredericka-the-great, fredoka, hammersmith-one, indie-flower, kalam, lobster-two, patrick-hand, prosto-one, righteous, staatliches, yanone-kaffeesatz.</p>', [':demo' => 'https://w3css.flashwebcenter.com/node/104']),
      '#options' => [
        'none' => $this->t('None'),
        'd8w3css-abril-fatface' => $this->t('Abril Fatface  - cursive'),
        'd8w3css-aladin' => $this->t('Aladin  - cursive'),
        'd8w3css-anton' => $this->t('Anton  - sans-serif'),
        'd8w3css-archivo-black' => $this->t('Archivo Black - sans-serif'),
        'd8w3css-bebas-neue' => $this->t('Bebas Neue - cursive'),
        'd8w3css-big-shoulders-stencil-display' => $this->t('Big Shoulders Stencil Display - cursive'),
        'd8w3css-big-shoulders-stencil-text' => $this->t('Big Shoulders Stencil Text - cursive'),
        'd8w3css-black-ops-one' => $this->t('Black Ops One - cursive'),
        'd8w3css-bree-serif' => $this->t('Bree Serif - serif'),
        'd8w3css-cinzel' => $this->t('Cinzel - serif'),
        'd8w3css-dancing-script' => $this->t('Dancing Script - cursive'),
        'd8w3css-dm-serif-display' => $this->t('DM Serif Display - serif'),
        'd8w3css-doppio-one' => $this->t('Doppio - sans-serif'),
        'd8w3css-fjalla-one' => $this->t('Fjalla One - sans-serif'),
        'd8w3css-francois-one' => $this->t('Francois One - sans-serif'),
        'd8w3css-fredericka-the-great' => $this->t('Fredericka The Great  - cursive'),
        'd8w3css-fredoka' => $this->t('Fredoka - cursive'),
        'd8w3css-hammersmith-one' => $this->t('Hammersmith One - sans-serif'),
        'd8w3css-indie-flower' => $this->t('Indie Flower - cursive'),
        'd8w3css-kalam' => $this->t('Kalam - cursive'),
        'd8w3css-lobster-two' => $this->t('Lobster Two - cursive'),
        'd8w3css-patrick-hand' => $this->t('Patrick Hand - cursive'),
        'd8w3css-prosto-one' => $this->t('Prosto One - cursive'),
        'd8w3css-righteous' => $this->t('Righteous - cursive'),
        'd8w3css-staatliches' => $this->t('Staatliches - cursive'),
        'd8w3css-yanone-kaffeesatz' => $this->t('Yanone Kaffeesatz - sans-serif'),

      ],
      '#default_value' => $this->settings->get('typography.heading_font'),
    ];

    $form['drupal8_w3css_theme_settings']['w3_external_libraries']['typography_special_font'] = [
      '#parents' => ['typography', 'special_font'],
      '#type' => 'select',
      '#title' => $this->t('25 special fonts: they are not applied to any HTML tags by default and they are only imported. Each font library has a CSS class that you can apply to any HTML tag'),
      '#description'   => $this->t('<p>Special fonts always load from Google Fonts, even when local loading is checked above.</p><p><a href=":demo" target="_blank" rel="noopener noreferrer">View a live demo of all special fonts (opens in a new tab)</a>.</p><p>Special Font CSS Classes: akronim, barriecito, bungee-shade, ceviche-one, creepster, faster-one, finger-paint, frijole, irish-grover, kumar-one, londrina-outline, megrim, metal-mania, monoton, moo-lah-lah, new-rocker, nosifer, pirata-one, ruslan-display, rye, sancreek, slackey, trade-winds, train-one, unifrakturmaguntia.</p>', [':demo' => 'https://w3css.flashwebcenter.com/node/102']),
      '#options' => [
        'none' => $this->t('None'),
        'd8w3css-akronim' => $this->t('akronim - cursive'),
        'd8w3css-barriecito' => $this->t('barriecito - cursive'),
        'd8w3css-bungee-shade' => $this->t('bungee-shade - cursive'),
        'd8w3css-ceviche-one' => $this->t('ceviche-one - cursive'),
        'd8w3css-creepster' => $this->t('creepster - cursive'),
        'd8w3css-faster-one' => $this->t('faster-one - cursive'),
        'd8w3css-finger-paint' => $this->t('finger-paint - cursive'),
        'd8w3css-frijole' => $this->t('frijole - cursive'),
        'd8w3css-irish-grover' => $this->t('irish-grover - cursive'),
        'd8w3css-kumar-one' => $this->t('kumar-one - cursive'),
        'd8w3css-londrina-outline' => $this->t('londrina-outline - cursive'),
        'd8w3css-megrim' => $this->t('megrim - cursive'),
        'd8w3css-metal-mania' => $this->t('metal-mania - cursive'),
        'd8w3css-monoton' => $this->t('monoton - cursive'),
        'd8w3css-moo-lah-lah' => $this->t('moo-lah-lah - cursive'),
        'd8w3css-new-rocker' => $this->t('new-rocker - cursive'),
        'd8w3css-nosifer' => $this->t('nosifer - cursive'),
        'd8w3css-pirata-one' => $this->t('pirata-one - cursive'),
        'd8w3css-ruslan-display' => $this->t('ruslan-display - cursive'),
        'd8w3css-rye' => $this->t('rye - cursive'),
        'd8w3css-sancreek' => $this->t('sancreek - cursive'),
        'd8w3css-slackey' => $this->t('slackey - cursive'),
        'd8w3css-trade-winds' => $this->t('trade-winds - cursive'),
        'd8w3css-train-one' => $this->t('train-one - cursive'),
        'd8w3css-unifrakturmaguntia' => $this->t('unifrakturmaguntia - cursive'),

      ],
      '#default_value' => $this->settings->get('typography.special_font'),
    ];
  }

}
