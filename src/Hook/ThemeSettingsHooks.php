<?php

declare(strict_types=1);

namespace Drupal\drupal8_w3css_theme\Hook;

use Drupal\Core\Extension\ThemeSettingsProvider;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
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
use Drupal\drupal8_w3css_theme\Settings\SitenameAnimation;
use Drupal\drupal8_w3css_theme\Settings\SocialLinks;
use Drupal\drupal8_w3css_theme\Settings\WebsiteWidth;

/**
 * Theme settings form hook for drupal8_w3css_theme.
 *
 * This must be a class. Drupal 12 finds procedural theme hooks only in
 * <theme>.theme, so a function in theme-settings.php is never called there and
 * the page renders with every section of this theme missing.
 */
final class ThemeSettingsHooks {

  public function __construct(
    private readonly ThemeSettingsProvider $themeSettings,
  ) {}

  /**
   * Implements hook_form_system_theme_settings_alter().
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
      new PredefinedThemes($this->themeSettings),
      new AdvancedSiteColors($this->themeSettings),
      new SocialLinks($this->themeSettings),
      new Copyright($this->themeSettings),
      new Credit($this->themeSettings),
    ];
    foreach ($sections as $section) {
      $section->build($form);
    }
  }

}
