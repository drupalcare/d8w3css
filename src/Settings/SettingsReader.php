<?php

declare(strict_types=1);

namespace Drupal\drupal8_w3css_theme\Settings;

use Drupal\Core\Extension\ThemeSettingsProvider;
use Drupal\drupal8_w3css_theme\Upgrade\SettingsMap;
use Drupal\drupal8_w3css_theme\Upgrade\SettingsMigrator;

/**
 * Reads this theme's settings by their new names.
 *
 * A stale config import, or a sub-theme installed later with old default config,
 * can leave a value under its old name. The new key is read first, then the old
 * one through the map. Nothing is written here, so this is not a second store;
 * the settings form heals a theme's config on its next save.
 *
 * This is a plain class. Core loads no services file for a theme, so each hook
 * class and settings section creates it from the injected settings provider.
 */
final class SettingsReader {

  public function __construct(
    private readonly ThemeSettingsProvider $provider,
  ) {}

  /**
   * Returns a setting of the active theme.
   *
   * @param string $key
   *   The new key, such as layout.width.
   *
   * @return mixed
   *   The value, or NULL if the setting is absent.
   */
  public function get(string $key): mixed {
    $value = $this->provider->getSetting($key);
    if ($value !== NULL) {
      return $value;
    }
    $old = SettingsMap::oldName($key);
    if ($old === NULL) {
      return NULL;
    }
    [$castable, $cast] = SettingsMigrator::castValue(
      $this->provider->getSetting($old),
      SettingsMap::MAP['moved'][$old]['type'],
    );
    return $castable ? $cast : NULL;
  }

  /**
   * Returns the value of a Twig variable.
   *
   * Templates of this theme and its sub-themes keep the variable names, which
   * are the old key names, so the map turns each into its new key.
   *
   * @param string $variable
   *   The Twig variable name.
   *
   * @return mixed
   *   The value, or NULL if the setting is absent or the name is not in the map.
   */
  public function forTwigVariable(string $variable): mixed {
    $row = SettingsMap::MAP['moved'][$variable] ?? NULL;
    return $row === NULL ? NULL : $this->get($row['new']);
  }

}
