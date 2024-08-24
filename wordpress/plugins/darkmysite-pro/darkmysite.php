<?php
/**
 * Plugin Name:       DarkMySite Pro
 * Plugin URI:        https://darkmysite.com
 * Description:       Simplest way to enable dark mode on your website - DarkMySite.
 * Version:           1.2.7
 * Author:            DarkMySite
 * Author URI:        https://darkmysite.com
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       darkmysite
 * Domain Path:       /languages
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

defined( 'DARKMYSITE_PRO_VERSION' ) or define( 'DARKMYSITE_PRO_VERSION', '1.2.7' );
defined( 'DARKMYSITE_PRO_PATH' ) or define( 'DARKMYSITE_PRO_PATH', plugin_dir_path( __FILE__ ) );
defined( 'DARKMYSITE_PRO_URL' ) or define( 'DARKMYSITE_PRO_URL', plugin_dir_url( __FILE__ ) );
defined( 'DARKMYSITE_PRO_BASE_FILE' ) or define( 'DARKMYSITE_PRO_BASE_FILE', __FILE__ );
defined( 'DARKMYSITE_PRO_BASE_PATH' ) or define( 'DARKMYSITE_PRO_BASE_PATH', plugin_basename(__FILE__) );
defined( 'DARKMYSITE_PRO_IMG_DIR' ) or define( 'DARKMYSITE_PRO_IMG_DIR', plugin_dir_url( __FILE__ ) . 'assets/img/' );
defined( 'DARKMYSITE_PRO_CSS_DIR' ) or define( 'DARKMYSITE_PRO_CSS_DIR', plugin_dir_url( __FILE__ ) . 'assets/css/' );
defined( 'DARKMYSITE_PRO_JS_DIR' ) or define( 'DARKMYSITE_PRO_JS_DIR', plugin_dir_url( __FILE__ ) . 'assets/js/' );
defined( 'DARKMYSITE_PRO_SERVER' ) or define( 'DARKMYSITE_PRO_SERVER', 'https://darkmysite.com' );


function darkmysite_pro_activated() {
    update_option('darkmysite_pro_activation_date', time());
}
register_activation_hook( __FILE__, 'darkmysite_pro_activated' );


require_once DARKMYSITE_PRO_PATH . 'update.php';
require_once DARKMYSITE_PRO_PATH . 'includes/DarkMySiteUtils.php';
require_once DARKMYSITE_PRO_PATH . 'includes/DarkMySiteSettings.php';
require_once DARKMYSITE_PRO_PATH . 'includes/DarkMySiteExternalSupport.php';
require_once DARKMYSITE_PRO_PATH . 'backend/class-darkmysite-ajax.php';
require_once DARKMYSITE_PRO_PATH . 'backend/class-darkmysite-admin.php';

require_once DARKMYSITE_PRO_PATH . 'frontend/class-darkmysite-support.php';
require_once DARKMYSITE_PRO_PATH . 'frontend/class-darkmysite-shortcode.php';
require_once DARKMYSITE_PRO_PATH . 'frontend/class-darkmysite-ajax.php';
require_once DARKMYSITE_PRO_PATH . 'frontend/class-darkmysite-client.php';