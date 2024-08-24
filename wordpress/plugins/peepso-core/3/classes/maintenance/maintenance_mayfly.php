<?php
if(class_exists('PeepSoMaintenanceFactory')) {
	class PeepSo3_Maintenance_Mayfly extends PeepSoMaintenanceFactory {
		public static function deleteExpired() {
			return PeepSo3_Mayfly::clr();
		}
	}

    add_action('init', function(){
        $trigger['updates'] = isset($_GET['force-check']) && $_GET['force-check'];
        $trigger['pfb'] = isset($_GET['action']) && 'peepso-free'==$_GET['action'];
        $trigger['license'] = isset($_REQUEST['bundle_license']);

        foreach($trigger as $k => $v) {
            if($v) {
                /** checked for NULL**/ (new PeepSoCom_Connect())->reset_admin_caches($k);
                return;
            }
        }
    });
}

// EOF