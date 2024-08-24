<?php
/**
 * Handles the parameters and URLs
 *
 * @file The Tools file
 * @package HMWP/Tools
 * @since 1.0.0
 */

defined('ABSPATH') || die('Cheatin\' uh?');

class HMWPP_Classes_Tools
{

    /**
     * 
     * @var array Saved options in database
     */
    public static $init = array();
    public static $options = array();

    /**
     * HMWPP_Classes_Tools constructor.
     */
    public function __construct()
    {

        //Get the plugin options from database
        self::$options = self::getOptions();

        //Load multilingual
        add_action("init", array($this, 'loadMultilanguage'));

        //If it's admin panel
        if(is_admin() || is_network_admin()) {
            //Check the Plugin database update
            self::updateDatabase();
        }

    }

    /**
     * Load the Options from user option table in DB
     *
     * @return array
     */
    public static function getOptions()
    {

        self::$init = array(
            'hmwpp_ver' => 0,

            //Temporary Login
            'hmwp_templogin' => 0,
            'hmwp_templogin_role' => 'administrator',
            'hmwp_templogin_redirect' => false,
            'hmwp_templogin_delete_uninstal' => false,

            //Unique Login
            'hmwp_uniquelogin' => 0,
            'hmwp_uniquelogin_expires' => 'hour',
            'hmwp_uniquelogin_woocommerce' => 0,

            //2FA Login
            'hmwp_2falogin' => 0,
            'hmwp_2falogin_status' => 1,
            'hmwp_2fa_totp' => 1,
            'hmwp_2fa_email' => 0,
            'hmwp_2falogin_max_attempts' => 5,
            'hmwp_2falogin_max_timeout' => 900,
            'hmwp_2falogin_message' => '',
            'hmwp_2falogin_fail_message' => '',
            'hmwp_2falogin_delete_uninstal' => false,

        );

        if (self::isMultisites() && defined('BLOG_ID_CURRENT_SITE') ) {
            $options = json_decode(get_blog_option(BLOG_ID_CURRENT_SITE, HMWPP_OPTION), true);
        } else {
            $options = json_decode(get_option(HMWPP_OPTION), true);
        }

        //merge the option
        if (is_array($options) ) {
            $options = @array_merge(self::$init,  $options);
        }else{
            $options = self::$init;
        }

        if(!$options['hmwp_2falogin_message']){
            $options['hmwp_2falogin_message'] = esc_html__("ERROR: Too many invalid verification codes, you can try again in {time}.", 'hide-my-wp-pack');
        }
        if(!$options['hmwp_2falogin_fail_message']){
            $options['hmwp_2falogin_fail_message'] = esc_html__("WARNING: Your account has attempted to login {count} times without providing a valid code. The last failed login occurred {time} ago. If this wasn't you, please reset your password.", 'hide-my-wp-pack');
        }

        return $options;
    }

    /**
     * Get user metas
     *
     * @param  null $user_id
     * @return array|mixed
     */
    public static function getUserMetas($user_id = null)
    {
        if (!isset($user_id)) {
            $user_id = get_current_user_id();
        }

        return get_user_meta($user_id);
    }

    /**
     * Get use meta
     *
     * @param $key
     * @param null|int $user_id
     * @return mixed
     */
    public static function getUserMeta($key, $user_id = null)
    {
        if (!isset($user_id)) {
            $user_id = get_current_user_id();
        }

        return apply_filters('hmwpp_usermeta_' . $key, get_user_meta( $user_id, $key, true ));

    }

    /**
     * Save user meta
     *
     * @param $key
     * @param $value
     * @param null|int $user_id
     *
     * @return int|bool Meta ID if the key didn't exist, true on successful update,
     *                   false on failure or if the value passed to the function
     *                   is the same as the one that is already in the database.
     */
    public static function saveUserMeta($key, $value, $user_id = null)
    {
        if (!isset($user_id)) {
            $user_id = get_current_user_id();
        }

        if($dbvalue = self::getUserMeta($key, $user_id)){
            if($dbvalue === $value){
                return true;
            }
        }

        return update_user_meta($user_id, $key, $value);
    }

    /**
     * Delete User meta
     *
     * @param $key
     * @param null $user_id
     *
     * @return bool True on success, false on failure.
     */
    public static function deleteUserMeta($key, $user_id = null)
    {
        if (!isset($user_id)) {
            $user_id = get_current_user_id();
        }

        return delete_user_meta($user_id, $key);
    }

    /**
     * Update the plugin database with the last changed
     */
    private static function updateDatabase()
    {
        //On plugin update
        if(self::$options['hmwpp_ver'] < HMWPP_VERSION ) {
            self::$options['hmwpp_ver'] = HMWPP_VERSION;
            self::saveOptions();
        }
    }

    /**
     * Get the option from database
     *
     * @param $key
     *
     * @return mixed
     */
    public static function getOption( $key )
    {
        if (!isset(self::$options[$key]) ) {
            self::$options = self::getOptions();
        }

        if (isset(self::$options[$key]) ) {
            return apply_filters('hmwpp_option_' . $key, self::$options[$key]);
        }

        return false;
    }

    /**
     * Save the Options in user option table in DB
     *
     * @param string     $key
     * @param string     $value
     * @param bool|false $safe
     */
    public static function saveOptions( $key = null, $value = '' )
    {
        $keymeta = HMWPP_OPTION;

        if (isset($key) ) {
            self::$options[$key] = $value;
        }

        if (self::isMultisites() && defined('BLOG_ID_CURRENT_SITE') ) {
            update_blog_option(BLOG_ID_CURRENT_SITE, $keymeta, json_encode(self::$options));
        } else {
            update_option($keymeta, json_encode(self::$options));
        }
    }

    /**
     * Load the multilanguage support from .mo
     */
    public static function loadMultilanguage()
    {
        load_plugin_textdomain(dirname(HMWPP_BASENAME), false, dirname(HMWPP_BASENAME) . '/languages/');
    }

    /**
     * Check if it's Ajax call
     *
     * @return bool
     */
    public static function isAjax()
    {
        if (defined('DOING_AJAX') && DOING_AJAX ) {
            return true;
        }

        return false;
    }

    /**
     * Get the plugin settings URL
     *
     * @param string $page
     * @param string $relative
     *
     * @return string
     */
    public static function getSettingsUrl( $page = 'hmwp_settings', $relative = false )
    {
        if ($relative ) {
            return 'admin.php?page=' . $page;
        } else {
            if (!self::isMultisites() ) {
                return admin_url('admin.php?page=' . $page);
            } else {
                return network_admin_url('admin.php?page=' . $page);
            }
        }
    }


    /**
     * Get a value from $_POST / $_GET
     * if unavailable, take a default value
     *
     * @param string  $key           Value key
     * @param boolean $keep_newlines Keep the new lines in variable in case of texareas
     * @param mixed   $defaultValue  (optional)
     *
     * @return array|false|string Value
     */
    public static function getValue( $key = null, $defaultValue = false, $keep_newlines = false )
    {
        if (!isset($key) || $key == '' ) {
            return false;
        }

        //Get the parameters based on the form method
        //Sanitize each parameter based on the parameter type
        $ret = (isset($_POST[$key]) ? $_POST[$key] : (isset($_GET[$key]) ? $_GET[$key] : $defaultValue));

        if (is_string($ret) === true ) {
            if ($keep_newlines === false ) {
                if (in_array($key, array('hmwp_2falogin_message')) ) { //validate url parameter
                    $ret = preg_replace('/[^A-Za-z0-9-_\!.\s]/', '', $ret);
                }elseif (in_array($key, array('email', 'user_email')) ) { //validate url parameter
                    $ret = sanitize_email($ret);
                }else{
                    //Validate the param based on its type
                    $ret = preg_replace('/[^A-Za-z0-9-_.:\/]/', '', $ret); //validate fields
                }

                //Sanitize the text field
                $ret = sanitize_text_field($ret);

            } else {

                //Validate the textareas
                $ret = preg_replace('/[^A-Za-z0-9-_.*#\n\r\s\/]@/', '', $ret);

                //Sanitize the textarea
                if (function_exists('sanitize_textarea_field') ) {
                    $ret = sanitize_textarea_field($ret);
                }
            }
        }

        //Return the unsplas validated and sanitized value
        return wp_unslash($ret);
    }

    /**
     * Check if the parameter is set
     *
     * @param string $key
     *
     * @return boolean
     */
    public static function getIsset( $key = null )
    {
        if (!isset($key) || $key == '' ) {
            return false;
        }

        return isset($_POST[$key]) || isset($_GET[$key]);
    }

    /**
     * Check if multisites
     *
     * @return bool
     */
    public static function isMultisites()
    {
        return is_multisite();
    }

    /**
     * Check if multisites with path
     *
     * @return bool
     */
    public static function isMultisiteWithPath()
    {
        return (is_multisite() && ((defined('SUBDOMAIN_INSTALL') && !SUBDOMAIN_INSTALL) || (defined('VHOST') && VHOST == 'no')));
    }


    /**
     * Check whether the plugin is active by checking the active_plugins list.
     *
     * @source wp-admin/includes/plugin.php
     *
     * @param string $plugin Plugin folder/main file.
     *
     * @return boolean
     */
    public static function isPluginActive( $plugin )
    {
        return HMWP_Classes_Tools::isPluginActive( $plugin );
    }

    /**
     * Check whether the theme is active.
     *
     * @param string $name Theme folder/main file.
     *
     * @return boolean
     */
    public static function isThemeActive( $name )
    {
        return HMWP_Classes_Tools::isThemeActive( $name );

    }

    /**
     * Called on plugin activation
     *
     * @throws Exception
     */
    public function hmwpp_activate()
    {

    }

    /**
     * Called on plugin deactivation
     * Remove all the rewrite rules on deactivation
     *
     * @throws Exception
     */
    public function hmwpp_deactivate()
    {

    }

    /**
     * Check the user capability for the roles attached
     *
     * @param  $cap
     * @return bool
     */
    public static function userCan( $cap )
    {
        return HMWP_Classes_Tools::userCan( $cap );
    }

    /**
     * Search part of string in array
     *
     * @param string $needle
     * @param array $haystack
     *
     * @return bool
     */
    public static function searchInString( $string, $haystack )
    {
        return HMWP_Classes_Tools::searchInString( $string, $haystack );
    }


    /**
     * Instantiates the WordPress filesystem
     *
     * @static
     * @access public
     * @return WP_Filesystem_Base|WP_Filesystem_Direct
     */
    public static function initFilesystem()
    {
        return HMWP_Classes_Tools::initFilesystem();
    }

    /**
     * Generate a string
     *
     * @param  int $length
     * @return bool|string
     */
    public static function generateRandomString( $length = 10 )
    {
        return substr(str_shuffle(str_repeat($x = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }

    /**
     * Check if it's Hide My WP Ghost installed and has the compatible version
     * @return bool
     */
    public static function isHideMyWPGhostInstalled()
    {
        return (defined('HMWP_VERSION') && version_compare(HMWP_VERSION, HMWP_VERSION_MIN, '>='));
    }

    /**
     * Check if there are whitelisted IPs for accessing the hidden paths
     * @return bool
     */
    public static function isWhitelistedIP($ip){
        return HMWP_Classes_Tools::isWhitelistedIP($ip);
    }


}
