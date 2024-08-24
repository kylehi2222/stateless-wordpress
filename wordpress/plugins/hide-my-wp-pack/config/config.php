<?php
/**
 * List of plugin configurations. Database tables
 *
 * @file The configuration file
 *
 * @package HMWP\Config
 */

defined('ABSPATH') || die('Cheatin\' uh?');

/**
 * No path file? error ...
 */
require_once dirname(__FILE__) . '/paths.php';


//Plugin nonce
defined('HMWPP_NONCE') || define('HMWPP_NONCE', 'hmwp_nonce');

/**
 * Define the record name in the Option and UserMeta tables
 */
define('HMWPP_OPTION', 'hmwp_options');
