<?php
defined('ABSPATH') || exit;

if ( ! class_exists('Better_Messages_Mobile_App_Builds') ) {
    class Better_Messages_Mobile_App_Builds
    {
        public $settings;

        public $defaults;

        public static function instance(): ?Better_Messages_Mobile_App_Builds
        {
            // Store the instance locally to avoid private static replication
            static $instance = null;
            // Only run these methods if they haven't been run previously

            if (null === $instance) {
                $instance = new Better_Messages_Mobile_App_Builds();
            }

            // Always return the instance
            return $instance;
            // The last metroid is in captivity. The galaxy is at peace.
        }

        public function __construct(){
            add_action( 'rest_api_init',  array( $this, 'rest_api_init' ) );

            add_action('init', array( $this, 'builds_checker' ) );
            add_action('check_build_statuses', array( $this, 'check_build_statuses' ) );
        }

        public function builds_checker()
        {
            if ( ! wp_next_scheduled('ba_check_build_statuses') ) {
                wp_schedule_event( time(), 'ba_every_five_minutes', 'ba_check_build_statuses' );
            }
        }

        public function check_build_statuses()
        {
            global $wpdb;

            $table = $wpdb->prefix . 'bm_app_builds';

            $builds = $wpdb->get_results( "SELECT id, status, site_id, secret FROM $table WHERE status = 'in-queue' OR status = 'building'", ARRAY_A );

            if( count( $builds ) > 0 ) {
                foreach ($builds as $build) {
                    $request = wp_remote_get(add_query_arg( [
                        'id' => $build['id'],
                        'site_id' => $build['site_id'],
                        'secret' => $build['secret'],
                    ], 'https://builder.wordplus.org/api/getBuildStatus'));

                    if( ! Better_Messages()->functions->is_response_good( $request ) ){
                        continue;
                    }

                    $response = json_decode($request['body'], true );

                    if ( wp_remote_retrieve_response_code($request) != 200 ) {
                        continue;
                    }

                    if( $response['status'] === 'built' ){
                        $wpdb->update( $table, [
                            'status' => 'built',
                        ], [
                            'id' => $build['id'],
                        ] );
                    }
                }
            }
        }

        public function rest_api_init(): void
        {
            register_rest_route('better-messages/v1/admin/app', '/prepareBuild', array(
                'methods' => 'POST',
                'callback' => array($this, 'prepare_build'),
                'permission_callback' => array($this, 'user_is_admin'),
            ));

            register_rest_route('better-messages/v1/admin/app', '/createBuild', array(
                'methods' => 'POST',
                'callback' => array($this, 'create_build'),
                'permission_callback' => array($this, 'user_is_admin'),
            ));

            register_rest_route('better-messages/v1/admin/app', '/getBuilds', array(
                'methods' => 'GET',
                'callback' => array($this, 'get_builds'),
                'permission_callback' => array($this, 'user_is_admin'),
            ));

            register_rest_route('better-messages/v1/admin/app', '/deleteBuild', array(
                'methods' => 'DELETE',
                'callback' => array($this, 'delete_build'),
                'permission_callback' => array($this, 'user_is_admin'),
            ));
        }

        public function user_is_admin(): bool
        {
            return current_user_can('manage_options');
        }

        public function delete_build( WP_REST_Request $request )
        {
            $build_id = (int) $request->get_param( 'build_id' );

            global $wpdb;

            $table = $wpdb->prefix . 'bm_app_builds';

            $build = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT id, platform, type, status, site_id, secret FROM $table WHERE `id` = %d",
                    $build_id )
            );

            if( ! $build ){
                return new WP_Error('invalid_build', 'The build does not exist');
            }

            $request = wp_remote_request('https://builder.wordplus.org/api/deleteBuild', array(
                'method' => 'DELETE',
                'body' => [
                    'id' => $build->id,
                    'site_id' => $build->site_id,
                    'secret' => $build->secret,
                ]
            ));

            if( ! Better_Messages()->functions->is_response_good( $request ) ){
                return Better_Messages()->functions->is_response_good( $request );
            }

            $response = json_decode($request['body'], true );

            if ( wp_remote_retrieve_response_code($request) != 200 ) {
                if( isset( $response['error'] ) ){
                    return new WP_Error('request_failed', $response['error']);
                } else {
                    return new WP_Error('request_failed', 'The network request failed.');
                }
            }

            if( $response === 'deleted' ){
                $wpdb->delete( $table, [
                    'id' => $build->id,
                ], [ '%d' ] );
            }

            return true;
        }

        public function get_builds( WP_REST_Request $request )
        {
            $this->check_build_statuses();

            global $wpdb;

            $table = $wpdb->prefix . 'bm_app_builds';

            $builds = $wpdb->get_results( "SELECT id, platform, type, status, site_id, secret, build_info FROM $table ORDER BY id DESC", ARRAY_A );

            if( count( $builds ) > 0 ) {
                foreach ($builds as $key => $build) {
                    $build_info = json_decode($build['build_info'], true);

                    $app_name = $build_info['app_name'];
                    $app_icon = $build_info['app_icon'];

                    unset($builds[$key]['build_info']);

                    $builds[$key]['app_name'] = $app_name;
                    $builds[$key]['app_icon'] = $app_icon;

                    if( $build['status'] === 'built' ){
                        $download_url = add_query_arg( array(
                            'id' => $build['id'],
                            'site_id' => $build['site_id'],
                            'secret' => $build['secret'],
                        ), 'https://builder.wordplus.org/api/getIpa/' );

                        $builds[$key]['download_url'] = $download_url;

                        $install_destination = add_query_arg( array(
                            'id' => $build['id'],
                            'site_id' => $build['site_id'],
                            'secret' => $build['secret'],
                        ), 'https://builder.wordplus.org/api/getDevelopmentManifest/' );

                        $install_url = 'itms-services://?action=download-manifest&amp;url=' . urlencode( $install_destination );

                        $builds[$key]['install_url'] = $install_url;

                    }
                }
            }

            return $builds;
        }

        public function prepare_build( WP_REST_Request $request )
        {
            $platform = $request->get_param('platform');

            if ( $platform !== 'ios' && $platform !== 'android' ) {
                return new WP_Error('invalid_platform', 'Platform must be ios or android');
            }

            $type     = $request->get_param('type');
            if ( $type !== 'development' && $type !== 'production' ) {
                return new WP_Error('invalid_type', 'Type must be development or production');
            }

            $fs_site = bpbm_fs()->get_site();

            if( ! $fs_site ){
                return new WP_Error('invalid_site', 'The site is not valid');
            }

            if ($platform === 'ios' && $type === 'development') {
                // Code for iOS development
                return $this->get_ios_dev_build_info();
            } else if ($platform === 'ios' && $type === 'production') {
                // Code for iOS production
                return $this->get_ios_dist_build_info();
            } else if ($platform === 'android' && $type === 'development') {
                // Code for Android development
                return new WP_Error('not_implemented', 'Android development builds are not implemented yet');
            } else if ($platform === 'android' && $type === 'production') {
                // Code for Android production
                return new WP_Error('not_implemented', 'Android production builds are not implemented yet');
            } else {
                // Code for other cases
                return new WP_Error('not_implemented', 'This type of application is not implemented yet');
            }
        }

        public function create_build( WP_REST_Request $request )
        {
            $platform = $request->get_param('platform');

            if ( $platform !== 'ios' && $platform !== 'android' ) {
                return new WP_Error('invalid_platform', 'Platform must be ios or android');
            }

            $type     = $request->get_param('type');
            if ( $type !== 'development' && $type !== 'production' ) {
                return new WP_Error('invalid_type', 'Type must be development or production');
            }

            if ($platform === 'ios' && $type === 'development') {
                // Code for iOS development
                $info = $this->get_ios_dev_build_info(true);

                if( is_wp_error( $info ) ){
                    return $info;
                }

                return $this->process_build_request( $info );
            } else if ($platform === 'ios' && $type === 'production') {
                // Code for iOS production
                $info = $this->get_ios_dist_build_info(true);

                if( is_wp_error( $info ) ){
                    return $info;
                }

                return $this->process_build_request( $info );
            } else if ($platform === 'android' && $type === 'development') {
                // Code for Android development
                return new WP_Error('not_implemented', 'Android development builds are not implemented yet');
            } else if ($platform === 'android' && $type === 'production') {
                // Code for Android production
                return new WP_Error('not_implemented', 'Android production builds are not implemented yet');
            } else {
                // Code for other cases
                return new WP_Error('not_implemented', 'This type of application is not implemented yet');
            }
        }

        public function get_ios_dev_build_info($build_info = false)
        {
            $settings = Better_Messages()->mobile_app->settings->get_settings();

            $bundle_id   = $settings['iosBundleDev'];
            $certificate_id = $settings['iosCertificateDev'];
            $profile_id  = $settings['iosProfileDev'];

            $site_url = get_site_url();
            $parse = parse_url($site_url);
            $domain = $parse['host'];

            $fs_site = bpbm_fs()->get_site();

            if( ! $fs_site ){
                return new WP_Error('site_not_connected', 'The site is not connected');
            }

            $certificate = Better_Messages()->mobile_app->ios->get_certificate( $certificate_id );

            if( is_wp_error( $certificate ) ){
                return $certificate;
            }

            $allowed_cert_types = [
                'DEVELOPMENT',
                'IOS_DEVELOPMENT'
            ];

            if( !in_array( $certificate['certificateType'], $allowed_cert_types ) ){
                return new WP_Error('invalid_certificate', 'The certificate is not a development certificate');
            }

            $bundle = Better_Messages()->mobile_app->ios->get_bundle( $bundle_id );

            if( is_wp_error( $bundle ) ){
                return $bundle;
            }

            $profile = Better_Messages()->mobile_app->ios->get_provisioning_profile( $profile_id );

            if( is_wp_error( $profile ) ){
                return $profile;
            }

            if( $profile['profileState'] !== 'ACTIVE' ){
                return new WP_Error('invalid_profile', 'The provisioning profile is not active');
            }

            if( ! $build_info ) {
                unset($profile['profileContent']);
                unset($certificate['certificateContent']);

                if ($profile['certificates'] && count($profile['certificates']) > 0) {
                    $profile['certificates'] = array_map(function ($certificate) {
                        unset($certificate['certificateContent']);
                        return $certificate;
                    }, $profile['certificates']);
                }
            }

            if( ! $settings['applicationName'] ){
                return new WP_Error('no_app_name', 'The Application Name is not set');
            }

            if( ! $settings['appIcon'] ){
                return new WP_Error('no_app_icon', 'The Application Icon is not set');
            }

            if( ! $settings['appSplash'] ){
                return new WP_Error('no_app_splash', 'The Application Splash Screen is not set');
            }

            if( ! $settings['loginLogo'] ){
                return new WP_Error('no_login_logo', 'The Application Login Logo is not set');
            }

            $api_url = esc_url_raw(get_rest_url(null, '/better-messages/v1/'));


            $return = [
                'platform' => 'ios',
                'type' => 'development',
                'site_id' => $fs_site->id,
                'build_info' => [
                    'domain'               => $domain,
                    'bundle'               => $bundle,
                    'app_name'             => $settings['applicationName'],
                    'app_icon'             => $settings['appIcon'],
                    'app_splash'           => $settings['appSplash'],
                    'api_url'              => $api_url,
                    'signing_certificate'  => $certificate,
                    'provisioning_profile' => $profile,
                ]
            ];

            if( $build_info ){
                $return['build_info']['certificate'] = get_option('better-messages-app-ios-certificate-DEVELOPMENT');
                $return['build_info'] = json_encode( $return['build_info'] );
            }


            return $return;
        }

        public function get_ios_dist_build_info($build_info = false)
        {
            $settings = Better_Messages()->mobile_app->settings->get_settings();

            $bundle_id   = $settings['iosBundleProd'];
            $certificate_id = $settings['iosCertificateProd'];
            $profile_id  = $settings['iosProfileProd'];

            $notification_bundle_id   = $settings['iosBundleService'];
            $notification_profile_id  = $settings['iosProfileService'];

            $site_url = get_site_url();
            $parse = parse_url($site_url);
            $domain = $parse['host'];

            $fs_site = bpbm_fs()->get_site();

            if( ! $fs_site ){
                return new WP_Error('site_not_connected', 'The site is not connected');
            }

            $certificate = Better_Messages()->mobile_app->ios->get_certificate( $certificate_id );

            if( is_wp_error( $certificate ) ){
                return $certificate;
            }

            $allowed_cert_types = [
                'DISTRIBUTION',
                'IOS_DISTRIBUTION'
            ];

            if( !in_array( $certificate['certificateType'], $allowed_cert_types ) ){
                return new WP_Error('invalid_certificate', 'The certificate is not a distribution certificate');
            }

            $bundle = Better_Messages()->mobile_app->ios->get_bundle( $bundle_id );

            if( is_wp_error( $bundle ) ){
                return $bundle;
            }

            $profile = Better_Messages()->mobile_app->ios->get_provisioning_profile( $profile_id );

            if( is_wp_error( $profile ) ){
                return $profile;
            }

            if( $profile['profileState'] !== 'ACTIVE' ){
                return new WP_Error('invalid_profile', 'The provisioning profile is not active');
            }

            $notfication_bundle = Better_Messages()->mobile_app->ios->get_bundle( $notification_bundle_id );

            if( is_wp_error( $notfication_bundle ) ){
                return $notfication_bundle;
            }

            $notification_profile = Better_Messages()->mobile_app->ios->get_provisioning_profile( $notification_profile_id );

            if( is_wp_error( $notification_profile ) ){
                return $notification_profile;
            }

            if( $notification_profile['profileState'] !== 'ACTIVE' ){
                return new WP_Error('invalid_notification_profile', 'The notification provisioning profile is not active');
            }

            if( ! $build_info ) {
                unset($profile['profileContent']);
                unset($certificate['certificateContent']);
                unset($notification_profile['profileContent']);

                if ($profile['certificates'] && count($profile['certificates']) > 0) {
                    $profile['certificates'] = array_map(function ($certificate) {
                        unset($certificate['certificateContent']);
                        return $certificate;
                    }, $profile['certificates']);
                }
            }

            if( ! $settings['applicationName'] ){
                return new WP_Error('no_app_name', 'The Application Name is not set');
            }

            if( ! $settings['appIcon'] ){
                return new WP_Error('no_app_icon', 'The Application Icon is not set');
            }

            if( ! $settings['appSplash'] ){
                return new WP_Error('no_app_splash', 'The Application Splash Screen is not set');
            }

            if( ! $settings['loginLogo'] ){
                return new WP_Error('no_login_logo', 'The Application Login Logo is not set');
            }

            $api_url = esc_url_raw(get_rest_url(null, '/better-messages/v1/'));

            $return = [
                'platform' => 'ios',
                'type'     => 'production',
                'site_id'  => $fs_site->id,
                'build_info' => [
                    'domain'               => $domain,
                    'bundle'               => $bundle,
                    'notification_bundle'  => $notfication_bundle,
                    'app_name'             => $settings['applicationName'],
                    'app_icon'             => $settings['appIcon'],
                    'app_splash'           => $settings['appSplash'],
                    'version'              => 4,
                    'api_url'              => $api_url,
                    'signing_certificate'  => $certificate,
                    'provisioning_profile' => $profile,
                    'notification_profile' => $notification_profile,
                ]
            ];

            if( $build_info ){
                $return['build_info']['certificate'] = get_option('better-messages-app-ios-certificate-DISTRIBUTION');
                $return['build_info'] = json_encode( $return['build_info'] );
            }

            return $return;
        }

        public function process_build_request( $data )
        {
            global $wpdb;

            $table = $wpdb->prefix . 'bm_app_builds';

            $request = wp_remote_post('https://builder.wordplus.org/api/requestBuild', array(
                'body' => $data
            ));

            if( ! Better_Messages()->functions->is_response_good( $request ) ){
                return Better_Messages()->functions->is_response_good( $request );
            }

            $response = json_decode($request['body'], true );

            if ( wp_remote_retrieve_response_code($request) != 200 ) {
                if( isset( $response['error'] ) ){
                    return new WP_Error('request_failed', $response['error']);
                } else {
                    return new WP_Error('request_failed', 'The network request failed.');
                }
            }

            $wpdb->insert( $table, [
                'id'         => $response['build']['id'],
                'site_id'    => $data['site_id'],
                'platform'   => $data['platform'],
                'type'       => $data['type'],
                'status'     => 'in-queue',
                'secret'     => $response['build']['secret'],
                'build_info' => $data['build_info'],
            ] );

            return $response['message'];
        }
    }


}

