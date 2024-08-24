<?php
/**
 * HMWP plugin file.
 *
 * @package HMWPP\Uninstall
 */

/**
 * Called on plugin uninstall
 */
if (!defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

/* Call config files */
require dirname(__FILE__) . '/index.php';

//Uninstall the temporary logins on plguin uninstall
HMWPP_Classes_ObjController::getClass('HMWPP_Classes_Tools');
if(HMWPP_Classes_Tools::getOption('hmwp_templogin_delete_uninstal')){
    HMWPP_Classes_ObjController::getClass('HMWPP_Models_Templogin')->deleteTempLogins();
}
if(HMWPP_Classes_Tools::getOption('hmwp_2falogin_delete_uninstal')){
    HMWPP_Classes_ObjController::getClass('HMWPP_Models_Twofactor')->deleteTwoFactorLogins();
}