<?php

declare(strict_types=1);

namespace Drupal\drupal8_w3css_theme\Settings;

/**
 * CSS text animation effects settings.
 */
final class SitenameAnimation extends SettingsSection {

  /**
   * {@inheritdoc}
   */
  public function build(array &$form): void {
    $form['drupal8_w3css_theme_settings']['w3_sitename_animation'] = [
      '#type' => 'details',
      '#title' => $this->t("CSS Text Animation Effects - W3CSS THEME"),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
      '#description'   => $this->t('<p><strong>Available CSS Text Animation Effects Classes: </strong></p><p>animate-text-00, animate-text-01, animate-text-02, animate-text-03, animate-text-04, animate-text-05, animate-text-06, animate-text-07, animate-text-08, animate-text-09, animate-text-10, animate-text-11, animate-text-12, animate-text-13, animate-text-14, animate-text-14, animate-text-15, animate-text-16, animate-text-17, animate-text-18, animate-text-19, animate-text-20, animate-text-21, animate-text-22, animate-text-23, animate-text-24. Any of the previous CSS classes you can apply to any html tag.<a target="_blank" href="https://www.drupal.org/node/2866181#change-font"> Click here to view the documentaion on drupal.org</a>.<a target="_blank" href="w3css.flashwebcenter.com/node/106"> Click here to view an example for all the CSS Text Animation Effects</a>.</p>
    '),
    ];

    $form['drupal8_w3css_theme_settings']['w3_sitename_animation']['sitename_animation'] = [
      '#type' => 'select',
      '#title' => $this->t("Choose the desired CSS text animation effects for the site name."),
      '#options' => [
        'none' => $this->t('None'),
        'animate-name-01' => $this->t('Style 01 (animate-text-01)'),
        'animate-name-02' => $this->t('Style 02 (animate-text-02)'),
        'animate-name-03' => $this->t('Style 03 (animate-text-03)'),
        'animate-name-04' => $this->t('Style 04 (animate-text-04)'),
        'animate-name-05' => $this->t('Style 05 (animate-text-05)'),
        'animate-name-06' => $this->t('Style 06 (animate-text-06)'),
        'animate-name-07' => $this->t('Style 07 (animate-text-07)'),
        'animate-name-08' => $this->t('Style 08 (animate-text-08)'),
        'animate-name-09' => $this->t('Style 09 (animate-text-09)'),
        'animate-name-10' => $this->t('Style 10 (animate-text-10)'),
        'animate-name-11' => $this->t('Style 11 (animate-text-11)'),
        'animate-name-12' => $this->t('Style 12 (animate-text-12)'),
        'animate-name-13' => $this->t('Style 13 (animate-text-13)'),
        'animate-name-14' => $this->t('Style 14 (animate-text-14)'),
        'animate-name-15' => $this->t('Style 15 (animate-text-15)'),
        'animate-name-16' => $this->t('Style 16 (animate-text-16)'),
        'animate-name-17' => $this->t('Style 17 (animate-text-17)'),
        'animate-name-18' => $this->t('Style 18 (animate-text-18)'),
        'animate-name-19' => $this->t('Style 19 (animate-text-19)'),
        'animate-name-20' => $this->t('Style 20 (animate-text-20)'),
        'animate-name-21' => $this->t('Style 21 (animate-text-21)'),
        'animate-name-22' => $this->t('Style 22 (animate-text-22)'),
        'animate-name-23' => $this->t('Style 23 (animate-text-23)'),
        'animate-name-24' => $this->t('Style 24 (animate-text-24)'),

      ],
      '#default_value' => $this->themeSettings->getSetting('sitename_animation'),
    ];

    $form['drupal8_w3css_theme_settings']['w3_sitename_animation']['pagetitle_animation'] = [
      '#type' => 'select',
      '#title' => $this->t("Choose the desired CSS text animation effects for the page title."),
      '#options' => [
        'none' => $this->t('None'),
        'animate-title-01' => $this->t('Style 01'),
        'animate-title-02' => $this->t('Style 02'),
        'animate-title-03' => $this->t('Style 03'),
        'animate-title-04' => $this->t('Style 04'),
        'animate-title-05' => $this->t('Style 05'),
        'animate-title-06' => $this->t('Style 06'),
        'animate-title-07' => $this->t('Style 07'),
        'animate-title-08' => $this->t('Style 08'),
        'animate-title-09' => $this->t('Style 09'),
        'animate-title-10' => $this->t('Style 10'),
        'animate-title-11' => $this->t('Style 11'),
        'animate-title-12' => $this->t('Style 12'),
        'animate-title-13' => $this->t('Style 13'),
        'animate-title-14' => $this->t('Style 14'),
        'animate-title-15' => $this->t('Style 15'),
        'animate-title-16' => $this->t('Style 16'),
        'animate-title-17' => $this->t('Style 17'),
        'animate-title-18' => $this->t('Style 18'),
        'animate-title-19' => $this->t('Style 19'),
        'animate-title-20' => $this->t('Style 20'),
        'animate-title-21' => $this->t('Style 21'),
        'animate-title-22' => $this->t('Style 22'),
        'animate-title-23' => $this->t('Style 23'),
        'animate-title-24' => $this->t('Style 24'),

      ],
      '#default_value' => $this->themeSettings->getSetting('pagetitle_animation'),
    ];
  }

}
