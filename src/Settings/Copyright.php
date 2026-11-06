<?php

declare(strict_types=1);

namespace Drupal\drupal8_w3css_theme\Settings;

/**
 * Copyright text settings.
 */
final class Copyright extends SettingsSection {

  /**
   * {@inheritdoc}
   */
  public function build(array &$form): void {
    $form['drupal8_w3css_theme_settings']['copyright'] = [
      '#type' => 'details',
      '#title' => $this->t('Copyright - W3CSS Theme'),
    ];
    $form['drupal8_w3css_theme_settings']['copyright']['footer_copyright_text'] = [
      '#parents' => ['footer', 'copyright_text'],
      '#type' => 'textfield',
      '#title' => $this->t('Enter copyright text'),
      '#description' => $this->t("Type %year% instead of an actual year number for dynamic copyright. (Copyright © %year% Company Name - All rights reserved)."),
      '#default_value' => $this->settings->get('footer.copyright_text'),
    ];
  }

}
