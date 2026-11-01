<?php

declare(strict_types=1);

namespace Drupal\drupal8_w3css_theme\Hook;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Form alter hooks for the node, comment and search forms.
 */
final class FormAlterHooks {
  use StringTranslationTrait;

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

}
