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

    $form['drupal8_w3css_theme_settings']['social_icon']['social_media_size'] = [
      '#type' => 'radios',
      '#title' => $this->t('Select The Social Media Icon Size'),
      '#default_value' => $this->themeSettings->getSetting('social_media_size'),
      '#options' => [
        0 => $this->t('Small - Default'),
        1 => $this->t('Medium'),
        2 => $this->t('Large'),
      ],
    ];

    $form['drupal8_w3css_theme_settings']['social_icon']['show_social_icon'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show Social Icons'),
      '#default_value' => $this->themeSettings->getSetting('show_social_icon'),
      '#description' => $this->t("Check this option to show Social Icon. Uncheck to hide."),
    ];
    $form['drupal8_w3css_theme_settings']['social_icon']['facebook_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Facebook Page'),
      '#default_value' => $this->themeSettings->getSetting('facebook_url'),
    ];
    $form['drupal8_w3css_theme_settings']['social_icon']['twitter_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('X Profile'),
      '#default_value' => $this->themeSettings->getSetting('twitter_url'),
    ];
    $form['drupal8_w3css_theme_settings']['social_icon']['pinterest_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Pinterest Profile'),
      '#default_value' => $this->themeSettings->getSetting('pinterest_url'),
    ];
    $form['drupal8_w3css_theme_settings']['social_icon']['linkedin_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Linkedin Profile'),
      '#default_value' => $this->themeSettings->getSetting('linkedin_url'),
    ];
    $form['drupal8_w3css_theme_settings']['social_icon']['instagram_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Instagram Profile'),
      '#default_value' => $this->themeSettings->getSetting('instagram_url'),
    ];
    $form['drupal8_w3css_theme_settings']['social_icon']['youtube_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Youtube Profile'),
      '#default_value' => $this->themeSettings->getSetting('youtube_url'),
    ];
    $form['drupal8_w3css_theme_settings']['social_icon']['drupal_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Drupal Profile'),
      '#default_value' => $this->themeSettings->getSetting('drupal_url'),
    ];
    $form['drupal8_w3css_theme_settings']['social_icon']['rss_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('RSS Link'),
      '#default_value' => $this->themeSettings->getSetting('rss_url'),
    ];
    $form['drupal8_w3css_theme_settings']['social_icon']['mastodon_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Mastodon Profile'),
      '#default_value' => $this->themeSettings->getSetting('mastodon_url'),
    ];
  }

}
