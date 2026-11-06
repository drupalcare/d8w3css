<?php

declare(strict_types=1);

namespace Drupal\drupal8_w3css_theme\Hook;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Extension\ThemeSettingsProvider;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Path\CurrentPathStack;
use Drupal\Core\Routing\AdminContext;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\drupal8_w3css_theme\Settings\SettingsReader;

/**
 * Page, document and maintenance-page preprocess hooks.
 */
final class DocumentHooks {

  /**
   * Reads the settings by their new names; a plain class, not a service.
   */
  private readonly SettingsReader $settings;

  public function __construct(
    ThemeSettingsProvider $themeSettings,
    private readonly RouteMatchInterface $routeMatch,
    private readonly CurrentPathStack $currentPath,
    private readonly AdminContext $adminContext,
    private readonly TimeInterface $time,
  ) {
    $this->settings = new SettingsReader($themeSettings);
  }

  /**
   * Implements hook_preprocess_HOOK() for HTML document templates.
   *
   * @param array<string, mixed> $variables
   *   The template variables.
   */
  #[Hook('preprocess_html')]
  public function preprocessHtml(array &$variables): void {
    $routeMatch = $this->routeMatch;
    // Test route name.
    switch ($routeMatch->getRouteName()) {
      // Add node id to the body class.
      case 'entity.node.canonical':
        $node = $routeMatch->getParameter('node');
        if ($node) {
          $node_id = $node->id();
          if ($node_id) {
            $variables['attributes']['class'][] = 'path-node-' . $node_id;
          }
        }
        break;

      // Add vocabulary name and term id to the body class.
      case 'entity.taxonomy_term.canonical':
        $term = $routeMatch->getParameter('taxonomy_term');
        if ($term) {
          if ($term->id()) {
            $variables['attributes']['class'][] = 'path-taxonomy-term-' . $term->id();
          }
          if ($term->bundle()) {
            $variables['attributes']['class'][] = 'path-vocabulary-' . Html::cleanCssIdentifier($term->bundle());
          }
        }
        break;
    }
    // If the user is logged in use ID if not use second word after user/login.
    $curr_path = $this->currentPath->getPath();
    $curr_path = ltrim($curr_path, '/');
    $user_url = explode('/', $curr_path);
    if (isset($user_url[1]) && $user_url[0] == 'user') {
      $variables['attributes']['class'][] = Html::getClass('path-user-' . $user_url[1]);
    }
    // Add view id name to the body class.
    $view_id = $routeMatch->getParameter('view_id');
    if ($view_id) {
      $variables['attributes']['class'][] = 'path-view-' . Html::cleanCssIdentifier((string) $view_id);
    }
    if ($this->adminContext->isAdminRoute()) {
      $variables['attributes']['class'][] = 'w3css-admin-context';
    }
    // Name the active predefined color scheme on the body, so a sub-theme
    // can tie color fixes to the scheme they were made for.
    $scheme = (string) $this->settings->get('color_scheme.predefined');
    if ($scheme !== '' && $scheme !== 'none') {
      $variables['attributes']['class'][] = Html::getClass('w3css-scheme-' . $scheme);
    }
  }

  /**
   * Implements hook_preprocess_page() for page templates.
   *
   * @param array<string, mixed> $variables
   *   The template variables.
   */
  #[Hook('preprocess_page')]
  public function preprocessPage(array &$variables): void {
    // Top regions variables.
    $has_top_first = !empty($variables['page']['top_first']);
    $has_top_second = !empty($variables['page']['top_second']);
    $has_top_third = !empty($variables['page']['top_third']);
    // Main regions variables.
    $has_sidebar_first = !empty($variables['page']['sidebar_first']);
    $has_sidebar_second = !empty($variables['page']['sidebar_second']);
    // Bottom regions variables.
    $has_bottom_first = !empty($variables['page']['bottom_first']);
    $has_bottom_second = !empty($variables['page']['bottom_second']);
    $has_bottom_third = !empty($variables['page']['bottom_third']);
    $has_bottom_forth = !empty($variables['page']['bottom_forth']);
    // Footer regions variables.
    $has_footer_first = !empty($variables['page']['footer_first']);
    $has_footer_second = !empty($variables['page']['footer_second']);
    $has_footer_third = !empty($variables['page']['footer_third']);
    // Add W3.CSS Responsive classes to Top Container.
    if ($has_top_first && $has_top_second && $has_top_third) {
      $variables['top_w3css'] = 'w3-third';
    }
    elseif ($has_top_first && $has_top_second || $has_top_first && $has_top_third || $has_top_second && $has_top_third) {
      $variables['top_w3css'] = 'w3-half';
    }
    else {
      $variables['top_w3css'] = 'w3-col';
    }
    // Add W3.CSS Responsive classes to left and right sidebar.
    if ($has_sidebar_first && $has_sidebar_second) {
      $variables['content_w3css'] = 'w3-half';
      $variables['sidebarfirst'] = 'w3-quarter';
      $variables['sidebarsecond'] = 'w3-quarter';
    }
    elseif ($has_sidebar_first) {
      $variables['content_w3css'] = 'w3-threequarter';
      $variables['sidebarfirst'] = 'w3-quarter';
    }
    elseif ($has_sidebar_second) {
      $variables['content_w3css'] = 'w3-threequarter';
      $variables['sidebarsecond'] = 'w3-quarter';
    }
    else {
      $variables['content_w3css'] = 'w3-col';
    }
    // Add 3col stacked equal width.
    $variables['equal_width'] = $this->settings->forTwigVariable('equal_width');
    // Truthiness, not a NULL compare: a legacy '0' string must read as off.
    if ($variables['equal_width']) {
      if ($has_sidebar_first && $has_sidebar_second) {
        $variables['content_w3css'] = 'w3-third';
        $variables['sidebarfirst'] = 'w3-third';
        $variables['sidebarsecond'] = 'w3-third';
      }
      elseif ($has_sidebar_first) {
        $variables['content_w3css'] = 'w3-twothird';
        $variables['sidebarfirst'] = 'w3-third';
      }
      elseif ($has_sidebar_second) {
        $variables['content_w3css'] = 'w3-twothird';
        $variables['sidebarsecond'] = 'w3-third';
      }
      else {
        $variables['content_w3css'] = 'w3-col';
      }
    }
    // Add W3.CSS Responsive classes to Bottom Container.
    if ($has_bottom_first && $has_bottom_second && $has_bottom_third && $has_bottom_forth) {
      $variables['bottom_w3css'] = 'w3-quarter';
    }
    elseif ($has_bottom_first && $has_bottom_second && $has_bottom_third || $has_bottom_first && $has_bottom_third && $has_bottom_forth || $has_bottom_first && $has_bottom_second && $has_bottom_forth || $has_bottom_second && $has_bottom_third && $has_bottom_forth) {
      $variables['bottom_w3css'] = 'w3-third';
    }
    elseif ($has_bottom_first && $has_bottom_second || $has_bottom_first && $has_bottom_third || $has_bottom_first && $has_bottom_forth || $has_bottom_second && $has_bottom_forth || $has_bottom_second && $has_bottom_third || $has_bottom_third && $has_bottom_forth) {
      $variables['bottom_w3css'] = 'w3-half';
    }
    else {
      $variables['bottom_w3css'] = 'w3-col';
    }
    // Add W3.CSS Responsive classes to Footer Container.
    if ($has_footer_first && $has_footer_second && $has_footer_third) {
      $variables['footer_w3css'] = 'w3-third';
    }
    elseif ($has_footer_first && $has_footer_second || $has_footer_first && $has_footer_third || $has_footer_second && $has_footer_third) {
      $variables['footer_w3css'] = 'w3-half';
    }
    else {
      $variables['footer_w3css'] = 'w3-col';
    }
    // The lists below are Twig variable names, kept for this theme's and the
    // sub-themes' templates. The reader turns each into its new settings key.
    // Mix variables.
    $mvar = [
      'd8w3css_site_gfs',
      'd8w3css_site_gfh',
      'd8w3css_site_gf',
      'd8w3css_google_font_local',
      'd8w3css_font_awesome',
      'full_opacity_onscroll',
      'layout_user_login_custom',
      'pagetitle_animation',
      'breadcrumb_page_title',
      'sitename_animation',
      'border_animation',
      'top_region',
      'main_region',
      'bottom_region',
      'footer_region',
      'website_width',
    ];
    foreach ($mvar as $value) {
      $variables[$value] = $this->settings->forTwigVariable($value);
    }
    // These selects store 'none' for "no class"; the templates print them as
    // classes, so 'none' would land in class="..." as a stray token.
    foreach (['website_width', 'sitename_animation', 'pagetitle_animation'] as $value) {
      if ($variables[$value] === 'none') {
        $variables[$value] = '';
      }
    }
    // Social Media's $variables.
    $svar = [
      'social_media_size',
      'show_social_icon',
      'facebook_url',
      'twitter_url',
      'pinterest_url',
      'linkedin_url',
      'instagram_url',
      'youtube_url',
      'drupal_url',
      'rss_url',
      'mastodon_url',
      'copyright_text',
      'show_credit_link',
    ];
    foreach ($svar as $value) {
      $variables[$value] = $this->settings->forTwigVariable($value);
      // The settings form rejects unsafe links, but config import and older
      // saved values skip it: strip javascript:, data: and the like here too.
      if (str_ends_with($value, '_url') && is_string($variables[$value])) {
        $variables[$value] = UrlHelper::stripDangerousProtocols($variables[$value]);
      }
    }
    $this->replaceCopyrightYear($variables);
    // Color's variables.
    $cvar = [
      'color_page_wrapper',
      'color_header',
      'color_primary_menu',
      'color_welcome_text',
      'color_highlighted',
      'color_top_container',
      'color_top_first',
      'color_top_second',
      'color_top_third',
      'color_page_title',
      'color_main',
      'color_breadcrumb',
      'color_sidebar_first',
      'color_sidebar_second',
      'color_content',
      'color_bottom_container',
      'color_bottom_first',
      'color_bottom_second',
      'color_bottom_third',
      'color_bottom_forth',
      'color_footer_container',
      'color_footer_first',
      'color_footer_second',
      'color_footer_third',
      'color_footer_menu',
      'color_copyright',
    ];
    foreach ($cvar as $value) {
      $variables[$value] = $this->settings->forTwigVariable($value);
    }
    // Check if the current user on login/register or reset.
    if ($this->settings->get('layout.centered_login_pages')) {
      $curr_path = $this->currentPath->getPath();
      $curr_path = ltrim($curr_path, '/');
      if ($curr_path == 'user/login' || $curr_path == 'user/register' || $curr_path == 'user/password') {
        unset($variables['page']['sidebar_first'], $variables['page']['sidebar_second']);
        $variables['#attached']['library'][] = 'drupal8_w3css_theme/d8w3css-user-login-custom';
      }
    }
  }

  /**
   * Replaces %year% in the copyright text and limits the cache to the year.
   *
   * The page markup is cached, so the year must not outlive December 31.
   * Drupal sets PHP's default time zone to the user's or the site's, which is
   * the zone the year is read in; the timezone cache context follows it.
   *
   * @param array<string, mixed> $variables
   *   The page template variables.
   */
  private function replaceCopyrightYear(array &$variables): void {
    $text = $variables['copyright_text'] ?? NULL;
    if (!is_string($text) || !str_contains($text, '%year%')) {
      return;
    }
    $now = $this->time->getRequestTime();
    $today = (new \DateTimeImmutable('@' . $now))
      ->setTimezone(new \DateTimeZone(date_default_timezone_get()));
    $year = (int) $today->format('Y');
    $next_year = $today->setDate($year + 1, 1, 1)->setTime(0, 0);
    $variables['copyright_text'] = str_replace('%year%', (string) $year, $text);
    $variables['#cache']['contexts'][] = 'timezone';
    $variables['#cache']['max-age'] = Cache::mergeMaxAges(
      $variables['#cache']['max-age'] ?? Cache::PERMANENT,
      max(1, $next_year->getTimestamp() - $now),
    );
  }

  /**
   * Implements hook_preprocess_HOOK() for maintenance-page.html.twig.
   *
   * @param array<string, mixed> $variables
   *   The template variables.
   */
  #[Hook('preprocess_maintenance_page')]
  public function preprocessMaintenancePage(array &$variables): void {
    // By default, site_name is set to Drupal if no db connection is available
    // or during site installation. Setting site_name to an empty string makes
    // the site and update pages look cleaner.
    // @see template_preprocess_maintenance_page
    if (!$variables['db_is_active']) {
      $variables['site_name'] = '';
    }
    // The maintenance page styles (css/components/maintenance-page.css) load
    // with d8w3css-global-styling, a global library in the info.yml file.
  }

}
