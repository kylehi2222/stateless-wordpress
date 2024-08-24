<?php

namespace FluentBookingPro\App\Services\Integrations\Elementor;


use FluentBooking\App\Models\Calendar;

class ElementorIntegration
{
    public function register()
    {
        // Add new Elementor Categories
        add_action('elementor/init', [$this, 'addElementorCategory']);

        add_action('elementor/widgets/widgets_registered', [$this, 'registerWidget']);
        add_action('elementor/controls/controls_registered', [$this, 'registerControl']);


        add_action('elementor/editor/before_enqueue_scripts', [$this, 'editorScripts']);
        add_action('elementor/editor/after_enqueue_scripts', [$this, 'editorScripts']);

        add_action('wp_ajax_get_calendar_events', [$this, 'ajaxGetCalendarEvents']);
        add_action('wp_ajax_nopriv_get_calendar_events', [$this, 'ajaxGetCalendarEvents']);
    }

    public function editorScripts()
    {
        wp_enqueue_script('fcal-custom-elementor', plugin_dir_url(__FILE__) . 'fcal-custom-elementor.js', ['jquery'], time(), true);

        wp_localize_script('fcal-custom-elementor', 'fcal_elementor_ajax_object', array(
            'nonce'   => wp_create_nonce('calendar_events_nonce'),
            'ajaxurl' => admin_url('admin-ajax.php'),
        ));
    }


    private function getCalendarEvents($selectedCalId = '')
    {
        if (empty($selectedCalId)) {
            return [];
        }

        $calendar = Calendar::with(['events' => function ($query) {
            $query->where('status', 'active');
        }])->find($selectedCalId);

        if (!$calendar) {
            return [];
        }

        $formattedData = [];

        foreach ($calendar->events as $event) {
            $formattedData[$event->id] = $event->title;
        }

        return $formattedData;
    }

    public function addElementorCategory()
    {
        \Elementor\Plugin::instance()->elements_manager->add_category('fluentbooking', [
            'title' => __('FluentBooking', 'fluent-booking-pro'),
        ], 1);
    }

    /**
     * @throws \Exception
     */
    public function registerWidget()
    {
        $this->includeWidgets();
        $this->registerWidgets();
    }

    /**
     * @throws \Exception
     */
    public function includeWidgets()
    {
        $this->loadFile('/Widgets/FcalCalendarEvent.php');
        $this->loadFile('/Widgets/FcalBookings.php');
        $this->loadFile('/Widgets/FcalCalendar.php');
    }

    /**
     * @throws \Exception
     */
    public function registerControl($controls_manager)
    {
        $this->loadFile('/Controls/FluentBookingCustomGroupSelect.php');
        $this->registerFluentBookingCustomGroupSelect($controls_manager);
    }

    public function ajaxGetCalendarEvents() {
        if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'calendar_events_nonce')) {
            wp_send_json_error(['message' => __('Nonce verification failed', 'fluent-booking-pro')]);
            exit;
        }

        if (!isset($_POST['cal_id'])) {
            wp_send_json_error(['message' => __('No calendar ID provided', 'fluent-booking-pro')]);
        }

        $calId = intval($_POST['cal_id']);
        $events = []; // Fetch events using your getCalendarEvents method or similar

        // Example of fetching events (you need to replace this with your actual method)
        $events = $this->getCalendarEvents($calId);

        wp_send_json_success($events);
    }

    public function registerWidgets()
    {
        \Elementor\Plugin::instance()->widgets_manager->register(new \FluentBookingPro\App\Services\Integrations\Elementor\Widgets\FcalCalendarEvent());
        \Elementor\Plugin::instance()->widgets_manager->register(new \FluentBookingPro\App\Services\Integrations\Elementor\Widgets\FcalBookings());
        \Elementor\Plugin::instance()->widgets_manager->register(new \FluentBookingPro\App\Services\Integrations\Elementor\Widgets\FcalCalendar());
    }

    /**
     * @throws \Exception
     */
    private function loadFile($relativePath)
    {
        $filePath = __DIR__ . $relativePath;
        if (file_exists($filePath)) {
            require_once($filePath);
        } else {
            throw new \Exception(sprintf(__("File not found: %s", "fluent-booking-pro"), $filePath));
        }
    }

    private function registerFluentBookingCustomGroupSelect($controls_manager)
    {
        $control = new \FluentBookingCustomGroupSelect();
        \Elementor\Plugin::instance()->controls_manager->register($control, 'fcal_group_select');
    }

}
