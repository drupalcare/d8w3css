<?php

declare(strict_types=1);

namespace Drupal\drupal8_w3css_theme\Upgrade;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\StorageInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\Extension\ThemeExtensionList;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\KeyValueStore\KeyValueStoreInterface;
use Psr\Log\LoggerInterface;

/**
 * Moves stored theme settings from their old names to their new names.
 *
 * Covers this theme and every installed theme whose base-theme chain reaches
 * it. A value is never filled from a default and is never logged: reports carry
 * theme, key, file and line names only.
 *
 * This is a plain class, because core loads no services file for a theme.
 * create() is the one place it reaches the container; the post-update, the
 * settings form submit handler and the release note's restore call all use it.
 */
final class SettingsMigrator {

  /**
   * The machine name of this theme.
   */
  public const THEME = 'drupal8_w3css_theme';

  /**
   * The key-value collection holding the backup.
   */
  public const BACKUP_COLLECTION = 'drupal8_w3css_theme.settings_backup';

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly StorageInterface $storage,
    private readonly KeyValueStoreInterface $backup,
    private readonly ThemeExtensionList $themeList,
    private readonly ModuleExtensionList $moduleList,
    private readonly TypedConfigManagerInterface $typedConfig,
    private readonly LoggerInterface $logger,
  ) {}

  /**
   * Creates the migrator from the container.
   */
  public static function create(): self {
    return new self(
      \Drupal::configFactory(),
      \Drupal::service('config.storage'),
      \Drupal::keyValue(self::BACKUP_COLLECTION),
      \Drupal::service('extension.list.theme'),
      \Drupal::service('extension.list.module'),
      \Drupal::service('config.typed'),
      \Drupal::logger(self::THEME),
    );
  }

  /**
   * Returns this theme and each installed theme built on it, at any depth.
   *
   * @return list<string>
   *   The theme machine names, by base-theme chain and never by name.
   */
  public function themes(): array {
    $themes = [];
    foreach ($this->themeList->getList() as $name => $extension) {
      if (empty($extension->status)) {
        continue;
      }
      if ($name === self::THEME || isset($extension->base_themes[self::THEME])) {
        $themes[] = (string) $name;
      }
    }
    return $themes;
  }

  /**
   * Migrates one theme's settings in every config collection.
   *
   * The whole result is built in memory and backed up before the first write.
   * If a write fails, whatever was written is put back and the error is thrown,
   * so a failing theme stays untouched.
   *
   * @param string $theme
   *   The theme machine name.
   *
   * @return list<string>
   *   The names of old keys that could not move.
   *
   * @throws \Throwable
   *   If a write fails.
   */
  public function migrateTheme(string $theme): array {
    $name = $theme . '.settings';
    $plans = [];
    $stuck = [];
    $collections = array_merge([StorageInterface::DEFAULT_COLLECTION], $this->storage->getAllCollectionNames());
    foreach ($collections as $collection) {
      $raw = $this->storage->createCollection($collection)->read($name);
      if (!is_array($raw)) {
        continue;
      }
      $result = self::migrateData($raw);
      $stuck = array_merge($stuck, $result['stuck']);
      $this->logResult($theme, $result);
      if ($result['data'] !== $raw) {
        $plans[$collection] = ['raw' => $raw, 'data' => $result['data']];
      }
    }
    foreach ($plans as $collection => $plan) {
      $this->backup->setIfNotExists($name . ':' . $collection, $plan['raw']);
    }
    $written = [];
    try {
      foreach ($plans as $collection => $plan) {
        $written[] = $collection;
        $this->write($name, (string) $collection, $plan['data']);
      }
    }
    catch (\Throwable $e) {
      foreach ($written as $collection) {
        $this->write($name, (string) $collection, $plans[$collection]['raw']);
      }
      throw $e;
    }
    return array_values(array_unique($stuck));
  }

  /**
   * Casts the stored value of a moved key to its new type, aliases applied.
   *
   * @param string $old
   *   The old key, a row of SettingsMap::MAP['moved'].
   * @param mixed $raw
   *   The stored value.
   *
   * @return array{0: bool, 1: mixed}
   *   Whether the value casts, and the value.
   */
  public static function castMoved(string $old, mixed $raw): array {
    $row = SettingsMap::MAP['moved'][$old];
    [$castable, $value] = self::castValue($raw, $row['type']);
    if ($castable && is_string($value) && isset($row['aliases'][$value])) {
      $value = $row['aliases'][$value];
    }
    return [$castable, $value];
  }

  /**
   * Moves the old keys in one config array.
   *
   * @param array<string, mixed> $raw
   *   The stored config.
   *
   * @return array{data: array<string, mixed>, stuck: list<string>, conflicts: list<string>, unknown: list<string>}
   *   The new config; old keys left in place because their value cannot cast or
   *   their target is taken; old keys dropped because the new key held another
   *   value; top-level keys neither mapped nor core's.
   */
  public static function migrateData(array $raw): array {
    $data = $raw;
    $stuck = [];
    $conflicts = [];
    $groups = [];
    foreach (SettingsMap::MAP['moved'] as $old => $row) {
      $parts = explode('.', $row['new']);
      $groups[$parts[0]] = TRUE;
      if (!array_key_exists($old, $data)) {
        continue;
      }
      [$castable, $value] = self::castMoved($old, $data[$old]);
      if (!$castable || !self::canHold($data, $parts)) {
        $stuck[] = $old;
        continue;
      }
      if (NestedArray::keyExists($data, $parts)) {
        // The new key wins. A re-run or an import can leave both.
        if (NestedArray::getValue($data, $parts) !== $value) {
          $conflicts[] = $old;
        }
      }
      else {
        NestedArray::setValue($data, $parts, $value);
      }
      unset($data[$old]);
    }
    $known = array_merge(array_flip($stuck), $groups, array_flip(SettingsMap::MAP['kept']));
    return [
      'data' => $data,
      'stuck' => $stuck,
      'conflicts' => $conflicts,
      'unknown' => array_map('strval', array_keys(array_diff_key($data, $known))),
    ];
  }

  /**
   * Casts a stored value to the type of its new key.
   *
   * Explicit, because a sub-theme with no schema gets no cast from core.
   *
   * @param mixed $value
   *   The stored value.
   * @param string $type
   *   The target type: boolean, integer or string.
   *
   * @return array{0: bool, 1: mixed}
   *   Whether the value casts, and the cast value. NULL stays NULL.
   */
  public static function castValue(mixed $value, string $type): array {
    if ($value === NULL) {
      return [TRUE, NULL];
    }
    return match ($type) {
      'boolean' => in_array($value, [TRUE, FALSE, 0, 1, '0', '1', ''], TRUE) ? [TRUE, (bool) $value] : [FALSE, NULL],
      'integer' => self::castInteger($value),
      'string' => is_scalar($value) ? [TRUE, (string) $value] : [FALSE, NULL],
      default => [FALSE, NULL],
    };
  }

  /**
   * Casts a stored value to an integer without losing anything.
   *
   * An empty string becomes NULL, as core's own cast does.
   *
   * @param mixed $value
   *   The stored value.
   *
   * @return array{0: bool, 1: mixed}
   *   Whether the value casts, and the cast value.
   */
  private static function castInteger(mixed $value): array {
    if ($value === '') {
      return [TRUE, NULL];
    }
    if ((is_int($value) || is_float($value) || is_string($value)) && is_numeric($value) && (float) (int) $value === (float) $value) {
      return [TRUE, (int) $value];
    }
    return [FALSE, NULL];
  }

  /**
   * Checks that no scalar sits on the way to a new key.
   *
   * A sub-theme can have its own scalar setting named like a new group.
   *
   * @param array<string, mixed> $data
   *   The config being migrated.
   * @param list<string> $parts
   *   The new key, split.
   *
   * @return bool
   *   TRUE if the new key can be stored.
   */
  private static function canHold(array $data, array $parts): bool {
    array_pop($parts);
    foreach ($parts as $part) {
      if (!array_key_exists($part, $data)) {
        return TRUE;
      }
      if (!is_array($data[$part])) {
        return FALSE;
      }
      $data = $data[$part];
    }
    return TRUE;
  }

  /**
   * Returns old key names stored in, or overriding, one config object.
   *
   * @param string $name
   *   The config name.
   *
   * @return array{stored: list<string>, overridden: list<string>}
   *   The old keys in active storage, and those set by settings.php or a module.
   */
  public function staleKeys(string $name): array {
    $raw = $this->storage->read($name);
    $config = $this->configFactory->get($name);
    $stored = [];
    $overridden = [];
    foreach (array_keys(SettingsMap::MAP['moved']) as $old) {
      if (is_array($raw) && array_key_exists($old, $raw)) {
        $stored[] = $old;
      }
      if ($config->hasOverrides($old)) {
        $overridden[] = $old;
      }
    }
    return ['stored' => $stored, 'overridden' => $overridden];
  }

  /**
   * Finds code that may read or write an old key.
   *
   * Scans sub-themes and installed custom modules, one line at a time, so a call
   * split across lines can be missed. This theme is skipped: only its upgrade
   * classes and Twig list know old names.
   *
   * @return list<string>
   *   One "path:line key" entry per match.
   */
  public function scanCode(): array {
    $names = implode('|', array_keys(SettingsMap::MAP['moved']));
    $pattern = '/(?:theme_get_setting|getSetting|->get|->set|->clear)\(\s*["\'](' . $names . ')["\']/';
    $roots = [];
    foreach ($this->themes() as $theme) {
      if ($theme !== self::THEME) {
        $roots[] = $this->themeList->getPath($theme);
      }
    }
    foreach ($this->moduleList->getList() as $name => $extension) {
      if (!empty($extension->status) && str_contains($extension->getPath(), '/custom/')) {
        $roots[] = $this->moduleList->getPath((string) $name);
      }
    }
    $found = [];
    foreach ($roots as $root) {
      foreach ($this->codeFiles($root) as $file) {
        foreach (file($file) ?: [] as $index => $line) {
          if (preg_match($pattern, $line, $match) === 1) {
            $found[] = $file . ':' . ($index + 1) . ' ' . $match[1];
          }
        }
      }
    }
    return $found;
  }

  /**
   * Lists the PHP files under a directory.
   *
   * @param string $root
   *   The directory.
   *
   * @return list<string>
   *   The file paths, leaving out vendor, node_modules and tests.
   */
  private function codeFiles(string $root): array {
    // Extension paths are relative to the Drupal root, not to the working
    // directory, which depends on how the update was started.
    $root = DRUPAL_ROOT . '/' . $root;
    if (!is_dir($root)) {
      return [];
    }
    $directories = new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS);
    $filter = new \RecursiveCallbackFilterIterator($directories, static function (\SplFileInfo $file): bool {
      if ($file->isDir()) {
        return !in_array($file->getFilename(), ['vendor', 'node_modules', 'tests', '.git'], TRUE);
      }
      return in_array($file->getExtension(), ['php', 'module', 'theme', 'inc', 'install'], TRUE);
    });
    $files = [];
    foreach (new \RecursiveIteratorIterator($filter) as $file) {
      $files[] = $file->getPathname();
    }
    return $files;
  }

  /**
   * Returns sub-themes whose own schema still defines old keys.
   *
   * @return list<string>
   *   The theme machine names.
   */
  public function staleSchemas(): array {
    $stale = [];
    foreach ($this->themes() as $theme) {
      $name = $theme . '.settings';
      if ($theme === self::THEME || !$this->typedConfig->hasConfigSchema($name)) {
        continue;
      }
      $mapping = $this->typedConfig->getDefinition($name)['mapping'] ?? [];
      if (array_intersect_key($mapping, SettingsMap::MAP['moved'])) {
        $stale[] = $theme;
      }
    }
    return $stale;
  }

  /**
   * Puts every backed-up config back as it was before the migration.
   *
   * Settings saved after the migration are lost by this. Run it before rolling
   * the code back.
   *
   * @return list<string>
   *   The restored backup keys, as "config name:collection".
   */
  public function restore(): array {
    $restored = [];
    foreach ($this->backup->getAll() as $key => $raw) {
      [$name, $collection] = explode(':', (string) $key, 2);
      $this->write($name, $collection, $raw);
      $restored[] = (string) $key;
    }
    return $restored;
  }

  /**
   * Submit handler: removes old keys from the theme's config.
   *
   * Core adds its own submit handler after the form alter, so this runs first.
   * The old values move to their new names and core's save then sets the posted
   * values over them, so no value is ever only in memory.
   *
   * @param array<string, mixed> $form
   *   The form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public static function healOnSave(array &$form, FormStateInterface $form_state): void {
    $name = $form_state->getValue('config_key');
    if (!is_string($name)) {
      return;
    }
    $config = \Drupal::configFactory()->getEditable($name);
    $raw = $config->getRawData();
    $result = self::migrateData($raw);
    if ($result['data'] !== $raw) {
      $config->setData($result['data'])->save();
    }
    // Core's save sets each posted group over the whole stored group
    // (theme_settings_convert_to_config(), core/includes/theme.inc:241), so a
    // key a sub-theme keeps inside a group, and a key the form has no field
    // for, would be wiped. Put the stored keys under the posted ones; a posted
    // key still wins.
    foreach (self::groupNames() as $group) {
      $stored = $result['data'][$group] ?? NULL;
      $posted = $form_state->getValue($group);
      if (is_array($stored) && is_array($posted)) {
        $form_state->setValue($group, array_replace_recursive($stored, $posted));
      }
      elseif ($stored !== NULL && !is_array($stored)) {
        // A single value cannot share a key with a group, so core's save will
        // replace it. Named by key only.
        \Drupal::logger(self::THEME)->warning('Theme config @name: setting @key held a single value and is replaced by the group of that name on save.', [
          '@name' => $name,
          '@key' => $group,
        ]);
      }
    }
  }

  /**
   * Returns the group names that settings moved into.
   *
   * @return list<string>
   *   The first part of each new key.
   */
  public static function groupNames(): array {
    $groups = [];
    foreach (SettingsMap::MAP['moved'] as $row) {
      $groups[explode('.', $row['new'])[0]] = TRUE;
    }
    return array_map('strval', array_keys($groups));
  }

  /**
   * Returns group names that hold a single value in one config object.
   *
   * Core replaces the whole key on save, so the value cannot be kept.
   *
   * @param string $name
   *   The config name.
   *
   * @return list<string>
   *   The group names.
   */
  public function scalarGroups(string $name): array {
    $raw = $this->storage->read($name);
    $found = [];
    foreach (self::groupNames() as $group) {
      if (is_array($raw) && isset($raw[$group]) && !is_array($raw[$group])) {
        $found[] = $group;
      }
    }
    return $found;
  }

  /**
   * Writes one config object in one collection.
   *
   * @param string $name
   *   The config name.
   * @param string $collection
   *   The collection name.
   * @param array<string, mixed> $data
   *   The data.
   */
  private function write(string $name, string $collection, array $data): void {
    if ($collection === StorageInterface::DEFAULT_COLLECTION) {
      $this->configFactory->getEditable($name)->setData($data)->save();
      return;
    }
    $this->storage->createCollection($collection)->write($name, $data);
    $this->configFactory->reset($name);
    Cache::invalidateTags(['config:' . $name]);
  }

  /**
   * Logs what one config array could not move, by key name only.
   *
   * @param string $theme
   *   The theme machine name.
   * @param array{data: array<string, mixed>, stuck: list<string>, conflicts: list<string>, unknown: list<string>} $result
   *   The result of migrateData().
   */
  private function logResult(string $theme, array $result): void {
    $messages = [
      'stuck' => 'Theme @theme: not moved, value or target does not fit: @keys.',
      'conflicts' => 'Theme @theme: new key already set to another value, old key removed: @keys.',
      'unknown' => 'Theme @theme: keys not in the map, left as they are: @keys.',
    ];
    foreach ($messages as $list => $message) {
      if ($result[$list]) {
        $this->logger->warning($message, [
          '@theme' => $theme,
          '@keys' => implode(', ', $result[$list]),
        ]);
      }
    }
  }

}
