<?php

namespace Themeco\Cornerstone\Services;

use Cornerstone_Looper_Provider_Custom;

class LooperProviders {

  const LOOPER_PROVIDER_PREFIX = "looper_provider_";
  const CUSTOM_LOOPER_PREFIX = "cs_looper_custom_";

  const PROVIDER_TYPE_KEY = 'looper_provider_type';
  const OMEGA_KEY = 'omega:looper-provider';

  private static $providers = [];
  private static $loopKeyTypes = [];

  public static function add($type, $provider = []) {
    if (!$type) {
      trigger_error('No valid type passed to LooperProviders::add : ' . json_encode($provider));
      return false;
    }

    $provider['type'] = $type;

    static::$providers[$type] = $provider;

    // Add filter for custom looper
    // if setup this
    // doesn't format - to _
    if (!empty($provider['filter']) && is_callable($provider['filter'])) {
      add_filter(
        static::CUSTOM_LOOPER_PREFIX . $type,
        $provider['filter'],
        0,
        3
      );
    }

    // Show loop key controls for this type
    if (!empty($provider['loop_keys'])) {
      static::$loopKeyTypes[] = $type;
    }

    // Extend Omega
    static::extendValues($provider);
  }

  /**
   * Is valid type
   */
  public static function isValid($type) {
    return !empty(static::$providers[$type]);
  }

  /**
   * Get provider data
   */
  public static function getProvider($type) {
    return static::isValid($type)
      ? static::$providers[$type]
      : null;
  }

  // Valid types that have a the loop_key option
  public static function loopKeyTypes() {
    return static::$loopKeyTypes;
  }

  public static function getProviderForLooper($type, $element = []) {
    $provider = static::getProvider($type);

    $values = cs_split_to_object(
      $element,
      static::LOOPER_PROVIDER_PREFIX . $type
    );

    $values = array_merge($provider['values'], $values);

    // Render dynamic content values prior to provider
    if (empty($provider['no_dynamic_content'])) {
      $values = cs_dynamic_content_object($values);
    }

    // Invalid class passed
    if (!empty($provider['class']) && !class_exists($provider['class'])) {
      trigger_error("Invalid class passed to looper type : " . $type);
    } else if (!empty($provider['class'])) {
      // Class looper
      $providerForLooper = new $provider['class']($values);
      return $providerForLooper;
    }

    // Uses custom looper internally
    // Passing in the control args as the
    return new Cornerstone_Looper_Provider_Custom($type, $values);
  }

  /**
   * Get built controls
   */
  public static function getControls() {
    $out = [];

    foreach (static::$providers as $type => $provider) {
      $out = array_merge($out, static::getControl($provider));
    }

    return $out;
  }

  /**
   * Get built control
   * will add a condition so these controls
   * only show up when the active provider is this
   */
  public static function getControl($provider) {

    // Get Provider
    $provider = is_string($provider)
      ? static::$providers[$provider]
      : $provider;

    if (!is_array($provider)) {
      trigger_error('Provider not valid in LoopProviders::getControl');
      return null;
    }

    // Get controls
    $controls = cs_get_array_value($provider, 'controls');

    // If function call it now
    if (is_callable($controls)) {
      $controls = $controls();
    }

    if (empty($controls)) {
      return [];
    }

    // Setup conditions
    $type = cs_get_array_value($provider, 'type');

    // Combined with other conditions
    // that could have been passed
    $conditions = [
      [
        static::PROVIDER_TYPE_KEY => $type,
      ]
    ];

    return static::formatControls($provider, $controls, $conditions);
  }

  /**
   * Format controls with proper key prefix
   */
  public static function formatControls($provider, $controls, $conditions = []) {
    // Build
    $out = [];

    foreach ($controls as $control) {
      // It's valid to pass null or empty
      if (empty($control)) {
        continue;
      }

      // Setup conditions
      $controlConditions = cs_get_array_value($control, 'conditions', []);
      $controlConditions = static::formatKeys($provider['type'], $controlConditions);
      $control['conditions'] = array_merge($controlConditions, $conditions);

      // Format main key
      if (isset($control['key'])) {
        $control['key'] = static::formatKey($provider['type'], $control['key']);
      }

      // Format keys {} object
      // This formats the value actually
      if (!empty($control['keys']) && is_array($control['keys'])) {
        // Loop keys
        foreach ($control['keys'] as $key => $val) {
          $formattedKey = static::formatKey($provider['type'], $val);
          $control['keys'][$key] = $formattedKey;
        }
      }


      // Group types
      // slightly different with no
      // top level condition needed
      if (!empty($control['controls'])) {
        $control['controls'] = static::formatControls($provider, $control['controls']);
      }

      $out[] = $control;
    }

    return $out;

  }

  // Format control keys with looper provider type prefix
  public static function formatKeys($type, $controls) {
    foreach ($controls as &$control) {
      // Invalids
      if (empty($control['key'])) {
        continue;
      }

      $control['key'] = static::formatKey($type, $control['key']);
    }

    return $controls;
  }

  // Format key with prefixes
  public static function formatKey($type, $key) {
    return static::LOOPER_PROVIDER_PREFIX . $type . '_' . $key;
  }

  /**
   * Get choices of providers as select
   * box choices, used by Omega
   *
   * @return array
   */
  public static function getChoices() {
    $out = [];

    foreach(static::$providers as $type => $config) {
      $out[] = [
        'value' => $type,
        'label' => $config['label'],
      ];
    }

    return $out;
  }

  /**
   * Extends the Omega controls so that element decoration
   * works properly
   *
   * @return void
   */
  private static function extendValues($provider) {

    $built = [];

    // Loop values
    foreach ($provider['values'] as $key => $value) {
      // Format
      $builtKey = static::formatKey($provider['type'], $key);

      // If array it assumes it was already passed through cs_value()
      $built[$builtKey] = is_array($value)
        ? $value
        : cs_value($value, 'markup');
    }

    cs_extend_values(static::OMEGA_KEY, $built);
  }
}
