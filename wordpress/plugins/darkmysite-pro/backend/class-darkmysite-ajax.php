<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

if (!class_exists('DarkMySiteAdminAjax')) {
    class DarkMySiteAdminAjax
    {

        public $base_admin;

        public function __construct($base_admin)
        {
            $this->base_admin = $base_admin;

            add_action( 'wp_ajax_darkmysite_license_validate', array($this, 'darkmysite_license_validate') );
            add_action( 'wp_ajax_darkmysite_license_remove', array($this, 'darkmysite_license_remove') );
            add_action( 'wp_ajax_darkmysite_update_settings', array($this, 'darkmysite_update_settings') );
            add_action( 'wp_ajax_darkmysite_search_wp_posts', array($this, 'darkmysite_search_wp_posts') );
        }

        public function darkmysite_license_validate() {
            include_once DARKMYSITE_PRO_PATH . "backend/api/license_validate.php";
            wp_die();
        }

        public function darkmysite_license_remove() {
            include_once DARKMYSITE_PRO_PATH . "backend/api/license_remove.php";
            wp_die();
        }

        public function darkmysite_update_settings() {
            include_once DARKMYSITE_PRO_PATH . "backend/api/update_settings.php";
            wp_die();
        }

        public function darkmysite_search_wp_posts() {
            include_once DARKMYSITE_PRO_PATH . "backend/api/search_wp_posts.php";
            wp_die();
        }
        
    }
}
