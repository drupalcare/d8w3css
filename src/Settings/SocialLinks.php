<?php

declare(strict_types=1);

namespace Drupal\drupal8_w3css_theme\Settings;

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
      '#title' => $this->t('Social Media Links - W3CSS Theme'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    ];

    $form['drupal8_w3css_theme_settings']['social_icon']['social_icons_size'] = [
      '#parents' => ['social_icons', 'size'],
      '#type' => 'radios',
      '#title' => $this->t('Select The Social Media Icon Size'),
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
      '#title' => $this->t('Show Social Icons'),
      '#default_value' => $this->settings->get('social_icons.enabled'),
      '#description' => $this->t("Check this option to show Social Icon. Uncheck to hide."),
    ];
    $form['drupal8_w3css_theme_settings']['social_icon']['social_icons_links_facebook'] = [
      '#parents' => ['social_icons', 'links', 'facebook'],
      '#type' => 'textfield',
      '#title' => $this->t('Facebook Page'),
      '#default_value' => $this->settings->get('social_icons.links.facebook'),
    ];
    $form['drupal8_w3css_theme_settings']['social_icon']['social_icons_links_twitter'] = [
      '#parents' => ['social_icons', 'links', 'twitter'],
      '#type' => 'textfield',
      '#title' => $this->t('X Profile'),
      '#default_value' => $this->settings->get('social_icons.links.twitter'),
    ];
    $form['drupal8_w3css_theme_settings']['social_icon']['social_icons_links_pinterest'] = [
      '#parents' => ['social_icons', 'links', 'pinterest'],
      '#type' => 'textfield',
      '#title' => $this->t('Pinterest Profile'),
      '#default_value' => $this->settings->get('social_icons.links.pinterest'),
    ];
    $form['drupal8_w3css_theme_settings']['social_icon']['social_icons_links_linkedin'] = [
      '#parents' => ['social_icons', 'links', 'linkedin'],
      '#type' => 'textfield',
      '#title' => $this->t('Linkedin Profile'),
      '#default_value' => $this->settings->get('social_icons.links.linkedin'),
    ];
    $form['drupal8_w3css_theme_settings']['social_icon']['social_icons_links_instagram'] = [
      '#parents' => ['social_icons', 'links', 'instagram'],
      '#type' => 'textfield',
      '#title' => $this->t('Instagram Profile'),
      '#default_value' => $this->settings->get('social_icons.links.instagram'),
    ];
    $form['drupal8_w3css_theme_settings']['social_icon']['social_icons_links_youtube'] = [
      '#parents' => ['social_icons', 'links', 'youtube'],
      '#type' => 'textfield',
      '#title' => $this->t('Youtube Profile'),
      '#default_value' => $this->settings->get('social_icons.links.youtube'),
    ];
    $form['drupal8_w3css_theme_settings']['social_icon']['social_icons_links_drupal'] = [
      '#parents' => ['social_icons', 'links', 'drupal'],
      '#type' => 'textfield',
      '#title' => $this->t('Drupal Profile'),
      '#default_value' => $this->settings->get('social_icons.links.drupal'),
    ];
    $form['drupal8_w3css_theme_settings']['social_icon']['social_icons_links_rss'] = [
      '#parents' => ['social_icons', 'links', 'rss'],
      '#type' => 'textfield',
      '#title' => $this->t('RSS Link'),
      '#default_value' => $this->settings->get('social_icons.links.rss'),
    ];
    $form['drupal8_w3css_theme_settings']['social_icon']['social_icons_links_mastodon'] = [
      '#parents' => ['social_icons', 'links', 'mastodon'],
      '#type' => 'textfield',
      '#title' => $this->t('Mastodon Profile'),
      '#default_value' => $this->settings->get('social_icons.links.mastodon'),
    ];
  }

}
