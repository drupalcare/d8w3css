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
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    ];
    $form['drupal8_w3css_theme_settings']['credit_link']['footer_show_credit_link'] = [
      '#parents' => ['footer', 'show_credit_link'],
      '#type' => 'checkbox',
      '#title' => $this->t('Show/Hide Credit Text'),
      '#default_value' => $this->settings->get('footer.show_credit_link'),
      '#description'   => $this->t('Please display credit in footer'),
    ];
  }

}
