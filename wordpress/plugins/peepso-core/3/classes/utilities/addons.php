<?php

class PeepSo3_Helper_Addons {

    public static function get_license() {
        $license = (isset($_REQUEST['bundle_license'])) ? $_REQUEST['bundle_license'] : PeepSo::get_option('bundle_license');

        if (!$license || !is_string($license) || strlen($license) < 10) return FALSE;

        return strlen($license) ? $license : FALSE;
    }

    public static function license_to_data($license=NULL, $cache = TRUE) {

        if(NULL===$license) {
            $license=self::get_license();
        }

        if(!$license) return FALSE;

        $pscc = /** checked for NULL**/ (new PeepSoCom_Connect(['license_to_id' => $license],$cache, NULL, TRUE))->get();

        if(NULL === $pscc) {
            return FALSE;
        }

        $data = (array) $pscc;

        if(is_array($data) && isset($data['bundle_id']) && isset($data['bundle_name'])) {
            return $data;
        }

        return FALSE;
    }

    public static function license_to_id($license, $cache = TRUE) {
        $data = self::license_to_data($license, $cache);

        if(FALSE !== $data) {
            return $data['bundle_id'];
        }

        return FALSE;
    }

    public static function license_to_name($license=NULL, $cache = TRUE) {
        if(NULL === $license) {
            $license = self::get_license();
        }

        $data = self::license_to_data($license, $cache);
        if(FALSE !== $data) {
            return $data['bundle_name'];
        }

        return FALSE;
    }

    public static function license_is_eligible_upgrade() {

        // License is free bundle
        if (PeepSo3_Helper_Addons::license_is_free_bundle()) {
            return TRUE;
        }

        // No license (fresh install / PeepSo Foundation only
        $license = PeepSo3_Helper_Addons::get_license();
        if (!strlen($license)) {
            return TRUE;
        }

        // License Provided but PeepSo Foundation only
        if (strlen($license)) {
            $current_item_id = PeepSo3_Helper_Addons::license_to_id($license);
            if (!$current_item_id) {
                return TRUE;
            }
        }

        $display_warning = PeepSo3_Mayfly::get('peepso_has_displayed_license_warning');
        if (PeepSo3_Utilities_String::maybe_strlen($display_warning)) {
            return TRUE;
        }
        return FALSE;
    }

    public static function license_is_free_bundle($cache = TRUE) {
        return ('bundle-free' == PeepSo3_Helper_Addons::license_to_name(NULL, $cache));
    }

    public static function license_is_ultimate_bundle($cache = TRUE) {
        return ('bundle-ultimate-yearly' == PeepSo3_Helper_Addons::license_to_name(NULL, $cache) || 'bundle-ultimate-five-years' == PeepSo3_Helper_Addons::license_to_name(NULL, $cache));
    }

    public static function license_is_ultimate_five_years($cache = TRUE) {
        return ('bundle-ultimate-five-years' == PeepSo3_Helper_Addons::license_to_name(NULL, $cache));
    }

    public static function license_is_basic_bundle($cache = TRUE) {
        return ('bundle-basic' == PeepSo3_Helper_Addons::license_to_name(NULL, $cache));
    }

    public static function license_is_monthly_bundle($cache = TRUE) {
        return ('bundle-monthly' == PeepSo3_Helper_Addons::license_to_name(NULL, $cache) || 'bundle-ultimate-monthly' == PeepSo3_Helper_Addons::license_to_name(NULL, $cache));
    }

    public static function get_pfb_disabled_text() {
        $pscc =  /** checked for NULL**/ (new PeepSoCom_Connect('upsell/peepso-free-bundle/admin-disabled-message.html'))->get();

        if(NULL === $pscc) {
            return '';
        }

        return $pscc;
    }

    public static function get_upsell() {

        $license = self::get_license();
        $discount = 0;
        $expired = FALSE;
        $expired_since = -1;
        $args = ['location'=>'admin_top'];

        // Override start date with these
        $ps_time = self::get_peepso_age();

        $delay_1 = 24;      // hours
        $delay_2 = 24*7*1;  // 1 week
        $delay_3 = 24*7*2;  // 2 weeks
        $delay_4 = 24*7*3;  // 3 weeks
        $delay_5 = 24*7*4;  // 4 weeks

        // Reset cache if license is being saved
        $data = PeepSo3_Helper_Addons::license_to_data(NULL, !isset($_REQUEST['bundle_license']));

        if(is_array($data) && isset($data['expiration_raw']) && $data['expiration_raw'] > 0 ) {
            $expired_since = intval((strtotime(current_time('Y-m-d H:i:s')) - strtotime($data['expiration'])) / 3600);

            if ($expired_since > 0) {
                $expired = TRUE;
            }
        }



        // No license (fresh install / PeepSo Foundation only)
        // 24 hours delay on discounts
        // Very important, prevents new clients from seeing upsell discounts
        if (!strlen($license)) {
            if($ps_time) {                                                                      if(isset($_GET['dbg'])) echo 'Fresh';
                if ($ps_time > $delay_1) { $discount = 5; }
            }
        }

        // Any expired license
        elseif($expired) {
            $discount = 15;                                                                     if(isset($_GET['dbg'])) echo 'Expired';
        }

        // Monthly Basic and PFB always 50%
        elseif(
            PeepSo3_Helper_Addons::license_is_basic_bundle()
            || PeepSo3_Helper_Addons::license_is_monthly_bundle()
            || PeepSo3_Helper_Addons::license_is_free_bundle()
        ) {
            $discount = 5;                                                                      if(isset($_GET['dbg'])) echo 'M B F';
        }

        // Ultimate and any other scenario - hide PeepSo
        else {
            $discount = 0;                                                                       if(isset($_GET['dbg'])) echo 'Ultimate';
            $args['show_peepso'] = 0;
            if(PeepSo3_Helper_Addons::license_is_ultimate_five_years()) {
                $args['show_lifetime'] = 1;
            }
        }

        $args['expired_since'] = ($expired && $expired_since > 0) ? $expired_since : 0;

        if (!strlen($license)) {
            $args['show_free'] = 1;
        }

        if(class_exists('ALSP')) {
            $args['show_alsp'] = 0;
        }

        if($discount) {
            $args['discount'] = $discount;
        }

        $args['installer_url'] = admin_url('admin.php?page=peepso-installer&action=peepso-free');
        $url = add_query_arg($args, 'upsell/upsell.php');

        $pscc =  /** checked for NULL**/ (new PeepSoCom_Connect($url,TRUE,NULL, FALSE,TRUE))->get();

        if(NULL === $pscc) {
            return '';
        }

        if(is_string($pscc)) {
            if(stristr($pscc, 'upsell_is_empty')) {
                return '';
            }
            return $pscc;
        }

        return '';

    }

    public static function get_addons() {
        global $wp_version;
        $has_new = FALSE;

        $args = [
            'ver_bundle'    => PeepSo3_Helper_Addons::license_to_name(),
            'ver_wp'        => $wp_version,
            'ver_php'       => PHP_VERSION,
            'ver_locale'    => get_locale(),
            'theme'         => wp_get_theme()->get('Name'),
        ];

        foreach($args as $k=>$v) {
            $args[$k] = urlencode($v);
        }

        $url = add_query_arg($args, $url = "?product_bundles_list");

        $pscc =  /** checked for NULL**/ (new PeepSoCom_Connect($url))->get();

        if (NULL === $pscc) {
            return FALSE;
        } else {
            $result = json_decode($pscc);

            PeepSo3_Mayfly::set('bundle_info', $result, 24*3600);

            foreach($result as $item) {
                if(isset($item->new)) {
                    $has_new = $item->id;
                    break;
                }
            }

            if($has_new) {
                PeepSo3_Mayfly_Int::set('installer_has_new', $has_new);
            } else {
                PeepSo3_Mayfly_Int::del('installer_has_new');
            }
        }

        return $result;
    }

    public static function maybe_powered_by_peepso() {

        if(PeepSo::get_option('system_show_peepso_link',0)) {

            $pscc =  /** checked for NULL**/ (new PeepSoCom_Connect('upsell/peepso-free-bundle/powered-by-peepso.php'))->get();

            if(NULL === $pscc) {
                return '';
            }

            if(is_string($pscc)) {
                return $pscc;
            }

            return '';
        }

        if(apply_filters('peepso_free_bundle_should_brand', FALSE) && self::license_is_free_bundle()) {
            $PeepSoConfigSettings = PeepSoConfigSettings::get_instance();
            $PeepSoConfigSettings->set_option('system_show_peepso_link', 1);
            $pscc =  /** checked for NULL**/ (new PeepSoCom_Connect('upsell/peepso-free-bundle/powered-by-peepso.php'))->get();

            if(NULL === $pscc) {
                return '';
            }

            if(is_string($pscc)) {
                return $pscc;
            }

            return '';
        }

        return '';
    }

    public static function maybe_optin_stats() {

        if(PeepSo::get_option('optin_stats',0)) {
            return TRUE;
        }

        if(self::license_is_free_bundle()) {
            PeepSoConfigSettings::get_instance()->set_option('optin_stats', 1);
            return TRUE;
        }

        return PeepSo::get_option('optin_stats',0);
    }

    public static function maybe_installer_has_new() {
        return (PeepSo3_Mayfly_Int::get('installer_has_new') || !self::get_license());
    }


    /**
     * Retrieve the age of PeepSo installation in HOURS
     * @return int amount of HOURS passed since PeepSo installation
     */
    public static function get_peepso_age() {
        $start_date = isset($_GET['peepso_start_date']) ? $_GET['peepso_start_date'] : get_option('peepso_install_date');

        $ps_start= strtotime($start_date);
        $ps_time = 0;
        if($ps_start) {
            $ps_time = intval((strtotime(current_time('Y-m-d H:i:s')) - $ps_start) / 3600);
        }

        return $ps_time;
    }
}

add_action('admin_notices', function() {

    if(!PeepSo::is_admin()) { return; }

    $mayfly = 'user_'.get_current_user_id().'upsell_dismiss';

    if(isset($_GET['peepso_upsell_dismiss_reset'])) {
        PeepSo3_Mayfly::del($mayfly);
        PeepSo3_Utility_Redirect::_(remove_query_arg('peepso_upsell_dismiss_reset'));
    }

    if(isset($_GET['peepso_upsell_dismiss'])) {
        // Default: dismiss for  7 days
        $ttl = 7*24*3600;

        if(PeepSo3_Helper_Addons::license_is_ultimate_bundle()) {
            // Ultimate: dismiss for 14 days
            $ttl = 14*24*3600;
        }

        PeepSo3_Mayfly::set($mayfly,1,$ttl);
        PeepSo3_Utility_Redirect::_(remove_query_arg('peepso_upsell_dismiss'));
    }

    if(PeepSo3_Mayfly::get($mayfly)) { return; }

    $upsell = PeepSo3_Helper_Addons::get_upsell();

    if(($upsell)) {
        ?>
        <div style="clear:both">
        <a href="<?php echo esc_url(add_query_arg(['peepso_upsell_dismiss'=>1]));?>">Dismiss</a>
        <?php
        echo $upsell;
        echo "</div>";
    }
},0,99);