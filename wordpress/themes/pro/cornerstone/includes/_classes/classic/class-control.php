<?php

class Cornerstone_Control {

	public $name;
	public $type;
	public $ui;
	public $options;
	public $context;
	public $suggest;
	public $priority;

	public $group;
	public $condition;
	public $key;
	public $controls;

	protected $default_context = 'all';
	protected $default_value = '';
	protected $default_options = array();
	protected static $control_classes;
	public static $kses_tags;

	final public static function factory( $name, $config ) {

		if ( isset( $config['mixin'] ) ) {
			return self::mixinFactory( $name, $config );
		}

		if ( isset( $config['group'] ) && $config['group'] === true ) {
			return self::groupFactory( $name, $config );
		}

		$config = wp_parse_args( $config, array(
			'type'      => 'text',
			'ui'        => array(),
			'options'   => array(),
			'context'   => null,
			'suggest'   => null,
			'condition' => array(),
			'priority'  => null,
			'group'     => null,
			'key'       => null,
		) );

		$class_name = self::getControlClass( $config['type'] );

		return new $class_name( $name, $config );

	}


	final public static function deep_array_merge( array $defaults, array $data, $max_depth = -1 ) {

		if ( 1 === $max_depth-- ) {
			return $defaults;
		}

		foreach ( $defaults as $key => $value ) {
			if ( isset( $data[ $key ] ) && is_array( $value ) && is_array( $data[ $key ] ) ) {
				$data[ $key ] = self::deep_array_merge( $value, $data[ $key ], $max_depth );
				continue;
			}
			$data[ $key ] = $value;
		}

		return $data;
	}

	final public static function mixinFactory( $name, $config = array() ) {

		$mixins = Cornerstone_Control_Mixins::get_mk2_mixins();

		$type = $config['mixin'];
		unset( $config['mixin'] );

		if ( !isset( $mixins[$type] ) )
			return new WP_Error( 'cornerstone', "Mixin '$type' not found." );

		// Set a maximium merge depth. This allows top level keys to be merged,
		// but allows full arrays to be overriden at the control option level.
		$depth = 3;

		// For groups, we need an additional level of merge depth
		if ( ( isset( $config['group'] ) && $config['group'] === true ) || ( isset( $mixins[$type]['group'] ) && $mixins[$type]['group'] === true ) )
			$depth = 4;

		$merged = self::deep_array_merge( $mixins[$type], $config, $depth );

		return self::factory( $name, $merged );

	}

	final public static function groupFactory( $name, $config ) {

		unset( $config['group'] );

		$mapped = array();

		foreach ($config as $key => $value) {
			$child_name = "{$name}_$key";
			$mapped[$child_name] = $value;
			$mapped[$child_name]['group'] = $name;
		}

		$controls = array();

		foreach ( $mapped as $key => $value) {
			$controls[] = self::factory( $key, $value );
		}

		return $controls;

	}

	final public static function getControlClass( $type ) {

		if ( !isset( self::$control_classes ) ) {
			self::$control_classes = apply_filters( 'cornerstone_control_types', array(
				'checkbox'     => 'Cornerstone_Control_Checkbox',
				'choose'       => 'Cornerstone_Control_Choose',
				'color'        => 'Cornerstone_Control_Color',
				'code-editor'  => 'Cornerstone_Control_Code_Editor',
				'date'         => 'Cornerstone_Control_Date',
				'dimensions'   => 'Cornerstone_Control_Dimensions',
				'editor'       => 'Cornerstone_Control_Editor',
				'icon-choose'  => 'Cornerstone_Control_Icon_Choose',
				'image'        => 'Cornerstone_Control_Image',
				'info'         => 'Cornerstone_Control_Info',
				'multi-choose' => 'Cornerstone_Control_Multi_Choose',
				'number'       => 'Cornerstone_Control_Number',
				'select'       => 'Cornerstone_Control_Select',
				'sortable'     => 'Cornerstone_Control_Sortable',
				'text'         => 'Cornerstone_Control_Text',
				'textarea'     => 'Cornerstone_Control_Textarea',
				'toggle'       => 'Cornerstone_Control_Toggle',
			) );
		}

		$class_name = 'Cornerstone_Control_Undefined';

		if ( isset(self::$control_classes[$type]) && class_exists( self::$control_classes[$type] ) ) {
			$class_name = self::$control_classes[$type];
		}

		return $class_name;

	}

	public function __construct( $name, $config ) {
		$this->name      = $name;
		$this->type      = $config['type'];
		$this->ui        = $config['ui'];
		$this->options   = wp_parse_args( $config['options'], $this->default_options );
		$this->context   = is_null( $config['context'] ) ? $this->default_context : $config['context'];
		$this->suggest   = $this->transformSuggestion( $config['suggest'] );
		$this->condition = $config['condition'];
		$this->priority  = $config['priority'];
		$this->group     = $config['group'];
		$this->key       = is_null( $config['key'] ) ? $this->name : $config['key'];

	}

	final public function model_data() {

		$model = array(
			'name'    => $this->name,
			'type'    => $this->type,
			'ui'      => $this->ui,
			'context' => $this->context
		);

		if ( !is_null( $this->key ) )
			$model['key'] = $this->key;

		if ( !empty( $this->options ) )
			$model['options'] = $this->options;

		if ( !empty( $this->condition ) )
			$model['condition'] = $this->expandConditions( $this->condition );

		if ( !is_null( $this->priority ) )
			$model['priority'] = $this->priority;

		if ( !is_null( $this->group ) )
			$model['group'] = $this->group;

		return $model;

	}

	public function expandConditions( $conditions = array() ) {

		$expanded = array();

		foreach ( $conditions as $key => $value ) {
			$new_key = str_replace( 'group::', "{$this->group}_", $key );
			$expanded[$new_key] = $value;
		}

		return $expanded;
	}

	public function transformSuggestion( $suggestion ) {
		return $suggestion;
	}

	public function transform_for_shortcode( $data ) {
		return $data;
	}

	public function sanitize( $data ) {
		return self::default_sanitize( $data );
	}


	// Used in cases where a control doesn't specify a sanitization method,
	// or a definition can not be identified
	public static function default_sanitize( $item, $key = '' ) {

		if ( is_bool( $item ) )
			return $item;

		if ( is_array( $item ) ) {
			$sanitized = array();
			foreach ($item as $key => $value) {
				$key = ( is_int( $key ) ) ? $key : sanitize_text_field( $key );
				$sanitized[$key] = self::default_sanitize( $value, $key );
			}
			return $sanitized;
		}

		if ( !is_scalar( $item ) || is_null( $item ) ) {
			return '';
    	}

		return (string) $item;
	}

	public static function kses_tags( ) {

		if ( !isset( self::$kses_tags ) ) {

			self::$kses_tags = wp_kses_allowed_html( 'post' );

			self::$kses_tags['iframe'] = array (
		    'align'       => true,
		    'frameborder' => true,
		    'height'      => true,
		    'width'       => true,
		    'sandbox'     => true,
		    'seamless'    => true,
		    'scrolling'   => true,
		    'srcdoc'      => true,
		    'src'         => true,
		    'class'       => true,
		    'id'          => true,
		    'style'       => true,
		    'border'      => true,
		    'list'        => true //YouTube embeds
			);

		}

		return self::$kses_tags;

	}

	public static function simplify_dimensions( $item ) {

		array_pop( $item ); // remove 'linked'

		// Single value (unlinked, but still equal)
		if ( count( array_unique( $item ) ) === 1 )
			return $item[0];

		// Vertical, Horizontal
		if ( ( $item[0] == $item[2] ) && ( $item[1] == $item[3] ) )
			return $item[0] . ' ' . $item[1];

		// Top, Left/Right, Bottom
		if ( $item[1] == $item[3] )
			return $item[0] . ' ' . $item[1] . ' ' . $item[2];

		// Unique sides
		return implode( ' ', $item );

	}

}

class Cornerstone_Control_Checkbox extends Cornerstone_Control {

	protected $default_value = false;

	public function sanitize( $item ) {

		if ( $item == 'true' )
			return true;

		if ( $item == 'false' )
			return false;

		return (bool) $item;
	}

	public function transform_for_shortcode( $item ) {
		return ( $item ) ? 'true' : 'false';
	}

}

class Cornerstone_Control_Choose extends Cornerstone_Control {

	protected $default_options = array(
		'columns' => 2,
		'choices' => array()
  );

}

class Cornerstone_Control_Code_Editor extends Cornerstone_Control {

	protected $default_context = 'content';

	public function sanitize( $data ) {
		return $data;
	}

}

class Cornerstone_Control_Color extends Cornerstone_Control {

	protected $default_context = 'design';

	protected $default_options = array(
		'output_format' => 'hsl'
  );

}

class Cornerstone_Control_Date extends Cornerstone_Control {

	protected $default_options = array(
		'choose_format' => true,
		'default_format'   => 'Do MMMM YYYY',
		'available_formats' => array(

			'Do MMMM YYYY',
			'MMMM Do YYYY',
			'MMMM D YYYY',

			'M/D/YYYY',
			'M-D-YYYY',
			'M.D.YYYY',

			'M/D/YY',
			'M-D-YY',
			'M.D.YY',

			'D/M/YYYY',
			'D-M-YYYY',
			'D.M.YYYY',

			'D/M/YY',
			'D-M-YY',
			'D.M.YY',

			'YYYY/M/D',
			'YYYY-M-D',
			'YYYY.M.D',
			'YYYY-MM-DD',
		),
		'delimiter' => '|'
	);

	public function sanitize( $data ) {
		return $data;
	}

}

class Cornerstone_Control_Dimensions extends Cornerstone_Control {

	protected $default_value = array( '0px', '0px', '0px', '0px', 'linked' );

	public function sanitize( $item ) {

		if ( !is_array( $item ) || count($item) != 5 )
			return $this->default_value;

		return array_map( 'sanitize_text_field', $item );

	}

	// Convert stored data into something usable in CSS
	public function transform_for_shortcode( $item ) {
		return self::simplify_dimensions( $item );
	}

}

class Cornerstone_Control_Editor extends Cornerstone_Control {
	protected $default_context = 'content';
	protected $default_options = array( 'expandable' => true );

	public function sanitize( $data ) {
		return $data;
	}

}

class Cornerstone_Control_Icon_Choose extends Cornerstone_Control {

	protected $default_options = array(
		'expandable'  => true,
	);

	protected $default_value = 'star';

}

class Cornerstone_Control_Image extends Cornerstone_Control {
	protected $default_context = 'content';
}

class Cornerstone_Control_Info extends Cornerstone_Control {
	protected $default_options = array(
		'help-text' => true,
		'heading' => '',
		'message' => ''
  );
}

class Cornerstone_Control_Multi_Choose extends Cornerstone_Control {

	protected $default_value = array();

	protected $default_options = array(
		'columns' => 2,
		'choices' => array(),
		'delimiter' => ' '
  );

  public function sanitize( $item ) {

		if ( !is_array( $item ) )
			return $this->default_value;

		return array_map( 'sanitize_text_field', $item );

	}

	public function transform_for_shortcode( $item ) {
		return implode($this->options['delimiter'], $item );
	}

}

class Cornerstone_Control_Number extends Cornerstone_Control {

}

class Cornerstone_Control_Select extends Cornerstone_Control {

	protected $default_options = array(
		'choices' => array()
  );

}

class Cornerstone_Control_Sortable extends Cornerstone_Control {

	protected $default_options = array(
		'element' => 'undefined',
		'title_field' => 'title'
	);

	public function sanitize( $items ) {
		return $items;
	}

	public function transformSuggestion( $suggestions = array() ) {

		if ( is_null( $suggestions ) )
			return $suggestions;

		if ( !is_array( $suggestions ) ) {
			trigger_error( 'Cornerstone_Control_Sortable: $suggestion must be an array', E_USER_WARNING );
			return array();
		}

		$items = array();

		foreach ($suggestions as $suggestion ) {

			if ( !isset( $suggestion['_type'] ) ) {
				$suggestion['_type'] = 'classic:' . $this->options['element'];
			}

			$items[] = $suggestion;

		}

		return $items;

	}

}

class Cornerstone_Control_Text extends Cornerstone_Control {

	protected $default_context = 'content';

	public function sanitize( $data ) {
		return $data;
	}

}

class Cornerstone_Control_Textarea extends Cornerstone_Control {
	protected $default_context = 'content';

	protected $default_options = array(
		'expandable'  => true,
		'placeholder' => '',
	);

	public function sanitize( $data ) {
		return $data;
	}

}

class Cornerstone_Control_Toggle extends Cornerstone_Control {

	protected $default_value = false;

	public function sanitize( $item ) {

		if ( $item == 'true' )
			return true;

		if ( $item == 'false' )
			return false;

		return (bool) $item;
	}

	public function transform_for_shortcode( $item ) {
		return ( $item ) ? 'true' : 'false';
	}

}

class Cornerstone_Control_Undefined extends Cornerstone_Control {

}
