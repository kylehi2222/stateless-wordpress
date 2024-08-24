<?php
/*
  Copyright (c) 2023, WPPlugins.
  Plugin Name: Hide My WP Ghost - Advanced Pack
  Plugin URI: https://hidemywp.com
  Author: WPPlugins
  Description: Advanced security features for Hide My WP Ghost plugin
  Version: 1.3.3
  Author URI: https://hidemywp.com
  Network: true
  Requires at least: 4.6
  Tested up to: 6.3
  Requires PHP: 7.0
 */

if (defined('ABSPATH') && !defined('HMW_VERSION') ) {

    //Set current plugin version
    define('HMWPP_VERSION', '1.3.3');
    define('HMWP_VERSION_MIN', '5.0.00');

    //Set the plugin basename
    define('HMWPP_BASENAME',  plugin_basename(__FILE__));

    //important to check the PHP version
    try {

        //Call config files
        include dirname(__FILE__) . '/config/config.php';

        //import main classes
        include_once _HMWPP_CLASSES_DIR_ . 'ObjController.php';

        if(class_exists('HMWPP_Classes_ObjController')) {

            //Load Exception, Error and Tools class
            HMWPP_Classes_ObjController::getClass('HMWPP_Classes_Error');
            HMWPP_Classes_ObjController::getClass('HMWPP_Classes_Tools');

            //Load Front Controller
            HMWPP_Classes_ObjController::getClass('HMWPP_Classes_FrontController');

            //if the disable signal is on, return
            //don't run cron hooks and update if there are installs
            if (defined('HMWPP_DISABLE') && HMWPP_DISABLE) {
                return;
            }elseif (!is_multisite() && defined('WP_INSTALLING') && WP_INSTALLING) {
                return;
            }elseif (is_multisite() && defined('WP_INSTALLING_NETWORK') && WP_INSTALLING_NETWORK) {
                return;
            }elseif (defined('WP_UNINSTALL_PLUGIN') && WP_UNINSTALL_PLUGIN <> ''){
                return;
            }

            //don't load brute force and events on cron jobs
            if(!defined('DOING_CRON') || !DOING_CRON){
                if (HMWPP_Classes_Tools::getOption('hmwp_templogin') ) {
                    HMWPP_Classes_ObjController::getClass('HMWPP_Controllers_Templogin');
                }
                if (HMWPP_Classes_Tools::getOption('hmwp_uniquelogin') ) {
                    HMWPP_Classes_ObjController::getClass('HMWPP_Controllers_Uniquelogin');
                }
                if (HMWPP_Classes_Tools::getOption('hmwp_2falogin') ) {
                    HMWPP_Classes_ObjController::getClass('HMWPP_Controllers_Twofactor');
                }
            }

            //Request the plugin update when a new version is released
            require dirname(__FILE__) . '/update.php';

        }

    } catch ( Exception $e ) {

    }

}
