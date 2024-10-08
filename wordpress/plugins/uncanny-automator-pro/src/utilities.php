<?php

namespace Uncanny_Automator_Pro;

/**
 *
 */
class Utilities {
	/**
	 * The instance of the class
	 *
	 * @since    3.1.0
	 * @access   public
	 * @var      Object
	 */
	public static $instance = null;

	/**
	 * Creates singleton instance of class
	 *
	 * @return Utilities $instance The Utilities Class
	 * @since 3.1.0
	 */
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * The references to autoloaded class instances
	 *
	 * @use get_autoloaded_class_instance()
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array
	 */
	private static $class_instances = array();

	/**
	 * The references to autoloaded class instances
	 *
	 * @use get_autoloaded_class_instance()
	 *
	 * @since    2.1.0
	 * @access   private
	 * @var      array
	 */
	private static $helper_instances = array();

	/**
	 * Set the main plugin file path
	 *
	 * @param string $class_name The name of the class instance
	 * @param object $class_instance The reference to the class instance
	 *
	 * @since    1.0.0
	 */
	public static function add_class_instance( $class_name, $class_instance ) {

		self::$class_instances[ $class_name ] = $class_instance;

	}

	/**
	 * Determine whether the class instance is available or not.
	 *
	 * @param mixed $class_name
	 *
	 * @return bool
	 */
	public static function has_class_instance( $class_name ) {

		return isset( self::$class_instances[ $class_name ] );

	}

	/**
	 * Returns an existing instance of the class.
	 *
	 * @param mixed $class_name
	 *
	 * @return object Instance of the parameter #1. Return type of this class is object so do declare the type of the instance for IDE support.
	 */
	public static function get_class_instance( $class_name ) {

		if ( self::has_class_instance( $class_name ) ) {
			return self::$class_instances[ $class_name ];
		}

		return null;
	}

	/**
	 * Get all class instances
	 *
	 * @return array
	 * @since    1.0.0
	 */
	public static function get_all_class_instances() {
		return self::$class_instances;
	}

	/**
	 * Set the main plugin file path
	 *
	 * @param string $integration The name of the class instance
	 * @param object $class_instance The reference to the class instance
	 *
	 * @since    1.0.0
	 */
	public static function add_helper_instances( $integration, $class_instance ) {

		self::$helper_instances[ $integration ] = $class_instance;

	}

	/**
	 * Get all class instances
	 *
	 * @return array
	 * @since    1.0.0
	 */
	public static function get_all_helper_instances() {
		return self::$helper_instances;
	}

	/**
	 * Returns the full url for the passed CSS file
	 *
	 * @param string $file_name
	 *
	 * @return string $asset_url
	 * @since    1.0.0
	 */
	public static function get_css( $file_name ) {
		return plugins_url( 'assets/css/' . $file_name, __FILE__ );
	}

	/**
	 * Returns the full url for the passed JS file
	 *
	 * @param string $file_name
	 *
	 * @return string $asset_url
	 * @since    1.0.0
	 */
	public static function get_js( $file_name ) {
		return plugins_url( 'assets/js/' . $file_name, __FILE__ );
	}

	/**
	 * Returns the full url for the passed vendor file
	 *
	 * @param string $file_name
	 *
	 * @return string $asset_url
	 * @since    1.0.0
	 */
	public static function get_vendor_asset( $file_name ) {
		return plugins_url( 'assets/vendor/' . $file_name, __FILE__ );
	}

	/**
	 * Returns the full url for the recipe UI dist directory
	 *
	 * @param string $file_name
	 *
	 * @return string $asset_url
	 * @since    1.0.0
	 */
	public static function get_recipe_dist( $file_name ) {
		return plugins_url( 'recipe-ui/dist/' . $file_name, __FILE__ );
	}

	/**
	 * Returns the full url for the passed Icon within recipe UI
	 *
	 * @param string $file_name
	 *
	 * @return string $asset_url
	 * @since    1.0.0
	 */
	public static function get_integration_icon( $file_name ) {
		return plugins_url( 'recipe-ui/dist/media/integrations/' . $file_name, __FILE__ );
	}

	/**
	 * Returns the full server path for the passed view file
	 *
	 * @param string $file_name
	 *
	 * @return string
	 */
	public static function get_view( $file_name ) {

		$views_directory = UAPro_ABSPATH . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR;

		/**
		 * Filters the director path to the view file
		 *
		 * This can be used for view overrides by modifying the path to go to a directory in the theme or another plugin.
		 *
		 * @param string $views_directory Path to the plugins view folder
		 * @param string $file_name The file name of the view file
		 *
		 * @since 1.0.0
		 */
		$views_directory = apply_filters( 'uapro_view_path', $views_directory, $file_name );

		return $views_directory . $file_name;
	}

	/**
	 * Returns the full server path for the passed include file
	 *
	 * @param string $file_name
	 *
	 * @return string
	 */
	public static function get_include( $file_name ) {

		$includes_directory = UAPro_ABSPATH . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;

		/**
		 * Filters the director path to the include file
		 *
		 * This can be used for include overrides by modifying the path to go to a directory in the theme or another plugin.
		 *
		 * @param string $includes_directory Path to the plugins include folder
		 * @param string $file_name The file name of the include file
		 *
		 * @since 1.0.0
		 */
		$includes_directory = apply_filters( 'uapro_includes_path_to', $includes_directory, $file_name );

		return $includes_directory . $file_name;
	}

	/**
	 * @param string $trace_message
	 * @param string $trace_heading
	 * @param false $force_log
	 * @param string $file_name
	 */
	public static function log( $trace_message = '', $trace_heading = '', $force_log = false, $file_name = 'logs' ) {
		if ( function_exists( 'automator_log' ) ) {
			automator_log( $trace_message, $trace_heading, $force_log, $file_name );
		}
		if ( class_exists( '\Uncanny_Automator\Utilities' ) ) {
			\Uncanny_Automator\Utilities::log( $trace_message, $trace_heading, $force_log, $file_name );
		}
	}

	/**
	 * Add UTM parameters to a given URL
	 *
	 * @param String $url URL
	 * @param array $medium The value for utm_medium
	 * @param array $content The value for utm_content
	 *
	 * @return String           URL with the UTM parameters
	 */
	public static function utm_parameters( $url, $medium = '', $content = '' ) {
		// utm_source=plugin-id
		// utm_medium=section-id
		// utm_content=element-id+unique-id

		$default_utm_parameters = array(
			'source' => 'uncanny_automator_pro',
		);

		try {
			// Parse the URL
			$url_parts = wp_parse_url( $url );

			// If URL doesn't have a query string.
			if ( isset( $url_parts['query'] ) ) {
				// Avoid 'Undefined index: query'
				parse_str( $url_parts['query'], $params );
			} else {
				$params = array();
			}

			// Add default parameters
			foreach ( $default_utm_parameters as $default_utm_parameter_key => $default_utm_parameter_value ) {
				$params[ 'utm_' . $default_utm_parameter_key ] = $default_utm_parameter_value;
			}

			// Add custom parameters
			if ( ! empty( $medium ) ) {
				$params['utm_medium'] = $medium;
			}

			if ( ! empty( $content ) ) {
				$params['utm_content'] = $content;
			}

			// Encode parameters
			$url_parts['query'] = http_build_query( $params );

			if ( function_exists( 'http_build_url' ) ) {
				// If the user has pecl_http
				$url = http_build_url( $url_parts );
			} else {
				$url_parts['path'] = ! empty( $url_parts['path'] ) ? $url_parts['path'] : '';

				$url = $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . '?' . $url_parts['query'];
			}
		} catch ( \Exception $e ) {
			automator_log( $e->getMessage() );
		}

		return $url;
	}


	/**
	 * Method get_action_completed_label
	 *
	 * @param string $status The status code.
	 *
	 * @return string The label of the corresponding label type.
	 */
	public static function get_action_completed_label( $status = '0' ) {

		if ( ! class_exists( '\Uncanny_Automator\Automator_Status' ) ) {
			return 'Undefined';
		}

		return \Uncanny_Automator\Automator_Status::name( $status );

	}

	/**
	 * By default WordPress generate an array of post meta if
	 * using get_post_meta without a key. This function will flatten
	 * the result.
	 *
	 * @param mixed[] $meta The post meta.
	 *
	 * @return mixed[]
	 */
	public static function flatten_post_meta( $meta = array() ) {
		return array_map(
			function( $item ) {
				if ( is_array( $item ) && isset( $item[0] ) ) {
					return (string) $item[0];
				}
				return '';
			},
			$meta
		);
	}
}
