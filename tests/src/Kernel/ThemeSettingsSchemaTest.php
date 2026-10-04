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
    $this->assertSame(1, $settings->get('top_region'));
    $this->assertSame('none', $settings->get('current_theme'));
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
      ->set('d8w3css_font_awesome', '3')
      ->set('social_media_size', '2')
      ->set('d8w3css_site_gf', 'd8w3css-abel')
      ->set('d8w3css_google_font_local', 0)
      ->set('color_header', 'w3-red')
      ->save();
    $settings = $this->config('drupal8_w3css_theme.settings');
    $this->assertSame(3, $settings->get('d8w3css_font_awesome'));
    $this->assertSame(2, $settings->get('social_media_size'));
    $this->assertSame(0, $settings->get('d8w3css_google_font_local'));
  }

  /**
   * Every key the settings form reads is described by the schema.
   */
  public function testEveryFormKeyHasSchema(): void {
    $schema_keys = $this->customSchemaKeys();
    $form_keys = $this->formKeys();
    $this->assertNotEmpty($form_keys, 'The scan found form keys (instrument control).');
    $this->assertContains('color_header', $form_keys, 'Control: a known form key was found.');
    $this->assertSame([], array_values(array_diff($form_keys, $schema_keys)), 'Form keys with no schema entry.');
  }

  /**
   * Every schema entry is shipped by install config or used by the form.
   *
   * Stops the schema accumulating keys nothing writes or reads.
   */
  public function testNoOrphanSchemaKeys(): void {
    $install = Yaml::decode((string) file_get_contents($this->themeDir() . '/config/install/drupal8_w3css_theme.settings.yml'));
    $used = array_merge(array_keys($install), $this->formKeys());
    $this->assertSame([], array_values(array_diff($this->customSchemaKeys(), $used)), 'Schema keys nothing ships or reads.');
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
   * Setting names the form files read, parsed from includes/*.inc.
   *
   * @return string[]
   *   Key names.
   */
  private function formKeys(): array {
    $keys = [];
    foreach (glob($this->themeDir() . '/includes/*.inc') ?: [] as $file) {
      preg_match_all("/getSetting\('([a-z0-9_]+)'\)/", (string) file_get_contents($file), $m);
      $keys = array_merge($keys, $m[1]);
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
