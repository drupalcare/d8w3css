<?php

declare(strict_types=1);

namespace Drupal\drupal8_w3css_theme\Settings;

/**
 * Opacity animation on scroll settings.
 */
final class FullOpacityOnScroll extends SettingsSection {

  /**
   * {@inheritdoc}
   */
  public function build(array &$form): void {
    $form['drupal8_w3css_theme_settings']['w3_full_opacity'] = [
      '#type' => 'details',
      '#title' => $this->t("Enable full opacity on scroll - W3CSS Theme"),
      '#description'   => $this->t('<p>When you visit the page, the hidden blocks are invisible and they will start to show/animate when you scroll down. The default setting is enabled. Visitors who ask their system for reduced motion see every block at once.</p>'),
    ];
    $form['drupal8_w3css_theme_settings']['w3_full_opacity']['effects_reveal_on_scroll'] = [
      '#parents' => ['effects', 'reveal_on_scroll'],
      '#type' => 'checkbox',
      '#title' => $this->t('Animate the opacity on scroll down'),
      '#default_value' => $this->settings->get('effects.reveal_on_scroll'),
    ];
  }

}
