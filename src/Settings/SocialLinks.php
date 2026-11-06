<?php

declare(strict_types=1);

namespace Drupal\drupal8_w3css_theme\Settings;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Social media profile links settings.
 */
final class SocialLinks extends SettingsSection {

  /**
   * {@inheritdoc}
   */
  public function build(array &$form): void {
    $form['drupal8_w3css_theme_settings']['social_icon'] = [
      '#type' => 'details',
      '#title' => $this->t('Social media links - W3CSS Theme'),
    ];

    $form['drupal8_w3css_theme_settings']['social_icon']['social_icons_size'] = [
      '#parents' => ['social_icons', 'size'],
      '#type' => 'radios',
      '#title' => $this->t('Select the social media icon size'),
      '#default_value' => $this->settings->get('social_icons.size'),
      '#options' => [
        0 => $this->t('Small - Default'),
        1 => $this->t('Medium'),
        2 => $this->t('Large'),
      ],
    ];

    $form['drupal8_w3css_theme_settings']['social_icon']['social_icons_enabled'] = [
      '#parents' => ['social_icons', 'enabled'],
      '#type' => 'checkbox',
      '#title' => $this->t('Show social icons'),
      '#default_value' => $this->settings->get('social_icons.enabled'),
      '#description' => $this->t("Check this option to show Social Icon. Uncheck to hide."),
    ];
    $form['drupal8_w3css_theme_settings']['social_icon']['social_icons_links_facebook'] = [
      '#parents' => ['social_icons', 'links', 'facebook'],
      '#type' => 'textfield',
      '#title' => $this->t('Facebook page'),
      '#default_value' => $this->settings->get('social_icons.links.facebook'),
    ];
    $form['drupal8_w3css_theme_settings']['social_icon']['social_icons_links_twitter'] = [
      '#parents' => ['social_icons', 'links', 'twitter'],
      '#type' => 'textfield',
      '#title' => $this->t('X profile'),
      '#default_value' => $this->settings->get('social_icons.links.twitter'),
    ];
    $form['drupal8_w3css_theme_settings']['social_icon']['social_icons_links_pinterest'] = [
      '#parents' => ['social_icons', 'links', 'pinterest'],
      '#type' => 'textfield',
      '#title' => $this->t('Pinterest profile'),
      '#default_value' => $this->settings->get('social_icons.links.pinterest'),
    ];
    $form['drupal8_w3css_theme_settings']['social_icon']['social_icons_links_linkedin'] = [
      '#parents' => ['social_icons', 'links', 'linkedin'],
      '#type' => 'textfield',
      '#title' => $this->t('LinkedIn profile'),
      '#default_value' => $this->settings->get('social_icons.links.linkedin'),
    ];
    $form['drupal8_w3css_theme_settings']['social_icon']['social_icons_links_instagram'] = [
      '#parents' => ['social_icons', 'links', 'instagram'],
      '#type' => 'textfield',
      '#title' => $this->t('Instagram profile'),
      '#default_value' => $this->settings->get('social_icons.links.instagram'),
    ];
    $form['drupal8_w3css_theme_settings']['social_icon']['social_icons_links_youtube'] = [
      '#parents' => ['social_icons', 'links', 'youtube'],
      '#type' => 'textfield',
      '#title' => $this->t('YouTube profile'),
      '#default_value' => $this->settings->get('social_icons.links.youtube'),
    ];
    $form['drupal8_w3css_theme_settings']['social_icon']['social_icons_links_drupal'] = [
      '#parents' => ['social_icons', 'links', 'drupal'],
      '#type' => 'textfield',
      '#title' => $this->t('Drupal profile'),
      '#default_value' => $this->settings->get('social_icons.links.drupal'),
    ];
    $form['drupal8_w3css_theme_settings']['social_icon']['social_icons_links_rss'] = [
      '#parents' => ['social_icons', 'links', 'rss'],
      '#type' => 'textfield',
      '#title' => $this->t('RSS link'),
      '#default_value' => $this->settings->get('social_icons.links.rss'),
    ];
    $form['drupal8_w3css_theme_settings']['social_icon']['social_icons_links_mastodon'] = [
      '#parents' => ['social_icons', 'links', 'mastodon'],
      '#type' => 'textfield',
      '#title' => $this->t('Mastodon profile'),
      '#default_value' => $this->settings->get('social_icons.links.mastodon'),
    ];

    // Every link field is checked on save, so a javascript: or data: address
    // can never be stored. Stored values are also cleaned on output, in
    // DocumentHooks::preprocessPage(), for config that skipped this form.
    foreach ($form['drupal8_w3css_theme_settings']['social_icon'] as $key => &$element) {
      if (is_string($key) && str_starts_with($key, 'social_icons_links_') && is_array($element)) {
        $element['#element_validate'][] = [self::class, 'validateLink'];
        $element['#description'] = $this->t('A full web address that starts with https:// or http://, or a path on this site such as /rss.xml. Leave empty to hide the icon.');
      }
    }
    unset($element);
  }

  /**
   * Form element validation handler for one social link field.
   *
   * @param array<string, mixed> $element
   *   The form element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public static function validateLink(array &$element, FormStateInterface $form_state): void {
    $value = trim((string) ($element['#value'] ?? ''));
    $form_state->setValueForElement($element, $value);
    if ($value === '' || self::isAllowedLink($value)) {
      return;
    }
    $form_state->setError($element, new TranslatableMarkup('@field must be a full web address that starts with https:// or http://, or a path on this site.', [
      '@field' => $element['#title'] ?? '',
    ]));
  }

  /**
   * Checks that a link is an absolute http(s) URL or a path on this site.
   *
   * @param string $value
   *   The trimmed link.
   *
   * @return bool
   *   TRUE when the link is allowed.
   */
  public static function isAllowedLink(string $value): bool {
    // A colon before the first "/", "?" or "#" starts a scheme, the same way a
    // browser reads it. Only http and https are allowed.
    $colon = strpos($value, ':');
    if ($colon !== FALSE && $colon < strcspn($value, '/?#')) {
      $scheme = strtolower(substr($value, 0, $colon));
      return in_array($scheme, ['http', 'https'], TRUE) && UrlHelper::isValid($value, TRUE);
    }
    // A protocol-relative address points at another host: not a site path.
    if (str_starts_with($value, '//')) {
      return FALSE;
    }
    return UrlHelper::isValid($value);
  }

}
