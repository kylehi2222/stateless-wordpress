<?php namespace RealTimeAutoFindReplacePro\functions\advScreenOptions;

/**
 * Class: Screen Options - Restore in DB
 *
 * @package Functions
 * @since 1.2.2
 * @author M.Tuhin <info@codesolz.net>
 */

if ( ! defined( 'CS_BFRP_VERSION' ) ) {
    die();
}

use RealTimeAutoFindReplace\lib\Util;

class LicensePage{

    private static $dblogs_per_page_optn_id = 'bfrp_restore_list_per_page';

    function  __construct(){
        //statement
    }

    /**
     * Add screen option
     *
     * @return void
     */
    public static function bfrp_license_screen_options(){
        $screen = \get_current_screen();
        if( self::help_tabs() ){
            foreach ( self::help_tabs() as $tab ) {
                $tab = (object) $tab;
                $screen->add_help_tab(array(
                    'id'    => $tab->id,
                    'title' => $tab->title,
                    'content'   => $tab->content,
                    'callback' => $tab->callback,
                    'priority' => $tab->priority
                ));
            }
        }
        $screen->set_help_sidebar( self::license_help_sidebar_content());
    }


    /**
     * Help tabs
     *
     * @return void
     */
    public static function help_tabs(){
        return array(
            array(
                'id'    => 'overview',
                'title' => __( 'Overview', 'better-find-and-replace-pro' ),
                'content'   => '',
                'callback' => array( __class__, 'license_overview' ),
                'priority' => 1
            ),
            array(
                'id'    => 'screen_content',
                'title' => __( 'How to activate pro?', 'better-find-and-replace-pro' ),
                'content'   => '',
                'callback' => array( __class__, 'license_screen_content' ),
                'priority' => 1
            ),
        );
    }

    /**
     * Overview
     *
     * @return void
     */
    public static function license_overview(){
        echo \sprintf(
            __( '%s This screen provides option to verify your license and automatic update of your plugin. If you activate your license you will get regular update of the pro version like other regular plugin from your plugin\'s section. %s', 'better-find-and-replace-pro' ),
            '<p>',
            '</p>'
        );
    }

    /**
     * Screen content tab
     *
     * @return void
     */
    public static function license_screen_content(){
        ob_start();
        ?>
            <p>
                <?php _e( 'You can activate pro version by following steps:', 'better-find-and-replace-pro' ); ?>
            </p>
            <ul>
                <li>
                    <?php echo sprintf( __( '%s Email %s : Enter your email address what you have used during purchase pro package.', 'better-find-and-replace-pro' ), '<strong>', '</strong>' ); ?>
                </li>
                <li>
                    <?php echo sprintf( __( '%s Password %s : Enter your password what you have received during purchase. Your temporary password has been sent to your purchase confirmation email. If you lost it, just go to the our website %swordpressplugins.codesolz.net/login%s and reset your password.', 'better-find-and-replace-pro' ), '<strong>', '</strong>', '<a href="https://wordpressplugins.codesolz.net/login" target="_blank">', '</a>' ); ?>
                </li>
                <li>
                    <?php echo sprintf( __( '%s License Key %s : Enter your license key. You can find your license key in the "My license Keys" menu in our %slicense server%s.', 'better-find-and-replace-pro' ), '<strong>', '</strong>', '<a href="https://wordpressplugins.codesolz.net/dashboard/api" target="_blank">', '</a>' ); ?>
                </li>
            </ul>
            <p>
                <?php echo sprintf( __( '%s Tutorial %s : To read more about the steps,  %scheck license verify documentation%s from our website', 'better-find-and-replace-pro' ), '<strong>', '</strong>', '<a href="https://docs.codesolz.net/better-find-and-replace/how-to-upgrade-to-pro/" target="_blank">', '</a>' ); ?>
            </p>
            <?php
        $html = ob_get_clean();
        
        echo $html;
    }
    

    /**
     * Help Sidebar Content
     *
     * @return void
     */
    public static function license_help_sidebar_content(){
        ob_start();
        ?>
            <p><strong><?php _e( 'For more information: ', 'better-find-and-replace-pro' ); ?></strong></p>
            <p>
                <?php _e( 'Looking for features details? Check plugin\'s ', 'better-find-and-replace-pro' ); ?>
                <a href="https://docs.codesolz.net/better-find-and-replace/" target="_blank"><?php _e( 'Documentation', 'better-find-and-replace-pro' ); ?></a></p>
            <p><a href="https://codesolz.net/our-products/wordpress-plugin/real-time-auto-find-and-replace/" target="_blank"><?php _e( 'Support', 'better-find-and-replace-pro' ); ?></a></p>					
        <?php
        $html = ob_get_clean();

        return $html;
    }


}