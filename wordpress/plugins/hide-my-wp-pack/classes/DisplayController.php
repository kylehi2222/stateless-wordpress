<?php
/**
 * The class handles the theme part in WP
 *
 * @package HMWP/Display
 * @file The Display View file
 *
 */

defined('ABSPATH') || die('Cheatin\' uh?');

class HMWPP_Classes_DisplayController
{

    private static $cache;

    /**
     * echo the css link from theme css directory
     *
     * @param string $uri        The name of the css file or the entire uri path of the css file
     * @param array  $dependency
     *
     * @return void
     */
    public static function loadMedia($uri = '', $dependency = null)
    {
        $css_uri = '';
        $js_uri = '';

        if (HMWPP_Classes_Tools::isAjax()) {
            return;
        }

        if(!$dependency){
            $dependency = array('jquery');
        }

        //Initialize WordPress Filesystem
        $wp_filesystem = HMWPP_Classes_ObjController::initFilesystem();

        /* if is a custom css file */
        $name = strtolower($uri);
        $id = strtolower(_HMWPP_NAMESPACE_) . '_' . $name;

        if ($wp_filesystem->exists(_HMWPP_ASSETS_DIR_ . 'css/' . $name .'.min.css')) {
            $css_uri = _HMWPP_ASSETS_URL_ . 'css/' . $name . '.min.css?ver=' . HMWPP_VERSION;
        }
        if ($wp_filesystem->exists(_HMWPP_ASSETS_DIR_ . 'js/' . $name . '.min.js')) {
            $js_uri = _HMWPP_ASSETS_URL_ . 'js/' . $name . '.min.js?ver=' . HMWPP_VERSION;
        }

        if ($css_uri <> '') {
            if (!wp_style_is($id)) {
                wp_enqueue_style($id, $css_uri, false, HMWPP_VERSION);
            }
        }

        if ($js_uri <> '') {
            if (!wp_script_is($id)) {
                wp_enqueue_script($id, $js_uri, $dependency, HMWPP_VERSION, true);
            }
        }


        //check the main library also
        self::loadMainMedia($uri, $dependency);

    }

    /**
     * echo the css link from theme css directory
     *
     * @param string $uri        The name of the css file or the entire uri path of the css file
     * @param array  $dependency
     *
     * @return void
     */
    public static function loadMainMedia($uri = '', $dependency = null){

        //Check the HMWP library
        if(defined('_HMWP_NAMESPACE_') && defined('_HMWP_ASSETS_DIR_') && defined('_HMWP_ASSETS_URL_')){

            $css_uri = '';
            $js_uri = '';

            //Initialize WordPress Filesystem
            $wp_filesystem = HMWPP_Classes_ObjController::initFilesystem();

            /* if is a custom css file */
            $name = strtolower($uri);
            $id = strtolower(_HMWP_NAMESPACE_) . '_' . $name;

            if ($wp_filesystem->exists(_HMWP_ASSETS_DIR_ . 'css/' . $name .'.min.css')) {
                $css_uri = _HMWP_ASSETS_URL_ . 'css/' . $name . '.min.css?ver=' . HMWPP_VERSION;
            }
            if ($wp_filesystem->exists(_HMWP_ASSETS_DIR_ . 'js/' . $name . '.min.js')) {
                $js_uri = _HMWP_ASSETS_URL_ . 'js/' . $name . '.min.js?ver=' . HMWPP_VERSION;
            }

            if ($css_uri <> '') {
                if (!wp_style_is($id)) {
                    wp_enqueue_style($id, $css_uri, false, HMWPP_VERSION);
                }
            }

            if ($js_uri <> '') {
                if (!wp_script_is($id)) {
                    wp_enqueue_script($id, $js_uri, $dependency, HMWPP_VERSION, true);
                }
            }

        }

    }

    /**
     * return the block content from theme directory
     *
     * @param  string $block
     * @param  HMWPP_Classes_FrontController $view Used in the included file
     * @return null|string
     */
    public function getView($block, $view)
    {
        $output = null;

        //Initialize WordPress Filesystem
        $wp_filesystem = HMWPP_Classes_ObjController::initFilesystem();

        //Set the current view file from /view
        $file = _HMWPP_THEME_DIR_ . $block . '.php';

        if ($wp_filesystem->exists($file)) {
            ob_start();
            include $file;
            $output .= ob_get_clean();
        }

        return $output;
    }

}
