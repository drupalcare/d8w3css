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
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    ];
    $form['drupal8_w3css_theme_settings']['copyright']['copyright_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter Copyright Text'),
      '#description' => $this->t("Type %year% instead of an actual year number for dynamic copyright. (Copyright © %year% Company Name - All rights reserved)."),
      '#default_value' => $this->themeSettings->getSetting('copyright_text'),
    ];
  }

}
