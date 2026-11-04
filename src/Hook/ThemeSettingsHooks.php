<?php

declare(strict_types=1);

namespace Drupal\drupal8_w3css_theme\Hook;

use Drupal\Core\Extension\ThemeExtensionList;
use Drupal\Core\Extension\ThemeSettingsProvider;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\drupal8_w3css_theme\Settings\AdvancedSiteColors;
use Drupal\drupal8_w3css_theme\Settings\BorderAnimation;
use Drupal\drupal8_w3css_theme\Settings\BreadcrumbPageTitle;
use Drupal\drupal8_w3css_theme\Settings\Copyright;
use Drupal\drupal8_w3css_theme\Settings\Credit;
use Drupal\drupal8_w3css_theme\Settings\EqualWidth;
use Drupal\drupal8_w3css_theme\Settings\ExternalLibraries;
use Drupal\drupal8_w3css_theme\Settings\FullOpacityOnScroll;
use Drupal\drupal8_w3css_theme\Settings\LayoutUserLogin;
use Drupal\drupal8_w3css_theme\Settings\PredefinedThemes;
use Drupal\drupal8_w3css_theme\Settings\SettingsLayout;
use Drupal\drupal8_w3css_theme\Settings\SitenameAnimation;
use Drupal\drupal8_w3css_theme\Settings\SocialLinks;
use Drupal\drupal8_w3css_theme\Settings\WebsiteWidth;
use Drupal\drupal8_w3css_theme\Upgrade\SettingsMigrator;

/**
 * Theme settings form hook for drupal8_w3css_theme.
 *
 * This must be a class. Drupal 12 finds procedural theme hooks only in
 * <theme>.theme, so a function in theme-settings.php is never called there and
 * the page renders with every section of this theme missing.
 */
final class ThemeSettingsHooks {
  use StringTranslationTrait;

  public function __construct(
    private readonly ThemeSettingsProvider $themeSettings,
    private readonly ThemeExtensionList $themeList,
  ) {}

  /**
   * Implements hook_form_system_theme_settings_alter().
   *
   * @param array<string, mixed> $form
   *   The form render array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current form state.
   */
  #[Hook('form_system_theme_settings_alter')]
  public function formSystemThemeSettingsAlter(array &$form, FormStateInterface $form_state): void {
    $sections = [
      new WebsiteWidth($this->themeSettings),
      new EqualWidth($this->themeSettings),
      new LayoutUserLogin($this->themeSettings),
      new FullOpacityOnScroll($this->themeSettings),
      new BreadcrumbPageTitle($this->themeSettings),
      new BorderAnimation($this->themeSettings),
      new ExternalLibraries($this->themeSettings),
      new SitenameAnimation($this->themeSettings),
      new PredefinedThemes($this->themeSettings, $this->themeList),
      new AdvancedSiteColors($this->themeSettings),
      new SocialLinks($this->themeSettings),
      new Copyright($this->themeSettings),
      new Credit($this->themeSettings),
    ];
    foreach ($sections as $section) {
      $section->build($form);
    }
    (new SettingsLayout())->arrange($form);
    $form['#submit'][] = [SettingsMigrator::class, 'healOnSave'];
    $this->addStaleKeysWarning($form);
  }

  /**
   * Warns about old key names in the config of the theme being edited.
   *
   * Names only, never values. Old stored keys clear on save. Overrides live in
   * settings.php or a module, so a person has to change them.
   *
   * @param array<string, mixed> $form
   *   The form being altered.
   */
  private function addStaleKeysWarning(array &$form): void {
    $name = $form['config_key']['#value'] ?? NULL;
    if (!is_string($name)) {
      return;
    }
    $migrator = SettingsMigrator::create();
    $stale = $migrator->staleKeys($name);
    $messages = [];
    $scalars = $migrator->scalarGroups($name);
    if ($scalars) {
      $messages[] = $this->t('A setting of a sub-theme uses the same name as a group of this theme, so saving this form replaces it. Names: @keys.', [
        '@keys' => implode(', ', $scalars),
      ]);
    }
    if ($stale['stored']) {
      $messages[] = $this->t('This theme still stores settings under old names, so they were probably imported from an old export. The page still shows them. Save this form to move them, then export your configuration again. Old names: @keys.', [
        '@keys' => implode(', ', $stale['stored']),
      ]);
    }
    if ($stale['overridden']) {
      $messages[] = $this->t('Overrides in settings.php or a module use old setting names and no longer apply. Change them by hand. Old names: @keys.', [
        '@keys' => implode(', ', $stale['overridden']),
      ]);
    }
    if ($messages) {
      $form['d8w3css_stale_keys'] = [
        '#theme' => 'status_messages',
        '#message_list' => ['warning' => $messages],
        '#status_headings' => ['warning' => $this->t('Warning message')],
        '#weight' => -100,
      ];
    }
  }

}
