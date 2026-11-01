<?php

declare(strict_types=1);

namespace Drupal\drupal8_w3css_theme\Hook;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Controller\TitleResolverInterface;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Extension\ThemeExtensionList;
use Drupal\Core\Extension\ThemeSettingsProvider;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Path\PathMatcherInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Preprocess hooks for blocks, nodes, comments, breadcrumbs and search results.
 */
final class ContentPreprocessHooks {
  use StringTranslationTrait;

  public function __construct(
    private readonly ThemeSettingsProvider $themeSettings,
    private readonly RouteMatchInterface $routeMatch,
    private readonly PathMatcherInterface $pathMatcher,
    private readonly RequestStack $requestStack,
    private readonly TitleResolverInterface $titleResolver,
    private readonly DateFormatterInterface $dateFormatter,
    private readonly TimeInterface $time,
    private readonly ThemeExtensionList $themeList,
  ) {}

  /**
   * Implements hook_preprocess_HOOK() for block templates.
   */
  #[Hook('preprocess_block')]
  public function preprocessBlock(array &$variables): void {
    // The branding template prints the site name as h1 on the front page only.
    // Core gives is_front to the page template, not to blocks.
    if (($variables['plugin_id'] ?? '') === 'system_branding_block') {
      $variables['is_front'] = $this->pathMatcher->isFrontPage();
      $variables['#cache']['contexts'][] = 'url.path.is_front';
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
    if ($variables['breadcrumb'] && !$this->themeSettings->getSetting('breadcrumb_page_title')) {
      $page_title = $this->titleResolver->getTitle($this->requestStack->getCurrentRequest(), $this->routeMatch->getRouteObject());
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
      '@time' => $this->dateFormatter->formatInterval($this->time->getRequestTime() - (int) $date),
    ]);
  }

  /**
   * Implements hook_preprocess_HOOK() for search-result.html.twig.
   */
  #[Hook('preprocess_search_result')]
  public function preprocessSearchResult(array &$variables): void {
    // Removes the bullets from the results list where the library exists. It is
    // Claro's, and Drupal 12 core has no Claro: attaching it there only logs a
    // missing theme warning.
    if ($this->themeList->exists('claro')) {
      $variables['#attached']['library'][] = 'claro/classy.search-results';
    }
  }

}
