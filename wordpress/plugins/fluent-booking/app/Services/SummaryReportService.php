<?php

namespace FluentBooking\App\Services;

use FluentBooking\App\App;
use FluentBooking\App\Services\Helper;
use FluentBooking\App\Models\Booking;
use FluentBooking\Framework\Support\Arr;
use FluentBooking\App\Services\Libs\Emogrifier\Emogrifier;

class SummaryReportService
{
    public static function maybeSendSummary()
    {
        $notificationSettings = Helper::getGlobalSettings('administration');

        $currentDay = strtolower(gmdate('D'));

        $status     = Arr::get($notificationSettings, 'summary_notification');
        $frequency  = Arr::get($notificationSettings, 'notification_frequency');
        $adminEmail = Arr::get($notificationSettings, 'admin_email');
        $sendingDay = Arr::get($notificationSettings, 'notification_day');

        if ($status != 'yes' || ($frequency == 'weekly' && $sendingDay != $currentDay)) {
            return;
        }

        $reportDays = $frequency == 'daily' ? 1 : 7; 

        $reportDateFrom = gmdate('Y-m-d', time() - $reportDays * 60 * 60 * 24);

        $totalBooked = Booking::where('end_time', '>', $reportDateFrom)->count();
        $totalCompleted = Booking::where('created_at', '>', $reportDateFrom)->where('status', 'completed')->count();

        if (!$totalBooked && !$totalCompleted) {
            return;
        }

        $data = [
            'days'           => $reportDays,
            'frequency'      => $frequency,
            'totalBooked'    => $totalBooked,
            'totalCompleted' => $totalCompleted,
        ];

        $adminEmail = str_replace('{{wp.admin_email}}', get_option('admin_email'), $adminEmail);

        if(!$adminEmail) {
            return;
        }

        // translators: %d is replaced with the number of days
        $emailSubject = sprintf(esc_html__('Email Summary of Your Bookings (Last %d Days)', 'fluent-booking'), $reportDays);

        $emailBody = (string)App::make('view')->make('emails.summary_report', $data);

        $emogrifier = new Emogrifier($emailBody);
        $emogrifier->disableInvisibleNodeRemoval();
        $emailBbody = (string)$emogrifier->emogrify();

        return Mailer::send($adminEmail, $emailSubject, $emailBbody);
    }
}
