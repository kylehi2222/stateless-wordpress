<?php

//use Themeco\Cornerstone\Partials\range as csRange;

namespace Themeco\Cornerstone;

const RANDOM_GROUP = 'random';

add_action( 'cs_dynamic_content_register', function() {
  Random::register();
});

class Random {
  static public function register() {
    // Templater
    add_filter( 'cs_dynamic_content_' . RANDOM_GROUP, [ Random::class, 'supplyField' ], 10, 4 );

    // Setup
    add_action( 'cs_dynamic_content_setup', [ Random::class, 'setup' ] );
  }

  static public function setup() {
    // Register group
    cornerstone_dynamic_content_register_group([
      'name'  => RANDOM_GROUP,
      'label' => __( 'Random', CS_LOCALIZE ),
    ]);

    // Field Int
    cornerstone_dynamic_content_register_field([
      'name'  => 'integer',
      'group' => RANDOM_GROUP,
      'label' => __( 'Integer', CS_LOCALIZE ),
      'controls' => [
        [
          'key'     => 'min',
          'type'    => 'text',
          'label'   => __( 'Min', CS_LOCALIZE ),
          'options' => [ 'placeholder' => '0' ]
        ],
        [
          'key'     => 'max',
          'type'    => 'text',
          'label'   => __( 'Max', CS_LOCALIZE ),
          'options' => [ 'placeholder' => '10' ]
        ],
      ],
    ]);

    // Field Float
    cornerstone_dynamic_content_register_field([
      'name'  => 'float',
      'group' => RANDOM_GROUP,
      'label' => __( 'Float', CS_LOCALIZE ),
      'controls' => [
        [
          'key'     => 'min',
          'type'    => 'text',
          'label'   => __( 'Min', CS_LOCALIZE ),
          'options' => [ 'placeholder' => '0' ]
        ],
        [
          'key'     => 'max',
          'type'    => 'text',
          'label'   => __( 'Max', CS_LOCALIZE ),
          'options' => [ 'placeholder' => '1' ]
        ],
        [
          'key'     => 'decimals',
          'type'    => 'text',
          'label'   => __( 'Decimals', CS_LOCALIZE ),
          'options' => [ 'placeholder' => '2' ]
        ],
      ],
    ]);

    // Field Color
    cornerstone_dynamic_content_register_field([
      'name'  => 'color',
      'group' => RANDOM_GROUP,
      'label' => __( 'Color', CS_LOCALIZE ),
      'controls' => [
        // Red
        [
          'key' => 'rMin',
          'type'    => 'text',
          'label' => __('Red Min', CS_LOCALIZE),
          'options' => [ 'placeholder' => '0 (0 - 255)' ]
        ],
        [
          'key' => 'rMax',
          'type'    => 'text',
          'label' => __('Red Max', CS_LOCALIZE),
          'options' => [ 'placeholder' => '255 (0 - 255)' ]
        ],

        // Green
        [
          'key' => 'gMin',
          'type'    => 'text',
          'label' => __('Green Min', CS_LOCALIZE),
          'options' => [ 'placeholder' => '0 (0 - 255)' ]
        ],
        [
          'key' => 'gMax',
          'type'    => 'text',
          'label' => __('Green Max', CS_LOCALIZE),
          'options' => [ 'placeholder' => '255 (0 - 255)' ]
        ],

        // Blue
        [
          'key' => 'bMin',
          'type'    => 'text',
          'label' => __('Blue Min', CS_LOCALIZE),
          'options' => [ 'placeholder' => '0 (0 - 255)' ]
        ],
        [
          'key' => 'bMax',
          'type'    => 'text',
          'label' => __('Blue Max', CS_LOCALIZE),
          'options' => [ 'placeholder' => '255 (0 - 255)' ]
        ],

        [
          'key'     => 'opacityMax',
          'type'    => 'text',
          'label' => __('Opacity Max', CS_LOCALIZE),
          'options' => [ 'placeholder' => '1 (0 - 1)' ]
        ],
      ],
    ]);

    // Uniq ID
    cornerstone_dynamic_content_register_field([
      'name'  => 'uniqueid',
      'group' => RANDOM_GROUP,
      'label' => __( 'Unique ID', CS_LOCALIZE ),
      'controls' => [
        // Prefix
        [
          'key' => 'prefix',
          'type' => 'text',
          'label' => __('Prefix', CS_LOCALIZE),
        ],
      ],
    ]);

  }

  static public function randomMinMax($min, $max, $decimals = 2) {
    $powered = pow(10, max(1, $decimals));
    return rand($min, $max * $powered) / $powered;
      /// (mt_rand() / (getrandmax() + 1));
  }

  // Main templater
  static public function supplyField($result, $field, $args = []) {
    // Find which type
    switch ( $field ) {
      case 'integer':
        $min = (int)cs_get_array_value($args, 'min', 0);
        $max = (int)cs_get_array_value($args, 'max', 10);

        $result = rand($min, $max);
        break;

      case 'float':
      case 'number':
        $min = (float)cs_get_array_value($args, 'min', 0);
        $max = (float)cs_get_array_value($args, 'max', 10);

        $decimals = (int)cs_get_array_value($args, 'decimals', 2);

        // Random float math
        $result = static::randomMinMax($min, $max, $decimals);
        break;

      // Random RGB
      case 'color':
        $r = rand(
          (int)cs_get_array_value($args, 'rMin', 0),
          (int)cs_get_array_value($args, 'rMax', 255)
        );
        $g = rand(
          (int)cs_get_array_value($args, 'gMin', 0),
          (int)cs_get_array_value($args, 'gMax', 255)
        );
        $b = rand(
          (int)cs_get_array_value($args, 'bMin', 0),
          (int)cs_get_array_value($args, 'bMax', 255)
        );

        if (!empty($args['opacityMax'])) {
          $a = static::randomMinMax(0, $args['opacityMax']);
          $a = max(0, min($a, 1));
          $result = "rgba($r, $g, $b, $a)";
        } else {
          $result = "rgb($r, $g, $b)";
        }

        break;

      case 'uniqueid':
        $prefix = cs_get_array_value($args, 'prefix', '');
        $result = uniqid($prefix);
        break;
    }

    return $result;
  }
}

