<?php

use FluentBooking\App\Services\CurrenciesHelper;

/*
 * Remote calendars
 */
(new \FluentBookingPro\App\Services\Integrations\Calendars\RemoteCalendarsInit())->boot();

(new \FluentBookingPro\App\Services\Integrations\Twilio\Bootstrap())->register();
(new \FluentBookingPro\App\Services\Integrations\ZoomMeeting\Bootstrap())->register();
(new \FluentBookingPro\App\Services\Integrations\Webhook\WebhookIntegration())->register();

(new \FluentBookingPro\App\Modules\SingleEvent\SingleEvent())->register();

/*
 * Elementor
 */
if (defined('ELEMENTOR_VERSION')) {
    (new \FluentBookingPro\App\Services\Integrations\Elementor\ElementorIntegration())->register();
}

// WooCommerce
add_action('init', function () {
    if (defined('WC_PLUGIN_FILE')) {
        (new \FluentBookingPro\App\Services\Integrations\Woo\Bootstrap())->register();
    }
}, 1);
