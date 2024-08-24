<?php defined('ABSPATH') or die;

if (!function_exists('fluentwp_scheduler_register') && function_exists('add_action')) { // WRCS: DEFINED_VERSION.

    if (!class_exists('ActionScheduler_Versions', false)) {
        require_once FLUENT_BOOKING_DIR . 'vendor/woocommerce/action-scheduler/classes/ActionScheduler_Versions.php';
        add_action('plugins_loaded', array('ActionScheduler_Versions', 'initialize_latest_version'), 1, 0);
    }

    add_action('plugins_loaded', 'fluentwp_scheduler_register', 0, 0); // WRCS: DEFINED_VERSION.

    /**
     * Registers this version of Action Scheduler.
     */
    function fluentwp_scheduler_register()
    { // WRCS: DEFINED_VERSION.
        $versions = ActionScheduler_Versions::instance();
        $versions->register('3.6.9', 'fluentwp_scheduler_initialize'); // WRCS: DEFINED_VERSION.
    }

    /**
     * Initializes this version of Action Scheduler.
     */
    function fluentwp_scheduler_initialize()
    { // WRCS: DEFINED_VERSION.
        // A final safety check is required even here, because historic versions of Action Scheduler
        // followed a different pattern (in some unusual cases, we could reach this point and the
        // ActionScheduler class is already defined—so we need to guard against that).
        if (!class_exists('ActionScheduler', false)) {
            require_once FLUENT_BOOKING_DIR . 'vendor/woocommerce/action-scheduler/classes/abstracts/ActionScheduler.php';
            ActionScheduler::init(FLUENT_BOOKING_DIR . 'vendor/woocommerce/action-scheduler/action-scheduler.php');
        }
    }

    // Support usage in themes - load this version if no plugin has loaded a version yet.
    if (did_action('plugins_loaded') && !doing_action('plugins_loaded') && !class_exists('ActionScheduler', false)) {
        fluentwp_scheduler_initialize(); // WRCS: DEFINED_VERSION.
        do_action('action_scheduler_pre_theme_init');
        ActionScheduler_Versions::initialize_latest_version();
    }
}
