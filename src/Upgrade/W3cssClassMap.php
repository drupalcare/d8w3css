<?php

declare(strict_types=1);

namespace Drupal\drupal8_w3css_theme\Upgrade;

/**
 * Hands the class migration engine the theme's rename map.
 *
 * The rows live once, in ClassMap::MAP. Nothing here copies them; this class
 * only reshapes the sections into the decoded form ClassMigrator reads:
 * a `prefix`, and pattern keys turned into the engine's notation.
 *
 * - A trailing {n} or {menu} becomes a dynamic tail `*`: the old prefix
 *   followed by any value becomes the new prefix with the same value.
 * - {size} is expanded into one row per size, because the engine has no
 *   leading or inner wildcard.
 * - Any other pattern stops the migration: it must not guess.
 *
 * The vocabulary and unreleased sections are not passed on: vocabulary
 * names are core's and are never rewritten, and no site has an unreleased
 * name.
 *
 * @internal
 */
final class W3cssClassMap {

  /**
   * The prefix of every new name.
   */
  public const string PREFIX = 'w3css-';

  /**
   * The values {size} stands for, as ClassMap documents it.
   *
   * @var list<string>
   */
  public const array SIZES = ['small', 'medium', 'large'];

  /**
   * Engine sections whose values are new names.
   *
   * @var list<string>
   */
  private const array RENAME_SECTIONS = ['structural', 'ids', 'state', 'values', 'generic'];

  /**
   * Returns the map in the shape ClassMigrator reads.
   *
   * @return array<string, mixed>
   *   The map sections, plus theme and prefix.
   *
   * @throws \RuntimeException
   *   When a key uses a pattern the engine cannot express.
   */
  public static function load(): array {
    $map = [
      'theme' => 'drupal8_w3css_theme',
      'prefix' => self::PREFIX,
    ];
    foreach (self::RENAME_SECTIONS as $section) {
      $map[$section] = [];
      foreach (ClassMap::MAP[$section] as $old => $new) {
        $map[$section] += self::expand((string) $old, (string) $new);
      }
    }
    foreach (['context', 'removed'] as $section) {
      $map[$section] = [];
      foreach (ClassMap::MAP[$section] as $old => $note) {
        if (str_contains((string) $old, '{')) {
          throw new \RuntimeException('The class map has a pattern the migration cannot read: ' . $old);
        }
        $map[$section][$old] = $note;
      }
    }
    $map['frozen'] = ClassMap::MAP['frozen'];
    return $map;
  }

  /**
   * Returns the rename rows of every other kind: variables, JavaScript, ids.
   *
   * They live once, in w3css-rename-map.json next to this class. The same file
   * feeds docs/CLASS-MIGRATION.md through scripts/class-list.py.
   *
   * @return list<array<string, mixed>>
   *   The rows, each with kind, old, new, action, handling and source.
   *
   * @throws \RuntimeException
   *   When the file cannot be read or holds no rows.
   */
  public static function nameRows(): array {
    $file = __DIR__ . '/w3css-rename-map.json';
    $json = @file_get_contents($file);
    if ($json === FALSE) {
      throw new \RuntimeException('Could not read ' . $file . '.');
    }
    try {
      $data = json_decode($json, TRUE, 512, JSON_THROW_ON_ERROR);
    }
    catch (\JsonException $e) {
      throw new \RuntimeException('The rename map ' . $file . ' is not valid JSON: ' . $e->getMessage(), 0, $e);
    }
    $rows = is_array($data) ? ($data['rows'] ?? NULL) : NULL;
    if (!is_array($rows) || $rows === []) {
      throw new \RuntimeException('The rename map ' . $file . ' holds no rows.');
    }
    return array_values(array_filter($rows, 'is_array'));
  }

  /**
   * Builds the engine for variables, JavaScript names and library ids.
   *
   * @throws \RuntimeException
   *   When the rename map cannot be read or holds nothing to rewrite.
   */
  public static function nameMigrator(): NameMigrator {
    try {
      return new NameMigrator(self::nameRows());
    }
    catch (\InvalidArgumentException $e) {
      throw new \RuntimeException($e->getMessage(), 0, $e);
    }
  }

  /**
   * Builds the engine from the map.
   *
   * @throws \RuntimeException
   *   When the map cannot be read or holds no rows.
   */
  public static function migrator(): ClassMigrator {
    try {
      return new ClassMigrator(self::load());
    }
    catch (\InvalidArgumentException $e) {
      throw new \RuntimeException($e->getMessage() . ' (' . ClassMap::class . ')', 0, $e);
    }
  }

  /**
   * Turns one map row into engine rows.
   *
   * @return array<string, string>
   *   Old name => new name.
   *
   * @throws \RuntimeException
   *   When the row uses a pattern the engine cannot express.
   */
  private static function expand(string $old, string $new): array {
    if (!str_contains($old, '{')) {
      return [$old => $new];
    }
    if (str_contains($old, '{size}')) {
      $rows = [];
      foreach (self::SIZES as $size) {
        $rows += self::expand(str_replace('{size}', $size, $old), str_replace('{size}', $size, $new));
      }
      return $rows;
    }
    foreach (['{n}', '{menu}'] as $part) {
      if (str_ends_with($old, $part) && str_ends_with($new, $part) && substr_count($old, '{') === 1 && substr_count($new, '{') === 1) {
        return [substr($old, 0, -strlen($part)) . '*' => substr($new, 0, -strlen($part)) . '*'];
      }
    }
    throw new \RuntimeException('The class map has a pattern the migration cannot read: ' . $old);
  }

}
