<?php

namespace Themeco\Cornerstone\API;

class Extension {

  public static $returnTypes = [];

  public static $requestTypes = [];

  public static function registerReturnType($type, $config = []) {
    $config['type'] = $type;

    static::$returnTypes[$type] = $config;
  }

  public static function registerRequestType($type, $config = []) {
    $config['type'] = $type;
    $config['controls'] = cs_get_array_value($config, 'controls', []);

    static::$requestTypes[$type] = $config;
  }

  /**
   * Filter used to api
   */
  public static function filterReturn($data, $type, $args = []) {
    if (empty(static::$returnTypes[$type]) || empty(static::$returnTypes[$type]['filter'])) {
      return $data;
    }

    return static::$returnTypes[$type]['filter']($data, $type, $args);
  }

  /**
   * Filter request then sent to the API
   * not run through cache
   *
   * @return string
   */
  public static function filterRequest($body, $type, $args) {
    if (empty(static::$requestTypes[$type]) || empty(static::$requestTypes[$type]['request_filter'])) {
      return $body;
    }

    return static::$requestTypes[$type]['request_filter']($body, $type, $args);
  }

  public static function getExtensionValues() {
    return array_merge(
      static::getExtensionTypeValues(static::$returnTypes),
      static::getExtensionTypeValues(static::$requestTypes)
    );
  }

  public static function getExtensionTypeValues($types) {
    $out = [];

    foreach ($types as $extension) {
      if (empty($extension['values'])) {
        continue;
      }

      $out = array_merge($out, $extension['values']);
    }

    return $out;
  }


  /**
   * Registered return types
   */
  public static function getReturnTypes() {
    return static::$returnTypes;
  }

  /**
   * Get return types for a select box
   */
  public static function getReturnTypesAsChoices() {
    $out = [
      static::getGlobalChoiceValue(),
    ];

    $types = static::getReturnTypes();

    foreach ($types as $type => $config) {
      $out[] = [
        'label' => empty($config['label'])
          ? $type
          : $config['label'],
        'value' => $type,
      ];
    }

    return $out;
  }

  public static function getReturnTypeControls() {
    $out = [
      [
        'key' => 'return_type',
        'label' => __("Return Type", "cornerstone"),
        'type' => 'select',
        'options' => [
          'choices' => static::getReturnTypesAsChoices(),
        ],
      ],
    ];

    $out = array_merge($out, static::getReturnTypeControlTypes());

    return $out;
  }


  public static function getReturnTypeControlTypes() {
    $out = [];

    foreach (static::$returnTypes as $type => $returnType) {
      if (empty($returnType['controls'])) {
        continue;
      }

      $out = array_merge(
        $out,
        static::getTypeControls($returnType['controls'], 'return_type', $type)
      );
    }

    return $out;

  }

  /**
   * Builds out request type controls based on extensions
   */
  public static function getRequestTypeControls() {
    $out = [
      [
        'key' => 'request_type',
        'label' => __("Request", "cornerstone"),
        'type' => 'select',
        'description' => __("The GET args for a GET request, or the form body on other methods", "cornerstone"),
        'options' => [
          'choices' => static::getRequestTypeChoices(),
        ],
      ],
    ];

    $out = array_merge($out, static::getRequestTypeControlTypes());

    return $out;
  }

  /**
   * Request type controls
   */
  public static function getRequestTypeControlTypes() {
    $out = [];

    foreach (static::$requestTypes as $type => $requestType) {
      if (empty($requestType['controls'])) {
        continue;
      }

      $out = array_merge(
        $out,
        static::getTypeControls($requestType['controls'], 'request_type', $type)
      );
    }

    return $out;
  }

  public static function getTypeControls($controls, $key, $type) {
    $out = [];

    foreach ($controls as $control) {
      // Setup conditions
      $conditions = cs_get_array_value($control, 'conditions', []);
      $control['conditions'] = array_merge(
        $conditions,
        [
          [
            'key' => $key,
            'op' => '==',
            'value' => $type,
          ],
        ]
      );

      $out[] = $control;
    }

    return $out;
  }

  // Request types as select choices
  public static function getRequestTypeChoices() {
    $out = [
      static::getGlobalChoiceValue(),
    ];

    foreach (static::$requestTypes as $type => $requestType) {
      $out[] = [
        'value' => $type,
        'label' => $requestType['label'],
      ];
    }

    return $out;
  }

  /**
   * Default empty value placeholder
   */
  public static function getGlobalChoiceValue() {
    return [
      'value' => '',
      'label' => __("Default (Global)", "cornerstone"),
    ];
  }
}
