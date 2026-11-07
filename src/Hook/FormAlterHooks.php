<?php

declare(strict_types=1);

namespace Drupal\drupal8_w3css_theme\Hook;

use Drupal\comment\CommentInterface;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Entity\EntityFormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Form alter hooks for the node, comment and search forms.
 */
final class FormAlterHooks {
  use StringTranslationTrait;

  /**
   * Implements hook_form_BASE_FORM_ID_alter() for node_form.
   *
   * Changes vertical tabs to container.
   *
   * @param array<string, mixed> $form
   *   The form render array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current form state.
   */
  #[Hook('form_node_form_alter')]
  public function formNodeFormAlter(array &$form, FormStateInterface $form_state): void {
    $form['#theme'] = [
      'node_edit_form',
    ];
    $form['advanced']['#type'] = 'container';
    $form['meta']['#type'] = 'container';
    // NodeForm shows "meta" (status, last saved, author) only to users with
    // "administer nodes". The container is opened so it can hold the revision
    // log below, which carries its own access, but its own items keep core's
    // decision: nothing here is shown that NodeForm hid.
    $meta_access = $this->isAllowed($form['meta']['#access'] ?? TRUE);
    $form['meta']['#access'] = TRUE;
    foreach (['published', 'changed', 'author'] as $item) {
      if (isset($form['meta'][$item])) {
        $form['meta'][$item]['#access'] = $meta_access && $this->isAllowed($form['meta'][$item]['#access'] ?? TRUE);
      }
    }
    foreach (['changed', 'author'] as $item) {
      if (isset($form['meta'][$item])) {
        $form['meta'][$item]['#wrapper_attributes']['class'][] = 'container-inline';
      }
    }
    $form['revision_information']['#type'] = 'container';
    $form['revision_information']['#group'] = 'meta';
  }

  /**
   * Implements hook_form_BASE_FORM_ID_alter() for comment_form.
   *
   * @param array<string, mixed> $form
   *   The form render array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current form state.
   * @param string $form_id
   *   The form ID.
   */
  #[Hook('form_comment_form_alter')]
  public function formCommentFormAlter(array &$form, FormStateInterface $form_state, string $form_id): void {
    $form_object = $form_state->getFormObject();
    $comment = $form_object instanceof EntityFormInterface ? $form_object->getEntity() : NULL;
    if ($comment instanceof CommentInterface && $comment->hasParentComment()) {
      $form['actions']['submit']['#value'] = $this->t('Reply to comment');
    }
    else {
      $form['actions']['submit']['#value'] = $this->t('Post comment');
    }
  }

  /**
   * Implements hook_form_FORM_ID_alter().
   *
   * @param array<string, mixed> $form
   *   The form render array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current form state.
   */
  #[Hook('form_search_block_form_alter')]
  public function formSearchBlockFormAlter(array &$form, FormStateInterface $form_state): void {
    $form['keys']['#attributes']['placeholder'] = $this->t('Search');
    // The value is the button's visible label and accessible name;
    // search-block.css joins it to the field at the same height.
    $form['actions']['submit']['#value'] = $this->t('Search');
    $form['actions']['submit']['#attributes']['class'][] = 'w3css-search-button-block';
  }

  /**
   * Implements hook_form_FORM_ID_alter().
   *
   * @param array<string, mixed> $form
   *   The form render array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current form state.
   */
  #[Hook('form_search_form_alter')]
  public function formSearchFormAlter(array &$form, FormStateInterface $form_state): void {
    unset($form['help_link']);
  }

  /**
   * Reads an #access value the way the renderer does.
   *
   * @param mixed $access
   *   A boolean or an access result object.
   *
   * @return bool
   *   TRUE only when access is granted; a forbidden or neutral result is FALSE.
   */
  private function isAllowed(mixed $access): bool {
    return $access instanceof AccessResultInterface ? $access->isAllowed() : (bool) $access;
  }

}
