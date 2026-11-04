<?php

declare(strict_types=1);

namespace Drupal\drupal8_w3css_theme\Upgrade;

/**
 * The settings keys of this theme, old name to new name.
 *
 * Generated once from planning/d8w3css-config-map.yml in the lane repository,
 * which retires with this class: from here on, this constant is the only copy.
 * Change a key here and in the schema together.
 */
final class SettingsMap {

  /**
   * The moved keys, by old name, and the keys core owns.
   *
   * The type is the type of the new key. A value moves only if it casts to it.
   */
  public const MAP = [
    'moved' => [
      'current_theme' => ['new' => 'color_scheme.predefined', 'type' => 'string'],
      'website_width' => ['new' => 'layout.width', 'type' => 'string'],
      'equal_width' => ['new' => 'layout.equal_columns', 'type' => 'boolean'],
      'layout_user_login_custom' => ['new' => 'layout.centered_login_pages', 'type' => 'boolean'],
      'full_opacity_onscroll' => ['new' => 'effects.reveal_on_scroll', 'type' => 'boolean'],
      'breadcrumb_page_title' => ['new' => 'breadcrumb.hide_page_title', 'type' => 'boolean'],
      'border_animation' => ['new' => 'effects.animated_borders', 'type' => 'boolean'],
      'sitename_animation' => ['new' => 'effects.site_name_animation', 'type' => 'string'],
      'pagetitle_animation' => ['new' => 'effects.page_title_animation', 'type' => 'string'],
      'd8w3css_font_awesome' => ['new' => 'libraries.font_awesome', 'type' => 'integer'],
      'd8w3css_google_font_local' => ['new' => 'typography.load_fonts_locally', 'type' => 'boolean'],
      'd8w3css_site_gf' => ['new' => 'typography.site_font', 'type' => 'string'],
      'd8w3css_site_gfh' => ['new' => 'typography.heading_font', 'type' => 'string'],
      'd8w3css_site_gfs' => ['new' => 'typography.special_font', 'type' => 'string'],
      'top_region' => ['new' => 'region_groups.top.marker_class', 'type' => 'boolean'],
      'main_region' => ['new' => 'region_groups.main.marker_class', 'type' => 'boolean'],
      'bottom_region' => ['new' => 'region_groups.bottom.marker_class', 'type' => 'boolean'],
      'footer_region' => ['new' => 'region_groups.footer.marker_class', 'type' => 'boolean'],
      'show_social_icon' => ['new' => 'social_icons.enabled', 'type' => 'boolean'],
      'social_media_size' => ['new' => 'social_icons.size', 'type' => 'integer'],
      'facebook_url' => ['new' => 'social_icons.links.facebook', 'type' => 'string'],
      'twitter_url' => ['new' => 'social_icons.links.twitter', 'type' => 'string'],
      'pinterest_url' => ['new' => 'social_icons.links.pinterest', 'type' => 'string'],
      'linkedin_url' => ['new' => 'social_icons.links.linkedin', 'type' => 'string'],
      'instagram_url' => ['new' => 'social_icons.links.instagram', 'type' => 'string'],
      'youtube_url' => ['new' => 'social_icons.links.youtube', 'type' => 'string'],
      'drupal_url' => ['new' => 'social_icons.links.drupal', 'type' => 'string'],
      'rss_url' => ['new' => 'social_icons.links.rss', 'type' => 'string'],
      'mastodon_url' => ['new' => 'social_icons.links.mastodon', 'type' => 'string'],
      'copyright_text' => ['new' => 'footer.copyright_text', 'type' => 'string'],
      'show_credit_link' => ['new' => 'footer.show_credit_link', 'type' => 'boolean'],
      'color_page_wrapper' => ['new' => 'region_colors.page_wrapper', 'type' => 'string'],
      'color_header' => ['new' => 'region_colors.header', 'type' => 'string'],
      'color_primary_menu' => ['new' => 'region_colors.primary_menu', 'type' => 'string'],
      'color_welcome_text' => ['new' => 'region_colors.welcome_text', 'type' => 'string'],
      'color_highlighted' => ['new' => 'region_colors.highlighted', 'type' => 'string'],
      'color_top_container' => ['new' => 'region_colors.top_container', 'type' => 'string'],
      'color_top_first' => ['new' => 'region_colors.top_first', 'type' => 'string'],
      'color_top_second' => ['new' => 'region_colors.top_second', 'type' => 'string'],
      'color_top_third' => ['new' => 'region_colors.top_third', 'type' => 'string'],
      'color_page_title' => ['new' => 'region_colors.page_title', 'type' => 'string'],
      'color_main' => ['new' => 'region_colors.main', 'type' => 'string'],
      'color_breadcrumb' => ['new' => 'region_colors.breadcrumb', 'type' => 'string'],
      'color_sidebar_first' => ['new' => 'region_colors.sidebar_first', 'type' => 'string'],
      'color_content' => ['new' => 'region_colors.content', 'type' => 'string'],
      'color_sidebar_second' => ['new' => 'region_colors.sidebar_second', 'type' => 'string'],
      'color_bottom_container' => ['new' => 'region_colors.bottom_container', 'type' => 'string'],
      'color_bottom_first' => ['new' => 'region_colors.bottom_first', 'type' => 'string'],
      'color_bottom_second' => ['new' => 'region_colors.bottom_second', 'type' => 'string'],
      'color_bottom_third' => ['new' => 'region_colors.bottom_third', 'type' => 'string'],
      'color_bottom_forth' => ['new' => 'region_colors.bottom_fourth', 'type' => 'string'],
      'color_footer_container' => ['new' => 'region_colors.footer_container', 'type' => 'string'],
      'color_footer_first' => ['new' => 'region_colors.footer_first', 'type' => 'string'],
      'color_footer_second' => ['new' => 'region_colors.footer_second', 'type' => 'string'],
      'color_footer_third' => ['new' => 'region_colors.footer_third', 'type' => 'string'],
      'color_footer_menu' => ['new' => 'region_colors.footer_menu', 'type' => 'string'],
      'color_copyright' => ['new' => 'region_colors.copyright', 'type' => 'string'],
    ],
    'kept' => [
      '_core',
      'features',
      'logo',
      'favicon',
    ],
  ];

  /**
   * Returns the old name of a new key, or NULL if the key never moved.
   */
  public static function oldName(string $new): ?string {
    foreach (self::MAP['moved'] as $old => $row) {
      if ($row['new'] === $new) {
        return $old;
      }
    }
    return NULL;
  }

}
