<?php namespace RealTimeAutoFindReplacePro\actions;

/**
 * Class: WP Hooks
 *
 * @package Action
 * @since 1.2.2
 * @author M.Tuhin <info@codesolz.net>
 */

if ( ! defined( 'CS_BFRP_VERSION' ) ) {
    die();
}

use RealTimeAutoFindReplacePro\functions\advScreenOptions\ScreenOptions;


class BFRP_WP_Hooks {

    function __construct() { 
        add_action( "admin_menu", array( $this, "bfrp_current_screen_options"), 25 );
        add_filter('set-screen-option', array( $this, 'bfrp_set_dblogs_per_page' ), 15, 3);
    }

    /**
     * Set Screen options
     *
     * @return void
     */
    public function bfrp_current_screen_options(){
        global $rtafr_menu;

    //    pre_print($rtafr_menu);

        if( isset( $rtafr_menu['restore_in_db'] ) && !empty($rtafr_menu['restore_in_db'] ) ){
            add_action("load-".$rtafr_menu['restore_in_db'], array( $this, 'bfrp_dblogs_screen_options') );
        }

        if( isset( $rtafr_menu['brafp_license'] ) && !empty($rtafr_menu['brafp_license'] ) ){
            add_action("load-".$rtafr_menu['brafp_license'], array( $this, 'bfrp_license_screen_options') );
        }

        if( isset( $rtafr_menu['all_masking_rules'] ) && !empty($rtafr_menu['all_masking_rules'] ) ){
            add_action( 'bfar_amr_available_actions_content', array( $this, 'bfar_amr_available_actions_content' ) );
        }
    }

    /**
     * Add Screen Options
     *
     * @return void
     */
    public function bfrp_dblogs_screen_options(){
        return ScreenOptions::bfrp_dblogs_screen_options();
    }

    /**
     * Save Screen option
     *
     * @return void
     */
    public function bfrp_set_dblogs_per_page( $status, $option, $value ){
        return ScreenOptions::bfrp_set_dblogs_per_page( $status, $option, $value );
    }

    /**
     * License screen option
     *
     * @return void
     */
    public function bfrp_license_screen_options(){
        return ScreenOptions::bfrp_license_screen_options();
    }

    /**
     * Screen option : all masking rules content
     *
     * @return void
     */
    public function bfar_amr_available_actions_content(){
        return ScreenOptions::bfar_amr_available_actions_content();
    }


}