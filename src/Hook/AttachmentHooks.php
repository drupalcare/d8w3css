<?php

declare(strict_types=1);

namespace Drupal\drupal8_w3css_theme\Hook;

use Drupal\Core\Extension\ThemeSettingsProvider;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Routing\AdminContext;

/**
 * Libraries the theme settings attach to every page.
 */
final class AttachmentHooks {
  public function __construct(
    private readonly ThemeSettingsProvider $themeSettings,
    private readonly AdminContext $adminContext,
  ) {}

  /**
   * Implements hook_page_attachments_alter().
   */
  #[Hook('page_attachments_alter')]
  public function pageAttachmentsAlter(array &$attachments): void {
    // Load google fonts locally - Global.
    if ($this->themeSettings->getSetting('d8w3css_google_font_local') && $this->themeSettings->getSetting('d8w3css_site_gf')) {
      $in_font = $this->themeSettings->getSetting('d8w3css_site_gf');
      $current_ingf = 'drupal8_w3css_theme/d8w3css-gfin' . substr((string) $in_font, 7);
      $attachments['#attached']['library'][] = $current_ingf;
    }
    // Load fonts external - Global.
    if (!$this->themeSettings->getSetting('d8w3css_google_font_local') && $this->themeSettings->getSetting('d8w3css_site_gf')) {
      $ex_font = $this->themeSettings->getSetting('d8w3css_site_gf');
      $current_exgf = 'drupal8_w3css_theme/d8w3css-gfex' . substr((string) $ex_font, 7);
      $attachments['#attached']['library'][] = $current_exgf;
    }
    // Load google fonts locally - Heading.
    if ($this->themeSettings->getSetting('d8w3css_google_font_local') && $this->themeSettings->getSetting('d8w3css_site_gfh')) {
      $in_font_h = $this->themeSettings->getSetting('d8w3css_site_gfh');
      $current_ingfh = 'drupal8_w3css_theme/d8w3css-gfin' . substr((string) $in_font_h, 7);
      $attachments['#attached']['library'][] = $current_ingfh;
    }
    // Load fonts external - Heading.
    if (!$this->themeSettings->getSetting('d8w3css_google_font_local') && $this->themeSettings->getSetting('d8w3css_site_gfh')) {
      $ex_font_h = $this->themeSettings->getSetting('d8w3css_site_gfh');
      $current_exgfh = 'drupal8_w3css_theme/d8w3css-gfex' . substr((string) $ex_font_h, 7);
      $attachments['#attached']['library'][] = $current_exgfh;
    }
    // Load special fonts' library.
    if ($this->themeSettings->getSetting('d8w3css_site_gfs')) {
      $ex_font_s = $this->themeSettings->getSetting('d8w3css_site_gfs');
      $current_exgfs = 'drupal8_w3css_theme/d8w3css-gfex' . substr((string) $ex_font_s, 7);
      $attachments['#attached']['library'][] = $current_exgfs;
    }
    // Load default color theme if not.
    if (!$this->themeSettings->getSetting('current_theme') || $this->themeSettings->getSetting('current_theme') == 'none') {
      $attachments['#attached']['library'][] = 'drupal8_w3css_theme/w3css-default-theme';
    }
    else {
      $my_current_theme = $this->themeSettings->getSetting('current_theme');
      $predefined_theme_filename = 'drupal8_w3css_theme/' . str_replace('w3-theme-', 'w3-css-theme-', (string) $my_current_theme);
      $attachments['#attached']['library'][] = $predefined_theme_filename;
    }
    // Load font awesome.
    switch ($this->themeSettings->getSetting('d8w3css_font_awesome')) {
      case 1:
        $attachments['#attached']['library'][] = 'drupal8_w3css_theme/font-awesome-4-in';
        break;

      case 2:
        $attachments['#attached']['library'][] = 'drupal8_w3css_theme/font-awesome-4-ex';
        break;

      case 3:
        $attachments['#attached']['library'][] = 'drupal8_w3css_theme/font-awesome-5-ex';
        break;

      case 4:
        $attachments['#attached']['library'][] = 'drupal8_w3css_theme/font-awesome-6-ex';
        break;
    }
    switch ($this->themeSettings->getSetting('social_media_size')) {
      case 1:
        $attachments['#attached']['library'][] = 'drupal8_w3css_theme/social-media-icon-size-m';
        break;

      case 2:
        $attachments['#attached']['library'][] = 'drupal8_w3css_theme/social-media-icon-size-l';
        break;
    }
    // Load full opacity if enabled but not on admin pages.
    if ($this->themeSettings->getSetting('full_opacity_onscroll') && !$this->adminContext->isAdminRoute()) {
      $attachments['#attached']['library'][] = 'drupal8_w3css_theme/d8w3css-full-opacity';
    }
  }

}
