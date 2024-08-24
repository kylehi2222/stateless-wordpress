<?php

/**
 * Yaml return type
 */
cs_api_register_return_type("yaml", [
  'label' => __("Yaml", "cornerstone"),
  'filter' => function($result) {
    return yaml_parse($result);
  },
]);
