<?php

declare(strict_types=1);

namespace Drupal\drupal8_w3css_theme\Settings;

/**
 * Font Awesome and Google Fonts settings.
 */
final class ExternalLibraries extends SettingsSection {

  /**
   * The prefix of a stored font choice.
   */
  public const FONT_PREFIX = 'w3css-font-';

  /**
   * The prefix a font choice had before 3.0.0.
   */
  private const OLD_FONT_PREFIX = 'd8w3css-';

  /**
   * Maps a stored font choice to its current value.
   *
   * Config not yet rewritten by the post update still holds "d8w3css-<font>";
   * this turns it into "w3css-font-<font>". Any other value is returned as is.
   *
   * @param mixed $value
   *   The stored font choice.
   *
   * @return mixed
   *   The current value of the choice.
   */
  public static function currentFont(mixed $value): mixed {
    if (is_string($value) && str_starts_with($value, self::OLD_FONT_PREFIX) && $value !== self::OLD_FONT_PREFIX) {
      return self::FONT_PREFIX . substr($value, strlen(self::OLD_FONT_PREFIX));
    }
    return $value;
  }

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
        1 => $this->t('Load Font Awesome 7 locally.'),
        2 => $this->t('Load Font Awesome 7 from the CDN.'),
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
        'w3css-font-abel' => $this->t('Abel - sans-serif'),
        'w3css-font-alegreya-sans' => $this->t('Alegreya Sans - sans-serif'),
        'w3css-font-alegreya-sans-sc' => $this->t('Alegreya Sans SC - sans-serif'),
        'w3css-font-amita' => $this->t('Amita - cursive'),
        'w3css-font-archivo-narrow' => $this->t('Archivo Narrow - sans-serif'),
        'w3css-font-arimo' => $this->t('Arimo - sans-serif'),
        'w3css-font-arvo' => $this->t('Arvo - serif'),
        'w3css-font-barlow' => $this->t('Barlow - sans-serif'),
        'w3css-font-barlow-semi-condensed' => $this->t('Barlow Semi Condensed - sans-serif'),
        'w3css-font-bitter' => $this->t('Bitter - serif'),
        'w3css-font-cairo' => $this->t('Cairo - sans-serif'),
        'w3css-font-comfortaa' => $this->t('Comfortaa - cursive'),
        'w3css-font-courgette' => $this->t('Courgette - cursive'),
        'w3css-font-domine' => $this->t('Domine - serif'),
        'w3css-font-dosis' => $this->t('Dosis - sans-serif'),
        'w3css-font-exo-2' => $this->t('EXO 2 - sans-serif'),
        'w3css-font-fira-sans' => $this->t('Fira Sans - sans-serif'),
        'w3css-font-glegoo' => $this->t('Glegoo - serif'),
        'w3css-font-hind-siliguri' => $this->t('Hind Siliguri - sans-serif'),
        'w3css-font-ibm-plex-sans' => $this->t('IBM Plex Sans - sans-serif'),
        'w3css-font-inconsolata' => $this->t('Inconsolata - monospace'),
        'w3css-font-josefin-sans' => $this->t('Josefin Sans  - sans-serif'),
        'w3css-font-kanit' => $this->t('Kanit - sans-serif'),
        'w3css-font-kavivanar' => $this->t('Kavivanar - cursive'),
        'w3css-font-lato' => $this->t('Lato - sans-serif'),
        'w3css-font-libre-baskerville' => $this->t('Libre Baskerville  - serif'),
        'w3css-font-lora' => $this->t('Lora - serif'),
        'w3css-font-merienda' => $this->t('Merienda - cursive'),
        'w3css-font-merriweather' => $this->t('Merriweather  - serif'),
        'w3css-font-montserrat' => $this->t('Montserrat - sans-serif'),
        'w3css-font-mukta' => $this->t('Mukta - sans-serif'),
        'w3css-font-nunito' => $this->t('Nunito - sans-serif'),
        'w3css-font-open-sans' => $this->t('Open Sans - sans-serif'),
        'w3css-font-oranienbaum' => $this->t('Oranienbaum - serif'),
        'w3css-font-oswald' => $this->t('Oswald - sans-serif'),
        'w3css-font-oxygen' => $this->t('Oxygen - sans-serif'),
        'w3css-font-playfair-display' => $this->t('Playfair Display - serif'),
        'w3css-font-poppins' => $this->t('Poppins - sans-serif'),
        'w3css-font-prompt' => $this->t('Prompt - sans-serif'),
        'w3css-font-pt-serif' => $this->t('PT Serif - serif'),
        'w3css-font-quicksand' => $this->t('Quicksand - sans-serif'),
        'w3css-font-raleway' => $this->t('Raleway - sans-serif'),
        'w3css-font-roboto' => $this->t('Roboto - sans-serif'),
        'w3css-font-roboto-condensed' => $this->t('Roboto Condensed - sans-serif'),
        'w3css-font-roboto-mono' => $this->t('Roboto Mono - monospace'),
        'w3css-font-spline-sans' => $this->t('Spline Sans - sans-serif'),
        'w3css-font-source-sans-3' => $this->t('Source Sans 3 - sans-serif'),
        'w3css-font-titillium-web' => $this->t('Titillium Web - sans-serif'),
        'w3css-font-ubuntu' => $this->t('Ubuntu - sans-serif'),
        'w3css-font-work-sans' => $this->t('Work Sans - sans-serif'),

      ],
      '#default_value' => self::currentFont($this->settings->get('typography.site_font')),
    ];

    $form['drupal8_w3css_theme_settings']['w3_external_libraries']['typography_heading_font'] = [
      '#parents' => ['typography', 'heading_font'],
      '#type' => 'select',
      '#title' => $this->t('26 heading fonts: choose the heading font - the font will be applied to h1, h2 and h3 only'),
      '#description'   => $this->t('<p><a href=":demo" target="_blank" rel="noopener noreferrer">View a live demo of all heading fonts (opens in a new tab)</a>.</p><p>Heading Font CSS Classes: abril-fatface, aladin, anton, archivo-black, bebas-neue, big-shoulders-stencil-display, big-shoulders-stencil-text, black-ops-one, bree-serif, cinzel, dancing-script, dm-serif-display, doppio-one, fjalla-one, francois-one, fredericka-the-great, fredoka, hammersmith-one, indie-flower, kalam, lobster-two, patrick-hand, prosto-one, righteous, staatliches, yanone-kaffeesatz.</p>', [':demo' => 'https://w3css.flashwebcenter.com/node/104']),
      '#options' => [
        'none' => $this->t('None'),
        'w3css-font-abril-fatface' => $this->t('Abril Fatface  - cursive'),
        'w3css-font-aladin' => $this->t('Aladin  - cursive'),
        'w3css-font-anton' => $this->t('Anton  - sans-serif'),
        'w3css-font-archivo-black' => $this->t('Archivo Black - sans-serif'),
        'w3css-font-bebas-neue' => $this->t('Bebas Neue - cursive'),
        'w3css-font-big-shoulders-stencil-display' => $this->t('Big Shoulders Stencil Display - cursive'),
        'w3css-font-big-shoulders-stencil-text' => $this->t('Big Shoulders Stencil Text - cursive'),
        'w3css-font-black-ops-one' => $this->t('Black Ops One - cursive'),
        'w3css-font-bree-serif' => $this->t('Bree Serif - serif'),
        'w3css-font-cinzel' => $this->t('Cinzel - serif'),
        'w3css-font-dancing-script' => $this->t('Dancing Script - cursive'),
        'w3css-font-dm-serif-display' => $this->t('DM Serif Display - serif'),
        'w3css-font-doppio-one' => $this->t('Doppio - sans-serif'),
        'w3css-font-fjalla-one' => $this->t('Fjalla One - sans-serif'),
        'w3css-font-francois-one' => $this->t('Francois One - sans-serif'),
        'w3css-font-fredericka-the-great' => $this->t('Fredericka The Great  - cursive'),
        'w3css-font-fredoka' => $this->t('Fredoka - cursive'),
        'w3css-font-hammersmith-one' => $this->t('Hammersmith One - sans-serif'),
        'w3css-font-indie-flower' => $this->t('Indie Flower - cursive'),
        'w3css-font-kalam' => $this->t('Kalam - cursive'),
        'w3css-font-lobster-two' => $this->t('Lobster Two - cursive'),
        'w3css-font-patrick-hand' => $this->t('Patrick Hand - cursive'),
        'w3css-font-prosto-one' => $this->t('Prosto One - cursive'),
        'w3css-font-righteous' => $this->t('Righteous - cursive'),
        'w3css-font-staatliches' => $this->t('Staatliches - cursive'),
        'w3css-font-yanone-kaffeesatz' => $this->t('Yanone Kaffeesatz - sans-serif'),

      ],
      '#default_value' => self::currentFont($this->settings->get('typography.heading_font')),
    ];

    $form['drupal8_w3css_theme_settings']['w3_external_libraries']['typography_special_font'] = [
      '#parents' => ['typography', 'special_font'],
      '#type' => 'select',
      '#title' => $this->t('25 special fonts: they are not applied to any HTML tags by default and they are only imported. Each font library has a CSS class that you can apply to any HTML tag'),
      '#description'   => $this->t('<p>Special fonts always load from Google Fonts, even when local loading is checked above.</p><p><a href=":demo" target="_blank" rel="noopener noreferrer">View a live demo of all special fonts (opens in a new tab)</a>.</p><p>Special Font CSS Classes: akronim, barriecito, bungee-shade, ceviche-one, creepster, faster-one, finger-paint, frijole, irish-grover, kumar-one, londrina-outline, megrim, metal-mania, monoton, moo-lah-lah, new-rocker, nosifer, pirata-one, ruslan-display, rye, sancreek, slackey, trade-winds, train-one, unifrakturmaguntia.</p>', [':demo' => 'https://w3css.flashwebcenter.com/node/102']),
      '#options' => [
        'none' => $this->t('None'),
        'w3css-font-akronim' => $this->t('akronim - cursive'),
        'w3css-font-barriecito' => $this->t('barriecito - cursive'),
        'w3css-font-bungee-shade' => $this->t('bungee-shade - cursive'),
        'w3css-font-ceviche-one' => $this->t('ceviche-one - cursive'),
        'w3css-font-creepster' => $this->t('creepster - cursive'),
        'w3css-font-faster-one' => $this->t('faster-one - cursive'),
        'w3css-font-finger-paint' => $this->t('finger-paint - cursive'),
        'w3css-font-frijole' => $this->t('frijole - cursive'),
        'w3css-font-irish-grover' => $this->t('irish-grover - cursive'),
        'w3css-font-kumar-one' => $this->t('kumar-one - cursive'),
        'w3css-font-londrina-outline' => $this->t('londrina-outline - cursive'),
        'w3css-font-megrim' => $this->t('megrim - cursive'),
        'w3css-font-metal-mania' => $this->t('metal-mania - cursive'),
        'w3css-font-monoton' => $this->t('monoton - cursive'),
        'w3css-font-moo-lah-lah' => $this->t('moo-lah-lah - cursive'),
        'w3css-font-new-rocker' => $this->t('new-rocker - cursive'),
        'w3css-font-nosifer' => $this->t('nosifer - cursive'),
        'w3css-font-pirata-one' => $this->t('pirata-one - cursive'),
        'w3css-font-ruslan-display' => $this->t('ruslan-display - cursive'),
        'w3css-font-rye' => $this->t('rye - cursive'),
        'w3css-font-sancreek' => $this->t('sancreek - cursive'),
        'w3css-font-slackey' => $this->t('slackey - cursive'),
        'w3css-font-trade-winds' => $this->t('trade-winds - cursive'),
        'w3css-font-train-one' => $this->t('train-one - cursive'),
        'w3css-font-unifrakturmaguntia' => $this->t('unifrakturmaguntia - cursive'),

      ],
      '#default_value' => self::currentFont($this->settings->get('typography.special_font')),
    ];
  }

}
