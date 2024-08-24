<?php

namespace Themeco\Cornerstone\Util;

use Themeco\Cornerstone\Vm\Constants;
use Themeco\Cornerstone\Util\Factory;

class Parameter {


  protected $type;
  protected $is_group;
  protected $initial;
  protected $is_list;
  protected $params;
  protected $isVar = false;
  protected $isResponsive = false;
  protected static $vars = [];
  protected static $responsiveVars = [];
  protected static $path = [];
  protected static $renderInFrame = [];

  public static function defineParametersForRender($data, $frame, $id = null) {
    if ( is_array( $data ) ) {
      foreach($data as $key => $value) {
        // Render before setting for static dc calls
        if (in_array($key, static::$renderInFrame)) {
          $value = cs_dynamic_content($value);
        }

        $frame->set(Constants::Parameter, $key, $value);
      }

      if ( $id && isset( $data['_bp_data'] ) && isset( $data['_bp_data']['_bp_var_keys'] ) ) {
        foreach ( $data['_bp_data']['_bp_var_keys'] as $path ) {
          $frame->set(Constants::ParameterCss, $path, $id);
        }
      }
    }
    // $frame->set(Constants::ParameterScope, 'source', $frame->id() );
  }

  public static function createFromSchema( $input, $name = '' ) {
    return Factory::create(self::class)->normalizeSchemaInput( $input, $name );
  }

  public static function create( $parameters, $types, $json ) {
    return self::createFromSchema( [
      'type' => 'group',
      'params' => is_string( $json ) ? json_decode($json, true) : $json
    ] );
  }

  public function parseShorthandInput( $str ) {
    $parts = explode("|", $str);

    $item = [
      'initial' => array_pop($parts)
    ];

    if (isset($parts[0])) {
      $item['type'] = $parts[0];
    }

    return $item;
  }

  public function normalizeSchemaInput( $input, $key = '' ) {

    if ( is_string($input) ) {
      return $this->normalizeSchemaInput($this->parseShorthandInput($input));
    }

    if ( !isset( $input['type'] ) ) {
      $input['type'] = 'text';
    }

    $this->is_list = strpos( $input['type'], '[]' ) === strlen( $input['type'] ) - 2;
    $input = ManagedParameters::transform($input, $input['type'], $this->is_list );
    $this->type = $input['type'];

    $this->is_group = $this->type === 'group';



    if ( $this->is_list ) {
      $this->type = str_replace('[]', '', $this->type );
    }

    if ( $this->is_list ) {
      $this->initial = isset( $input['initial'] ) && is_array( $input['initial'] ) ? $input['initial'] : [];
    } else if ( $this->is_group) {
      $this->initial = isset( $input['initial'] ) ? (object) $input['initial'] : new \stdClass();
    } else {
      $this->initial = isset( $input['initial'] ) ? $input['initial'] : '';

      $this->initial = cs_dynamic_content($this->initial);
    }


    if ( $this->type === 'group') {

      $this->params = new \stdClass();
      if ( isset( $input['params'] ) && is_array( $input['params'] ) ) {
        foreach ( $input['params'] as $key => $value ) {
          list($name, $flag) = $this->normalizeKey($key);
          if ($flag === '#') {
            $this->params->{$name} = self::createFromSchema( [ 'type' => 'group', 'params' => $value ], $name );
            continue;
          }
          if ($flag === '[]') {
            $this->params->{$name} = self::createFromSchema( [ 'type' => 'group[]', 'params' => $value ], $name );
            continue;
          }
          $this->params->{$name} = self::createFromSchema( $value, $name );
        }
      }
    } else {
      // Render in Frame, run dynamic content before setting variable
      // so it is static
      if (!empty($input['renderInFrame'])) {
        static::$renderInFrame[] = $key;
      }

      $this->isVar = isset( $input['isVar'] ) && $input['isVar'];
      $this->isResponsive = $this->isVar && ( !isset( $input['responsive'] ) || ! $input['responsive'] );
    }

    return $this;

  }

  public function applyGroup( $data ) {
    $result = [];

    foreach ($this->params as $key => $param) {

      $value = isset( $data[$key] ) ? $data[$key] : null;

      if ( is_null( $value ) && isset( $this->initial->{$key} ) ) {
        $value = $this->initial->{$key};
      }

      self::$path[] = $key;
      $result[$key] = $param->_apply( $value );
      array_pop(self::$path);
    }

    return $result;
  }

  // Take some stored component data and merge it with the parameters
  // This will make sure the data complies to the correct shape and has default values
  // Returns an associative array

  public function _apply( $data ) {

    if ( $this->is_list ) {

      $values = is_array( $data ) ? array_values( $data ) : [];

      $result = [];

      foreach ($values as $index => $item) {
        self::$path[] = $index;
        $result[] = $this->applyGroup($item);
        array_pop(self::$path);
      }

      return $result;

    }

    if ( $this->is_group ) {
      return $this->applyGroup( $data );
    }

    $value = $this->initializeValue( $data );

    if ($this->isVar) {
      $path = implode('.', self::$path);
      self::$vars[$path] = $value;
      if ($this->isResponsive) {
        self::$responsiveVars[] = $path;
      }
    }

    return $value;

  }

  // These values may contain Dynamic Content.
  // They should not be expanded here because at this stage we are just preparing data that may be used multiple times
  function initializeValue( $data ) {


    if ( $this->type === 'color-pair' ) {

      $_data = is_array( $data ) ? $data : [];

      if ( !empty($_data) ) {
        if (empty($_data['base'])) {
          unset($_data['base']);
        }
        if (empty($_data['alt'])) {
          $_data['alt'] = empty( $_data['base'] ) ? '' : $_data['base'];
        }
      }

      $colorPairValue = array_merge(
        is_array( $this->initial ) ? $this->initial : [],
        is_array( $_data ) ? $_data : []
      );

      if ( empty($colorPairValue['base']) ) {
        $colorPairValue['base'] = 'transparent';
      }

      if ( empty($colorPairValue['alt']) ) {
        $colorPairValue['alt'] = $colorPairValue['base'];
      }

      return $colorPairValue;

    }

    return is_null( $data ) ? $this->initial : $data;
  }

  // root invocation
  public function apply( $data ) {
    self::$vars = [];
    self::$responsiveVars = [];
    self::$path = [];

    $result = $this->_apply($data);

    if ( count(self::$vars) > 0 ) {

      $varKeys = array_keys(self::$vars);

      $result['_bp_data'] = self::$vars;

      $result['_bp_data']['_bp_var_keys'] = $varKeys;


      foreach ($data as $key => $value) {
        if (strpos($key, '_bp_' ) === 0 ) {
          $result['_bp_data'][$key] = $value;
        }
      }

    }

    return $result;
  }

  public function normalizeKey($key) {
    preg_match('/^([\w-]+)(#|\[\])?$/', $key, $matches);
    return [$matches[1], isset($matches[2]) ? $matches[2] : ''];
  }

}
