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
      '#description'   => $this->t("<p><strong>You can enable/disable the border animation for the top regions, bottom regions and footer regions.</strong></p>"),
    ];

    $form['drupal8_w3css_theme_settings']['w3_border_animation']['effects_animated_borders'] = [
      '#parents' => ['effects', 'animated_borders'],
      '#type' => 'checkbox',
      '#title' => $this->t("Check this box to animate the region's border."),
      '#default_value' => $this->settings->get('effects.animated_borders'),
    ];
  }

}
