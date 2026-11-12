<?php

/**
 * @file
 * Post update functions for the W3CSS Theme.
 */

declare(strict_types=1);

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\drupal8_w3css_theme\Settings\ExternalLibraries;
use Drupal\drupal8_w3css_theme\Upgrade\SettingsMigrator;

/**
 * Renames theme settings to nested keys in this theme and its sub-themes.
 */
function drupal8_w3css_theme_post_update_rename_settings(array &$sandbox): string {
  // One theme per pass. A theme that fails stays untouched, the others finish,
  // and the exception after the last pass names it. Core then keeps this update
  // pending, and a re-run changes only what still holds old keys.
  $migrator = SettingsMigrator::create();
  if (!isset($sandbox['themes'])) {
    $sandbox['themes'] = $migrator->themes();
    $sandbox['done'] = [];
    $sandbox['failed'] = [];
    $sandbox['stuck'] = [];
  }
  $theme = array_shift($sandbox['themes']);
  if ($theme !== NULL) {
    try {
      $stuck = $migrator->migrateTheme($theme);
      $sandbox['done'][] = $theme;
      if ($stuck) {
        $sandbox['stuck'][] = $theme . ' (' . implode(', ', $stuck) . ')';
      }
    }
    catch (\Throwable $e) {
      // The exception class only: a storage message can quote a stored value.
      $sandbox['failed'][] = $theme . ' (' . get_class($e) . ')';
      \Drupal::logger(SettingsMigrator::THEME)->error('Theme @theme: settings not migrated, @class.', [
        '@theme' => $theme,
        '@class' => get_class($e),
      ]);
    }
  }
  if ($sandbox['themes']) {
    $sandbox['#finished'] = count($sandbox['done']) + count($sandbox['failed']);
    $sandbox['#finished'] /= $sandbox['#finished'] + count($sandbox['themes']);
    return '';
  }
  $sandbox['#finished'] = 1;

  // Everything below is reported, never rewritten: names, files and lines only.
  $report = ['Settings renamed in: ' . (implode(', ', $sandbox['done']) ?: 'none') . '.'];
  if ($sandbox['stuck']) {
    $report[] = 'Keys left under their old name, value or target does not fit: ' . implode('; ', $sandbox['stuck']) . '.';
  }
  $overrides = [];
  foreach ($sandbox['done'] as $name) {
    $keys = $migrator->staleKeys($name . '.settings')['overridden'];
    if ($keys) {
      $overrides[] = $name . '.settings (' . implode(', ', $keys) . ')';
    }
  }
  if ($overrides) {
    $report[] = 'Overrides in settings.php or a module still use old names and no longer apply, change them by hand: ' . implode('; ', $overrides) . '.';
  }
  $schemas = $migrator->staleSchemas();
  if ($schemas) {
    $report[] = 'Sub-themes whose own schema still defines old keys: ' . implode(', ', $schemas) . '.';
  }
  $code = $migrator->scanCode();
  if ($code) {
    $report[] = 'Code that may use old key names, change it by hand: ' . implode('; ', $code) . '.';
  }
  $message = implode(' ', $report);
  // Info level, not notice: drush forwards notices to the console and also
  // prints the returned message, so notice showed the report twice.
  \Drupal::logger(SettingsMigrator::THEME)->info('@report', ['@report' => $message]);
  if ($sandbox['failed']) {
    throw new \RuntimeException('Theme settings not migrated for: ' . implode('; ', $sandbox['failed']) . '. ' . $message);
  }
  return $message;
}

/**
 * Moves the removed Font Awesome choices to Font Awesome 7 from the CDN.
 */
function drupal8_w3css_theme_post_update_switch_font_awesome_7(): string {
  // Sorts after rename_settings on purpose: post-updates run by name, and the
  // key below only exists once the settings are renamed. Choices 3 and 4 were
  // the removed 5.x and 6.x CDN options, and 2 is now the CDN. Choices 0 and 1
  // keep their meaning.
  $factory = \Drupal::configFactory();
  $moved = [];
  foreach (SettingsMigrator::create()->themes() as $theme) {
    $config = $factory->getEditable($theme . '.settings');
    if (in_array((int) $config->get('libraries.font_awesome'), [3, 4], TRUE)) {
      $config->set('libraries.font_awesome', 2)->save();
      $moved[] = $theme;
    }
  }
  return $moved ? 'Moved to Font Awesome 7 from the CDN: ' . implode(', ', $moved) . '.' : 'No theme used a removed Font Awesome choice.';
}

/**
 * Renames the stored font choices to the w3css-font prefix.
 */
function drupal8_w3css_theme_post_update_rename_typography_font_values(): string {
  // Post-updates run in name order: "rename_typography..." sorts after
  // "rename_settings", which creates the typography keys this reads. The
  // earlier name rename_font_values sorted first and found nothing (measured
  // on a 2.0.18 database, 2026-11-11). Only a value that
  // still starts with the old prefix changes, so a second run changes nothing.
  // Theme settings only: block config and other modules hold no font values.
  $factory = \Drupal::configFactory();
  $count = 0;
  foreach (SettingsMigrator::create()->themes() as $theme) {
    $config = $factory->getEditable($theme . '.settings');
    $changed = FALSE;
    foreach (['site_font', 'heading_font', 'special_font'] as $key) {
      $value = $config->get('typography.' . $key);
      if (is_string($value) && str_starts_with($value, 'd8w3css-')) {
        $config->set('typography.' . $key, ExternalLibraries::currentFont($value));
        $changed = TRUE;
        $count++;
      }
    }
    if ($changed) {
      $config->save();
    }
  }
  return (string) new TranslatableMarkup('Renamed @count stored font choices to the w3css-font prefix.', ['@count' => $count]);
}
