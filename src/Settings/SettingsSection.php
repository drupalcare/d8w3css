<?php

declare(strict_types=1);

namespace Drupal\drupal8_w3css_theme\Settings;

use Drupal\Core\Extension\ThemeSettingsProvider;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * One section of the theme settings form.
 *
 * Built and thrown away inside the form alter, so nothing here is serialized
 * with the form. Each field sets #parents to its nested settings key, and its
 * form key is that key with the dots replaced. Nothing on the form sets #tree.
 */
abstract class SettingsSection {
  use StringTranslationTrait;

  /**
   * The property of a section that holds its short hint, for the navigation.
   */
  public const HINT = '#d8w3css_hint';

  /**
   * Reads the settings by their new names; a plain class, not a service.
   */
  protected readonly SettingsReader $settings;

  public function __construct(ThemeSettingsProvider $themeSettings) {
    $this->settings = new SettingsReader($themeSettings);
  }

  /**
   * Adds this section's elements to the theme settings form.
   *
   * @param array<string, mixed> $form
   *   The form being altered.
   */
  abstract public function build(array &$form): void;

}
