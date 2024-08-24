<?php
/**
 * Definition of all the paths from the plugin
 *
 * @file The paths configuration file
 *
 * @package HMWP\Paths
 */

defined('ABSPATH') || die('Cheatin\' uh?');

$currentDir = dirname(__FILE__);

define('_HMWPP_NAMESPACE_', 'HMWPP');
define('_HMWPP_PLUGIN_FULL_NAME_', 'Hide My WP Ghost - Advanced Pack');

/**
 * Directories
 */
define('_HMWPP_ROOT_DIR_', realpath($currentDir . '/..'));
define('_HMWPP_CLASSES_DIR_', _HMWPP_ROOT_DIR_ . '/classes/');
define('_HMWPP_CONTROLLER_DIR_', _HMWPP_ROOT_DIR_ . '/controllers/');
define('_HMWPP_MODEL_DIR_', _HMWPP_ROOT_DIR_ . '/models/');
define('_HMWPP_TRANSLATIONS_DIR_', _HMWPP_ROOT_DIR_ . '/languages/');
define('_HMWPP_THEME_DIR_', _HMWPP_ROOT_DIR_ . '/view/');
define('_HMWPP_ASSETS_DIR_', _HMWPP_THEME_DIR_ . 'assets/');

/**
 * URLS paths
 */
define('_HMWPP_URL_', plugins_url() . '/' . plugin_basename(_HMWPP_ROOT_DIR_));
define('_HMWPP_THEME_URL_', _HMWPP_URL_ . '/view/');
define('_HMWPP_ASSETS_URL_', _HMWPP_THEME_URL_ . 'assets/');
define('_HMWPP_WPLOGIN_URL_', _HMWPP_THEME_URL_ . 'wplogin/');
