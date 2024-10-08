<?php


class Cornerstone_Classic_Element_Manager extends Cornerstone_Plugin_Component {

	protected $class_prefix = 'CSE';
	protected $elements = array();
	protected $registered = false;

	protected $classicElements = [
		'mk2' => [ 'alert', 'block-grid', 'block-grid-item', 'column', 'icon-list', 'icon-list-item', 'pricing-table', 'pricing-table-column', 'row', 'section', 'text' ],
		'mk1' => ['accordion-item', 'accordion', 'author', 'blockquote', 'button', 'callout', 'card', 'clear', 'code', 'columnize', 'contact-form-7', 'counter', 'creative-cta', 'custom-headline', 'embedded-audio', 'embedded-video', 'envira-gallery', 'essential-grid', 'feature-box', 'feature-headline', 'feature-list-item', 'feature-list', 'gap', 'google-map-marker', 'google-map', 'gravity-forms', 'icon', 'image', 'layerslider', 'line', 'mailchimp', 'map-embed', 'promo', 'prompt', 'protect', 'raw-content', 'recent-posts', 'revolution-slider', 'search', 'self-hosted-audio', 'self-hosted-video', 'skill-bar', 'slide', 'slider', 'social-sharing', 'soliloquy', 'tab','tabs', 'text-type', 'toc-item', 'toc', 'visibility', 'widget-area' ],
	];

	public function setup() {

		if (
			! cornerstone("Permissions")->userCan("element-library.classic")
			&& ! cornerstone('ThemeManagement')->isClassic()
		) {
			return;
		}

		// Base class to remove PHP 8.2 deprecations
		require_once(cornerstone()->path . '/includes/elements/classic/BaseClassicElement.php');

		CS()->component('Control_Mixins');

		Cornerstone_Shortcode_Preserver::init();

		// Load Mk2 Classic Elements

		foreach ( $this->classicElements['mk2'] as $name ) {

			if ( strpos( $name, '_' ) === 0 )
				continue;

			$words = explode( '-', $name );

			foreach ($words as $key => $value) {
				$words[$key] = ucfirst($value);
			}

			$class_name = $this->class_prefix . '_' . implode( '_', $words );


			$this->add( $class_name, $name, cornerstone()->path . "/includes/elements/classic/$name" );

		}

		// Load Shortcodes
		// ---------------

		$path = cornerstone()->path . '/includes/shortcodes/';

		foreach ( glob("$path*.php") as $filename ) {

			if ( !file_exists( $filename) ) continue;

			$words = explode('-', str_replace('.php', '', basename($filename) ) );
			if ( strpos($words[0], '_') === 0 ) continue;

			require_once( $filename );

		}

		// Load Mk1 Classic Elements
		// -------------------------

		foreach ( $this->classicElements['mk1'] as $name ) {

      $filename = cornerstone()->path . "/includes/elements/classic/_alternate/$name.php";

      if ( !file_exists( $filename ) ) {
        continue;
      }

			require_once( $filename );

      $words = explode('-', $name );

			foreach ($words as $key => $value) {
				$words[$key] = ucfirst($value);
			}

			$class_name = 'CS_' . implode('_', $words);

			$element = $this->add_mk1_element( $class_name );
			$element->native = true;

		}

		// Register
		// --------

		$register_hooks = $this->enumerate_hooks( [
			'actions' => [
				'register' => 'register',
				'after_register' => 'after_register',
			],
			'filters' => [
				'register_shortcode' => 'register_shortcode',
				'shortcode_output_atts' => array(
					'cb' => 'shortcode_output_atts',
					'args' => 3
				),
				'flags' => 'flags',
				'defaults' => 'defaults',
				'controls' => 'controls',
				'update_controls' => 'update_controls',
				'is_active' => 'is_active',
				'update_defaults' => 'update_defaults'
			]
		]);

		do_action( 'cornerstone_register_elements' );

		foreach ( $this->elements as $element ) {
			$name = $element->name();
			if ( $element->version() != 'mk1' ) {
				$this->source_hooks( $register_hooks, "cs_element_{$name}_", $element->definition() );
			}
			$element->register();
		}

		$this->registered = true;

		do_action( 'cornerstone_shortcodes_loaded' );

		// Load
		// ----

		$load_hooks = $this->enumerate_hooks( [
			'filters' => [
				'ui' => 'ui',
				'preview' => array(
					'cb' => 'preview',
					'args' => 3
				),
				'should_have_markup' => array(
					'cb' => 'should_have_markup',
					'args' => 4
				),
				'update_build_shortcode_atts' => array(
					'cb' => 'update_build_shortcode_atts',
					'args' => 3
				),
				'update_build_shortcode_content' => array(
					'cb' => 'update_build_shortcode_content',
					'args' => 2
				),
				'always_close_shortcode' => 'always_close_shortcode'
			]
		]);

		do_action( 'cornerstone_load_elements' );

		foreach ( $this->elements as $element ) {
			$name = $element->name();
			if ( $element->version() != 'mk1' ) {
				$this->source_hooks( $load_hooks, "cs_element_{$name}_", $element->definition() );
			}
		}

	}

	public function add_mk1_element( $class_name ) {

		if ( !class_exists( $class_name ) )
			return false;

		$element = new $class_name();

		$error = $element->is_valid();
		if ( is_wp_error( $error ) ) {
			unset($element);
			trigger_error( 'Cornerstone_Legacy_Elements::add | Failed to add element: ' . $class_name . ' | ' . $error->get_error_message(), E_USER_WARNING );
			return false;
		}

		$data = $element->data();
		$name = ( isset( $data['name'] ) ) ? $data['name'] : '';

		$element = $this->add( $class_name, $name );

		return $element;

	}


	/**
	 * Takes a class name, instantiate it, and add it to our list of elements
	 * @param string $class_name Class name - the class must already be defined
	 * @param string $name Unique name fo
	 * @param string $path Class name - the class must already be defined
	 * @return  boolean true if the class exists and could be loaded
	 */

	public function add( $class_name, $name = '', $path = '' ) {

		if ( $path && file_exists( "$path/definition.php" ) ) {
			require_once( "$path/definition.php" );
		}

		if ( !class_exists( $class_name ) ) {
			trigger_error( "Cornerstone_Classic_Element_Manager::add | Failed to add element: $name. Class '$class_name' not found.", E_USER_WARNING );
			return false;
		}

		if ( $name == '' ) {
			trigger_error( "Cornerstone_Classic_Element_Manager::add | Failed to add element: $name. A unique name must be provided.", E_USER_WARNING );
			return false;
		}

		if ( isset( $this->elements[$name] ) ) {
			trigger_error( "Cornerstone_Classic_Element_Manager::add | Failed to add element: $name. An element with that name has already been registered.", E_USER_WARNING );
			return false;
		}


		$definition = new $class_name();

		if ( is_a( $definition, 'Cornerstone_Element_Base' ) ) { // mk1
			$element = $definition;
		} else { // mk2
			$element = new Cornerstone_Element_Wrapper( $name, trailingslashit( $path ), $definition, ( strpos( $class_name, $this->class_prefix) === 0 ) );
		}


		$error = $element->is_valid();
		if ( is_wp_error( $error ) ) {
			unset($element);
			trigger_error( "Cornerstone_Classic_Element_Manager::add | Failed to add element: $name. | " . $error->get_error_message(), E_USER_WARNING );
			return false;
		}

		$this->elements[$name] = $element;
		return $element;

	}

	/**
	 * Remove a previously defined element from our library
	 * @param  string $name The unique element name
	 * @return boolean  true if successful and the element formerly existed.
	 */
	public function remove( $name ) {
		if (isset($this->elements[$name])) {
			unset($this->elements[$name]);
			return true;
		}
		return false;

	}

	public function get( $name ) {
		return ( isset( $this->elements[$name] ) ) ? $this->elements[$name] : $this->elements['undefined'];
	}

	public function getModels() {

		$model_data = array();

		foreach ( $this->elements as $element ) {
			$data = $element->model_data();
			$model_data[$data['name']] = $data;
		}

		ksort($model_data);
		return array_values( $model_data );

	}

	public function elements() {
		return $this->elements;
	}

	public function enumerate_hooks( $args ) {

		$actions = isset( $args['actions'] ) ? $args['actions'] : [];
		$filters = isset( $args['filters'] ) ? $args['filters'] : [];
		$defaults = array( 'hook' => '', 'cb' => '', 'priority' => 10, 'args' => 1, 'op' => 'add_action' );

		$hooks = array();

		foreach ($actions as $key => $value) {
			if ( is_scalar( $value ) ) {
				$value = array( 'cb' => $value );
			}
			$item = array_merge( $defaults, $value );
			$item['hook'] = $key;
			$hooks[] = array_values( $item );
		}

		foreach ($filters as $key => $value) {
			if ( is_scalar( $value ) ) {
				$value = array( 'cb' => $value );
			}
			$item = array_merge( $defaults, $value );
			$item['op'] = 'add_filter';
			$item['hook'] = $key;
			$hooks[] = array_values( $item );
		}

		return $hooks;

	}

	public function source_hooks( $hooks, $prefix, $source ) {
		foreach ( $hooks as $hook => $item ) {
			if ( method_exists( $source, $item[1] ) ) {
				$item[0] = $prefix . $item[0];
				$item[1] = array( $source, $item[1] );
				$op = array_pop( $item );
				call_user_func_array( $op, $item );
			}
		}
	}
}
