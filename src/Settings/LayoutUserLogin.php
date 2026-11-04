<?php

declare(strict_types=1);

namespace Drupal\drupal8_w3css_theme\Settings;

/**
 * Custom layout for login, register, and reset password pages.
 */
final class LayoutUserLogin extends SettingsSection {

  /**
   * {@inheritdoc}
   */
  public function build(array &$form): void {
    $form['drupal8_w3css_theme_settings']['w3_layout_user'] = [
      '#type' => 'details',
      '#title' => $this->t('Enable custom layout for the login, register and reset password pages.'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
      '#description'   => $this->t('<p>If you check this box the login form will be centered and left/right sides will be hidden.</p>'),
    ];
    $form['drupal8_w3css_theme_settings']['w3_layout_user']['layout_centered_login_pages'] = [
      '#parents' => ['layout', 'centered_login_pages'],
      '#type' => 'checkbox',
      '#title' => $this->t('Check this box to change the layout for the login, register and reset password pages.'),
      '#default_value' => $this->settings->get('layout.centered_login_pages'),
    ];
  }

}
