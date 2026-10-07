<?php

declare(strict_types=1);

namespace Drupal\Tests\drupal8_w3css_theme\Unit;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Theme code must never fetch its own classes from the service container.
 *
 * WHY THIS EXISTS
 * The 2.x theme fataled on every page on Drupal 10 and on Drupal 11.0-11.2:
 * "You have requested a non-existent service
 * Drupal\drupal8_w3css_theme\Hook\Drupal8W3cssThemeHooks". Each #[LegacyHook]
 * wrapper in drupal8_w3css_theme.theme called
 * \Drupal::service(Drupal8W3cssThemeHooks::class), and that service exists
 * only when core registers it:
 *
 * - DrupalKernel::discoverServiceProviders() reads <name>.services.yml for
 *   MODULES only, on every core version, so drupal8_w3css_theme.services.yml
 *   was never loaded anywhere.
 * - ThemeHookCollectorPass, added in Drupal 11.3.0, registers a theme's Hook
 *   classes as autowired services. Below 11.3 nothing does.
 *
 * So the wrappers worked on 11.3+ and 12, where every test ran, and broke on
 * every older core the theme declares support for. The sub-themes amun,
 * amunet and anhur inherit the base theme's hooks, so they fataled too. The
 * wrappers now use \Drupal::classResolver(), which returns the registered
 * service on 11.3+ and instantiates the class itself below that.
 *
 * WHAT THIS ENFORCES
 * 1. No \Drupal::service() call in theme code names a class in the theme's
 *    own namespace (Drupal\drupal8_w3css_theme\... or the starter
 *    sub-theme's). Core services such as ThemeSettingsProvider are not
 *    flagged: `use` statements are resolved, so the check is about the
 *    namespace, not the name.
 * 2. No *.services.yml ships in a theme directory, because core never reads
 *    one and its presence is what made the container lookup look safe.
 *
 * Red-proofed against the pre-fix tree: 18 calls flagged, and the
 * services.yml file. Reads files as text, so it runs on every core version,
 * including the Drupal 10 job where the fatal actually happens.
 *
 * Ported from the Solo theme's test of the same name (issue #3628961).
 *
 * @license SPDX-License-Identifier: GPL-2.0-or-later
 *
 * @group d8w3css
 */
#[Group('d8w3css')]
class HookClassResolutionTest extends TestCase {

  /**
   * The theme root.
   */
  private function themePath(): string {
    return dirname(__DIR__, 3);
  }

  /**
   * Procedural theme files: the .theme files, includes/ and theme-settings.
   *
   * @return list<string>
   *   Absolute paths.
   */
  private function proceduralFiles(): array {
    $root = $this->themePath();
    return array_merge(
      glob($root . '/*.theme') ?: [],
      glob($root . '/*.php') ?: [],
      glob($root . '/includes/*.inc') ?: [],
      glob($root . '/drupal8_w3css_subtheme/*.theme') ?: [],
    );
  }

  /**
   * Maps each imported short class name to its fully qualified name.
   *
   * @return array<string, string>
   *   FQCN keyed by the short name (or alias) used in the file.
   */
  private function imports(string $code): array {
    $map = [];
    preg_match_all('/^use\s+([\w\\\\]+)(?:\s+as\s+(\w+))?\s*;/m', $code, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
      $fqcn = ltrim($match[1], '\\');
      $short = $match[2] ?? '';
      if ($short === '') {
        $parts = explode('\\', $fqcn);
        $short = end($parts);
      }
      $map[$short] = $fqcn;
    }
    return $map;
  }

  /**
   * No wrapper asks the container for a class the theme itself defines.
   */
  public function testNoContainerLookupOfThemeOwnClasses(): void {
    $offenders = [];
    $resolved = 0;
    foreach ($this->proceduralFiles() as $file) {
      $code = (string) file_get_contents($file);
      $imports = $this->imports($code);
      preg_match_all('/\\\\Drupal::(service|classResolver)\(\s*\\\\?([\w\\\\]+)::class\s*\)/', $code, $calls, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
      foreach ($calls as $call) {
        $name = $call[2][0];
        $fqcn = str_contains($name, '\\') ? ltrim($name, '\\') : ($imports[$name] ?? $name);
        if (!preg_match('/^Drupal\\\\drupal8_w3css_(theme|subtheme)\\\\/', $fqcn)) {
          continue;
        }
        if ($call[1][0] === 'classResolver') {
          $resolved++;
          continue;
        }
        $line = substr_count(substr($code, 0, $call[0][1]), "\n") + 1;
        $offenders[] = sprintf('%s:%d \\Drupal::service(%s::class)', basename($file), $line, $name);
      }
    }

    $this->assertSame([], $offenders, sprintf(
      "Theme classes are not container services below Drupal 11.3; use \\Drupal::classResolver() instead. %d offending call(s):\n%s",
      count($offenders),
      implode("\n", $offenders)
    ));
    // A scan that matches nothing proves nothing: the theme has 18 wrappers
    // that resolve its Hook class. A moved file or a broken pattern must go
    // red.
    $this->assertGreaterThanOrEqual(18, $resolved, sprintf('Found %d classResolver() wrappers; expected at least 18.', $resolved));
  }

  /**
   * No theme directory ships a services.yml, which core never loads.
   */
  public function testNoThemeServicesYml(): void {
    $root = $this->themePath();
    $found = array_merge(
      glob($root . '/*.services.yml') ?: [],
      glob($root . '/drupal8_w3css_subtheme/*.services.yml') ?: [],
    );
    $this->assertSame([], array_map('basename', $found), 'Drupal core reads services.yml for modules only; a theme services.yml is never loaded.');
  }

}
