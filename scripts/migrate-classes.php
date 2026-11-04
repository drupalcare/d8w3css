<?php

/**
 * @file
 * Renames the W3CSS Theme's old classes and ids in a sub-theme or module.
 *
 * Run: drush php:script <path to this file> -- <target> [--apply]
 *   [--report=file] [--ext=css,js] [--stored] [--content]
 *
 * Dry run by default. Same rules as Solo's drush solo:migrate-classes:
 * .css .scss .sass .less .twig .js are rewritten; .theme .php .module .inc
 * are only reported; .yml, *.min.*, symbolic links and hard-linked files are
 * never read. The target must be inside the Drupal root and may not be core,
 * this theme or the public files folder. The report is a new file (0600),
 * never inside the web root. There is deliberately no web UI for this.
 */

declare(strict_types=1);

use Drush\Drush;
use Drupal\drupal8_w3css_theme\Upgrade\ClassMigrationPaths;
use Drupal\drupal8_w3css_theme\Upgrade\ClassMigrator;
use Drupal\drupal8_w3css_theme\Upgrade\SettingsMigrator;
use Drupal\drupal8_w3css_theme\Upgrade\StoredClassMigrator;
use Drupal\drupal8_w3css_theme\Upgrade\W3cssClassMap;

if (!isset($extra) || !defined('DRUPAL_ROOT')) {
  fwrite(STDERR, "Run this with: drush php:script <path to migrate-classes.php> -- <target>\n");
  return;
}

/**
 * Wraps text in a Markdown code span that survives backticks inside it.
 */
$code = static function (string $text): string {
  $fence = '`';
  while (str_contains($text, $fence)) {
    $fence .= '`';
  }
  $pad = (str_starts_with($text, '`') || str_ends_with($text, '`')) ? ' ' : '';
  return $fence . $pad . $text . $pad . $fence;
};
$say = static function (string $line): void {
  fwrite(STDOUT, $line . "\n");
};
$fail = static function (string $message): never {
  throw new \RuntimeException($message . ' Nothing was changed.');
};

// Arguments: one target, flags once each, nothing unknown.
$flags = ['apply' => FALSE, 'stored' => FALSE, 'content' => FALSE];
$values = ['report' => NULL, 'ext' => NULL];
$target = NULL;
$seen = [];
foreach ((array) $extra as $arg) {
  $arg = (string) $arg;
  if (!str_starts_with($arg, '--')) {
    if ($target !== NULL) {
      $fail('Give one target only.');
    }
    $target = $arg;
    continue;
  }
  [$name, $value] = array_pad(explode('=', substr($arg, 2), 2), 2, NULL);
  if (isset($seen[$name])) {
    $fail('Give --' . $name . ' once' . ($name === 'ext' ? ', with a comma-separated list, for example --ext=css,js.' : '.'));
  }
  $seen[$name] = TRUE;
  if (array_key_exists($name, $flags) && $value === NULL) {
    $flags[$name] = TRUE;
  }
  elseif (array_key_exists($name, $values)) {
    if ($value === NULL || trim($value) === '') {
      $fail('--' . $name . ' needs a value, for example ' . ($name === 'ext' ? '--ext=css,scss.' : '--report=w3css-report.md.'));
    }
    $values[$name] = $value;
  }
  else {
    $fail('Unknown option ' . $arg . '. Allowed: --apply --report=file --ext=list --stored --content.');
  }
}
if ($target === NULL || $target === '') {
  $fail('Give a target: a theme machine name, or a directory inside the Drupal root.');
}
$apply = $flags['apply'];

$themeList = \Drupal::service('extension.list.theme');
$public = \Drupal::service('file_system')->realpath('public://');
$forbidden = ['core'];
if ($themeList->exists(SettingsMigrator::THEME)) {
  $forbidden[] = $themeList->getPath(SettingsMigrator::THEME);
}
if (is_string($public) && $public !== '') {
  $forbidden[] = $public;
}
$cwd = class_exists('\Drush\Drush') ? Drush::config()->cwd() : (string) getcwd();

try {
  $migrator = W3cssClassMap::migrator();
  $extensions = $migrator->normalizeExtensions($values['ext']);
  $paths = new ClassMigrationPaths(
    DRUPAL_ROOT,
    static fn (string $name): ?string => $themeList->exists($name) ? $themeList->getPath($name) : NULL,
    $forbidden,
    is_string($public) && $public !== '' ? [$public] : [],
  );
  $root = $paths->resolveTarget($target, $cwd);
  $reportFile = NULL;
  $reportMovedFrom = NULL;
  if ($values['report'] !== NULL) {
    $resolved = $paths->resolveReport($values['report'], $cwd);
    $reportFile = $resolved['path'];
    $reportMovedFrom = $resolved['moved_from'];
  }
}
catch (\InvalidArgumentException $e) {
  $fail($e->getMessage());
}

// Create the report before touching anything, so a bad report path stops
// the run first. 'x' never overwrites; 0600 keeps source lines private.
$reportHandle = NULL;
if ($reportFile !== NULL) {
  $reportHandle = @fopen($reportFile, 'x');
  if ($reportHandle === FALSE) {
    $error = error_get_last();
    $fail('Could not create the report ' . $reportFile . ': ' . ($error['message'] ?? 'unknown error') . '.');
  }
  @chmod($reportFile, 0600);
}

$report = [
  '# W3CSS Theme 3 class migration report',
  '',
  'Target: ' . $paths->relative($root) . ' (relative to the Drupal root)',
  'Mode: ' . ($apply ? 'APPLIED' : 'dry run'),
  'Extensions: ' . implode(', ', $extensions),
  '',
];
$review = [];
$changedLines = 0;
$changedFiles = 0;
$failed = 0;

// Stored values are reported first: they change the live site at once,
// while theme files change only after a cache rebuild.
if ($flags['stored'] || $flags['content']) {
  try {
    $stored = new StoredClassMigrator(
      $migrator,
      \Drupal::configFactory(),
      \Drupal::entityTypeManager(),
      \Drupal::service('entity_field.manager'),
      \Drupal::time(),
      SettingsMigrator::create()->themes(),
    );
    $rows = [];
    if ($flags['stored']) {
      $rows = array_merge($stored->themeSettings($apply), $stored->configuration($apply));
    }
    if ($flags['content']) {
      $rows = array_merge($rows, $stored->content($apply));
    }
  }
  catch (\Throwable $e) {
    if ($reportHandle !== NULL) {
      fclose($reportHandle);
    }
    throw new \RuntimeException('Stored values: ' . $e->getMessage() . '. No theme file was changed.', 0, $e);
  }
  $report[] = '# Stored values (configuration' . ($flags['content'] ? ' and content' : '') . ')';
  $report[] = '';
  $report[] = '| Store | Name | Where | Before | After | Check |';
  $report[] = '|---|---|---|---|---|---|';
  foreach ($rows as $row) {
    $cells = [$row['store']];
    foreach (['name', 'path', 'before', 'after'] as $key) {
      $cells[] = $code(mb_substr(str_replace('|', '\\|', $row[$key]), 0, 120));
    }
    $cells[] = $row['note'];
    $report[] = '| ' . implode(' | ', $cells) . ' |';
  }
  $report[] = '';
  $renames = count(array_filter($rows, static fn (array $row): bool => $row['note'] === ''));
  $say(sprintf('Stored values: %d rename(s) %s, %d item(s) to check by hand.', $renames, $apply ? 'saved' : 'would be saved', count($rows) - $renames));
}

$scan = $migrator->scan($root, $extensions);
$scan['readonly'] = [];
$scan['owner'] = [];
$euid = function_exists('posix_geteuid') ? posix_geteuid() : NULL;
foreach ($scan['files'] as $path) {
  $real = realpath($path);
  if ($real === FALSE || !str_starts_with($real, $root . DIRECTORY_SEPARATOR)) {
    continue;
  }
  $relative = substr($real, strlen($root) + 1);
  // Never replace a read-only file, or one owned by another user (the
  // replacement would belong to whoever runs Drush, e.g. root via sudo).
  if (!is_writable($real)) {
    $scan['readonly'][] = $real;
    continue;
  }
  if ($euid !== NULL && fileowner($real) !== $euid) {
    $scan['owner'][] = $real;
    continue;
  }
  $contents = file_get_contents($real);
  if ($contents === FALSE) {
    fwrite(STDERR, 'Could not read ' . $relative . ".\n");
    $failed++;
    continue;
  }
  $result = $migrator->migrate($contents, $real);
  foreach ($result['notes'] as $note) {
    $review[$note['kind']][] = [$relative] + $note;
  }
  if ($result['changes'] === []) {
    continue;
  }
  if ($apply && !ClassMigrationPaths::writeAtomic($real, $result['content'])) {
    fwrite(STDERR, 'Could not write ' . $relative . "; it was left unchanged.\n");
    $failed++;
    continue;
  }
  $changedFiles++;
  $changedLines += count($result['changes']);
  $report[] = '## ' . $relative;
  $report[] = '';
  foreach ($result['changes'] as $change) {
    $report[] = '- L' . $change['line'];
    $report[] = '  - ' . $code($change['before']);
    $report[] = '  + ' . $code($change['after']);
  }
  $report[] = '';
  $say(sprintf('%s: %d line(s)', $relative, count($result['changes'])));
}

$skipped = [
  'symlinks' => 'Symbolic links skipped (not followed; migrate their targets separately)',
  'hardlinks' => 'Hard-linked files skipped (the same file has another name elsewhere)',
  'readonly' => 'Read-only files skipped (make them writable to migrate them)',
  'owner' => 'Files owned by another user skipped (run Drush as the user who owns the theme files)',
];
foreach ($skipped as $key => $label) {
  if ($scan[$key] === []) {
    continue;
  }
  $report[] = sprintf('## %s (%d)', $label, count($scan[$key]));
  $report[] = '';
  foreach ($scan[$key] as $path) {
    $report[] = '- ' . $code(substr($path, strlen($root) + 1));
  }
  $report[] = '';
  $say(sprintf('%s: %d', $label, count($scan[$key])));
}

$reviewCount = 0;
if ($review !== []) {
  $report[] = '# Needs manual review';
  $report[] = '';
}
foreach (ClassMigrator::NOTE_LABELS as $kind => $label) {
  if (empty($review[$kind])) {
    continue;
  }
  $reviewCount += count($review[$kind]);
  $report[] = sprintf('## %s (%d)', $label, count($review[$kind]));
  $report[] = '';
  $report[] = '| File | Line | Token | New | Source |';
  $report[] = '|---|---|---|---|---|';
  foreach ($review[$kind] as $note) {
    $source = mb_substr(str_replace('|', '\\|', $note['source']), 0, 120);
    $report[] = sprintf('| %s | %d | %s | %s | %s |', $note[0], $note['line'], $code($note['token']), $note['new'], $code($source));
  }
  $report[] = '';
  $say(sprintf('  - %-18s %4d  %s', $kind, count($review[$kind]), $label));
}

$say(sprintf('%s %d line(s) in %d file(s) %s; %d item(s) need manual review; %d file(s) skipped.',
  $apply ? 'Applied:' : 'Dry run:',
  $changedLines,
  $changedFiles,
  $apply ? 'changed' : 'would change',
  $reviewCount,
  count($scan['symlinks']) + count($scan['hardlinks']) + count($scan['readonly']) + count($scan['owner']),
));

if ($reportHandle !== NULL && $reportFile !== NULL) {
  $body = implode("\n", $report) . "\n";
  $written = fwrite($reportHandle, $body);
  fclose($reportHandle);
  if ($written !== strlen($body)) {
    throw new \RuntimeException('Could not write the whole report to ' . $reportFile . '.');
  }
  $say($reportMovedFrom !== NULL
    ? 'Report written to ' . $reportFile . ' (not ' . $reportMovedFrom . ': that is inside the web root, so it went to the project folder)'
    : 'Report written to ' . $reportFile);
}
elseif (!$apply) {
  $say('Nothing was written. Add --report=w3css-report.md to list every change, then --apply to write them.');
}

if ($failed > 0) {
  throw new \RuntimeException($failed . ' file(s) could not be read or written; see the messages above.');
}
