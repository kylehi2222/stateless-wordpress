<?php
if(class_exists('PeepSoMaintenanceFactory')) {
    class PeepSo3_Maintenance_Reports extends PeepSoMaintenanceFactory {

        public static function debugGenerateRendomReports() {

            return;

            global $wpdb;

            for($i = 0; $i < 100; $i++) {
                $external = rand(0,20000);
                $module = rand(0,2);
                $status=rand(0,1);
                $sql = "
                        INSERT INTO {$wpdb->prefix}peepso_report
                        (rep_user_id, rep_external_id, rep_module_id, rep_status, rep_reason, rep_desc)
                        VALUES
                        (1, $external, $module, $status,'test', 'test')
                        ";
                $wpdb->query($sql);
            }
        }

        public static function updateMeta() {
            global $wpdb;
            $limit = 20;
            $sql = "
                SELECT 
                    pr1.rep_external_id, 
                    pr1.rep_module_id, 
                    COALESCE(SUM(pr1.rep_status = 0), 0) AS total 
                FROM 
                    {$wpdb->prefix}peepso_report AS pr1 
                LEFT JOIN (
                    SELECT 
                        rep_external_id, 
                        rep_module_id 
                    FROM 
                        {$wpdb->prefix}peepso_report 
                    WHERE 
                        rep_status = 0
                ) AS pr2 
                ON 
                    pr1.rep_external_id = pr2.rep_external_id 
                    AND pr1.rep_module_id = pr2.rep_module_id 
                WHERE 
                    (pr1.rep_last_maintenance IS NULL OR pr1.rep_last_maintenance < NOW() - INTERVAL 1 DAY) 
                GROUP BY 
                    pr1.rep_external_id, 
                    pr1.rep_module_id 
                ORDER BY 
                    RAND() LIMIT $limit;

                    ";

            $result = $wpdb->get_results($sql);

            $i=0;
            foreach($result as $key=>$item) {
                $i++;

                if($item->total>0) {
                    $action = 'create';
                } else {
                    $action = 'delete';
                }

                if(0==$item->rep_module_id) {

                    if($action=='create') {
                        update_user_meta($item->rep_external_id, 'peepso_reported', 1);
                    } else {
                        delete_user_meta($item->rep_external_id, 'peepso_reported');
                    }

                    $meta = intval(get_user_meta($item->rep_external_id, 'peepso_reported', TRUE));
                    $meta = intval($meta > 0);
                    $type='profile';
                } else {
                    if($action=='create') {
                        update_post_meta($item->rep_external_id, 'peepso_reported', 1);
                    } else {
                        delete_post_meta($item->rep_external_id, 'peepso_reported');
                    }

                    $meta = intval(get_post_meta($item->rep_external_id, 'peepso_reported', TRUE));
                    $meta = intval($meta > 0);
                    $type='activity';
                }

                // update last maintenance
                $wpdb->query("UPDATE {$wpdb->prefix}peepso_report SET rep_last_maintenance=NOW() WHERE rep_external_id={$item->rep_external_id} AND rep_module_id={$item->rep_module_id}");

                $i++;
            }

            return $i;
        }
    }
}