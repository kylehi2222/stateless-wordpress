<?php

namespace Cornerstone\ControlPartials\SecondsSelect;

// Seconds selector
// small to large increments

cs_register_control_partial( 'seconds-select', function($settings = []) {

  // Output
  // ------
  $second = __("Second", "cornerstone");
  $seconds = __("Seconds", "cornerstone");

  $minute = __("Minute", "cornerstone");
  $minutes = __("Minutes", "cornerstone");

  $hour = __("Hour", "cornerstone");
  $hours = __("Hours", "cornerstone");

  $day = __("Day", "cornerstone");

  $week = __("Week", "cornerstone");

  $month = __("Month", "cornerstone");

  return array_merge(
    [
      'type'        => 'select',
      'label'       => __("Seconds", "cornerstone"),
      'options'    => [
        'choices' => [

          [
            'value' => '',
            'label' => __("Never", "cornerstone"),
          ],

          [
            'value' => 'once',
            'label' => __("Once", "cornerstone"),
          ],

          [
            'value' => 1,
            'label' => __("1 $second", "cornerstone"),
          ],

          [
            'value' => 5,
            'label' => __("5 $seconds", "cornerstone"),
          ],

          [
            'value' => 15,
            'label' => __("15 $seconds", "cornerstone"),
          ],

          [
            'value' => 30,
            'label' => __("30 $seconds", "cornerstone"),
          ],

          [
            'value' => 60,
            'label' => __("1 $minute", "cornerstone"),
          ],

          [
            'value' => 60 * 5,
            'label' => __("5 $minutes", "cornerstone"),
          ],

          [
            'value' => 60 * 10,
            'label' => __("10 $minutes", "cornerstone"),
          ],

          [
            'value' => 60 * 30,
            'label' => __("30 $minutes", "cornerstone"),
          ],

          [
            'value' => HOUR_IN_SECONDS,
            'label' => __("1 $hour", "cornerstone"),
          ],

          [
            'value' => HOUR_IN_SECONDS * 3,
            'label' => __("3 $hours", "cornerstone"),
          ],

          [
            'value' => HOUR_IN_SECONDS * 6,
            'label' => __("6 $hours", "cornerstone"),
          ],

          [
            'value' => HOUR_IN_SECONDS * 12,
            'label' => __("12 $hours", "cornerstone"),
          ],

          [
            'value' => DAY_IN_SECONDS,
            'label' => __("1 $day", "cornerstone"),
          ],

          [
            'value' => WEEK_IN_SECONDS,
            'label' => __("1 $week", "cornerstone"),
          ],

          [
            'value' => MONTH_IN_SECONDS,
            'label' => __("1 $month", "cornerstone"),
          ],

        ],
      ],
    ],
    $settings
  );
});
