<?php
/**
 * Temp Login Model
 *
 * @file  The Temp Login file
 * @package HMWPP/Temp Login
 * @since 1.0.0
 */
defined('ABSPATH') || die('Cheatin\' uh?');

class HMWPP_Models_Templogin
{

    /** @var array Define all session expire options */
    public $expires;
    
    /** @var string the user session expire timestamp  */
    const USER_SESSION_EXPIRE = '_hmwp_expire';
    const USER_SESSION_CREATED = '_hmwp_created';

    const USER_LOGGED_TEMPORARY = '_hmwp_user';
    const USER_TOKEN_KEY = '_hmwp_token';
    const USER_LAST_LOGIN = '_hmwp_last_login';
    const USER_SESSION_UPDATED = '_hmwp_updated';
    const USER_REDIRECT = '_hmwp_redirect_to';
    const USER_LANGUAGE = 'locale';

    public function __construct()
    {
        $this->expires = array(
            'hour'  => array( 'label' => esc_html__( 'One Hour', 'hide-my-wp-pack' ), 'timestamp' => HOUR_IN_SECONDS ),
            '3_hours' => array( 'label' => esc_html__( 'Three Hours', 'hide-my-wp-pack' ), 'timestamp' => HOUR_IN_SECONDS * 3 ),
            'day' => array( 'label' => esc_html__( 'One Day', 'hide-my-wp-pack' ), 'timestamp' => DAY_IN_SECONDS ),
            '3_days' => array( 'label' => esc_html__( 'Three Days', 'hide-my-wp-pack' ), 'timestamp' => DAY_IN_SECONDS * 3 ),
            'week' => array( 'label' => esc_html__( 'One Week', 'hide-my-wp-pack' ), 'timestamp' => WEEK_IN_SECONDS ),
            'month' => array( 'label' => esc_html__( 'One Month', 'hide-my-wp-pack' ), 'timestamp' => MONTH_IN_SECONDS ),
            'halfyear' => array( 'label' => esc_html__( 'Six Months', 'hide-my-wp-pack' ), 'timestamp' => (6* MONTH_IN_SECONDS) ),
            'year' => array( 'label' => esc_html__( 'One Year', 'hide-my-wp-pack' ), 'timestamp' => YEAR_IN_SECONDS ),
        );
    }

    /**
     * Get valid temporary user based on token
     *
     * @param string $token
     *
     * @return array|bool
     * @since 1.0.0
     *
     */
    public function findUserByToken( $token = '') {

        if ( empty( $token ) ) {
            return false;
        }

        $args = array(
            'fields'     => 'all',
            'meta_key'   => self::USER_SESSION_EXPIRE,
            'order'      => 'DESC',
            'orderby'    => 'meta_value',
            'meta_query' => array(
                0 => array(
                    'key'     => self::USER_TOKEN_KEY,
                    'value'   => sanitize_text_field( $token ),
                    'compare' => '=',
                ),
            ),
        );

        if(HMWPP_Classes_Tools::isMultisites()){

            //initiate users
            $users = array();

            $current_blog_id = get_current_blog_id();
            // Now, add this user to all sites
            $sites = get_sites( array( 'number'  => 10000, 'public'  => 1, 'deleted' => 0, ) );
            if (!empty($sites) && count($sites) > 0) {
                foreach ($sites as $site) {
                    switch_to_blog($site->blog_id);
                    if($sub_query = new WP_User_Query( $args )){
                        $sub_users = $sub_query->get_results();
                        $users = array_merge($users, $sub_users);
                    }
                }
            }
            switch_to_blog($current_blog_id);
        }else{
            $query = new WP_User_Query( $args );
            $users = $query->get_results();
        }

        if ( empty( $users ) ) {
            return false;
        }

        foreach ( $users as $user ) {
            if(!$expire = HMWPP_Classes_Tools::getUserMeta(self::USER_SESSION_EXPIRE, $user->ID)){
                return false;
            }

            if ( is_numeric($expire) && $expire <= $this->gtmTimestamp()) {
                return false;
            } elseif ( $expire <= $this->gtmTimestamp() ) {
                $timestamp      = ! empty( $this->expires[ $expire ] ) ? $this->expires[ $expire ]['timestamp'] : 0;
                HMWPP_Classes_Tools::saveUserMeta(self::USER_SESSION_EXPIRE, $this->gtmTimestamp() + $timestamp, $user->ID);
            }

            $user->details = $this->getUserDetails($user);
            return $user;
        }

        return false;

    }


    /**
     * Get user temp login details
     *
     * @param $user
     * @return mixed
     *
     * @since 1.0.0
     */
    public function getUserDetails($user){
        $details = array();

        $details['redirect_to']  = HMWPP_Classes_Tools::getUserMeta(self::USER_REDIRECT, $user->ID);
        $details['expire']  = HMWPP_Classes_Tools::getUserMeta(self::USER_SESSION_EXPIRE, $user->ID);
        $details['locale']  = HMWPP_Classes_Tools::getUserMeta(self::USER_LANGUAGE, $user->ID);
        $details['templogin_url'] = $this->getTempLoginUrl( $user->ID );

        $details['last_login_time'] = HMWPP_Classes_Tools::getUserMeta(self::USER_LAST_LOGIN, $user->ID);
        $details['last_login'] = esc_html__( 'Not yet logged in', 'hide-my-wp-pack' );
        if ( ! empty( $details['last_login_time'] ) ) {
            $details['last_login'] = $this->timeElapsed( $details['last_login_time'], true );
        }

        $details['status'] = 'Active';
        if ( $this->isExpired( $user->ID ) ) {
            $details['status'] = 'Expired';
        }
        $details['is_active'] = ( 'active' === strtolower( $details['status'] ) ) ? true : false;

        $details['user_role'] = '';
        $details['user_role_name'] = '';

        if ( HMWPP_Classes_Tools::isMultisites() && is_super_admin( $user->ID ) ) {
            $details['user_role'] = 'super_admin';
            $details['user_role_name'] = esc_html__( 'Super Admin', 'hide-my-wp-pack' );
        } else {
            global $wpdb;

            $capabilities = $user->{$wpdb->prefix . 'capabilities'};

            if(HMWPP_Classes_Tools::isMultisites()){
                if($blog_id = get_user_meta( $user->ID, 'primary_blog', true )){
                    if(defined('BLOG_ID_CURRENT_SITE')){
                        if(BLOG_ID_CURRENT_SITE <> $blog_id){
                            $capabilities = $user->{$wpdb->prefix . $blog_id . '_' . 'capabilities'};
                            $details['user_blog_id'] = $blog_id;
                        }
                    }
                }
            }

            $wp_roles     = new WP_Roles();
            if(!empty($capabilities)){
                foreach ( $wp_roles->role_names as $role => $name ) {
                    if ( array_key_exists( $role, $capabilities ) ) {
                        $details['user_role'] = $role;
                        $details['user_role_name'] = $name;
                    }
                }
            }

        }

        return json_decode(json_encode($details));
    }

    /**
     * Create a Temporary user
     *
     * @return array
     *
     * @since 1.0.0
     */
    public function createNewUser( $data ) {

        $result = array(
            'error' => true
        );

        $expire = ! empty( $data['expire'] ) ? $data['expire'] : 'day';
        $blog_id = $data['blog_id'] ?? false;
        $super_admin = $data['super_admin'] ?? false;
        $password    = HMWPP_Classes_Tools::generateRandomString();
        $username    = $this->createUsername( $data );
        $first_name  = isset( $data['first_name'] ) ? sanitize_text_field( $data['first_name'] ) : '';
        $last_name   = isset( $data['last_name'] ) ? sanitize_text_field( $data['last_name'] ) : '';
        $email       = isset( $data['user_email'] ) ? sanitize_email( $data['user_email'] ) : '';
        $role        = ! empty( $data['user_role'] ) ? $data['user_role'] : 'subscriber';
        $redirect_to = ! empty( $data['redirect_to'] ) ? sanitize_text_field( $data['redirect_to'] ) : '';
        $user_args   = array(
            'first_name' => $first_name,
            'last_name'  => $last_name,
            'user_login' => $username,
            'user_pass'  => $password,
            'user_email' => $email,
            'role'       => $role,
        );

        if($username <> '' && username_exists($username)){
            $result['errcode'] = 'username_exists';
            $result['message'] = esc_html__('Email address already exists','hide-my-wp-pack');
        }elseif($email <> '' && email_exists($email)){
            $result['errcode'] = 'email_exists';
            $result['message'] = esc_html__('Email address already exists','hide-my-wp-pack');
        }else{
            $user_id = wp_insert_user( $user_args );

            if ( is_wp_error( $user_id ) ) {
                $code = $user_id->get_error_code();

                $result['errcode'] = $code;
                $result['message'] = $user_id->get_error_message( $code );

            } else {

                if (HMWPP_Classes_Tools::isMultisites()) {

                    if($super_admin){
                        // Grant super admin access to this temporary users
                        grant_super_admin($user_id);

                        // Now, add this user to all sites
                        $sites = get_sites( array( 'number'  => 10000, 'public'  => 1, 'deleted' => 0, ) );

                        if (!empty($sites) && count($sites) > 0) {
                            foreach ($sites as $site) {
                                // If user is not already member of blog? Add into this blog
                                if (!is_user_member_of_blog($user_id, $site->blog_id)) {
                                    add_user_to_blog($site->blog_id, $user_id, 'administrator');
                                }
                            }
                        }
                    }elseif($blog_id){

                        //if the user was not created for the main website
                        if(defined('BLOG_ID_CURRENT_SITE')){
                            if(BLOG_ID_CURRENT_SITE <> $blog_id){
                                remove_user_from_blog($user_id, BLOG_ID_CURRENT_SITE);
                            }
                        }

                        // If user is not already member of blog? Add into this blog
                        if (!is_user_member_of_blog($user_id, $blog_id)) {
                            add_user_to_blog($blog_id, $user_id, $role);
                        }
                    }

                }

                HMWPP_Classes_Tools::saveUserMeta(self::USER_LOGGED_TEMPORARY, true, $user_id);
                HMWPP_Classes_Tools::saveUserMeta(self::USER_SESSION_CREATED, $this->gtmTimestamp(), $user_id);
                HMWPP_Classes_Tools::saveUserMeta(self::USER_SESSION_EXPIRE, $expire, $user_id);
                HMWPP_Classes_Tools::saveUserMeta(self::USER_TOKEN_KEY, $this->generateToken( $user_id ), $user_id);
                HMWPP_Classes_Tools::saveUserMeta(self::USER_REDIRECT, $redirect_to, $user_id);


                //set locale
                $locale = ! empty( $data['locale'] ) ? $data['locale'] : 'en_US';
                HMWPP_Classes_Tools::saveUserMeta(self::USER_LANGUAGE, $locale, $user_id);

                $result['error']   = false;
                $result['user_id'] = $user_id;
            }
        }

        return $result;

    }

    /**
     * Create a ranadom username for the temporary user
     *
     * @param array $data
     *
     * @return string
     * @since 1.0.0
     */
    public function createUsername( $data ) {

        $first_name = isset( $data['user_first_name'] ) ? $data['user_first_name'] : '';
        $last_name  = isset( $data['user_last_name'] ) ? $data['user_last_name'] : '';
        $email      = isset( $data['user_email'] ) ? $data['user_email'] : '';

        $name = '';
        if ( ! empty( $first_name ) || ! empty( $last_name ) ) {
            $name = str_replace( array( '.', '+' ), '', strtolower( trim( $first_name . $last_name ) ) );
        } else {
            if ( ! empty( $email ) ) {
                $explode = explode( '@', $email );
                $name    = str_replace( array( '.', '+' ), '', $explode[0] );
            }
        }

        if ( username_exists( $name ) ) {
            $name = $name . substr( uniqid( '', true ), - 6 );
        }

        $username = sanitize_user( $name, true );

        /**
         * We are generating WordPress username from First Name & Last Name fields.
         * When First Name or Last Name comes with non latin words, generated username
         * is non latin and sanitize_user function discrad it and user is not being
         * generated.
         *
         * To avoid this, if this situation occurs, we are generating random username
         * for this user.
         */
        if ( empty( $username ) ) {
            $username = HMWPP_Classes_Tools::generateRandomString();
        }

        return sanitize_user( $username, true );
    }

    /**
     * Update user
     *
     * @param array $data
     *
     * @return array|int|WP_Error
     * @since 1.0.0
     */
    public function updateUser( $data ) {

        $expire = ! empty( $data['expire'] ) ? $data['expire'] : 'day';
        $blog_id = $data['blog_id'] ?? false;
        $super_admin = $data['super_admin'] ?? false;
        $first_name  = isset( $data['first_name'] ) ? sanitize_text_field( $data['first_name'] ) : '';
        $last_name   = isset( $data['last_name'] ) ? sanitize_text_field( $data['last_name'] ) : '';
        $redirect_to = isset( $data['redirect_to'] ) ? sanitize_text_field( $data['redirect_to'] ) : '';
        $role        = ! empty( $data['user_role'] ) ? $data['user_role'] : 'subscriber';
        $user_args   = array(
            'first_name' => $first_name,
            'last_name'  => $last_name,
            'role'       => $role,
            'ID'         => $data['user_id']
        );

        $user_id = wp_update_user( $user_args );

        if ( is_wp_error( $user_id ) ) {
            $code = $user_id->get_error_code();

            return array(
                'error'   => true,
                'errcode' => $code,
                'message' => $user_id->get_error_message( $code ),
            );
        }


        if ( HMWPP_Classes_Tools::isMultisites() ) {
            if($super_admin){
                grant_super_admin( $user_id );
            }elseif($blog_id){
                $sites = get_sites( array( 'number'  => 10000, 'public'  => 1, 'deleted' => 0, ) );

                //if the website was changed for the current user
                if (!empty($sites) && count($sites) > 0) {
                    foreach ($sites as $site) {
                        if($site->blog_id <> $blog_id){
                            // If user is not already member of blog? Add into this blog
                            if (is_user_member_of_blog($user_id, $site->blog_id)) {
                                remove_user_from_blog($user_id, $site->blog_id);
                            }
                        }
                    }
                }

                // If user is not already member of blog? Add into this blog
                if (!is_user_member_of_blog($user_id, $blog_id)) {
                    add_user_to_blog($blog_id, $user_id, $role);
                }
            }
        }


        HMWPP_Classes_Tools::saveUserMeta(self::USER_SESSION_UPDATED, $this->gtmTimestamp(), $user_id);
        HMWPP_Classes_Tools::saveUserMeta(self::USER_SESSION_EXPIRE, $expire, $user_id);
        HMWPP_Classes_Tools::saveUserMeta(self::USER_REDIRECT, $redirect_to, $user_id);

        //set locale
        $locale = ! empty( $data['locale'] ) ? $data['locale'] : 'en_US';
        HMWPP_Classes_Tools::saveUserMeta(self::USER_LANGUAGE, $locale, $user_id);

        return $user_id;

    }

    /**
     * Get the expiration time based on string
     *
     * @param string $expire
     * @param string $date
     *
     * @return false|float|int
     * @since 1.0.0
     *
     */
    public function getUserExpireTime( $expire = 'day', $date = '' ) {

        $expire = in_array( $expire, array_keys( $this->expires ) ) ? $expire : 'day';

        $current_timestamp = $this->gtmTimestamp();
        $timestamp         = $this->expires[ $expire ]['timestamp'];

        return $current_timestamp + floatval( $timestamp );

    }

    /**
     * Save the last user login time
     * @param $user
     *
     * @return void
     */
    public function saveUserLastLogin( $user ){
        HMWPP_Classes_Tools::saveUserMeta(self::USER_LAST_LOGIN, $this->gtmTimestamp(), $user->ID);
    }

    /**
     * Get current GMT date time
     *
     * @return false|int
     * @since 1.0.0
     *
     */
    public function gtmTimestamp() {
        return strtotime( gmdate( 'Y-m-d H:i:s', time() ) );
    }

    /**
     * Get Temporary Logins
     *
     * @param string $role
     *
     * @return array|bool
     * @since 1.0.0
     *
     */
    public function getTempUsers( $role = '' ) {

        $args = array(
            'fields'     => 'all',
            'meta_key'   => self::USER_SESSION_EXPIRE,
            'order'      => 'DESC',
            'orderby'    => 'meta_value',
            'meta_query' => array(
                0 => array(
                    'key'   => self::USER_LOGGED_TEMPORARY,
                    'value' => 1,
                ),
            ),
        );

        if ( ! empty( $role ) ) {
            $args['role'] = $role;
        }

        if(HMWPP_Classes_Tools::isMultisites()){
            $users = array();
            $current_blog_id = get_current_blog_id();
            // Now, add this user to all sites
            $sites = get_sites( array( 'number'  => 10000, 'public'  => 1, 'deleted' => 0, ) );
            if (!empty($sites) && count($sites) > 0) {
                foreach ($sites as $site) {
                    switch_to_blog($site->blog_id);
                    if($sub_query = new WP_User_Query( $args )){
                        $sub_users = $sub_query->get_results();
                        $users = array_merge($users, $sub_users);
                    }
                }
            }
            switch_to_blog($current_blog_id);
        }else{
            $query = new WP_User_Query( $args );
            $users = $query->get_results();
        }

        return $users;

    }

    /**
     * Get the redable time elapsed string
     *
     * @param int $time
     * @param bool $ago
     *
     * @return string
     * @since 1.0.0
     *
     */
    public function timeElapsed( $time, $ago = false ) {

        if ( is_numeric( $time ) ) {

            if ( $ago ) {
                $etime = $this->gtmTimestamp() - $time;
            } else {
                $etime = $time - $this->gtmTimestamp();
            }

            if ( $etime < 1 ) {
                return esc_html__( 'Expired', 'hide-my-wp-pack' );
            }

            $a = array(
                // 365 * 24 * 60 * 60 => 'year',
                // 30 * 24 * 60 * 60 => 'month',
                24 * 60 * 60 => 'day',
                60 * 60      => 'hour',
                60           => 'minute',
                1            => 'second',
            );

            $a_plural = array(
                'year'   => 'years',
                'month'  => 'months',
                'day'    => 'days',
                'hour'   => 'hours',
                'minute' => 'minutes',
                'second' => 'seconds',
            );

            foreach ( $a as $secs => $str ) {
                $d = $etime / $secs;

                if ( $d >= 1 ) {
                    $r = round( $d );

                    $time_string = ( $r > 1 ) ? $a_plural[ $str ] : $str;

                    if ( $ago ) {
                        return sprintf( esc_html__('%d %s ago', 'hide-my-wp-pack'), $r, $time_string );
                    } else {
                        return sprintf( esc_html__('%d %s remaining', 'hide-my-wp-pack'), $r, $time_string );
                    }
                }
            }

            return __( 'Expired', 'hide-my-wp-pack' );
        } else {

            return ! empty( $expiry_options[ $time ] ) ? $this->expires[ $time ]['label'] : '';
        }

    }

    /**
     * Check if temporary login expired
     *
     * @param int $user_id
     *
     * @return bool
     *
     * @since 1.0.0
     */
    public function isExpired( $user_id = 0 ) {

        if ( empty( $user_id ) ) {
            $user_id = get_current_user_id();
        }

        if ( empty( $user_id ) ) {
            return false;
        }

        $expire = HMWPP_Classes_Tools::getUserMeta(self::USER_SESSION_EXPIRE, $user_id);

        return ! empty( $expire ) && is_numeric( $expire ) && $this->gtmTimestamp() >= floatval( $expire ) ? true : false;

    }

    /**
     * Get temporary login url
     *
     * @param $user_id
     *
     * @return string
     * @since 1.0.0
     *
     */
    public function getTempLoginUrl( $user_id ) {

        if ( empty( $user_id ) ) {
            return '';
        }

        $is_valid_temporary_login = $this->isValidTempLogin( $user_id );
        if ( ! $is_valid_temporary_login ) {
            return '';
        }

        $token = HMWPP_Classes_Tools::getUserMeta(self::USER_TOKEN_KEY, $user_id);
        if ( empty( $token ) ) {
            return '';
        }

        $login_url = add_query_arg( 'hmwp_token', $token, trailingslashit( home_url() ) );

        // Make it compatible with iThemes Security plugin with Custom URL Login enabled
        $login_url = apply_filters( 'itsec_notify_admin_page_url', $login_url );

        return apply_filters( 'hmwp_templogin_link', $login_url, $user_id );

    }

    /**
     * Checks whether user is valid temporary user
     *
     * @param int $user_id
     *
     * @return bool
     */
    public function isValidTempLogin( $user_id = 0 ) {

        if ( empty( $user_id ) ) {
            return false;
        }

        $check = HMWPP_Classes_Tools::getUserMeta(self::USER_LOGGED_TEMPORARY, $user_id);

        return ! empty( $check ) ? true : false;

    }

    /**
     * Generate Temporary Login Token
     *
     * @param $user_id
     *
     * @return false|string
     *
     * @since 1.0.0
     */
    public function generateToken( $user_id ) {
        $byte_length = 64;

        if ( function_exists( 'random_bytes' ) ) {
            try {
                return bin2hex( random_bytes( $byte_length ) ); // phpcs:ignore
            } catch ( \Exception $e ) {
            }
        }

        if ( function_exists( 'openssl_random_pseudo_bytes' ) ) {
            $crypto_strong = false;

            $bytes = openssl_random_pseudo_bytes( $byte_length, $crypto_strong );
            if ( true === $crypto_strong ) {
                return bin2hex( $bytes );
            }
        }

        // Fallback
        $str  = $user_id . microtime() . uniqid( '', true );
        $salt = substr( md5( $str ), 0, 32 );

        return hash( "sha256", $str . $salt );
    }

    /**
     * Get all pages which needs to be blocked for temporary users
     *
     * @return array
     * @since 1.0.0
     *
     */
    public function getRestrictedPages() {
        $pages = array( 'user-new.php', 'user-edit.php', 'profile.php' );
        $pages = apply_filters( 'hmwp_templogin_restricted_pages', $pages );

        return $pages;

    }

    /**
     * Get all pages which needs to be blocked for temporary users
     *
     * @return array
     * @since 1.0.0
     *
     */
    public function getRestrictedActions() {
        $actions = array( 'deleteuser', 'delete' );
        $actions = apply_filters( 'hmwp_templogin_restricted_actions', $actions );

        return $actions;

    }

    /**
     * Update the temporary login status
     *
     * @param int $user_id
     * @param string $action
     *
     * @return bool
     * @since 1.0.0
     *
     */
    public function updateLoginStatus( $user_id = 0, $action = '' ) {

        if ( empty( $user_id ) || empty( $action ) ) {
            return false;
        }

        if ( !$this->isValidTempLogin( $user_id )) {
            return false;
        }

        $manage_login = false;
        if ( 'disable' === $action ) {
            $manage_login = HMWPP_Classes_Tools::saveUserMeta(self::USER_SESSION_EXPIRE, $this->gtmTimestamp(), $user_id);
        } elseif ( 'enable' === $action ) {
            $manage_login = HMWPP_Classes_Tools::saveUserMeta(self::USER_SESSION_EXPIRE, 'day', $user_id);
        }

        if ( $manage_login ) {
            return true;
        }

        return false;

    }

    /**
     * Delete all temporary logins
     *
     * @since 1.0.0
     */
    public function deleteTempLogins() {

        $users = $this->getTempUsers();

        if ( count( $users ) > 0 ) {
            foreach ( $users as $user ) {
                if ( $user instanceof WP_User ) {
                    $user_id = $user->ID;

                    wp_delete_user( $user_id ); // Delete User

                    // delete user from Multisite network too!
                    if ( is_multisite() ) {

                        // If it's a super admin, we can't directly delete user from network site.
                        // We need to revoke super admin access first and then delete user
                        if ( is_super_admin( $user_id ) ) {
                            revoke_super_admin( $user_id );
                        }

                        wpmu_delete_user( $user_id );
                    }
                }
            }
        }

    }

    /**
     * Send the log with the magic link
     *
     * @param $action
     *
     * @return void
     * @throws Exception
     */
    public function sendToLog($action) {
        if (HMWP_Classes_Tools::getOption('hmwp_activity_log')) {

            $values = array(
                'referer' => 'temporary_login',
            );

            HMWP_Classes_ObjController::getClass('HMWP_Models_Log')->hmwp_log_actions($action, $values);
        }
    }

}
