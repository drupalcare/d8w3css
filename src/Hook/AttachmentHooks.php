<?php

declare(strict_types=1);

namespace Drupal\drupal8_w3css_theme\Hook;

use Drupal\Core\Extension\ThemeSettingsProvider;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Routing\AdminContext;
use Drupal\drupal8_w3css_theme\Settings\SettingsReader;

/**
 * Libraries the theme settings attach to every page.
 */
final class AttachmentHooks {

  /**
   * Reads the settings by their new names; a plain class, not a service.
   */
  private readonly SettingsReader $settings;

  public function __construct(
    ThemeSettingsProvider $themeSettings,
    private readonly AdminContext $adminContext,
  ) {
    $this->settings = new SettingsReader($themeSettings);
  }

  /**
   * Implements hook_page_attachments_alter().
   *
   * @param array<string, mixed> $attachments
   *   The page attachments render array.
   */
  #[Hook('page_attachments_alter')]
  public function pageAttachmentsAlter(array &$attachments): void {
    // Global and heading fonts load locally or from Google Fonts; special
    // fonts are always external.
    $local = (bool) $this->settings->get('typography.load_fonts_locally');
    $this->attachFont($attachments, $this->settings->get('typography.site_font'), $local);
    $this->attachFont($attachments, $this->settings->get('typography.heading_font'), $local);
    $this->attachFont($attachments, $this->settings->get('typography.special_font'), FALSE);
    // Load default color theme if not.
    if (!$this->settings->get('color_scheme.predefined') || $this->settings->get('color_scheme.predefined') == 'none') {
      $attachments['#attached']['library'][] = 'drupal8_w3css_theme/w3css-default-theme';
    }
    else {
      $my_current_theme = $this->settings->get('color_scheme.predefined');
      $predefined_theme_filename = 'drupal8_w3css_theme/' . str_replace('w3-theme-', 'w3-css-theme-', (string) $my_current_theme);
      $attachments['#attached']['library'][] = $predefined_theme_filename;
    }
    // Load font awesome.
    switch ((int) $this->settings->get('libraries.font_awesome')) {
      case 1:
        $attachments['#attached']['library'][] = 'drupal8_w3css_theme/font-awesome-7-in';
        break;

      case 2:
        $attachments['#attached']['library'][] = 'drupal8_w3css_theme/font-awesome-7-ex';
        break;
    }
    // The size styles only matter when the social icons are shown.
    if ($this->settings->get('social_icons.enabled')) {
      switch ($this->settings->get('social_icons.size')) {
        case 1:
          $attachments['#attached']['library'][] = 'drupal8_w3css_theme/social-media-icon-size-m';
          break;

        case 2:
          $attachments['#attached']['library'][] = 'drupal8_w3css_theme/social-media-icon-size-l';
          break;
      }
    }
    // Load full opacity if enabled but not on admin pages.
    if ($this->settings->get('effects.reveal_on_scroll') && !$this->adminContext->isAdminRoute()) {
      $attachments['#attached']['library'][] = 'drupal8_w3css_theme/d8w3css-full-opacity';
    }
  }

  /**
   * Attaches the library of one font choice.
   *
   * The choice is stored as "d8w3css-<font>" and its libraries are named
   * "d8w3css-gfin-<font>" (local) and "d8w3css-gfex-<font>" (Google Fonts).
   * "None", empty and any other value attach nothing, because no library with
   * that name exists.
   *
   * @param array<string, mixed> $attachments
   *   The page attachments render array.
   * @param mixed $font
   *   The stored font choice.
   * @param bool $local
   *   TRUE to load the font files shipped with the theme.
   */
  private function attachFont(array &$attachments, mixed $font, bool $local): void {
    if (!is_string($font) || !str_starts_with($font, 'd8w3css-') || $font === 'd8w3css-') {
      return;
    }
    $attachments['#attached']['library'][] = 'drupal8_w3css_theme/d8w3css-' . ($local ? 'gfin' : 'gfex') . substr($font, 7);
  }

}
