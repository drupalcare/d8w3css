<?php

declare(strict_types=1);

namespace Drupal\drupal8_w3css_theme\Settings;

use Drupal\Core\Extension\ThemeSettingsProvider;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * One section of the theme settings form.
 *
 * Built and thrown away inside the form alter, so nothing here is serialized
 * with the form.
 */
abstract class SettingsSection {
  use StringTranslationTrait;

  public function __construct(
    protected readonly ThemeSettingsProvider $themeSettings,
  ) {}

  /**
   * Adds this section's elements to the theme settings form.
   *
   * @param array<string, mixed> $form
   *   The form being altered.
   */
  abstract public function build(array &$form): void;

}
