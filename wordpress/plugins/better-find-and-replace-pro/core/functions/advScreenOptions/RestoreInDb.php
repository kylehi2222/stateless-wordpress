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

class RestoreInDb{

    private static $dblogs_per_page_optn_id = 'bfrp_restore_list_per_page';

    function  __construct(){
        //statement
    }

    /**
     * Add screen option
     *
     * @return void
     */
    public static function bfrp_dblogs_screen_options(){

        \add_screen_option( 'per_page', array( 
            'label' => __( 'Number of items per page : ', 'better-find-and-replace-pro' ), 
            'default' => 10, 
            'option' => self::$dblogs_per_page_optn_id 
        ));
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
        $screen->set_help_sidebar( self::dblogs_help_sidebar_content());
    }

    /**
      * Set dbLogs per page
      *
      * @param [type] $status
      * @param [type] $option
      * @param [type] $value
      * @return void
      */
    public static function bfrp_set_dblogs_per_page( $status, $option, $value ){
        return $value;
    }

    /**
     * Get dbLogs per page item number
     *
     * @return void
     */
    public static function bfrp_get_dblogs_per_page(){
        $current_user_id = Util::bfar_get_current_user_id();
        if( $current_user_id ){
            return \get_user_meta( $current_user_id, self::$dblogs_per_page_optn_id, true );
            return false;
        }

        return false;
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
                'callback' => array( __class__, 'dblogs_overview' ),
                'priority' => 1
            ),
            array(
                'id'    => 'screen_content',
                'title' => __( 'Screen Content', 'better-find-and-replace-pro' ),
                'content'   => '',
                'callback' => array( __class__, 'dblogs_screen_content' ),
                'priority' => 1
            ),
            array(
                'id'    => 'available_actions',
                'title' => __( 'Available Actions', 'better-find-and-replace-pro' ),
                'content'   => '',
                'callback' => array( __class__, 'dblogs_available_actions' ),
                'priority' => 1
            ),
            array(
                'id'    => 'bulk_actions',
                'title' => __( 'Bulk Actions', 'better-find-and-replace-pro' ),
                'content'   => '',
                'callback' => array( __class__, 'dblogs_bulk_actions' ),
                'priority' => 1
            ),
        );
    }

    /**
     * Overview
     *
     * @return void
     */
    public static function dblogs_overview(){
        echo \sprintf(
            __( '%s This screen provides access to all of your replaced items. This is the automatic backup of your replaced items. Please read from our website how and when the automatic backup works. Also you can customize the display of this screen to suit your workflow. %s', 'better-find-and-replace-pro' ),
            '<p>',
            '</p>'
        );
    }

    /**
     * Screen content tab
     *
     * @return void
     */
    public static function dblogs_screen_content(){
        ob_start();
        ?>
            <p>
                <?php _e( 'You can customize the display of this screen’s contents in a number of ways:', 'better-find-and-replace-pro' ); ?>
            </p>
            <ul>
                <li>
                    <?php _e( 'You can decide how many item to list per screen using the Screen Options tab.', 'better-find-and-replace-pro' ); ?>
                </li>
            </ul>
            <?php
        $html = ob_get_clean();
        
        echo $html;
    }
    
    /**
     * available actions
     *
     * @return void
     */
    public static function dblogs_available_actions(){
        ?>
            <p>
                <?php _e( 'Hovering over a row in the item list will display action links that allow you to manage your item. You can perform the following actions:', 'better-find-and-replace-pro' ); ?>
            </p>
            <ul>
                <li>
                    <?php echo sprintf( __( '%s Restore %s will re-insert the row in your database on the specific table. ', 'better-find-and-replace-pro' ), '<strong>', '</strong>' ); ?>
                </li>
            </ul>
            
            <p>
                <?php _e( ' You can perform the following actions by the buttons besides the bulk action: ', 'better-find-and-replace-pro' ); ?>
            </p>
            <ul>
                <li>
                    <?php echo sprintf( __( '%s Clear all %s will delete all the items. This is a permanent action. You can\'t revert.', 'better-find-and-replace-pro' ), '<strong>', '</strong>' ); ?>
                </li>
                <li>
                    <?php echo sprintf( __( '%s Search %s allows you to search item(s) from all the items.', 'better-find-and-replace-pro' ), '<strong>', '</strong>' ); ?>
                </li>
                <li>
                    <?php echo sprintf( __( '%s Pagination %s allows you go on a specific number of page', 'better-find-and-replace-pro' ), '<strong>', '</strong>' ); ?>
                </li>
                <li>
                    <?php echo sprintf( __( '%s Export Type %s requires to be selected when exporting log. You can export single item or all the items. %s ( pro extend version ) %s', 'better-find-and-replace-pro' ), '<strong>', '</strong>', '<em>', '</em>' ); ?>
                </li>
                <li>
                    <?php echo sprintf( __( '%s Export Logs %s will exports all the logs items from here. You can export by selecting  specific items or all items at a time. %s ( pro extend version ) %s', 'better-find-and-replace-pro' ), '<strong>', '</strong>', '<em>', '</em>' ); ?>
                </li>
                <li>
                    <?php echo sprintf( __( '%s Import Logs %s will allow you to import logs. %s ( pro extend version ) %s', 'better-find-and-replace-pro' ), '<strong>', '</strong>', '<em>', '</em>' ); ?>
                </li>
            </ul>
        
        
        <?php
    }

    public static function dblogs_bulk_actions(){
        ?>
            <p>
                <?php _e( 'You can also delete or restore multiple rows to the Database at once. Select the items you want to act on using the checkboxes, then select the action you want to take from the Bulk actions menu and click Apply.', 'better-find-and-replace-pro' ); ?>
            </p>
        
        <?php
    }

    /**
     * Help Sidebar Content
     *
     * @return void
     */
    public static function dblogs_help_sidebar_content(){
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