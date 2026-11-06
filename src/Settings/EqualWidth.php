<?php

declare(strict_types=1);

namespace Drupal\drupal8_w3css_theme\Settings;

/**
 * Main container column width settings.
 */
final class EqualWidth extends SettingsSection {

  /**
   * {@inheritdoc}
   */
  public function build(array &$form): void {
    $form['drupal8_w3css_theme_settings']['w3_equal_width'] = [
      '#type' => 'details',
      '#title' => $this->t("Main Container Columns' Width - W3CSS Theme"),
      '#description'   => $this->t('<p>The default settings for the columns in the main containers are <strong>(25% 50% 25%)</strong></p>'),
    ];
    $form['drupal8_w3css_theme_settings']['w3_equal_width']['layout_equal_columns'] = [
      '#parents' => ['layout', 'equal_columns'],
      '#type' => 'checkbox',
      '#title' => $this->t('Change the columns in the main container to (33% 33% 33%)'),
      '#description' => $this->t('With one sidebar, the columns become 66% and 33%.'),
      '#default_value' => $this->settings->get('layout.equal_columns'),
    ];
  }

}
