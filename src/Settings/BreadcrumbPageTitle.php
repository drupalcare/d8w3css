<?php

declare(strict_types=1);

namespace Drupal\drupal8_w3css_theme\Settings;

/**
 * Breadcrumb page title display settings.
 */
final class BreadcrumbPageTitle extends SettingsSection {

  /**
   * {@inheritdoc}
   */
  public function build(array &$form): void {
    $form['drupal8_w3css_theme_settings']['w3_breadcrumb_page_title'] = [
      '#type' => 'details',
      '#title' => $this->t("Remove page title from breadcrumb"),
      '#description'   => $this->t('<p>By default, the theme adds the page title as the last value to the breadcrumb.</p>'),
    ];
    $form['drupal8_w3css_theme_settings']['w3_breadcrumb_page_title']['breadcrumb_hide_page_title'] = [
      '#parents' => ['breadcrumb', 'hide_page_title'],
      '#type' => 'checkbox',
      '#title' => $this->t('Remove the page title from breadcrumb'),
      '#default_value' => $this->settings->get('breadcrumb.hide_page_title'),
    ];
  }

}
