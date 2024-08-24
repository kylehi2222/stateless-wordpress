<?php

namespace FluentBooking\App\Hooks\Handlers;

use FluentBooking\App\Services\TimeSlotService;

class TimeSlotServiceHandler
{
    public static function initService($calendar, $calendarEvent)
    {
        if ($calendarEvent->isProEvent()) {
            if (!defined('FLUENT_BOOKING_PRO_DIR_FILE')) {
                return new \WP_Error('pro_plugin_required', __('Pro plugin is required for this event.', 'fluent-booking'));
            }

            if ($calendarEvent->isTeamEvent()) {
                return new \FluentBookingPro\App\Services\TeamTimeSlotService($calendar, $calendarEvent);
            }

            if ($calendarEvent->isOneOffEvent()) {
                return new \FluentBookingPro\App\Modules\SingleEvent\SingleTimeSlotService($calendar, $calendarEvent);
            }
        }

        return new TimeSlotService($calendar, $calendarEvent);
    }

    public static function sendError($error, $calendarEvent, $timeZone)
    {
        wp_send_json([
            'error'           => true,
            'message'         => $error->get_error_message(),
            'available_slots' => [],
            'timezone'        => $timeZone,
            'max_lookup_date' => $calendarEvent->getMaxLookUpDate()
        ], 200);
    }
}