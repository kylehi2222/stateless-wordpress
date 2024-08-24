<?php namespace RealTimeAutoFindReplacePro\actions;

/**
 * Class: Register custom menu
 *
 * @package Action
 * @since 1.0.0
 * @author M.Tuhin <info@codesolz.net>
 */

if (!defined('CS_BFRP_VERSION')) {
    die();
}

use RealTimeAutoFindReplacePro\functions\ActionHandler;
use RealTimeAutoFindReplacePro\functions\FilterHandler;
use RealTimeAutoFindReplacePro\functions\MaskingRule;
use RealTimeAutoFindReplacePro\functions\RenderRTContent;

class BFRP_Hooks
{

    public function __construct()
    {

        /*** update url options */
        add_filter('bfrp_url_types', array($this, 'getAllUrlOptions'), 15);

        /*** update table list options */
        add_filter('bfrp_select_tables', array($this, 'getAllProTblsList'), 15);

        /*** custom tables */
        add_filter('bfrp_custom_tables', array($this, 'bfrpCustomProTbls'), 10, 2);

        /*** url replacer */
        add_filter('bfrp_url_replacer', array($this, 'bfrpCustomUrlReplacer'), 10, 2);

        /*** format find whole word */
        add_filter('bfrp_format_find_whole_word', array($this, 'bfrpFormatFindWholeWord'), 10, 3);

        /*** activate pro settings fields */
        add_filter('bfrp_replacedb_settings_fields', array($this, 'bfrpActivateProFields'), 10, 2);

        /*** activate pro fields */
        add_filter('bfrp_masking_rule_options', array($this, 'bfrpMaskingRules'), 10);
        add_filter('bfrp_masking_location_options', array($this, 'bfrpMaskingLocation'), 10);

        /*** apply advance rules */
        add_filter('bfrp_advance_regex_mask', array($this, 'bfrpAddAdvanceRegxMask'), 10, 3);

        /*** apply bypass rule for real-time rendering*/
        add_filter('bfrp_add_bypass_rule', array($this, 'bfrpAddBypassRule'), 10, 3);
        add_filter('bfrp_remove_bypass_rule', array($this, 'bfrpRemoveBypassRule'), 10, 3);
        add_filter('bfrp_masking_settings_fields', array($this, 'bfrpActivateProFields'), 10, 2);
        add_filter('bfrp_save_masking_rule', array($this, 'bfrpSaveMaskingRules'), 10, 2);
        add_filter('bfrp_masking_plain_filter', array($this, 'bfrpMaskingPlainFilter'), 10, 3);
        add_filter('bfrp_footer_add_new_rule_masking', array($this, 'bfrpFooterAddNewRuleMasking'), 10);

        /*** Store replaced item*/
        add_action('bfar_save_item_history', array($this, 'bfrpSaveItemHistory'));

        /*** Skip pages*/
        add_filter('bfrp_skip_pages', array($this, 'bfrpSkipPages'), 10, 2);
        add_filter('bfrp_active_skip_pages', array($this, 'bfrpActiveSkipPages'), 10, 2);
        add_filter('bfrp_skip_pages_desc_tip', array($this, 'bfrpSkipPagesDesTip'), 10, 2);
        add_filter('bfrp_skip_pages_title', array($this, 'bfrpSkipPagesTitle'), 10, 2);

        /*** Skip posts*/
        add_filter('bfrp_skip_posts', array($this, 'bfrpSkipPosts'), 10, 2);
        add_filter('bfrp_active_skip_posts', array($this, 'bfrpActiveSkipPosts'), 10, 2);
        add_filter('bfrp_skip_posts_desc_tip', array($this, 'bfrpSkipPostsDesTip'), 10, 2);
        add_filter('bfrp_skip_posts_title', array($this, 'bfrpSkipPostsTitle'), 10, 2);

        /*** Filter get sql rules*/
        add_filter('bfrp_get_rules_sql', array($this, 'bfrpGetRulesSql'));

        /*** Filter all tables rules list */
        add_filter('bfrp_all_masking_rules_tbl_rows', array($this, 'bfrpAllMaskingRulesTblRows'));
        add_action('bfrp_column_skip_pages', array($this, 'bfrpTblColumnSkipPages'));
        add_action('bfrp_column_skip_posts', array($this, 'bfrpTblColumnSkipPosts'));
        add_filter('bfrp_column_type_text', array($this, 'bfrpColumnTypeText'));
        add_filter('bfrp_where_to_replace', array($this, 'bfrpColumnWhereToReplace'));

        /*** Filter shortcodes */
        add_filter('the_content', array($this, 'bfrpShortcodeReplacer'));

        /*** Filter add new fields for shortcodes type*/
        add_filter('bfrp_filterSnAnrFields', array($this, 'bfrpAddNewRuleScFields'), 10, 3);

        /*** Filter comments*/
        add_filter('preprocess_comment', array($this, 'bfrpFilterComments'));
        add_filter('comment_text', array($this, 'bfrpFilterOldComments'));

        /*** Filter new post / auto post*/
        add_filter('wp_insert_post_data', array($this, 'bfrpFilterNewPosts'), 10, 2);

        /*** Filter new new rule before insert*/
        add_filter('bfrp_before_insert_new_rule', array($this, 'bfrpFilterNewRule'), 10, 2);

        /*** Filter final report of dry run*/
        add_filter('bfrp_dryrun_final_report', array($this, 'bfrpFilterDryRunReport'), 10);

        /*** extra table nav common*/
        add_action('rtafar_allmaskingrules_extra_tablenav', array($this, 'allmaskingrules_extra_tablenav_common'), 10);
        add_action('rtafar_restoreindb_extra_tablenav', array($this, 'rtafar_restoreindb_extra_tablenav_common'), 10);

        /** Render Real-time content */
        add_filter('bfrp_render_real_time_rules', array($this, 'bfrpRenderRealTimeContent'), 10, 2);

        /** filter content */
        add_filter('bfrp_regex_custom_mask', array($this, 'bfrpRegexCustomMask'), 10, 2);
        add_filter('bfrp_multi_byte_mask', array($this, 'bfrpMultiByteMask'), 10, 2);
    }

    /**
     * Get url types
     *
     * @param [type] $args
     * @return void
     */
    public function getAllUrlOptions($args)
    {
        return FilterHandler::getUrlsOptionsFromPostTbl($args, 'selectOptions');
    }

    /**
     * Get all pro table list
     *
     * @param [type] $args
     * @return void
     */
    public function getAllProTblsList($args)
    {
        return FilterHandler::getAllProTblsList($args);

    }

    /**
     * custom url replacer
     *
     * @return void
     */
    public function bfrpCustomUrlReplacer($settings, $inWhichUrl)
    {
        return ActionHandler::bfrpCustomUrlReplacer($settings, $inWhichUrl);
    }

    /**
     * Pro Tables Query Handler
     *
     * @param [type] $settings
     * @param [type] $inWhichUrl
     * @return void
     */
    public function bfrpCustomProTbls($settings, $tables)
    {
        return ActionHandler::bfrpCustomProTbls($settings, $tables);
    }

    /**
     * Format whole word
     *
     * @param [type] $settings
     * @param [type] $find
     * @return void
     */
    public function bfrpFormatFindWholeWord($settings, $isCaseInsensitive, $find)
    {
        return FilterHandler::bfrpFormatFindWholeWord($settings, $isCaseInsensitive, $find);
    }

    /**
     * Masking Rules
     *
     * @param [type] $fields
     * @return void
     */
    public function bfrpMaskingRules($fields)
    {
        return MaskingRule::bfrpMaskingRules($fields);
    }

    /**
     * Masking Rules Location
     *
     * @param [type] $fields
     * @return void
     */
    public function bfrpMaskingLocation($fields)
    {
        return MaskingRule::bfrpMaskingRules($fields);
    }

    /**
     * Add advance masking rules
     *
     * @param [type] $fields
     * @return void
     */
    public function bfrpAddAdvanceRegxMask($find, $replace, $buffer)
    {
        return MaskingRule::bfrpAddAdvanceRegxMask($find, $replace, $buffer);
    }

    /**
     * Apply Bypass Rules
     *
     * @param [type]  $item
     * @param [type]  $buffer
     * @param boolean $find
     * @return void
     */
    public function bfrpAddBypassRule($item, $buffer, $find = false)
    {
        return MaskingRule::applyBypassRule($item, $buffer, $find);
    }

    /**
     * Remove Bypass Rules
     *
     * @param [type]  $item
     * @param [type]  $buffer
     * @param boolean $find
     * @return void
     */
    public function bfrpRemoveBypassRule($item, $buffer, $find = false)
    {
        return MaskingRule::removeBypassRule($item, $buffer, $find);
    }

    /**
     * Activate Pro Fields
     *
     * @param [type] $fields
     * @return void
     */
    public function bfrpActivateProFields($fields, $options)
    {
        return FilterHandler::bfrpActivateProFields($fields, $options);
    }

    /**
     * Save Bypass Rules
     *
     * @param [type] $item_id
     * @param [type] $user_query
     * @return void
     */
    public function bfrpSaveMaskingRules($item_id, $user_query)
    {
        return MaskingRule::bfrpSaveMaskingRules($item_id, $user_query);
    }

    /**
     * Masking plain text filter
     *
     * @param [type] $item
     * @param [type] $find
     * @param [type] $buffer
     * @return void
     */
    public function bfrpMaskingPlainFilter($item, $find, $buffer)
    {
        return MaskingRule::bfrpMaskingPlainFilter($item, $find, $buffer);
    }

    /**
     * Masking page footer hook
     *
     * @return void
     */
    public function bfrpFooterAddNewRuleMasking()
    {
        return MaskingRule::bfrpFooterAddNewRuleMasking();
    }

    /**
     * Save dry run item to temp tbl
     *
     * @param [type] $tbl
     * @param [type] $reportRow
     * @return void
     */
    public function bfrpSaveItemHistory($search_report)
    {
        return ActionHandler::bfrpSaveItemHistory($search_report);
    }

    /**
     * Get skip pages
     *
     * @param boolean $pages
     * @return void
     */
    public function bfrpSkipPages($pages = false, $option = '')
    {
        return FilterHandler::bfrpSkipPages($pages, $option);
    }

    /**
     * Active skip pages
     *
     * @param [type] $page_ids
     * @return void
     */
    public function bfrpActiveSkipPages($page_ids, $option)
    {
        return FilterHandler::bfrpActiveSkipPages($page_ids, $option);
    }

    /**
     * Filter skip post des tip text
     *
     * @param [type] $option
     * @return void
     */
    public function bfrpSkipPagesDesTip($text, $option)
    {
        return FilterHandler::bfrpSkipPagesDesTip($text, $option);
    }

    /**
     * Filter skip post title text
     *
     * @param [type] $option
     * @return void
     */
    public function bfrpSkipPagesTitle($text, $option)
    {
        return FilterHandler::bfrpSkipPagesTitle($text, $option);
    }

    /**
     * Get skip posts
     *
     * @param boolean $posts
     * @return void
     */
    public function bfrpSkipPosts($posts = false, $option = '')
    {
        return FilterHandler::bfrpSkipPosts($posts, $option);
    }

    /**
     * Active skip posts
     *
     * @param [type] $post_ids
     * @return void
     */
    public function bfrpActiveSkipPosts($post_ids, $option)
    {
        return FilterHandler::bfrpActiveSkipPosts($post_ids, $option);
    }

    /**
     * Filter skip post des tip text
     *
     * @param [type] $option
     * @return void
     */
    public function bfrpSkipPostsDesTip($text, $option)
    {
        return FilterHandler::bfrpSkipPostsDesTip($text, $option);
    }

    /**
     * Filter skip post title text
     *
     * @param [type] $option
     * @return void
     */
    public function bfrpSkipPostsTitle($text, $option)
    {
        return FilterHandler::bfrpSkipPostsTitle($text, $option);
    }

    /**
     * Get filtered SQL
     *
     * @return void
     */
    public function bfrpGetRulesSql($args)
    {
        return FilterHandler::bfrpGetRulesSql($args);
    }

    /**
     * All rules tables rows
     *
     * @param [type] $args
     * @return void
     */
    public function bfrpAllMaskingRulesTblRows($args)
    {
        return MaskingRule::bfrpAllMaskingRulesTblRows($args);
    }

    /**
     * column skip pages | echo the column value
     *
     * @param [type] $args
     * @return void
     */
    public function bfrpTblColumnSkipPages($args)
    {
        echo MaskingRule::bfrpTblColumnSkipPages($args);
    }

    /**
     * column skip posts | echo the column value
     *
     * @param [type] $args
     * @return void
     */
    public function bfrpTblColumnSkipPosts($args)
    {
        echo MaskingRule::bfrpTblColumnSkipPosts($args);
    }

    /**
     * Column type text - data table
     *
     * @param [type] $content
     * @return void
     */
    public function bfrpColumnTypeText($item)
    {
        return MaskingRule::bfrpColumnTypeText($item);
    }

    /**
     * Column type text - data table
     *
     * @param [type] $content
     * @return void
     */
    public function bfrpColumnWhereToReplace($item)
    {
        return MaskingRule::bfrpColumnWhereToReplace($item);
    }

    /**
     * Filter add new rule fields
     *
     * @param [type] $global_class
     * @param [type] $options
     * @param [type] $ruleType
     * @return void
     */
    public function bfrpAddNewRuleScFields($global_class, $options, $ruleType)
    {
        return FilterHandler::bfrpAddNewRuleScFields($global_class, $options, $ruleType);
    }

    /**
     * Masking on shortcodes
     *
     * @param [type] $content
     * @return void
     */
    public function bfrpShortcodeReplacer($content)
    {
        return FilterHandler::bfrpShortcodeReplacer($content);
    }

    /**
     * Filter comments before insert into database
     *
     * @param [type] $comment_data
     * @return void
     */
    public function bfrpFilterComments($comment_data)
    {
        return FilterHandler::bfrpFilterComments($comment_data);
    }

    /**
     * Add masking on old comments
     *
     * @param [type] $comment_text
     * @return void
     */
    public function bfrpFilterOldComments($comment_text)
    {
        return FilterHandler::bfrpFilterOldComments($comment_text);
    }

    /**
     * Add Filter before inserting into database
     *
     * @param [type] $data
     * @param [type] $postarr
     * @return void
     */
    public function bfrpFilterNewPosts($data, $postarr)
    {
        return FilterHandler::bfrpFilterNewPosts($data, $postarr);
    }

    /**
     * Filter new rule before insert
     *
     * @param [type] $user_query
     * @return void
     */
    public function bfrpFilterNewRule($user_query)
    {
        return FilterHandler::bfrpFilterNewRule($user_query);
    }

    /**
     * Filter dry run report
     *
     * @param [type] $dry_run_report
     * @return void
     */
    public function bfrpFilterDryRunReport($dry_run_report)
    {
        return FilterHandler::bfrpFilterDryRunReport($dry_run_report);
    }

    /**
     * Clear rules list - extra table nav
     *
     * @return void
     */
    public function allmaskingrules_extra_tablenav_common()
    {
        return ActionHandler::allmaskingrules_extra_tablenav_common();
    }

    /**
     * Clear Log - extra table nav
     *
     * @return void
     */
    public function rtafar_restoreindb_extra_tablenav_common()
    {
        return ActionHandler::rtafar_restoreindb_extra_tablenav_common();
    }

    /**
     * Render Real-time content
     *
     * @return void
     */
    public function bfrpRenderRealTimeContent($rule, $buffer)
    {
        return RenderRTContent::render_content($rule, $buffer);
    }

    /**
     * Regex Custom Masking
     *
     * @return void
     */
    public function bfrpRegexCustomMask($rule, $buffer)
    {
        return MaskingRule::bfrpRegexCustomMask($rule, $buffer);
    }

    /**
     * Multi Byte Masking
     *
     * @param [type] $rule
     * @param [type] $buffer
     * @return string
     */
    public function bfrpMultiByteMask($rule, $buffer)
    {
        return MaskingRule::bfrpMultiByteMask($rule, $buffer);
    }

}
