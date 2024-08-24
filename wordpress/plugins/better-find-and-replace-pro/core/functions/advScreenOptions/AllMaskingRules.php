<?php namespace RealTimeAutoFindReplacePro\functions\advScreenOptions;

/**
 * Class: Screen Options - All masking rules
 *
 * @package Functions
 * @since 1.2.2
 * @author M.Tuhin <info@codesolz.net>
 */

if ( ! defined( 'CS_BFRP_VERSION' ) ) {
    die();
}

use RealTimeAutoFindReplace\lib\Util;

class AllMaskingRules{

    /**
     * all masking rules
     *
     * @return void
     */
    public static function bfar_amr_available_actions_content(){
        \ob_start();
        ?>
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
                    <?php echo sprintf( __( '%s Export Rules %s will exports all the rules items from here. You can export by selecting  specific items or all items at a time. %s ( pro extend version ) %s', 'better-find-and-replace-pro' ), '<strong>', '</strong>', '<em>', '</em>' ); ?>
                </li>
                <li>
                    <?php echo sprintf( __( '%s Import Rules %s will allow you to import rules. %s ( pro extend version ) %s', 'better-find-and-replace-pro' ), '<strong>', '</strong>', '<em>', '</em>' ); ?>
                </li>
            </ul>
        <?php
        $html = ob_get_clean();
        
        echo $html;
    }


}