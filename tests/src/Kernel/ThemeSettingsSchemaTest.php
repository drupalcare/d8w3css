<?php

declare(strict_types=1);

namespace Drupal\Tests\drupal8_w3css_theme\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Core\Serialization\Yaml;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * Keeps config/schema in step with config/install and the settings form.
 *
 * WHY THIS EXISTS. The theme shipped its install config (settings.yml)
 * and a settings form that saves ~60 keys, but NO config schema. Under strict
 * schema checking (every core and contrib Kernel/Functional test that installs
 * the theme) that is "No schema for drupal8_w3css_theme.settings", and the
 * three sub-themes (amun, amunet, anhur) inherited the failure.
 *
 * Strict checking is on here ($strictConfigSchema), so installing the theme
 * throws SchemaIncompleteException if the install config carries a key the
 * schema does not describe. The two drift tests close the other holes: keys the
 * FORM can save but the install config does not ship, and schema entries
 * nothing uses any more.
 *
 * @license SPDX-License-Identifier: GPL-2.0-or-later
 *
 * @group d8w3css
 */
#[Group('d8w3css')]
#[RunTestsInSeparateProcesses]
class ThemeSettingsSchemaTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['system'];

  /**
   * The theme's own directory, resolved from this file.
   */
  private function themeDir(): string {
    return dirname(__DIR__, 3);
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    // Strict checking is the point of this test. KernelTestBase defaults it to
    // TRUE; the property is typed on newer core so it cannot be overridden
    // portably, and the control is asserted instead.
    $this->assertTrue((new \ReflectionProperty($this, 'strictConfigSchema'))->getValue($this), 'Strict config schema checking must be on.');
    parent::setUp();
    $this->installConfig(['system']);
  }

  /**
   * Installing the theme under strict schema checking is clean.
   *
   * Before the schema existed this threw
   * "No schema for drupal8_w3css_theme.settings".
   */
  public function testInstallUnderStrictSchemaIsClean(): void {
    $this->container->get('theme_installer')->install(['drupal8_w3css_theme']);
    $settings = $this->config('drupal8_w3css_theme.settings');
    $this->assertFalse($settings->isNew());
    $this->assertTrue($settings->get('region_groups.top.marker_class'));
    $this->assertSame('none', $settings->get('color_scheme.predefined'));
    // Throws if any shipped key or type is not described.
    $this->assertSame([], $this->validationErrors('drupal8_w3css_theme.settings'));
  }

  /**
   * Values the settings form submits are cast to the schema's types.
   *
   * Form state hands radios back as strings ("3"); the theme's preprocess code
   * switches on the integer option keys, so the schema must cast them.
   */
  public function testFormStyleValuesAreCast(): void {
    $this->container->get('theme_installer')->install(['drupal8_w3css_theme']);
    $this->config('drupal8_w3css_theme.settings')
      ->set('libraries.font_awesome', '3')
      ->set('social_icons.size', '2')
      ->set('typography.site_font', 'd8w3css-abel')
      ->set('typography.load_fonts_locally', 0)
      ->set('region_colors.header', 'w3-red')
      ->save();
    $settings = $this->config('drupal8_w3css_theme.settings');
    $this->assertSame(3, $settings->get('libraries.font_awesome'));
    $this->assertSame(2, $settings->get('social_icons.size'));
    $this->assertFalse($settings->get('typography.load_fonts_locally'));
    $this->assertSame('w3-red', $settings->get('region_colors.header'));
  }

  /**
   * Every setting path the settings form reads is described by the schema.
   */
  public function testEveryFormKeyHasSchema(): void {
    $form_keys = $this->formKeys();
    $this->assertNotEmpty($form_keys, 'The scan found form keys (instrument control).');
    $this->assertContains('region_colors.header', $form_keys, 'Control: a known form key was found.');
    $this->assertContains('typography.site_font', $form_keys, 'Control: a known form key was found.');
    $missing = array_values(array_filter($form_keys, fn (string $path): bool => !$this->schemaHasPath($path)));
    $this->assertSame([], $missing, 'Form keys with no schema entry.');
    // Control: the path check rejects a path the schema does not describe.
    $this->assertFalse($this->schemaHasPath('typography.no_such_font'));
  }

  /**
   * Every top-level schema group is shipped by install config or read by the form.
   *
   * Stops the schema accumulating groups nothing writes or reads.
   */
  public function testNoOrphanSchemaKeys(): void {
    $install = Yaml::decode((string) file_get_contents($this->themeDir() . '/config/install/drupal8_w3css_theme.settings.yml'));
    $form_groups = array_map(fn (string $path): string => explode('.', $path)[0], $this->formKeys());
    $used = array_merge(array_keys($install), $form_groups);
    $this->assertSame([], array_values(array_diff($this->customSchemaKeys(), $used)), 'Schema keys nothing ships or reads.');
  }

  /**
   * Whether a dotted setting path is described by the theme's schema.
   */
  private function schemaHasPath(string $path): bool {
    $this->container->get('theme_installer')->install(['drupal8_w3css_theme']);
    $node = $this->container->get('config.typed')->getDefinition('drupal8_w3css_theme.settings');
    foreach (explode('.', $path) as $segment) {
      if (!isset($node['mapping'][$segment])) {
        return FALSE;
      }
      $node = $node['mapping'][$segment];
    }
    return TRUE;
  }

  /**
   * Schema keys that are the theme's own (not core's theme_settings base).
   *
   * @return string[]
   *   Key names.
   */
  private function customSchemaKeys(): array {
    // Schema from a theme is only known once the theme is installed.
    $this->container->get('theme_installer')->install(['drupal8_w3css_theme']);
    $typed = $this->container->get('config.typed');
    $own = array_keys($typed->getDefinition('drupal8_w3css_theme.settings')['mapping']);
    $base = array_keys($typed->getDefinition('theme_settings')['mapping']);
    return array_values(array_diff($own, $base));
  }

  /**
   * Dotted setting paths the form files name, parsed from src/Settings.
   *
   * Only strings whose first segment is one of the theme's schema groups count,
   * so route names such as 'user.login' are not mistaken for settings.
   *
   * @return string[]
   *   Setting paths, for example 'typography.site_font'.
   */
  private function formKeys(): array {
    $groups = $this->customSchemaKeys();
    $keys = [];
    foreach (glob($this->themeDir() . '/src/Settings/*.php') ?: [] as $file) {
      preg_match_all("/'([a-z][a-z0-9_]*(?:\.[a-z0-9_]+)+)'/", (string) file_get_contents($file), $m);
      foreach ($m[1] as $path) {
        if (in_array(explode('.', $path)[0], $groups, TRUE)) {
          $keys[] = $path;
        }
      }
    }
    return array_values(array_unique($keys));
  }

  /**
   * Validation messages for a config object ([] when it is clean).
   *
   * @return string[]
   *   Messages.
   */
  private function validationErrors(string $name): array {
    $errors = [];
    foreach ($this->container->get('config.typed')->createFromNameAndData($name, $this->config($name)->getRawData())->validate() as $violation) {
      $errors[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
    }
    return $errors;
  }

}
