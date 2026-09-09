<?php

/**
 * @file
 * User login page layout configuration.
 *
 * Filename:     layout_user_login.inc
 * Website:      https://www.flashwebcenter.com
 * Description:  Custom layout for login, register, and reset password pages.
 * Author:       Alaa Haddad https://www.alaahaddad.com.
 */

declare(strict_types=1);

use Drupal\Component\Utility\DeprecationHelper;
use Drupal\Core\Extension\ThemeSettingsProvider;

/**
 * User login layout theme settings.
 */
$form['drupal8_w3css_theme_settings']['w3_layout_user'] = [
  '#type' => 'details',
  '#title' => t('Enable custom layout for the login, register and reset password pages.'),
  '#collapsible' => TRUE,
  '#collapsed' => FALSE,
  '#description'   => t('<p>If you check this box the login form will be centered and left/right sides will be hidden.</p>'),
];
$form['drupal8_w3css_theme_settings']['w3_layout_user']['layout_user_login_custom'] = [
  '#type' => 'checkbox',
  '#title' => t('Check this box to change the layout for the login, register and reset password pages.'),
  '#default_value' => DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(ThemeSettingsProvider::class)->getSetting('layout_user_login_custom'), fn() => theme_get_setting('layout_user_login_custom')),
];
