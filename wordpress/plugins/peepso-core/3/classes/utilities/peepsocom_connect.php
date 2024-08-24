<?php


class PeepSoCom_Debug {

    public $error = '';

    public function __construct($err_msg='', $title=FALSE) {

        if (!PeepSo::get_option_new('peepsocom_connect_debug')) {
            return (FALSE);
        }

        $err_msg = maybe_serialize($err_msg);
        $message = $err_msg;

        if($title) $message = str_pad(strtoupper($title), 35, ' ', STR_PAD_RIGHT)."".$message;


        $peepso_dir = PeepSo::get_option('site_peepso_dir', WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'peepso', TRUE);
        $file = 'peepsocom_connect';
        error_log ( "\n".$message, 3, $peepso_dir.'/'.$file.'.txt');
        $this->error = $message;
    }


}

class PeepSoCom_Connect {

    private $url_base = 'www.peepso.com';
    private $use_dns_discovery = TRUE;
    private $use_ssl_verify = 1;
    private $use_cache = 1;
    private $cache_ttl = 3600*24;

    private $timeout = 10;
    private $args = [];

    private $request = '';

    private $backtrace = '';

    private $verbose = FALSE;

    private $rate_limit = 500;
    private $offline_threshold = 10;

    private $fallback_message = FALSE;
    private $json = FALSE;

    private $track_failure = TRUE;


    /**
     * @param $url string|array relative URL like 'directory/file.txt' or array or aguments like [var=>val]
     * @param $cache bool whether to use MayFly cache
     * @param $ttl int MayFly cache expiration
     * @param $json bool Whether to return json_decode object
     * @param $fallback_message bool Whether to return fallback text on failure
     * @param $args array additional arguments (not really used)
     *
     * @return NULL on failure && fallback_message=FALSE
     * @return string on success && $json=FALSE, OR fallback text on $fallback_message=TRUE
     * @return object on success $json=TRUE and json_decode is successful
     */
    public function __construct($url='', $cache = TRUE, $ttl = NULL, $json = FALSE,$fallback_message = FALSE, $args=[]) {

        if(is_int($ttl)) {
            $this->cache_ttl = $ttl;
        }

        // Add random amount of seconds to avoiud caches expiring simultaneously
        $this->cache_ttl += rand(60, 120);

        // Get file and line that called this function
        $bt = debug_backtrace();

        // Backtrace
        $this->backtrace = basename($bt[0]['file']).':'.$bt[0]['line'];

        // Clear caches in case of Installer actions
        if(isset($_GET['action']) && 'peepso-free'==$_GET['action']) {
            $cache = FALSE;
        }

        if(defined('PEEPSOCOM_CONNECT_NOCACHE')) {
            $cache = FALSE;
            $this->reset_admin_caches();
        }

        // Load configuration
        $this->url_base = PeepSo::get_option_new('peepsocom_connect_url_base');
        $this->use_dns_discovery = PeepSo::get_option_new('peepsocom_connect_use_dns_discovery');
        $this->use_ssl_verify = PeepSo::get_option_new('peepsocom_connect_use_ssl_verify');
        $this->use_cache = $cache;
        $this->timeout = PeepSo::get_option_new('peepsocom_connect_timeout');
        $this->verbose = (2==PeepSo::get_option_new('peepsocom_connect_debug'));
        $this->json = $json;
        $this->fallback_message = $fallback_message;

        // Build request
        if(is_array($url)) {
            $url = add_query_arg($url,'');
        }

        // Special case for products.json
        if(stristr($url, 'products.json')) {
            $this->url_base = 'www.peepso.com';
            $this->track_failure = FALSE;
        }

        $this->request = 'https://'.$this->url_base.'/'.ltrim($url, '/');

        // Builds args array without overriding keys passed to contructor
        $this->args = array_merge($this->args, $args);

        // Use config for timeouts
        $this->args['timeout'] = $this->timeout;

        // SSL Verify
        $this->args['sslverify'] = 1;
        if(!$this->use_ssl_verify) {
            $this->args['sslverify']=0;
        }
    }

        private function fail() {

            if($this->fallback_message) {
                ob_start();?>
                <div style="width:100%;padding:10px;box-sizing: border-box;background:white;">
                    <p>
                        Sorry, we are unable to connect to PeepSo.com at this time. Please try again later.
                    </p>
                    <p>
                        You can try changing the <a href="<?php echo admin_url('admin.php?page=peepso_config&tab=advanced#field_peepsocom_connect_message:parent');?>" target="_blank">advanced configuration</a> related to PeepSo.com connection.
                    </p>

                    <p>
                        You can also check our <a href="https://www.peep.so/docs" target="_blank">documentation</a> or <a href="https://www.peepso.com/contact" target="_blank">contact support</a>.
                    </p>
                </div>
                <?php return ob_get_clean();
            }

            return NULL;
        }

    public function get() {
        return $this->request('get');
    }

    public function post() {
        return $this->request('post');
    }

    private function format($data) {

        if(!is_string($data)) {
            return $this->fail();
        }

        if($this->json) {
            if ($data = json_decode($data, FALSE)) {
                return $data;
            } else {
                return $this->fail();
            }
        } else {
            return $data;
        }
    }

    private function request($method) {

        $cache_key = 'pscc_'.md5($this->url_base.$this->request.serialize($this->args));

        new PeepSoCom_Debug("\n\n".date('Y-m-d H:i:s'));
        new PeepSoCom_Debug($this->request, strtoupper($method), );

        if($this->use_cache) {
            $cache_value = PeepSo3_Mayfly::get($cache_key);
            if(NULL !== $cache_value) {
                new PeepSoCom_Debug('CACHED');
                return $this->format($cache_value);
            }
        }

        if(PeepSoCom_Connect::is_offline()) {
            new PeepSoCom_Debug('OFFLINE');
            return $this->fail();
        }


        if(!$this->rate_limit_check($this->request, $this->rate_limit)) {

            new PeepSoCom_Debug($this->request,'API LIMIT '.$this->rate_limit);

            $last_error = PeepSo3_Mayfly::del('pscc_error');
            $new_error = 'Rate limiting';
            if(strlen($last_error)) {
                $last_error="  + ".$new_error;
            } else {
                $last_error=$new_error;
            }

            PeepSo3_Mayfly::set('pscc_error', $last_error);
            return $this->fail();
        }

        $method = strtolower($method);

        if(!in_array($method, ['get', 'post'])) {
            new PeepSoCom_Debug("$method - method not allowed","error");
            return $this->fail();
        }

        $curl_options = [
            'general' => [
                CURLOPT_FAILONERROR         => TRUE,
                CURLOPT_FOLLOWLOCATION      => TRUE,
                CURLOPT_TIMEOUT             => $this->timeout,
                CURLOPT_SSL_VERIFYHOST      => $this->args['sslverify'] ? 2 : 0,
                CURLOPT_RETURNTRANSFER      => 1,
                CURLOPT_DNS_CACHE_TIMEOUT   =>60,
            ],
            'get' => [
                CURLOPT_HTTPGET             => 1,
            ],
            'post' => [
                CURLOPT_POST                => 1,
            ],
            'resolvers' => [
                // PeepSo.com - 72.52.156.125
                'peepso.com:80:72.52.156.125',
                'peepso.com:443:72.52.156.125',

                // PeepSoLicense.com -  72.52.156.109
                'peepsolicense.com:80:72.52.156.109',
                'peepsolicense.com:443:72.52.156.109',
            ],
        ];

        $start = microtime(TRUE);

        $curl = curl_init($this->request);

        foreach($curl_options['general'] as $key => $value) {
            curl_setopt($curl, $key, $value);
        }

        if(!$this->use_dns_discovery) {
            curl_setopt($curl, CURLOPT_RESOLVE, $curl_options['resolvers']);
        }

        if('get' == $method) {
            foreach($curl_options['get'] as $key => $value) {
                curl_setopt($curl, $key, $value);
            }
        } elseif('post' == $method) {
            foreach($curl_options['post'] as $key => $value) {
                curl_setopt($curl, $key, $value);
            }
        }

        // Grab the results and close
        $resp = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        $elapsed = microtime(TRUE) - $start;

        if($resp) {
            $result = $resp;
        } else {
            $result = NULL;
        }


        if($this->verbose) {
            new PeepSoCom_Debug(curl_getinfo($curl, CURLINFO_RESPONSE_CODE), "Header");
            new PeepSoCom_Debug(curl_getinfo($curl, CURLINFO_PRIMARY_IP).":".curl_getinfo($curl, CURLINFO_PRIMARY_PORT), "IP:PORT");
            new PeepSoCom_Debug($this->backtrace, 'backtrace');
            new PeepSoCom_Debug($this->url_base, 'config / url_base');
            new PeepSoCom_Debug($this->timeout, 'config / timeout');
            new PeepSoCom_Debug($this->use_ssl_verify, 'config / use_ssl_verify');
            new PeepSoCom_Debug($this->use_dns_discovery, 'config / use_dns_discovery');
        }

        if($resp) {
            PeepSo3_Mayfly::set($cache_key, $result, $this->cache_ttl);
            PeepSoCom_Connect::reset_errors();
            PeepSo3_Mayfly::set('peepsocom_speed',$elapsed);
            new PeepSoCom_Debug($elapsed, 'SUCCESS');
            if($this->verbose) {
                new PeepSoCom_Debug(substr($resp, 0, 1000), "Response");
            }
        } else {
            if($this->track_failure && !$this->rate_limit_check('pscc_offline', $this->offline_threshold)) {
                // Only set the flags if we encounter enough failures in the current hour
                PeepSo3_Mayfly::set('pscc_offline', 1, 3600);
                PeepSo3_Mayfly::set('pscc_error', $error);
            }
            new PeepSoCom_Debug($error, "Error");
        }

        return $this->format($result);
    }


    // Rate limiting

    private function rate_limit_check($url, $limit, $time_group=NULL) {

        // Setup
        global $wpdb;
        $table = $wpdb->prefix.'peepso_api_rate_limit';

        if(NULL == $time_group) {
            $time_group = date('Y-m-d H');
        }

        // Group license activation into one limit
        if(stristr($url, 'edd_action=activate_license')) {
            $url = 'https://'.$this->url_base.'/?edd_action=activate_license';
        }

        $do_request = TRUE;

        // Validate
        if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) return FALSE;

        $count = $wpdb->get_row("SELECT * FROM $table WHERE api_name='$url' AND time_group='$time_group'", ARRAY_A);

        if(!is_array($count)) {
            $count = array('api_name'=>$url,'count'=>1,'attempt_count'=>1, 'time_group'=>$time_group);
            $wpdb->insert($table,$count);
        }

        if($count['time_group']==$time_group && $count['count'] >= $limit) {
            $do_request = FALSE;
            $wpdb->query("UPDATE $table SET attempt_count=attempt_count+1 WHERE api_name='$url' AND time_group='$time_group' ");
        } elseif($count['time_group']==$time_group) {
            $wpdb->query("UPDATE $table SET count=count+1, attempt_count=attempt_count+1  WHERE api_name='$url' AND time_group='$time_group' ");
        }

        return $do_request;
    }

    public static function rate_limit_reset() {
        new PeepSoCom_Debug(__METHOD__);
        global $wpdb;
        $wpdb->query("DELETE FROM {$wpdb->prefix}peepso_api_rate_limit");
    }

    // Error handling

    public function reset_admin_caches($reason='') {

        new PeepSoCom_Debug( $this->backtrace.' '.$reason, 'Resetting cache');

        PeepSo3_Mayfly::clr(TRUE, 'pscc');
        PeepSo3_Mayfly::clr(TRUE, 'license');
        PeepSo3_Mayfly::clr(TRUE,PeepSoAdmin::MAYFLY_PLUGINS );
    }

    public static function reset_errors() {
        PeepSo3_Mayfly::del('pscc_offline');
        PeepSo3_Mayfly::del('pscc_error');
    }

    public static function is_offline() {
        return !empty(PeepSo3_Mayfly::get('pscc_offline'));
    }

    public static function last_error() {
        return empty($error=PeepSo3_Mayfly::get('pscc_error')) ? '' : $error;
    }
}