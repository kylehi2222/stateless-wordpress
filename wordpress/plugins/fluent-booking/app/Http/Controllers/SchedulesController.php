<?php

namespace FluentBooking\App\Http\Controllers;

use FluentBooking\App\App;
use FluentBooking\App\Models\Booking;
use FluentBooking\App\Models\Calendar;
use FluentBooking\App\Models\BookingActivity;
use FluentBooking\App\Models\Meta;
use FluentBooking\App\Services\EmailNotificationService;
use FluentBooking\App\Services\Helper;
use FluentBooking\App\Services\CurrenciesHelper;
use FluentBooking\Framework\Support\Arr;
use FluentBooking\Framework\Http\Request\Request;
use FluentBooking\App\Services\PermissionManager;
use FluentBooking\App\Services\CalendarService;

class SchedulesController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->get('filters', []);

        $period = Arr::get($filters, 'period', 'upcoming');

        $eventId = Arr::get($filters, 'event');

        $eventType = Arr::get($filters, 'event_type');

        $query = Booking::with(['calendar_event']);

        $author = Arr::get($filters, 'author');

        if ($author !== 'all') {
            $author = (int)$author;
        }

        if (!PermissionManager::userCanSeeAllBookings()) {
            $authorCalendar = Calendar::where('user_id', get_current_user_id())
                ->where('type', 'simple')
                ->first();

            if($authorCalendar) {
                $author = $authorCalendar->id;
            }
        }

        if ($author && $author !== 'all') {
            $query->where('calendar_id', $author);

            if ($eventId && $eventId !== 'all') {
                $query->where('event_id', $eventId);
            }

            if ($eventType && $eventType !== 'all') {
                $query->where('event_type', $eventType);
            }
        }

        do_action_ref_array('fluent_booking/schedules_query', [&$query]);

        $query->applyComputedStatus($period);

        if ($period == 'upcoming') {
            $query = $query->orderBy('start_time', 'ASC');
        } else if ($period == 'latest_bookings') {
            $query = $query->orderBy('created_at', 'DESC');
        } else if ($period == 'no_show') {
            $query = $query->orderBy('start_time', 'DESC');
        } else if ($period == 'latest_bookings') {
            $query = $query->orderBy('id', 'DESC');
        } else {
            $query = $query->orderBy('start_time', 'DESC');
        }

        $query->groupBy('group_id');

        $search = Arr::get($filters, 'search');

        if (!empty($search)) {
            $author = 'all';
            $query = $query->orderBy('start_time', 'DESC');
            $query = $query->searchBy($search);
        }

        $schedules = $query->paginate();

        foreach ($schedules as $schedule) {
            $this->formatBooking($schedule);
        }

        $data = [
            'schedules' => $schedules,
            'timezone'  => 'UTC'
        ];

        $data['calendar_event_lists'] = CalendarService::getCalendarOptionsByTitle();

        if ($author && $author != 'all') {
            $slotOptions = CalendarService::getSlotOptions($author);
            $data['slot_options'] = $slotOptions;
        }

        if ($request->get('page') == 1) {
            $pendingQuery = Booking::query()
                ->whereIn('status', ['pending', 'reserved'])
                ->distinct('group_id');
            if ($author && $author !== 'all') {
                $pendingCount = $pendingQuery->where('calendar_id', $author)->count('group_id');
            } else {
                $pendingCount = $pendingQuery->count('group_id');
            }

            $data['no_show_count'] = Booking::where('status', 'no_show')->count();
            $data['pending_count'] = $pendingCount;
            $data['cancelled_count'] = Booking::where('status', 'cancelled')->count();
        }

        return $data;
    }

    public function patchBooking(Request $request, $bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        $oldBooking = clone $booking;

        $data = $request->all();
        
        $this->validate($data, [
            'column' => 'required',
        ]);

        do_action('fluent_booking/before_patch_booking_schedule', $booking, $data);

        $value = $request->get('value');

        $column = $data['column'];

        if ($booking->{$column} == $value) {
            return $this->sendError(['message' => __('No changes found', 'fluent-booking')]);
        }

        $validColumns = [
            'internal_note',
            'email',
            'phone',
            'first_name',
            'last_name',
            'status'
        ];

        if (!in_array($column, $validColumns)) {
            return $this->sendError(['message' => __('Invalid column', 'fluent-booking')]);
        }

        if ($column === 'email') {
            if (!$value || !is_email($value)) {
                return $this->sendError(['message' => __('Invalid email address', 'fluent-booking')]);
            }
            $value = sanitize_email($value);
        } else {
            $value = sanitize_text_field($value);
        }

        if ($column == 'status') {
            if (!in_array($value, ['scheduled', 'completed', 'cancelled', 'rejected', 'no_show'])) {
                return $this->sendError(['message' => __('Invalid status', 'fluent-booking')]);
            }

            if ($value == 'scheduled' && $booking->payment_method && $booking->payment_order) {
                $order = $booking->payment_order;
                $order->total_paid = $order->total_amount;
                $order->completed_at = gmdate('Y-m-d H:i:s'); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
                $order->status = 'paid';
                $order->save();

                $updateData['payment_status'] = 'paid';

                do_action('fluent_booking/log_booking_activity', $this->getPaymentLog($booking->id));

                do_action('fluent_booking/payment/update_payment_status_paid', $booking);

            } else if ($value == 'scheduled') {
                do_action('fluent_booking/log_booking_activity', $this->getConfirmLog($booking->id));
            }

            if ($value == 'cancelled') {
                $cancelReason = sanitize_text_field($data['cancel_reason']);
                $booking->cancelMeeting($cancelReason, 'host', get_current_user_id());
                return [
                    'message' => __('The booking has been cancelled', 'fluent-booking')
                ];
            }

            if ($value == 'rejected') {
                $rejectReason = sanitize_text_field($data['reject_reason']);
                $booking->rejectMeeting($rejectReason, get_current_user_id());
                return [
                    'message' => __('The booking has been rejected', 'fluent-booking')
                ];
            }

            if ($booking->payment_method && Arr::get($data, 'refund_payment') == 'yes' && in_array($value, ['cancelled', 'rejected'])) {
                do_action('fluent_booking/refund_payment_' . $booking->payment_method, $booking, $booking->calendar_event);
            }
        }

        $updateData[$column] = $value;
        $booking->fill($updateData);
        $booking->save();

        if ($column === 'status') {
            do_action('fluent_booking/booking_schedule_' . $value, $booking, $booking->calendar_event);

            do_action('fluent_booking/pre_after_booking_' . $value, $booking, $booking->calendar_event);

            $booking = Booking::with(['calendar_event', 'calendar'])->find($booking->id);
        
            do_action('fluent_booking/after_booking_' . $value, $booking, $booking->calendar_event, $booking);
        }

        do_action('fluent_booking/after_patch_booking_schedule', $booking, $oldBooking);

        do_action('fluent_booking/after_patch_booking_' . $column, $booking, $booking->calendar_event, $oldBooking->{$column});

        return [
            /* translators: Updated column name */
            'message' => sprintf(__('%s has been updated', 'fluent-booking'), $column)
        ];
    }

    public function getBooking(Request $request, $bookingId)
    {
        $booking = Booking::with('calendar_event');

        if (!PermissionManager::userCanSeeAllBookings()) {
            $booking->whereHas('calendar', function ($q) {
                $q->where('user_id', get_current_user_id());
            });
        }

        $booking = $booking->findOrFail($bookingId);
        $booking = $this->formatBooking($booking);

        do_action_ref_array('fluent_booking/booking_schedule', [&$booking]);

        $data = [
            'schedule' => $booking
        ];

        if (in_array('all_data', $this->request->get('with', []))) {
            $data = array_merge($data, $this->getBookingMetaInfo($request, $bookingId));
        }

        return $data;
    }

    public function deleteBooking(Request $request, $bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        do_action('fluent_booking/before_delete_booking', $booking);

        $booking->delete();

        do_action('fluent_booking/after_delete_booking', $bookingId);

        if ($booking->isMultiGuestBooking()) {
            Booking::where('event_id', $booking->event_id)->where('group_id', $booking->group_id)->delete();
        }

        return [
            'message' => __('Booking Deleted Successfully!', 'fluent-booking')
        ];
    }

    public function sendConfirmationEmail(Request $request, $bookingId)
    {
        $booking = Booking::with(['calendar', 'calendar_event'])->find($bookingId);

        $emailTo = $request->get('email_to', 'guest');

        $notifications = $booking->calendar_event->getNotifications();

        $email = Arr::get($notifications, 'booking_conf_attendee.email', []);
        if ($emailTo == 'host') {
            $email = Arr::get($notifications, 'booking_conf_host.email', []);
        }

        $result = EmailNotificationService::emailOnBooked($booking, $email, $emailTo, 'scheduled', true);

        if (!$result) {
            return $this->sendError(['message' => __('Notification sending failed', 'fluent-booking')]);
        }

        return [
            'message' => __('Notification sent successfully', 'fluent-booking')
        ];
    }

    public function getGroupAttendees(Request $request, $groupId)
    {
        $booking = Booking::with('slot');

        if (!PermissionManager::userCanSeeAllBookings()) {
            $booking->whereHas('calendar', function ($q) {
                $q->where('user_id', get_current_user_id());
            });
        }

        $booking = $booking->where('group_id', $groupId)->first();

        if (!$booking || !$booking->isMultiGuestBooking()) {
            return $this->sendError(['message' => __('Invalid group id or the event is not a group event', 'fluent-booking')]);
        }

        $attendees = Booking::where('group_id', $booking->group_id);
        $search = sanitize_text_field($request->get('search'));

        if (!empty($search)) {
            $attendees = $attendees->searchBy($search);
        }
        $attendees = $attendees->paginate();

        foreach ($attendees as $attendee) {
            $attendee = $this->formatBooking($attendee);
        }

        return [
            'attendees' => $attendees
        ];
    }

    public function getBookingActivities(Request $request, $bookingId)
    {
        $activities = BookingActivity::where('booking_id', $bookingId)
            ->orderBy('id', 'DESC')
            ->get();

        return [
            'activities' => $activities
        ];
    }

    public function getBookingMetaInfo(Request $request, $bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        $activities = BookingActivity::where('booking_id', $booking->id)
            ->orderBy('id', 'DESC')
            ->get();

        $sidebarContents = [];
        $mainBodyContents = [];

        if (defined('FLUENTCRM')) {
            $profileHtml = fluentcrm_get_crm_profile_html($booking->email, false);
            if ($profileHtml) {
                $sidebarContents[] = [
                    'id'      => 'fluent_crm_profule',
                    'title'   => __('CRM Profile', 'fluent-booking'),
                    'content' => $profileHtml
                ];
            }
        }

        $order = null;
        if ($booking->payment_method && $booking->payment_order) {
            $order = $booking->payment_order;
            $order->load(['items', 'transaction']);
            $order->currency_sign = CurrenciesHelper::getCurrencySign($order->currency);
        }

        $mainBodyContents = apply_filters('fluent_booking/booking_meta_info_main_meta', $mainBodyContents, $booking);
        $mainBodyContents = apply_filters('fluent_booking/booking_meta_info_main_meta_' . $booking->source, $mainBodyContents, $booking);

        return [
            'activities'         => $activities,
            'sidebar_contents'   => $sidebarContents,
            'payment_order'      => $order,
            'main_body_contents' => $mainBodyContents
        ];
    }

    private function formatBooking(&$booking)
    {
        $autoCompleteTimeOut = (int) Helper::getGlobalAdminSetting('auto_complete_timing', 60) * 60; // 10 minutes

        if (in_array($booking->status, ['scheduled', 'pending']) && (time() - strtotime($booking->end_time)) > $autoCompleteTimeOut) {
            $bookingStatus = $booking->status == 'pending' ? 'cancelled' : 'completed';
            $booking->status = $bookingStatus;
            $booking->save();
            do_action('fluent_booking/booking_schedule_' . $bookingStatus, $booking, $booking->calendar_event);
        }

        if ($booking->isMultiHostBooking()) {
            $booking->host_profiles = $booking->getHostProfiles();
        }

        if ($booking->isMultiGuestBooking()) {
            $booking->booked_count = Booking::where('group_id', $booking->group_id)
                ->whereIn('status', ['scheduled', 'completed'])->count();
        } else {
            $booking->additional_guests = $booking->getAdditionalGuests();
        }

        $booking->title               = $booking->getBookingTitle(true);
        $booking->author              = $booking->getHostDetails(false);
        $booking->location            = $booking->getLocationDetailsHtml();
        $booking->reschedule_url      = $booking->getRescheduleUrl();
        $booking->happening_status    = $booking->getOngoingStatus();
        $booking->booking_status_text = $booking->getBookingStatus();
        $booking->payment_status_text = $booking->getPaymentStatus();
        $booking->custom_form_data    = $booking->getCustomFormData();

        do_action_ref_array('fluent_booking/format_booking_schedule', [&$booking]);

        return $booking;
    }

    private function getPaymentLog($bookingId)
    {
        return [
            'booking_id'  => $bookingId,
            'status'      => 'closed',
            'type'        => 'success',
            'title'       => __('Payment Successfully Completed', 'fluent-booking'),
            'description' => __('Payment marked as paid by admin', 'fluent-booking')
        ];
    }

    private function getConfirmLog($bookingId)
    {
        $confirmedBy = 'host';
        $userId = get_current_user_id();
        if ($userId && $user = get_user_by('ID', $userId)) {
            $confirmedBy = $user->display_name;
        }

        return [
            'booking_id'  => $bookingId,
            'status'      => 'closed',
            'type'        => 'success',
            'title'       => __('Booking Confirmed', 'fluent-booking'),
            'description' => __('Booking has been confirmed by ', 'fluent-booking') . $confirmedBy
        ];
    }
}
