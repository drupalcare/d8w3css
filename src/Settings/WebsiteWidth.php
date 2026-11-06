<?php

declare(strict_types=1);

namespace Drupal\drupal8_w3css_theme\Settings;

/**
 * Website maximum width settings.
 */
final class WebsiteWidth extends SettingsSection {

  /**
   * {@inheritdoc}
   */
  public function build(array &$form): void {
    $form['drupal8_w3css_theme_settings']['w3_website_width'] = [
      '#type' => 'details',
      '#title' => $this->t("Website's width - W3CSS Theme"),
      '#description' => $this->t("<p><strong>You can change the whole website's width by selecting any of the desired width in pixels.</strong></p><p>w3-width-100-percent, w3-width-2560, w3-width-1920, w3-width-1360, w3-width-1280, w3-width-1024 and w3-width-800. The default width is 1600 pixels.</p>"),
    ];

    $form['drupal8_w3css_theme_settings']['w3_website_width']['layout_width'] = [
      '#parents' => ['layout', 'width'],
      '#type' => 'select',
      '#title' => $this->t("Choose the desired website's width"),
      '#options' => [
        'none' => $this->t('Default - 1600 Pixels'),
        'w3-width-2560' => $this->t('2560 Pixels'),
        'w3-width-1920' => $this->t('1920 Pixels'),
        'w3-width-1360' => $this->t('1360 Pixels'),
        'w3-width-1280' => $this->t('1280 Pixels'),
        'w3-width-1024' => $this->t('1024 Pixels'),
        'w3-width-800' => $this->t('800 Pixels'),
        'w3-width-100-percent' => $this->t('100%'),
      ],
      '#default_value' => $this->settings->get('layout.width'),
    ];
  }

}
