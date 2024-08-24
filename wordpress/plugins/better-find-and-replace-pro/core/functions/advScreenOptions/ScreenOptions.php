<?php namespace RealTimeAutoFindReplacePro\functions\advScreenOptions;

/**
 * Class: Screen Options
 *
 * @package Functions
 * @since 1.2.2
 * @author M.Tuhin <info@codesolz.net>
 */

if ( ! defined( 'CS_BFRP_VERSION' ) ) {
    die();
}

use RealTimeAutoFindReplace\lib\Util;
use RealTimeAutoFindReplacePro\functions\advScreenOptions\RestoreInDb;
use RealTimeAutoFindReplacePro\functions\advScreenOptions\AllMaskingRules;

class ScreenOptions{


    /**
     * DbLogs - Screen options
     *
     * @return void
     */
    public static function bfrp_dblogs_screen_options(){
        return RestoreInDb::bfrp_dblogs_screen_options();
    }

    /**
     * Save screen option - DBLogs Page
     *
     * @param [type] $status
     * @param [type] $option
     * @param [type] $value
     * @return void
     */
    public static function bfrp_set_dblogs_per_page( $status, $option, $value ){
        return RestoreInDb::bfrp_set_dblogs_per_page( $status, $option, $value );
    }

    /**
     * Get dblogs per page number
     *
     * @return void
     */
    public static function bfrp_get_dblogs_per_page(){
        return RestoreInDb::bfrp_get_dblogs_per_page();
    }

    /**
     * license page : screen options
     *
     * @return void
     */
    public static function bfrp_license_screen_options(){
        return LicensePage::bfrp_license_screen_options();
    }

    /**
     * Action hooks : all masking rules available action contents 
     *
     * @return void
     */
    public static function bfar_amr_available_actions_content(){
        return AllMaskingRules::bfar_amr_available_actions_content();
    }

}