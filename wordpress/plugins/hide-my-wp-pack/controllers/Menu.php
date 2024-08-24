<?php
/**
 * The Menu function
 * Loaded when the user is logged in
 *
 * @file The Menu file
 * @package HMWPP/Menu
 * @since 1.0.0
 */

defined('ABSPATH') || die('Cheatin\' uh?');

class HMWPP_Controllers_Menu extends HMWPP_Classes_FrontController
{

    public function __construct() {

        parent::__construct();

        //On error or when plugin disabled.
        if (defined('HMWPP_DISABLE') && HMWPP_DISABLE ) {  return; }

        if (!HMWPP_Classes_Tools::isHideMyWPGhostInstalled()) {
            add_action( 'admin_notices', function(){
                echo '<div class="notice notice-warning is-dismissible"><p>'. esc_html(_HMWPP_PLUGIN_FULL_NAME_) . ' ' . esc_html__('requires Hide My WP Ghost plugin to be active.','hide-my-wp-pack').'</p></div>';
            } );

            return;
        }

        add_filter('hmwp_menu', array($this->model, 'getMenu'));
        add_filter('hmwp_features', array($this->model, 'getFeatures'));

        if( HMWPP_Classes_Tools::getOption('hmwp_2falogin') ){

            /** @var HMWPP_Controllers_Twofactor $twofactor */
            $twofactor = HMWPP_Classes_ObjController::getClass('HMWPP_Controllers_Twofactor');

            add_action( 'show_user_profile', array( $twofactor, 'hookUserSettings' ),11, 1 );
            add_action( 'edit_user_profile', array( $twofactor, 'hookUserSettings' ),11, 1 );
        }



    }

}
