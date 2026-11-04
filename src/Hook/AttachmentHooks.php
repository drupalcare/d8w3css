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
    // Load google fonts locally - Global.
    if ($this->settings->get('typography.load_fonts_locally') && $this->settings->get('typography.site_font')) {
      $in_font = $this->settings->get('typography.site_font');
      $current_ingf = 'drupal8_w3css_theme/d8w3css-gfin' . substr((string) $in_font, 7);
      $attachments['#attached']['library'][] = $current_ingf;
    }
    // Load fonts external - Global.
    if (!$this->settings->get('typography.load_fonts_locally') && $this->settings->get('typography.site_font')) {
      $ex_font = $this->settings->get('typography.site_font');
      $current_exgf = 'drupal8_w3css_theme/d8w3css-gfex' . substr((string) $ex_font, 7);
      $attachments['#attached']['library'][] = $current_exgf;
    }
    // Load google fonts locally - Heading.
    if ($this->settings->get('typography.load_fonts_locally') && $this->settings->get('typography.heading_font')) {
      $in_font_h = $this->settings->get('typography.heading_font');
      $current_ingfh = 'drupal8_w3css_theme/d8w3css-gfin' . substr((string) $in_font_h, 7);
      $attachments['#attached']['library'][] = $current_ingfh;
    }
    // Load fonts external - Heading.
    if (!$this->settings->get('typography.load_fonts_locally') && $this->settings->get('typography.heading_font')) {
      $ex_font_h = $this->settings->get('typography.heading_font');
      $current_exgfh = 'drupal8_w3css_theme/d8w3css-gfex' . substr((string) $ex_font_h, 7);
      $attachments['#attached']['library'][] = $current_exgfh;
    }
    // Load special fonts' library.
    if ($this->settings->get('typography.special_font')) {
      $ex_font_s = $this->settings->get('typography.special_font');
      $current_exgfs = 'drupal8_w3css_theme/d8w3css-gfex' . substr((string) $ex_font_s, 7);
      $attachments['#attached']['library'][] = $current_exgfs;
    }
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
    switch ($this->settings->get('libraries.font_awesome')) {
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
    switch ($this->settings->get('social_icons.size')) {
      case 1:
        $attachments['#attached']['library'][] = 'drupal8_w3css_theme/social-media-icon-size-m';
        break;

      case 2:
        $attachments['#attached']['library'][] = 'drupal8_w3css_theme/social-media-icon-size-l';
        break;
    }
    // Load full opacity if enabled but not on admin pages.
    if ($this->settings->get('effects.reveal_on_scroll') && !$this->adminContext->isAdminRoute()) {
      $attachments['#attached']['library'][] = 'drupal8_w3css_theme/d8w3css-full-opacity';
    }
  }

}
