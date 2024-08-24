<?php

class PeepSoReport
{
    const TABLE = 'peepso_report';

    public function __construct() {}

    public static function get_table_name()
    {
        global $wpdb;
        return $wpdb->prefix . self::TABLE;
    }


    /**
     * Create new report
     * @param int $post_id The ID of the item being reported
     * @param int $user_id The User Id of the person reporting the item
     * @param int $module_id The Module (Activity, Events, etc.) of the item being reported
     * @return Boolean TRUE on success; FALSE on failure
     */
    public function add_report($external_id, $user_id, $module_id, $reason, $desc)
    {
        $type = $this->module_id_to_type($module_id);

        $data = array(
            'rep_user_id' => $user_id,
            'rep_external_id' => $external_id,
        );

        if (NULL !== $module_id) {
            $data['rep_module_id'] = intval($module_id);
        }

        if (NULL !== $reason) {
            $data['rep_reason'] = $reason;
        }

        if (NULL !== $desc) {
            $data['rep_desc'] = $desc;
        }

        global $wpdb;

        if($wpdb->insert($wpdb->prefix . self::TABLE, $data)) {
            $data['rep_id'] = $wpdb->insert_id;

            if('activity'===$type) {
                update_post_meta($external_id, 'peepso_reported', 1);
                do_action('peepso_action_report_create', $data);
            }

            if('profile'===$type) {
                update_user_meta($external_id, 'peepso_reported', 1);
            }



            return TRUE;
        }

        return FALSE;
    }

    /**
     * Check whether content is already reported by the user and has not been ignored by admin
     * @param int $external_id The ID of the item being reported
     * @param int $user_id The User Id of the person reporting the item
     * @param int $module_id The Module (Activity, Events, etc.) of the item being reported
     * @return bool Whether the post is already reported
     */
    public function is_reported_by_user($external_id, $user_id, $module_id)
    {
        global $wpdb;

        $sql = "SELECT COUNT(`rep_id`) AS `count` " .
            " FROM `" . $this->get_table_name() . "` " .
            " WHERE `rep_external_id` = %d AND `rep_user_id` = %d AND `rep_module_id` = %d AND rep_status=0 ";

        $total_items = $wpdb->get_var($wpdb->prepare($sql, $external_id, $user_id, $module_id));

        if ($total_items > 0)
            return (TRUE);

        return (FALSE);
    }

    /*
     * Retrives a list of reported by
     * @param int $external_id The external_id
     * $param int $module_id The module_id
     * @return array The collection of items queried
     */
    public function get_reports($external_id, $module_id, $status = FALSE)
    {
        global $wpdb;

        $sql = " SELECT * " .
            " FROM `" . $this->get_table_name() . "` " .
            " WHERE `rep_external_id`={$external_id} AND `rep_module_id`={$module_id} ";

        if(FALSE !== $status && is_int($status)) {
            $sql .= " AND `rep_status` ={$status}";
        }
        $aItems = $wpdb->get_results($sql, ARRAY_A);
        return ($aItems);
    }

    /**
     * Automation: set a posts status to pending.
     * This only happens automatically, there is no manual trigger.
     * @param  int $rep_id The report ID.
     * @return bool
     */
    public function post_unpublish($rep_id)
    {
        global $wpdb;

        $query = $wpdb->prepare('SELECT * FROM `' . self::get_table_name() . '` WHERE rep_id = %d', $rep_id);
        $report = $wpdb->get_row($query);

        if (!is_null($report) && wp_update_post(array('ID' => $report->rep_external_id, 'post_status' => 'pending'))) {
            return TRUE;
        }

        return FALSE;
    }

    public function hide_reports($external_id, $module_id) {

        if(PeepSo::is_admin()) {

            $type = $this->module_id_to_type($module_id);

            if($external_id >0 && $module_id >=0) {
                global $wpdb;

                $wpdb->update(self::get_table_name(),
                    ['rep_status' => 1,],
                    ['rep_external_id' => $external_id, 'rep_module_id' => $module_id]
                );

                if('activity' == $type) {
                    delete_post_meta($external_id, 'peepso_reported');
                    $wpdb->update($wpdb->posts, ['post_status' => 'publish'], ['ID' => $external_id]);
                }

                if('profile'==  $type) {
                    delete_user_meta($external_id, 'peepso_reported');
                }

                return TRUE;
            }


        }

        return FALSE;
    }

    private function module_id_to_type($module_id) {
        
        $module_id = intval($module_id);
        
        switch($module_id) {
            case 0:
                return 'profile';
                break;
            default:
                return 'activity';
        }
    }
    public function post_check_reported_comments($post_id, $module_id) {

        // Go over all comments and replies attached to this post, including comments on photos in multi-photo
    }

    public function admin_emails() {

        $all_emails = [];
        $result = [];

        // WP Users - Administrators
        $args = array(
            'role' => 'administrator',
        );

        $user_query = new WP_User_Query($args);
        $users = $user_query->get_results();

        if (count($users) > 0) {
            foreach ($users as $user) {
                $all_emails[] = $user->data->user_email;
            }
        }

        // WP Config admin email
        $all_emails[] = get_option( 'admin_email' );

        // Additional emails from PeepSo config
        $peepso_emails = str_replace("\r", '', PeepSo::get_option('reporting_notify_email_list'));
        $peepso_emails = explode("\n", $peepso_emails);
        if (count($peepso_emails)) {
            foreach ($peepso_emails as $key => $email) {
                $all_emails[] = $email;
            }
        }

        // DRY
        $all_emails= array_unique($all_emails);

        // Load additional data
        if(count($all_emails)) {
            foreach ($all_emails as $email) {
                $name = '';
                $id = 0;

                $user = get_user_by('email', $email);
                if($user != FALSE){
                    $name = PeepSoUser::get_instance($user->ID)->get_fullname();
                    $id = $user->ID;
                }

                $result[] = [
                    'email' => $email,
                    'name' => $name,
                    'id' => $id
                ];
            }
        }
        
        return $result;
    }
}


// EOF