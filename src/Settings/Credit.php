<?php

declare(strict_types=1);

namespace Drupal\drupal8_w3css_theme\Settings;

/**
 * Credit link display settings.
 */
final class Credit extends SettingsSection {

  /**
   * {@inheritdoc}
   */
  public function build(array &$form): void {
    $form['drupal8_w3css_theme_settings']['credit_link'] = [
      '#type' => 'details',
      '#title' => $this->t('Credit - W3CSS Theme'),
    ];
    $form['drupal8_w3css_theme_settings']['credit_link']['footer_show_credit_link'] = [
      '#parents' => ['footer', 'show_credit_link'],
      '#type' => 'checkbox',
      '#title' => $this->t('Show/hide credit text'),
      '#default_value' => $this->settings->get('footer.show_credit_link'),
      '#description'   => $this->t('Shows the credit link in the footer.'),
    ];
  }

}
