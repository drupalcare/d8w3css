<?php

namespace Drupal\drupal8_w3css_theme\Hook;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Extension\ThemeSettingsProvider;
use Drupal\Component\Utility\DeprecationHelper;
use Drupal\Component\Utility\Html;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Hook implementations for drupal8_w3css_theme.
 */
class Drupal8W3cssThemeHooks {
  use StringTranslationTrait;

  /**
   * Implements hook_preprocess_HOOK() for HTML document templates.
   */
  #[Hook('preprocess_html')]
  public function preprocessHtml(array &$variables): void {
    // Add node id to the body class.
    $variables['path_info']['args'] = FALSE;
    $routeMatch = \Drupal::routeMatch();
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
    $curr_path = \Drupal::service('path.current')->getPath();
    $curr_path = ltrim($curr_path, '/');
    $user_url = explode('/', $curr_path);
    if (isset($user_url[0], $user_url[1]) && $user_url[0] == 'user') {
      $variables['attributes']['class'][] = 'path-user-' . $user_url[1];
    }
    // Add view id name to the body class.
    $view_id = $routeMatch->getParameter('view_id');
    if ($view_id) {
      $variables['attributes']['class'][] = 'path-view-' . Html::cleanCssIdentifier($view_id);
    }
    /** @var \Drupal\Core\Routing\AdminContext $admin_context */
    $admin_context = \Drupal::service('router.admin_context');
    if ($admin_context->isAdminRoute()) {
      $variables['attributes']['class'][] = 'admin-context';
    }
  }

  /**
   * Implements hook_preprocess_page() for page templates.
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
    $variables['equal_width'] = DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(ThemeSettingsProvider::class)->getSetting('equal_width'), fn() => theme_get_setting('equal_width'));
    if ($variables['equal_width'] != NULL) {
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
      $variables[$value] = DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(ThemeSettingsProvider::class)->getSetting($value), fn() => theme_get_setting($value));
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
      $variables[$value] = DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(ThemeSettingsProvider::class)->getSetting($value), fn() => theme_get_setting($value));
    }
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
      $variables[$value] = DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(ThemeSettingsProvider::class)->getSetting($value), fn() => theme_get_setting($value));
    }
    // Check if the current user on login/register or reset.
    if (DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(ThemeSettingsProvider::class)->getSetting('layout_user_login_custom'), fn() => theme_get_setting('layout_user_login_custom'))) {
      $curr_path = \Drupal::service('path.current')->getPath();
      $curr_path = ltrim($curr_path, '/');
      if ($curr_path == 'user/login' || $curr_path == 'user/register' || $curr_path == 'user/password') {
        unset($variables['page']['sidebar_first'], $variables['page']['sidebar_second']);
        $variables['#attached']['library'][] = 'drupal8_w3css_theme/d8w3css-user-login-custom';
      }
    }
  }

  /**
   * Implements hook_preprocess_HOOK() for node.html.twig.
   */
  #[Hook('preprocess_node')]
  public function preprocessNode(array &$variables): void {
    // Remove the "Add new comment" link on teasers or when the comment form is
    // displayed on the page.
    //
    // Drupal 12 NO LONGER SETS $variables['teaser']. Core's
    // template_preprocess_node() no longer exists; node preprocessing moved to
    // NodeThemeHooks::preprocessNode(), which documents view_mode and not
    // teaser. Reading the key raised "Undefined array key \"teaser\"" on every
    // node render -- measured live on 12.0.0-alpha1, 2026-09-08, the same
    // defect already fixed in the Solo theme.
    $is_teaser = ($variables['view_mode'] ?? '') === 'teaser';
    if ($is_teaser || !empty($variables['content']['comments']['comment_form'])) {
      unset($variables['content']['links']['comment']['#links']['comment-add']);
    }
  }

  /**
   * Implements hook_preprocess_breadcrumb().
   */
  #[Hook('preprocess_breadcrumb')]
  public function preprocessBreadcrumb(array &$variables): void {
    if ($variables['breadcrumb'] && !DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(ThemeSettingsProvider::class)->getSetting('breadcrumb_page_title'), fn() => theme_get_setting('breadcrumb_page_title'))) {
      $request = \Drupal::request();
      $route_match = \Drupal::routeMatch();
      $page_title = \Drupal::service('title_resolver')->getTitle($request, $route_match->getRouteObject());
      if (!empty($page_title)) {
        $variables['breadcrumb'][] = [
          'text' => $page_title,
        ];
        // Add cache context based on url.
        $variables['#cache']['contexts'][] = 'url';
      }
    }
  }

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_field__node__created')]
  public function preprocessFieldNodeCreated(array &$variables): void {
    foreach (Element::children($variables['items']) as $item) {
      unset($variables['items'][$item]['content']['#prefix']);
    }
  }

  /**
   * Implements hook_preprocess_HOOK() for setting classes.
   */
  #[Hook('preprocess_filter_caption')]
  public function preprocessFilterCaption(array &$variables): void {
    $variables['classes'] = isset($variables['classes']) && !empty($variables['classes']) ? $variables['classes'] . ' caption' : 'caption';
  }

  /**
   * Implements hook_preprocess_HOOK() for comment templates.
   */
  #[Hook('preprocess_comment')]
  public function preprocessComment(array &$variables): void {
    // Getting the node creation time stamp from the comment object.
    $date = $variables['comment']->getCreatedTime();
    // Formatting "created" as "X days ago".
    $variables['created'] = $this->t('@time ago', [
      '@time' => \Drupal::service('date.formatter')->formatInterval(\Drupal::time()->getRequestTime() - $date),
    ]);
  }

  /**
   * Implements hook_preprocess_HOOK() for maintenance-page.html.twig.
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
    // drupal8_w3css_theme has custom styling for the maintenance page.
    $variables['#attached']['library'][] = 'drupal8_w3css_theme/templates/layout/maintenance_page';
  }

  /**
   * Implements hook_page_attachments_alter().
   */
  #[Hook('page_attachments_alter')]
  public function pageAttachmentsAlter(array &$attachments): void {
    // Load google fonts locally - Global.
    if (DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(ThemeSettingsProvider::class)->getSetting('d8w3css_google_font_local'), fn() => theme_get_setting('d8w3css_google_font_local')) && DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(ThemeSettingsProvider::class)->getSetting('d8w3css_site_gf'), fn() => theme_get_setting('d8w3css_site_gf'))) {
      $in_font = DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(ThemeSettingsProvider::class)->getSetting('d8w3css_site_gf'), fn() => theme_get_setting('d8w3css_site_gf'));
      $current_ingf = 'drupal8_w3css_theme/d8w3css-gfin' . substr($in_font, 7);
      $attachments['#attached']['library'][] = $current_ingf;
    }
    // Load fonts external - Global.
    if (!DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(ThemeSettingsProvider::class)->getSetting('d8w3css_google_font_local'), fn() => theme_get_setting('d8w3css_google_font_local')) && DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(ThemeSettingsProvider::class)->getSetting('d8w3css_site_gf'), fn() => theme_get_setting('d8w3css_site_gf'))) {
      $ex_font = DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(ThemeSettingsProvider::class)->getSetting('d8w3css_site_gf'), fn() => theme_get_setting('d8w3css_site_gf'));
      $current_exgf = 'drupal8_w3css_theme/d8w3css-gfex' . substr($ex_font, 7);
      $attachments['#attached']['library'][] = $current_exgf;
    }
    // Load google fonts locally - Heading.
    if (DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(ThemeSettingsProvider::class)->getSetting('d8w3css_google_font_local'), fn() => theme_get_setting('d8w3css_google_font_local')) && DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(ThemeSettingsProvider::class)->getSetting('d8w3css_site_gfh'), fn() => theme_get_setting('d8w3css_site_gfh'))) {
      $in_font_h = DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(ThemeSettingsProvider::class)->getSetting('d8w3css_site_gfh'), fn() => theme_get_setting('d8w3css_site_gfh'));
      $current_ingfh = 'drupal8_w3css_theme/d8w3css-gfin' . substr($in_font_h, 7);
      $attachments['#attached']['library'][] = $current_ingfh;
    }
    // Load fonts external - Heading.
    if (!DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(ThemeSettingsProvider::class)->getSetting('d8w3css_google_font_local'), fn() => theme_get_setting('d8w3css_google_font_local')) && DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(ThemeSettingsProvider::class)->getSetting('d8w3css_site_gfh'), fn() => theme_get_setting('d8w3css_site_gfh'))) {
      $ex_font_h = DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(ThemeSettingsProvider::class)->getSetting('d8w3css_site_gfh'), fn() => theme_get_setting('d8w3css_site_gfh'));
      $current_exgfh = 'drupal8_w3css_theme/d8w3css-gfex' . substr($ex_font_h, 7);
      $attachments['#attached']['library'][] = $current_exgfh;
    }
    // Load special fonts' library.
    if (DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(ThemeSettingsProvider::class)->getSetting('d8w3css_site_gfs'), fn() => theme_get_setting('d8w3css_site_gfs'))) {
      $ex_font_s = DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(ThemeSettingsProvider::class)->getSetting('d8w3css_site_gfs'), fn() => theme_get_setting('d8w3css_site_gfs'));
      $current_exgfs = 'drupal8_w3css_theme/d8w3css-gfex' . substr($ex_font_s, 7);
      $attachments['#attached']['library'][] = $current_exgfs;
    }
    // Load default color theme if not.
    if (!DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(ThemeSettingsProvider::class)->getSetting('current_theme'), fn() => theme_get_setting('current_theme')) || DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(ThemeSettingsProvider::class)->getSetting('current_theme'), fn() => theme_get_setting('current_theme')) == 'none') {
      $attachments['#attached']['library'][] = 'drupal8_w3css_theme/w3css-default-theme';
    }
    else {
      $my_current_theme = DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(ThemeSettingsProvider::class)->getSetting('current_theme'), fn() => theme_get_setting('current_theme'));
      $predefined_theme_filename = 'drupal8_w3css_theme/' . str_replace('w3-theme-', 'w3-css-theme-', $my_current_theme);
      $attachments['#attached']['library'][] = $predefined_theme_filename;
    }
    // Load font awesome.
    switch (DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(ThemeSettingsProvider::class)->getSetting('d8w3css_font_awesome'), fn() => theme_get_setting('d8w3css_font_awesome'))) {
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
    switch (DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(ThemeSettingsProvider::class)->getSetting('social_media_size'), fn() => theme_get_setting('social_media_size'))) {
      case 1:
        $attachments['#attached']['library'][] = 'drupal8_w3css_theme/social-media-icon-size-m';
        break;

      case 2:
        $attachments['#attached']['library'][] = 'drupal8_w3css_theme/social-media-icon-size-l';
        break;
    }
    // Load full opacity if enabled but not on admin pages.
    /** @var \Drupal\Core\Routing\AdminContext $admin_context */
    $admin_context = \Drupal::service('router.admin_context');
    if (DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(ThemeSettingsProvider::class)->getSetting('full_opacity_onscroll'), fn() => theme_get_setting('full_opacity_onscroll')) && !$admin_context->isAdminRoute()) {
      $attachments['#attached']['library'][] = 'drupal8_w3css_theme/d8w3css-full-opacity';
    }
  }

  /**
   * Implements hook_form_BASE_FORM_ID_alter() for \Drupal\node\NodeForm.
   *
   * Changes vertical tabs to container.
   */
  #[Hook('form_node_form_alter')]
  public function formNodeFormAlter(array &$form, FormStateInterface $form_state): void {
    $form['#theme'] = [
      'node_edit_form',
    ];
    $form['advanced']['#type'] = 'container';
    $form['meta']['#type'] = 'container';
    $form['meta']['#access'] = TRUE;
    $form['meta']['changed']['#wrapper_attributes']['class'][] = 'container-inline';
    $form['meta']['author']['#wrapper_attributes']['class'][] = 'container-inline';
    $form['revision_information']['#type'] = 'container';
    $form['revision_information']['#group'] = 'meta';
  }

  /**
   * Implements hook_form_FORM_ID_alter().
   */
  #[Hook('form_comment_form_alter')]
  public function formCommentFormAlter(array &$form, FormStateInterface $form_state, string $form_id): void {
    $comment = $form_state->getFormObject()->getEntity();
    /** @var \Drupal\comment\Entity\Comment $comment */
    if ($comment->hasParentComment()) {
      $form['actions']['submit']['#value'] = $this->t('Reply to comment');
    }
    else {
      $form['actions']['submit']['#value'] = $this->t('Post comment');
    }
  }

  /**
   * Implements hook_form_FORM_ID_alter().
   */
  #[Hook('form_search_block_form_alter')]
  public function formSearchBlockFormAlter(array &$form, FormStateInterface &$form_state): void {
    $form['keys']['#attributes']['placeholder'] = $this->t('Search');
    $form['actions']['submit']['#value'] = '.';
    $form['actions']['submit']['#attributes']['class'][] = 'search-button-block';
  }

  /**
   * Implements hook_form_FORM_ID_alter().
   */
  #[Hook('form_search_form_alter')]
  public function formSearchFormAlter(array &$form, FormStateInterface &$form_state): void {
    unset($form['help_link']);
  }

  /**
   * Implements hook_theme_suggestions_block_alter().
   */
  #[Hook('theme_suggestions_block_alter')]
  public function themeSuggestionsBlockAlter(array &$suggestions, array $variables): void {
    if (!empty($variables['elements']['#id'])) {
      /** @var \Drupal\block\BlockInterface $block */
      $block = \Drupal::entityTypeManager()->getStorage('block')->load($variables['elements']['#id']);
      if ($block) {
        // Add region-specific block theme suggestions.
        $region = $block->getRegion();
        $suggestions[] = 'block__' . $region;
        $suggestions[] = 'block__' . $region . '__plugin_id__' . $variables['elements']['#plugin_id'];
        $suggestions[] = 'block__' . $region . '__id__' . $variables['elements']['#id'];
      }
    }
  }

  /**
   * Implements hook_theme_suggestions_HOOK_alter() for menu.
   */
  #[Hook('theme_suggestions_menu_alter')]
  public function themeSuggestionsMenuAlter(array &$suggestions, array $variables): void {
    if (isset($variables['attributes']['region'])) {
      $suggestions[] = 'menu__' . $variables['attributes']['region'];
    }
  }

  /**
   * Implements hook_theme_suggestions_HOOK_alter() for form templates.
   */
  #[Hook('theme_suggestions_form_alter')]
  public function themeSuggestionsFormAlter(array &$suggestions, array $variables): void {
    // Suggest the name for the search block.
    if ($variables['element']['#form_id'] == 'search_block_form') {
      $suggestions[] = 'form__search_block_form';
    }
  }

  /**
   * Implements hook_theme_suggestions_page_alter().
   */
  #[Hook('theme_suggestions_page_alter')]
  public function themeSuggestionsPageAlter(array &$suggestions, array $variables): void {
    $routeMatch = \Drupal::routeMatch();
    // Test route name.
    switch ($routeMatch->getRouteName()) {
      // Suggest the node page according to the content type name.
      case 'entity.node.canonical':
        $node = $routeMatch->getParameter('node');
        if ($node) {
          array_splice($suggestions, 1, 0, 'page__' . $node->bundle());
        }
        break;

      // Suggest the vocabulary page according to the vocabulary name.
      case 'entity.taxonomy_term.canonical':
        $term = $routeMatch->getParameter('taxonomy_term');
        if ($term) {
          array_splice($suggestions, 1, 0, 'page__taxonomy__' . $term->bundle());
        }
        break;
    }
    // Suggest the view page according to the view name.
    $route = $routeMatch->getRouteObject();
    if ($route && $view_name = $route->getDefault('view_id')) {
      $suggestions[] = 'page__' . $view_name;
    }
  }

  /**
   * Implements hook_theme_suggestions_HOOK_alter().
   */
  #[Hook('theme_suggestions_user_alter')]
  public function themeSuggestionsUserAlter(array &$suggestions, array $variables): void {
    $suggestions[] = 'user__' . $variables['elements']['#view_mode'];
  }

}
