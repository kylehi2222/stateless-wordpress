<?php

namespace Themeco\Cornerstone\API;

use function Cornerstone\Api\Controls\controls;

/**
 * Ready to setup
 */
add_action("after_setup_theme", function() {
  $values = array_merge(
    BUILTIN_VALUES,
    cs_api_extension_values()
  );

  /**
   * API Looper
   */
  cs_looper_provider_register("api", [
    'label' => __("External API", "cornerstone"),
    'controls' => controls(),

    'values' => $values,

    'loop_keys' => true,

    /**
     * Main
     */
    'filter' => function($result, $args) {
      return cs_api_run($args);
    },
  ]);

});
