<?php

declare(strict_types=1);

namespace Drupal\drupal8_w3css_theme\Hook;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Template suggestions for blocks, menus, forms, pages and users.
 */
final class ThemeSuggestionHooks {
  public function __construct(
    private readonly RouteMatchInterface $routeMatch,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Implements hook_theme_suggestions_block_alter().
   */
  #[Hook('theme_suggestions_block_alter')]
  public function themeSuggestionsBlockAlter(array &$suggestions, array $variables): void {
    if (!empty($variables['elements']['#id'])) {
      /** @var \Drupal\block\BlockInterface $block */
      $block = $this->entityTypeManager->getStorage('block')->load($variables['elements']['#id']);
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
    $routeMatch = $this->routeMatch;
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
