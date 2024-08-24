<?php

namespace FluentBooking\App\Services;

use FluentBooking\Framework\Support\Arr;
use FluentBooking\App\Models\Calendar;
use FluentBooking\App\Models\CalendarSlot;

class CalendarService
{
    public static function getSlotOptions($calendarId)
    {
        $calendarSlots = CalendarSlot::select(['id', 'title'])
            ->where('calendar_id', $calendarId)
            ->where('status', '!=', 'expired')
            ->latest()
            ->get();

        $options = [];
        foreach ($calendarSlots as $slot) {
            $options[] = [
                'id'    => $slot->id,
                'label' => $slot->title,
            ];
        }
        return apply_filters('fluent_booking/calendar_event_options', $options, $calendarId);
    }

    public static function getCalendarOptionsByHost()
    {
        $calendars = Calendar::select(['id', 'title'])
            ->when(!PermissionManager::hasAllCalendarAccess(true), function ($query) {
                return $query->where('user_id', get_current_user_id());
            })
            ->with(['slots' => function($query) {
                $query->where('status', '!=', 'expired');
            }])
            ->latest()
            ->get();

        $formattedCalendars = [];
        foreach ($calendars as $index => $calendar) {
            $slots = Arr::get($calendar, 'slots');
            if (!empty($slots)) {
                $options = [];
                foreach ($slots as $slot) {
                    $options[] = [
                        'label' => Arr::get($slot, 'title'),
                        'value' => Arr::get($slot, 'id')
                    ];
                }
                if (!empty($options)) {
                    $formattedCalendars[$index] = [
                        'label'   => Arr::get($calendar, 'title'),
                        'options' => $options
                    ];
                }
            }
        }
        return $formattedCalendars;
    }

    public static function getCalendarOptionsByTitle($condition = '')
    {
        $calendarQuery = Calendar::select(['id', 'title'])
            ->where('status', '!=', 'expired')
            ->when(!PermissionManager::hasAllCalendarAccess(true), function ($query) {
                return $query->where('user_id', get_current_user_id());
            });

        switch ($condition) {
            case 'only_hosts':
                $calendarQuery->where('type', 'simple');
                break;
            case 'only_teams':
                $calendarQuery->where('type', 'team');
                break;
            case 'only_events':
                $calendarQuery->where('type', 'event');
                break;
        }

        $calendars = $calendarQuery->with(['slots' => function($query) {
            $query->where('status', '!=', 'expired');
        }])->latest()->get();

        $formattedCalendars = [];
        foreach ($calendars as $index => $calendar) {
            $slots = Arr::get($calendar, 'slots');
            if (!empty($slots)) {
                $options = [];
                foreach ($slots as $slot) {
                    $options[] = [
                        'id'    => Arr::get($slot, 'id'),
                        'title' => Arr::get($slot, 'title')
                    ];
                }
                if (!empty($options)) {
                    $formattedCalendars[$index] = [
                        'id'      => Arr::get($calendar, 'id'),
                        'title'   => Arr::get($calendar, 'title'),
                        'options' => $options
                    ];
                }
            }
        }
        return apply_filters('fluent_booking/calendar_options_by_title', $formattedCalendars);
    }

    public static function updateCalendarEventsSchedule($calendarId, $oldTimezone, $updatedTimezone)
    {
        $calendarEvents = CalendarSlot::query()->where('calendar_id', $calendarId)->get();

        foreach ($calendarEvents as $event)
        {
            if ($weeklySchedule = Arr::get($event->settings, 'weekly_schedules', [])) {
                $originalSchedule = SanitizeService::weeklySchedules($weeklySchedule, 'UTC', $oldTimezone);
                $weeklySchedule = SanitizeService::weeklySchedules($originalSchedule, $updatedTimezone, 'UTC');
            }

            if ($dateOverride = Arr::get($event->settings, 'date_overrides', [])) {
                $originalOverride = SanitizeService::slotDateOverrides($dateOverride, 'UTC', $oldTimezone);
                $dateOverride = SanitizeService::slotDateOverrides($originalOverride, $updatedTimezone, 'UTC');
            }

            $event->settings = [
                'weekly_schedules' => $weeklySchedule,
                'date_overrides'   => $dateOverride
            ];

            $event->save();
        }
    }
}
