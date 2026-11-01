<?php

declare(strict_types=1);

namespace Drupal\drupal8_w3css_theme\Settings;

/**
 * Region border animation settings.
 */
final class BorderAnimation extends SettingsSection {

  /**
   * {@inheritdoc}
   */
  public function build(array &$form): void {
    $form['drupal8_w3css_theme_settings']['w3_border_animation'] = [
      '#type' => 'details',
      '#title' => $this->t("Enable Border Animation - W3CSS Theme"),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
      '#description'   => $this->t("<p><strong>You can enable/disable the border animation for the top regions, bottom regions and footer regions.</strong></p>"),
    ];

    $form['drupal8_w3css_theme_settings']['w3_border_animation']['border_animation'] = [
      '#type' => 'checkbox',
      '#title' => $this->t("Check this box to animate the region's border."),
      '#default_value' => $this->themeSettings->getSetting('border_animation'),
    ];
  }

}
