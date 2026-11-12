<?php

declare(strict_types=1);

namespace Drupal\drupal8_w3css_theme\Upgrade;

use Drupal\Component\Serialization\Exception\InvalidDataTypeException;
use Drupal\Component\Serialization\Yaml;

/**
 * Rewrites the renames that are not classes or ids: variables, JavaScript, data attributes and library ids.
 *
 * Pure logic plus one file walk for YAML; no Drush, no database, no web UI.
 * ClassMigrator keeps classes and ids; this class handles the other kinds of
 * the W3CSS Theme 3.0 rename list (src/Upgrade/w3css-rename-map.json), and its
 * result has the same shape, so the Drush script merges both. It is not shared
 * with Solo.
 *
 * Rules:
 * 1. A CSS variable is rewritten whole, never inside a longer name:
 *    `--w-theme` does not touch `--w-theme-b`. Prefix rows swap the prefix.
 * 2. A JavaScript behavior is rewritten where it is named: `Drupal.behaviors.x`
 *    and `Drupal.behaviors['x']`. A once id is rewritten as the first argument of
 *    once(), once.remove(), once.find() and once.filter(), and as the value of a
 *    data-once selector.
 * 3. A data attribute is rewritten as a whole name. The same name read through
 *    `dataset` is reported, because its spelling differs.
 * 4. A library id is rewritten only in `*.info.yml` and `*.libraries.yml` files
 *    when YAML rewriting is on. Everywhere else it is reported. The old id keeps
 *    working as a deprecated alias for one major.
 * 5. A moved theme file path is reported when the line names this theme: a
 *    libraries-override or libraries-extend entry by path stops matching.
 * 6. A name with no decided 3.0 name is reported, never rewritten.
 * 7. PHP, module and include files are reported, never written. YAML files
 *    are read only for library ids and file paths.
 * 8. A second run changes nothing.
 */
final class NameMigrator {

  /**
   * Extensions rewritten in place.
   *
   * @var list<string>
   */
  public const array REWRITE_EXTENSIONS = ['css', 'scss', 'sass', 'less', 'twig', 'js'];

  /**
   * Extensions read and reported, never written (YAML only with the flag).
   *
   * @var list<string>
   */
  public const array REPORT_EXTENSIONS = ['theme', 'php', 'module', 'inc', 'yml'];

  /**
   * Directory names never descended into.
   *
   * @var list<string>
   */
  private const array SKIP_DIRS = ['node_modules', 'vendor', '.git', 'dist'];

  /**
   * Extensions where a library id or file path is noted.
   *
   * @var list<string>
   */
  private const array ALL_EXTENSIONS = [
    'css', 'scss', 'sass', 'less', 'twig', 'js',
    'theme', 'php', 'module', 'inc', 'yml',
  ];

  /**
   * Extensions of files that print names in PHP.
   *
   * @var list<string>
   */
  private const array PHP_EXTENSIONS = ['theme', 'php', 'module', 'inc'];

  /**
   * The theme machine name a file path must be tied to before it is reported.
   */
  private const string THEME = 'drupal8_w3css_theme';

  /**
   * Report labels, keyed by note kind, in report order.
   *
   * @var array<string, string>
   */
  public const array NOTE_LABELS = [
    'name-in-php' => 'PHP, module or include lines that print a renamed variable, behavior, once id or data attribute (never written; change them by hand)',
    'variable-pending' => 'Color scheme variables whose 3.0 name is not decided yet (not rewritten)',
    'library-id' => 'Library ids that changed (rewritten only in info and libraries YAML files with --yml; the old id keeps working as a deprecated alias)',
    'file-path' => 'Theme file paths that moved or were split (an override by path stops matching; point it at the new library)',
    'dataset-name' => 'JavaScript that reads a renamed data attribute through dataset (change by hand)',
    'yaml-merge' => 'YAML files left unchanged because two old library ids become one new id there (merge the two entries by hand)',
  ];

  /**
   * The rules, built from the rename rows.
   *
   * Each rule has a regular expression whose whole match is the old name, a
   * map of old name to new text, the extensions it rewrites, the extensions
   * where it only notes, and the note kind.
   *
   * @var list<array{pattern: string, map: array<string, string>, write: list<string>, note_in: list<string>, kind: string, always: bool, needs: string}>
   */
  private array $rules = [];

  /**
   * Builds the engine from the decoded rename rows.
   *
   * @param list<array<string, mixed>> $rows
   *   The rows of w3css-rename-map.json.
   *
   * @throws \InvalidArgumentException
   *   When no row gives the migration anything to do.
   */
  public function __construct(array $rows) {
    $exact = [];
    $prefix = [];
    $pendingExact = [];
    $pendingPrefix = [];
    $behaviors = [];
    $once = [];
    $data = [];
    $libraries = [];
    $libraryNotes = [];
    $paths = [];
    foreach ($rows as $row) {
      $kind = self::text($row, 'kind');
      $handling = self::text($row, 'handling');
      $old = self::text($row, 'old');
      $new = self::text($row, 'new');
      if ($kind === 'css_variable' && in_array($handling, ['rewrite', 'report'], TRUE) && is_array($row['match'] ?? NULL)) {
        foreach ($row['match'] as $match) {
          if (!is_array($match)) {
            continue;
          }
          $from = self::text($match, 'old');
          $to = self::text($match, 'new');
          $isPrefix = self::text($match, 'type') === 'prefix';
          if ($from === '') {
            continue;
          }
          if ($handling === 'rewrite' && $to !== '') {
            if ($isPrefix) {
              $prefix[$from] = $to;
            }
            else {
              $exact[$from] = $to;
            }
          }
          elseif ($handling === 'report') {
            if ($isPrefix) {
              $pendingPrefix[$from] = $new;
            }
            else {
              $pendingExact[$from] = $new;
            }
          }
        }
      }
      elseif ($kind === 'js_behavior' && $handling === 'rewrite' && str_starts_with($old, 'Drupal.behaviors.') && str_starts_with($new, 'Drupal.behaviors.')) {
        $behaviors[substr($old, 17)] = substr($new, 17);
      }
      elseif ($kind === 'once_id' && $handling === 'rewrite') {
        $once[$old] = $new;
      }
      elseif ($kind === 'data_attribute' && $handling === 'rewrite' && preg_match('/^data-[a-z0-9-]+$/', $old) === 1) {
        $data[$old] = $new;
      }
      elseif ($kind === 'library' && preg_match('#^[a-z0-9_]+/[a-z0-9_-]+$#', $old) === 1) {
        if ($handling === 'rewrite-yml' && preg_match('#^[a-z0-9_]+/[a-z0-9_-]+$#', $new) === 1) {
          $libraries[$old] = $new;
        }
        elseif ($handling === 'report') {
          $libraryNotes[$old] = $new;
        }
      }
      elseif (in_array($kind, ['css_file', 'js_file'], TRUE) && $handling === 'report' && preg_match('#^[A-Za-z0-9_/.-]+\.(?:css|js)$#', $old) === 1) {
        $paths[$old] = $new;
      }
    }
    if ($exact === [] && $prefix === [] && $behaviors === [] && $once === [] && $data === [] && $libraries === []) {
      throw new \InvalidArgumentException('The rename map holds no variable, JavaScript, data attribute or library rows to rewrite.');
    }

    $rewrite = self::REWRITE_EXTENSIONS;
    $inPhp = self::PHP_EXTENSIONS;
    $word = '(?<![A-Za-z0-9_-])';
    $end = '(?![A-Za-z0-9_-])';
    $this->addRule($word . '(?:' . self::alternation(array_keys($prefix)) . ')(?=[A-Za-z0-9])', $prefix, $rewrite, $inPhp, 'name-in-php');
    $this->addRule($word . '(?:' . self::alternation(array_keys($exact)) . ')' . $end, $exact, $rewrite, $inPhp, 'name-in-php');
    $this->addRule('(?<![A-Za-z0-9_$.])Drupal\.behaviors\.\K(?:' . self::alternation(array_keys($behaviors)) . ')(?![A-Za-z0-9_$])', $behaviors, ['js'], $inPhp, 'name-in-php');
    $this->addRule('(?<![A-Za-z0-9_$.])Drupal\.behaviors\[\s*[\'"]\K(?:' . self::alternation(array_keys($behaviors)) . ')(?=[\'"])', $behaviors, ['js'], $inPhp, 'name-in-php');
    $this->addRule('\bonce(?:\.(?:remove|find|filter))?\(\s*[\'"]\K(?:' . self::alternation(array_keys($once)) . ')(?=[\'"])', $once, ['js'], $inPhp, 'name-in-php');
    $this->addRule('data-once[~|^$*]?=\s*["\']?\K(?:' . self::alternation(array_keys($once)) . ')' . $end, $once, $rewrite, $inPhp, 'name-in-php');
    $this->addRule($word . '(?:' . self::alternation(array_keys($data)) . ')' . $end, $data, $rewrite, $inPhp, 'name-in-php');
    $camel = [];
    foreach ($data as $old => $new) {
      $camel[self::camel($old)] = $new;
    }
    $dataset = 'dataset(?:\.|\[\s*[\'"])\K(?:' . self::alternation(array_keys($camel)) . ')(?![A-Za-z0-9_$])';
    $this->addRule($dataset, $camel, [], ['js', 'twig'], 'dataset-name', TRUE);
    $this->addRule($word . '(?:' . self::alternation(array_keys($pendingExact)) . ')' . $end, $pendingExact, [], self::ALL_EXTENSIONS, 'variable-pending', TRUE);
    $this->addRule($word . '(?:' . self::alternation(array_keys($pendingPrefix)) . ')(?=[A-Za-z0-9])', $pendingPrefix, [], self::ALL_EXTENSIONS, 'variable-pending', TRUE);
    $libraryPattern = '(?<![A-Za-z0-9_/-])(?:%s)' . $end;
    $this->addRule(sprintf($libraryPattern, self::alternation(array_keys($libraries))), $libraries, ['yaml'], self::ALL_EXTENSIONS, 'library-id');
    $this->addRule(sprintf($libraryPattern, self::alternation(array_keys($libraryNotes))), $libraryNotes, [], self::ALL_EXTENSIONS, 'library-id', TRUE);
    $this->addRule(sprintf('(?<![A-Za-z0-9_/-])(?:%s)', self::alternation(array_keys($paths))) . $end, $paths, [], self::ALL_EXTENSIONS, 'file-path', TRUE, self::THEME);
  }

  /**
   * Validates and returns the extensions a run reads.
   *
   * @param list<string> $extensions
   *   The extensions ClassMigrator accepted.
   * @param bool $includeYaml
   *   Whether YAML files are read too.
   *
   * @return list<string>
   *   The extensions to walk for this class.
   */
  public function extensions(array $extensions, bool $includeYaml): array {
    $selected = array_values(array_intersect($extensions, self::REWRITE_EXTENSIONS));
    return $includeYaml ? [...$selected, 'yml'] : $selected;
  }

  /**
   * Walks a directory for YAML files; links are never followed.
   *
   * @param string $root
   *   An existing directory (already resolved with realpath()).
   *
   * @return array{files: list<string>, symlinks: list<string>, hardlinks: list<string>}
   *   Absolute paths, each list sorted.
   */
  public function scanYaml(string $root): array {
    $files = [];
    $symlinks = [];
    $hardlinks = [];
    $directory = new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS);
    $filter = new \RecursiveCallbackFilterIterator(
      $directory,
      static function (\SplFileInfo $item) use (&$symlinks): bool {
        if ($item->isLink()) {
          $symlinks[] = $item->getPathname();
          return FALSE;
        }
        if ($item->isDir()) {
          return !in_array($item->getFilename(), self::SKIP_DIRS, TRUE);
        }
        return TRUE;
      },
    );
    foreach (new \RecursiveIteratorIterator($filter) as $item) {
      if (!$item instanceof \SplFileInfo || !$item->isFile() || strtolower($item->getExtension()) !== 'yml' || str_contains($item->getFilename(), '.min.')) {
        continue;
      }
      $stat = @stat($item->getPathname());
      if ($stat !== FALSE && $stat['nlink'] > 1) {
        $hardlinks[] = $item->getPathname();
        continue;
      }
      $files[] = $item->getPathname();
    }
    sort($files);
    sort($symlinks);
    sort($hardlinks);
    return ['files' => $files, 'symlinks' => $symlinks, 'hardlinks' => $hardlinks];
  }

  /**
   * Whether a file name is one this migrator reads.
   */
  public function accepts(string $filename): bool {
    $base = basename($filename);
    return !str_contains($base, '.min.') && in_array(strtolower(pathinfo($base, PATHINFO_EXTENSION)), self::ALL_EXTENSIONS, TRUE);
  }

  /**
   * Migrates the text of one file.
   *
   * @param string $contents
   *   The file contents.
   * @param string $filename
   *   The file name; its extension decides the rules.
   * @param bool $yaml
   *   Whether library ids in info and libraries YAML files are rewritten.
   *
   * @return array{content: string, changes: list<array{line: int, before: string, after: string}>, notes: list<array{line: int, kind: string, token: string, new: string, source: string}>}
   *   The migrated text, the changed lines and the manual-review notes.
   */
  public function migrate(string $contents, string $filename, bool $yaml = FALSE): array {
    $base = basename($filename);
    $ext = strtolower(pathinfo($base, PATHINFO_EXTENSION));
    $yamlWrite = $yaml && $ext === 'yml' && preg_match('/\.(?:info|libraries)\.yml$/', $base) === 1;
    $changes = [];
    $notes = [];
    $out = [];
    foreach (explode("\n", $contents) as $index => $orig) {
      $number = $index + 1;
      $line = $orig;
      foreach ($this->rules as $rule) {
        if ($rule['needs'] !== '' && !str_contains($line, $rule['needs'])) {
          continue;
        }
        $mode = self::mode($rule, $ext, $yamlWrite);
        if ($mode === NULL) {
          continue;
        }
        $hit = preg_match($rule['pattern'], $line);
        if ($hit === FALSE) {
          $notes[] = self::note($number, 'pcre-error', preg_last_error_msg(), '?', $orig);
          continue;
        }
        if ($hit === 0) {
          continue;
        }
        if ($mode === 'write') {
          $map = $rule['map'];
          $next = preg_replace_callback($rule['pattern'], static fn (array $m): string => $map[$m[0]] ?? $m[0], $line);
          if ($next === NULL) {
            $notes[] = self::note($number, 'pcre-error', preg_last_error_msg(), '?', $orig);
            continue;
          }
          $line = $next;
          continue;
        }
        if (preg_match_all($rule['pattern'], $line, $found) > 0) {
          foreach (array_unique($found[0]) as $token) {
            $notes[] = self::note($number, $rule['kind'], $token, $rule['map'][$token] ?? '?', $orig);
          }
        }
      }
      if ($line !== $orig) {
        $changes[] = ['line' => $number, 'before' => trim($orig), 'after' => trim($line)];
      }
      $out[] = $line;
    }
    $content = implode("\n", $out);
    // Two old library ids can map to one new id (a merge). Rewriting both keys
    // of a libraries-extend or libraries-override map would leave a duplicate
    // key, which Drupal refuses to parse. Keep such a file as it was.
    if ($yamlWrite && $changes !== [] && self::parses($contents) && !self::parses($content)) {
      $notes[] = self::note(1, 'yaml-merge', $base, 'merge by hand', trim($changes[0]['before']));
      return ['content' => $contents, 'changes' => [], 'notes' => $notes];
    }
    return ['content' => $content, 'changes' => $changes, 'notes' => $notes];
  }

  /**
   * Whether a YAML text parses, duplicate keys included as an error.
   */
  private static function parses(string $yaml): bool {
    try {
      Yaml::decode($yaml);
      return TRUE;
    }
    catch (InvalidDataTypeException) {
      return FALSE;
    }
  }

  /**
   * Adds one rule when its map has rows.
   *
   * @param string $pattern
   *   The pattern body, without delimiters; its whole match is the old name.
   * @param array<string, string> $map
   *   Old name to new text.
   * @param list<string> $write
   *   Extensions rewritten; `yaml` means YAML files when YAML rewriting is on.
   * @param list<string> $noteIn
   *   Extensions where a match is only noted.
   * @param string $kind
   *   The note kind.
   * @param bool $always
   *   TRUE for a rule that never rewrites: it notes in $noteIn only.
   * @param string $needs
   *   A string the line must contain for the rule to apply.
   */
  private function addRule(string $pattern, array $map, array $write, array $noteIn, string $kind, bool $always = FALSE, string $needs = ''): void {
    if ($map === []) {
      return;
    }
    $this->rules[] = [
      'pattern' => '#' . $pattern . '#',
      'map' => $map,
      'write' => $write,
      'note_in' => $noteIn,
      'kind' => $kind,
      'always' => $always,
      'needs' => $needs,
    ];
  }

  /**
   * Decides what a rule does in a file: write, note or nothing.
   *
   * @param array{write: list<string>, note_in: list<string>, always: bool} $rule
   *   The rule.
   * @param string $ext
   *   The file extension.
   * @param bool $yamlWrite
   *   Whether this is a YAML file that may be written.
   */
  private static function mode(array $rule, string $ext, bool $yamlWrite): ?string {
    if ($rule['always']) {
      return in_array($ext, $rule['note_in'], TRUE) ? 'note' : NULL;
    }
    if (in_array($ext, $rule['write'], TRUE) || ($yamlWrite && in_array('yaml', $rule['write'], TRUE))) {
      return 'write';
    }
    return in_array($ext, $rule['note_in'], TRUE) ? 'note' : NULL;
  }

  /**
   * Builds a pattern alternation, longest name first.
   *
   * @param list<string|int> $names
   *   The names.
   */
  private static function alternation(array $names): string {
    $names = array_map('strval', $names);
    usort($names, static fn (string $a, string $b): int => strlen($b) <=> strlen($a));
    return implode('|', array_map(static fn (string $name): string => preg_quote($name, '#'), $names));
  }

  /**
   * Returns the dataset spelling of a data attribute name.
   */
  private static function camel(string $attribute): string {
    $parts = explode('-', substr($attribute, 5));
    return array_shift($parts) . implode('', array_map('ucfirst', $parts));
  }

  /**
   * Builds one manual-review note.
   *
   * @return array{line: int, kind: string, token: string, new: string, source: string}
   *   The note.
   */
  private static function note(int $line, string $kind, string $token, string $new, string $source): array {
    return ['line' => $line, 'kind' => $kind, 'token' => $token, 'new' => $new, 'source' => $source];
  }

  /**
   * Reads a string cell of a row, or an empty string.
   *
   * @param array<mixed> $row
   *   The row.
   * @param string $key
   *   The cell name.
   */
  private static function text(array $row, string $key): string {
    $value = $row[$key] ?? '';
    return is_string($value) ? $value : '';
  }

}
