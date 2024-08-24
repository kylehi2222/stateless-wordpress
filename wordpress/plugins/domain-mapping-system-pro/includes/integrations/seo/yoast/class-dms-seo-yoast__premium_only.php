<?php

namespace DMS\Includes\Integrations\SEO\Yoast;

use DMS\Includes\DMS;
use DMS\Includes\Integrations\SEO\Abstract_Seo;
use DMS\Includes\Data_Objects\Mapping;
use DMS\Includes\Data_Objects\Mapping_Value;
use DMS\Includes\Data_Objects\Setting;
use DMS\Includes\Freemius;
use DMS\Includes\Frontend\Frontend;
use DMS\Includes\Utils\Helper;
use Exception;

/**
 * Yoast Seo class
 *
 * @since 1.9.4
 *
 */
class Seo_Yoast extends Abstract_Seo {

	/**
	 * Sitemap index path
	 */
	const SITEMAP_FILENAME = 'sitemap_index.xml';

	/**
	 * Pattern for sitemap objects. Ex: post-sitemap.xml, page-sitemap2.xml, etc ...
	 */
	const SITEMAP_OBJECT_PATTERN = '/([^\/]+?)-sitemap([0-9]+)?\.xml$/';
	/**
	 * DMS metas to override yoast same ones
	 *
	 * @var array
	 */
	public static $dms_metas
		= [
			'title',
			'description',
			'keywords',
			'opengraph-title',
			'opengraph-description',
			'opengraph-image-id',
			'opengraph-image',
			'twitter-title',
			'twitter-description',
			'twitter-image-id',
			'twitter-image'
		];
	/**
	 * post_meta table meta_key prefix
	 *
	 * @var string
	 */
	public static $meta_prefix = '_dms_yoast_wpseo_';
	/**
	 * Yoast's settings form input name prefix
	 *
	 * @var string
	 */
	public static $form_prefix = 'dms_yoast_wpseo_';
	/**
	 * Separator to apply from the end
	 *
	 * @var string
	 */
	public static $domain_separator = '-';
	/**
	 * DMS instance to be used here
	 *
	 * @var
	 */
	protected $dms_instance;
	/**
	 * Holds the base path ( sitemap's part excluded )
	 *
	 * @var string
	 */
	protected $path_without_sitemap;
	/**
	 * Holds requested mapping
	 *
	 * @var object
	 */
	protected $mapping;
	/**
	 * Holds main mapping
	 *
	 * @var object
	 */
	protected $main_mapping;
	/**
	 * Flag for seo options
	 *
	 * @var string
	 */
	private $options_per_domain;
	/**
	 * Flag for sitemap per domain
	 *
	 * @var string
	 */
	private $sitemap_per_domain;
	/**
	 * Holds the information that sitemap will be overridden
	 *
	 * @var bool
	 */
	private $interacting_with_sitemap = false;

	public $fs;
	public $frontend;

	/**
	 * Constructor
	 */
	private function __construct( ) {
		$this->frontend           = Frontend::get_instance();
		$this->fs                 = Freemius::getInstance()->fs;
		$this->options_per_domain = Setting::find( 'dms_seo_options_per_domain' )->get_value();
		$this->sitemap_per_domain = Setting::find( 'dms_seo_sitemap_per_domain' )->get_value();
	}

	/**
	 * Get singleton instance.
	 *
	 * @return Seo_Yoast
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * For initialize admin, and main part.
	 *
	 * @return void
	 */
	public static function run() {
		$yoast = Seo_Yoast::get_instance();
		add_action( 'init', array( $yoast, 'init' ) );
		add_action( 'admin_init', array( $yoast, 'admin_init' ) );
	}

	/**
	 * For running sitemap part,
	 * and for running head changing part.
	 *
	 * @return void
	 */
	public function init() {
		if ( ! empty( $this->options_per_domain ) && ! is_admin() && ! empty( $this->frontend->mapping_handler ) ) {
			add_action( 'wp', array( $this, 'override_head' ), 11 );
		}
		if ( ! empty( $this->sitemap_per_domain ) ) {
			$this->run_sitemap();
		}
	}

	/**
	 * Adding an actions for saving a meta and for yoast tab generating  .
	 *
	 * @return void
	 */
	public function admin_init() {
		// Showing tabs always no matter option is active or no
		add_filter( 'yoast_free_additional_metabox_sections', function () {
			return $this->add_tab( 'post' );
		} );
		add_filter( 'yoast_free_additional_taxonomy_metabox_sections', function ( $arg1 ) {
			return $this->add_tab( 'taxonomy', $arg1 );
		}, 2 );
		// Avoid enqueueing in dms page
		if ( ! isset( $_GET['page'] ) || $_GET['page'] !== DMS::get_instance()->get_plugin_name() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		}
		// Save actions
		add_action( 'save_post', array( $this, 'save_meta_for_post' ) );
		add_action( 'edit_term_taxonomy', array( $this, 'save_meta_for_taxonomy' ) );
	}

	/**
	 * @return array[]
	 *
	 * This function for adding a tab in yoast seo, for domains.
	 */
	public function add_tab( $type, $arg1 = null ) {
		return [
			[
				'name'         => '_dms_yoast_wpseo_tab',
				'link_content' => '<span class="dashicons-before dashicons-admin-site-alt3" style="margin-right: 8px"></span>' . __( 'Domain Mapping', 'domain-mapping-system' ),
				'content'      => $this->get_tab_content( $type, $arg1 = null )
			]
		];
	}

	/**
	 * Get tab content
	 *
	 * @param $type
	 * @param $arg1
	 *
	 * @return false|string
	 */
	public function get_tab_content( $type, $arg1 = null ) {
		if ( method_exists( $this, 'get_' . $type . '_tab_content' ) ) {
			return $this->{'get_' . $type . '_tab_content'}( $arg1 );
		} else {
			ob_start();
			require_once DMS::get_instance()->get_plugin_dir_path() . '/templates/seo/yoast/settings.php';
			$content = ob_get_contents();
			ob_end_clean();

			return $content;
		}
	}

	/**
	 * Register and queue admin styles
	 *
	 * @return void
	 */
	public function admin_styles() {
		$dms_instance = DMS::get_instance();
		wp_register_style( 'dms-yoast-min-css', $dms_instance->get_plugin_dir_url() . 'assets/css/dms-yoast.min.css', array(), $dms_instance->get_version(), 'all' );
		wp_enqueue_style( 'dms-yoast-min-css' );
	}

	/**
	 * Register and queue admin scripts
	 *
	 * @return void
	 */
	public function admin_scripts() {
		$dms_instance = DMS::get_instance();
		/**
		 * Collect data to localize
		 * translations for JS
		 * premium flag
		 */
		$localize_data = array(
			'nonce'        => wp_create_nonce( 'dms_nonce' ),
			'scheme'       => Helper::get_scheme(),
			'ajax_url'     => admin_url( 'admin-ajax.php' ),
			'translations' => include_once $dms_instance->plugin_dir_path . 'assets/js/localizations/js-translations.php',
			'is_premium'   => 1,
			'upgrade_url'  => Freemius::getInstance()->fs->get_upgrade_url()
		);
		// Load media library
		wp_enqueue_media();
		// Register main js dependencies
		wp_register_script( 'dms-yoast-js', $dms_instance->get_plugin_dir_url() . 'assets/js/dms-yoast.js', array(
			'jquery'
		), $dms_instance->get_version() );
		wp_enqueue_script( 'dms-yoast-js' );
		// Include js data into dms-js
		wp_localize_script( 'dms-yoast-js', 'dms_yoast_fs', $localize_data );
	}

	/**
	 * Adding an actions for changing a head content. Options per domain should be enabled.
	 *
	 * @return void
	 */
	public function override_head() {
		if ( $this->frontend->mapping_handler->mapped ) {
			$host_and_path = Helper::get_host_plus_path( $this->frontend->mapping_handler->mapping );
			// Collect override data
			$this->data = $this->get_data( $host_and_path, $this->frontend->mapping_handler->matching_mapping_value->object_id );
			// Part for changing a <head> meta values.
			add_filter( 'wpseo_title', array( $this, 'change_title' ) );
			add_filter( 'wpseo_metadesc', array( $this, 'change_desc' ) );
			add_filter( 'wpseo_metakeywords', array( $this, 'change_keyword' ) );
			add_filter( 'wpseo_opengraph_title', array( $this, 'change_og_title' ) );
			add_filter( 'wpseo_opengraph_desc', array( $this, 'change_og_desc' ) );
			add_filter( 'wpseo_opengraph_image', array( $this, 'change_og_image' ) );
			add_filter( 'wpseo_opengraph_url', array( $this, 'change_og_url' ) );
			add_filter( 'wpseo_twitter_title', array( $this, 'change_twit_title' ) );
			add_filter( 'wpseo_twitter_description', array( $this, 'change_twit_desc' ) );
			add_filter( 'wpseo_twitter_image', array( $this, 'change_twit_image' ) );
			add_filter( 'wpseo_canonical', array( $this, 'change_canonical' ) );
		}
	}

	/**
	 *
	 * This function is filtering meta_data by ( domain + path ). $id is a value in dms_mapping_values, it can be a string. If $id is null all data will be a false.
	 *
	 * @param string $url
	 * @param string|int|null $id
	 * @param string $type
	 *
	 * @return array
	 */
	public function get_data( $url, $id, $type = null ) {
		if ( empty( $type ) ) {
			$type = is_tax() ? 'taxonomy' : 'post';
		}
		$data = [];
		if ( method_exists( $this, 'get_data_' . $type ) ) {
			$data = $this->{'get_data_' . $type}( $url, $id );
		}

		return $data;
	}

	/**
	 * Checks if
	 *
	 * @return void
	 */
	public function run_sitemap() {
		try {
			if ( ! empty( $this->sitemap_per_domain ) ) {
				// Path for sitemap_index.xml
				$path = $this->frontend->request_params->path;
				if ( ! empty( $path ) ) {
					if ( ! str_starts_with( $this->frontend->request_params->get_domain(), $this->frontend->request_params->get_base_host() ) ) {
						// Not required to mean that the host is mapped in our end.
						// Now check if path ends by sitemap
						if ( $this->frontend->request_params->path === self::SITEMAP_FILENAME ) {
							// Main sitemap requested without extra path.Seems to do nothing
							$this->path_without_sitemap = '';
						} elseif ( preg_replace( self::SITEMAP_OBJECT_PATTERN, '', $this->frontend->request_params->path ) === '' ) {
							// Object sitemap requested without extra path.Seems to do nothing
							$this->path_without_sitemap = '';
						} elseif ( Helper::ends_with( $this->frontend->request_params->path, '/' . self::SITEMAP_FILENAME ) ) {
							// Maybe our mapping [ host + path + 'sitemap_index.xml' ] requested.
							$this->path_without_sitemap = trim( str_replace( self::SITEMAP_FILENAME, '', $this->frontend->request_params->path ), '/' );

						} elseif ( preg_match( self::SITEMAP_OBJECT_PATTERN, $this->frontend->request_params->path ) ) {
							$this->path_without_sitemap = preg_replace( self::SITEMAP_OBJECT_PATTERN, '', $this->frontend->request_params->path );
						}
						// If we have any mapping with requested params, then move further
						if ( isset( $this->path_without_sitemap ) ) {
							$this->path_without_sitemap = trim( $this->path_without_sitemap, '/' );
							$mapping                    = Mapping::where( [ 'host' => $this->frontend->request_params->domain ] );
							$mapping                    = ! empty( $mapping ) ? $mapping[0] : null;
						}
						if ( ! empty( $mapping ) ) {
							$this->interacting_with_sitemap = true;
							$this->mapping                  = $mapping;
							$this->main_mapping             = Frontend::get_instance()->main_mapping;
							/**
							 * We have mapping connected with domain+path, then organize sitemap for it.
							 * 1. add_rewrite_rules
							 * 2. setup filters
							 */
							$this->setup_sitemap_filters();
						}
					} elseif ( ! empty( Frontend::get_instance()->force_site_visitors ) ) {
						$this->interacting_with_sitemap = true;
						$main_mapping       = Frontend::get_instance()->main_mapping;
						$this->main_mapping = ! empty( $main_mapping ) ? $main_mapping : false;
						// Rewrite each object entry in the sitemap
						add_filter( 'wpseo_sitemap_entry', function ( $url, $type, $object ) {
							return $this->rewrite_entry( $url, $type, $object );
						}, 10, 3 );
						/**
						 * First links. For post_type=page it is page_on_front.
						 * For any other post_type it is archive page.
						 */
						add_filter( 'wpseo_sitemap_post_type_first_links', function ( $links, $post_type ) {
							// TODO, later we could check if that certain links are mapped in our side and allow them.
							return array();
						}, 10, 2 );
					}
				}
			}
		} catch ( Exception $e ) {
			Helper::log( $e, __METHOD__ );
		}
	}

	/**
	 * This function for changing a stylesheet href,
	 * and for adding a filter for excluding a terms/posts by id.
	 *
	 * @return void
	 */
	public function setup_sitemap_filters() {
		// Overriding the xsl stylesheet url.
		add_filter( 'wpseo_stylesheet_url', function ( $stylesheet ){
			// TODO include path. along side putting xsl add_rewrite_rule
			// TODO more proper replace will be good. Regex including href="
			return str_replace( Helper::get_base_host(), $this->frontend->request_params->domain, $stylesheet );
		}, 90 );
		// Rewrite index links -> post-sitemap.xml, etc ...
		add_filter( 'wpseo_sitemap_index_links', array( $this, 'rewrite_index_links' ) );
		// Rewrite each object entry in the sitemap
		add_filter( 'wpseo_sitemap_entry', function ( $url, $type, $object ) {
			return $this->rewrite_entry( $url, $type, $object, true );
		}, 10, 3 );
		// Exclude objects without any mapping. Only if global current mapping is not main mapping and global mapping is disabled.
		if ( ! empty( $this->main_mapping->id ) && $this->mapping->id != $this->main_mapping->id ) {
			add_filter( 'wpseo_exclude_from_sitemap_by_post_ids', array( $this, 'exclude_sitemap_posts' ) );
			add_filter( 'wpseo_exclude_from_sitemap_by_term_ids', array( $this, 'exclude_sitemap_terms' ) );
		}
		/**
		 * First links. For post_type=page it is page_on_front.
		 * For any other post_type it is archive page.
		 */
		add_filter( 'wpseo_sitemap_post_type_first_links', function ( $links, $post_type ) {
			// TODO, later we could check if that certain links are mapped in our side and allow them.
			return array();
		}, 10, 2 );
	}

	/**
	 * Rewrite entry
	 *
	 * @param array $url
	 * @param string $type
	 * @param \WP_Post|\WP_Term|\WP_User $object
	 * @param bool $removeExisting
	 *
	 * @return array
	 */
	public function rewrite_entry( $url, $type, $object, $removeExisting = false ) {
		if ( $object instanceof \WP_Post ) {
			$key = $object->ID;
			$type = 'post';
		} elseif ( $object instanceof \WP_Term ) {
			$key = $object->term_id;
			$type = 'term';
		}
		// $key non-emptiness is must for proceeding further
		if ( ! empty( $key ) ) {
			$mapping_val = Mapping_Value::where(['object_id' => $key, 'object_type' => $type]);
			if (!empty($mapping_val)){
				$mapping = Mapping::find($mapping_val[0]->mapping_id);
			}
			if (  ! str_starts_with( $this->frontend->request_params->get_domain(), $this->frontend->request_params->get_base_host() )  ) {
				if ( empty( $mapping ) && ! empty( Frontend::get_instance()->global_domain_mapping ) && ! empty( $this->main_mapping ) && $this->main_mapping->id == $this->mapping->id ) {
					$mapping = $this->main_mapping;
				} elseif ( ! empty( $mapping ) && ! Frontend::get_instance()->global_domain_mapping && ! empty( $this->main_mapping ) && $this->main_mapping->id == $this->mapping->id && $this->main_mapping->id != $mapping->id ) {
					$mapping = null;
				}
			} elseif ( ! empty( $this->frontend->force_site_visitors ) ) {
				if ( empty( $mapping ) && ! empty( $this->frontend->global_domain_mapping ) && ! empty( $this->main_mapping ) ) {
					$mapping = $this->main_mapping;
				}
			}
			if ( ! empty( $mapping->host ) ) {
				$replace_with           = $mapping->host . ( ! empty( $mapping->path ) ? '/' . $mapping->path : '' );
				$url_loc_without_scheme = preg_replace( "~^(https?://)~i", '', $url['loc'] );
				if ( strpos( $url_loc_without_scheme, $replace_with ) !== 0 ) {
					$replaced   = true;
					$url['loc'] = str_ireplace( Helper::get_base_host(), $replace_with, $url['loc'] );
					if ( ! empty( $url['images'] ) ) {
						foreach ( $url['images'] as $key => &$value ) {
							$value['src'] = str_ireplace( Helper::get_base_host(), $replace_with, $value['src'] );
						}
					}
				}
			}
		}
		/**
		 * Designed to remove rows which has no connection with mapping.
		 * In case the page viewed with our mapping.
		 */
		if ( empty( $replaced ) && $removeExisting ) {
			return null;
		}

		return $url;
	}

	/**
	 * Function for changing an index_sitemap links.
	 * Changing a main url to current mapped host url.
	 *
	 * @param $links
	 *
	 * @return mixed
	 */
	public function rewrite_index_links( $links ) {
		$DMS = $this->dms_instance;
		if ( ! empty( $links ) ) {
			foreach ( $links as &$val ) {
				$val['loc'] = str_ireplace( Helper::get_base_host(), rtrim( $this->frontend->request_params->domain . '/' . ( ! empty( $this->path_without_sitemap ) ? $this->path_without_sitemap : '' ), '/' ), $val['loc'] );
			}
		}

		return $links;
	}

	/**
	 * This function for excluding a post types from sitemap which is not mapped.
	 *
	 * @return array
	 */
	public function exclude_sitemap_posts() {
		if ( ! empty( $this->mapping->id ) ) {
			$mapping_values = Mapping_Value::where( [ 'mapping_id' => $this->mapping->id, 'object_type' => 'post' ] );
			if ( is_array( $mapping_values ) ) {
				$ids      = [];
				$post_ids = [];
				foreach ( $mapping_values as $value ) {
					$ids [] = $value->object_id;
				}
				$posts = get_posts( [
					'numberposts'  => - 1,
					'post__not_in' => $ids,
				] );
				if ( ! empty( $posts ) ) {
					foreach ( $posts as $post ) {
						$post_ids [] = $post->ID;
					}
				}

				return $post_ids;
			}
		}

		return [];
	}

	/**
	 * This function for excluding a terms from sitemap which is not mapped.
	 *
	 * @return array
	 */
	public function exclude_sitemap_terms() {
		if ( ! empty( $this->mapping->id ) ) {
			$values = Mapping_Value::where( [ 'mapping_id' => (int) $this->mapping->id, 'object_type' => 'term' ] );
			if ( ! empty( $values ) ) {
				foreach ( $values as $value ) {
					$ids[] = $value->object_id;
				}
			}
			// If ids is empty, we should exclude all terms
			$args = [ 'hide_empty' => false, ];
			if ( ! empty( $ids ) ) {
				$args['exclude'] = $ids;
			}
			$terms = get_terms( $args );
			if ( ! empty( $terms ) ) {
				foreach ( $terms as $term ) {
					$term_ids [] = $term->term_id;
				}
			}

			return ! empty( $term_ids ) ? array_map( 'intval', $term_ids ) : [];
		}

		return [];
	}

	/**
	 * Get post tab content
	 *
	 * @param $arg1
	 *
	 * @return false|string
	 */
	public function get_post_tab_content( $arg1 ) {
		global $post;
		if ( ! empty( $post->ID ) ) {
			$mappings = Mapping::get_by_mapping_value( 'post', $post->ID);
			$data     = [];
			if ( ! empty( $mappings ) ) {
				foreach ( $mappings as $key => $item ) {
					// Get host + path as an url part. Only if $item is numeric array
					$host_path = Helper::get_host_plus_path( $item );
					// Get meta data
					$data[] = [
						'host_path' => $host_path,
						'meta_data' => $this->get_data_post( $host_path, $post->ID )
					];
				}
			}
			ob_start();
			require_once DMS::get_instance()->get_plugin_dir_path() . '/templates/seo/yoast/settings.php';
			$content = ob_get_contents();
			ob_end_clean();

			return $content;
		}

		return '';
	}

	/**
	 *
	 * This function for taking a dms-data if data object is post
	 *
	 * @param $url
	 * @param $id
	 *
	 * @return array
	 */
	public function get_data_post( $url, $id ) {
		$data = [];
		$url  = str_replace( '.', '_', $url );
		foreach ( self::$dms_metas as $value ) {
			$identifier     = self::$meta_prefix . $value . self::$domain_separator . $url;
			$data[ $value ] = get_post_meta( $id, $identifier, true );
		}

		return $data;
	}

	/**
	 * Get taxonomy tab content
	 *
	 * @param $arg1
	 *
	 * @return false|string
	 */
	public function get_taxonomy_tab_content( $arg1 ) {
		$current_screen = get_current_screen();
		global $tag_ID;
		if ( ! empty( $current_screen ) && is_a( $current_screen, 'WP_Screen' ) && ! empty( $current_screen->taxonomy ) && ! empty( $tag_ID ) ) {
			$DMS  = $this->dms_instance;
			$term = get_term( $tag_ID, $current_screen->taxonomy );
			// Check if posts category or cpt taxonomy
			$value = $term->term_id;
			$mappings = Mapping::get_by_mapping_value( 'term', $value );
			$data     = [];
			if ( ! empty( $mappings ) ) {
				foreach ( $mappings as $key => $item ) {
					// Get host + path as an url part. Only if $item is numeric array
					$host_path = Helper::get_host_plus_path( $item );
					// Get meta data
					$data[] = [
						'host_path' => $host_path,
						'meta_data' => $this->get_data_taxonomy( $host_path, $tag_ID )
					];
				}
			}
			ob_start();
			require_once DMS::get_instance()->get_plugin_dir_path() . '/templates/seo/yoast/settings.php';
			$content = ob_get_contents();
			ob_end_clean();

			return $content;
		}

		return '';
	}

	/**
	 *
	 * This function for taking a dms-data if data object is taxonomy
	 *
	 * @param string $url
	 * @param string $id
	 *
	 * @return array
	 */
	public function get_data_taxonomy( $url, $id ) {
		$data = [];
		$url  = str_replace( '.', '_', $url );
		foreach ( self::$dms_metas as $value ) {
			$identifier     = self::$meta_prefix . $value . self::$domain_separator . $url;
			$data[ $value ] = get_term_meta( $id, $identifier, true );
		}

		return $data;
	}

	/**
	 * Designed to save data from post editor
	 *
	 * @param int $post_id
	 *
	 * @return void
	 */
	public function save_meta_for_post( $post_id ) {
		$data = Mapping::get_by_mapping_value( 'post', $post_id );
		if ( ! empty( $data ) ) {
			foreach ( $data as $value ) {
				// Get host + path as an url part. Only if mapping is numeric array
				$host_and_path = Helper::get_host_plus_path( $value );
				// Replacing because post key value couldn't be white '.'.
				$host_and_path = str_replace( '.', '_', $host_and_path );
				foreach ( self::$dms_metas as $meta ) {
					$form_identifier = self::$form_prefix . $meta . self::$domain_separator . $host_and_path;
					$meta_identifier = self::$meta_prefix . $meta . self::$domain_separator . $host_and_path;
					if ( isset( $_POST[ $form_identifier ] ) ) {
						update_post_meta( $post_id, $meta_identifier, sanitize_text_field( $_POST[ $form_identifier ] ) );
					}
				}
			}
		}
	}

	/**
	 * Designed to save data from term editor
	 *
	 * @param $term_id
	 * @param $term_taxonomy
	 *
	 * @return void
	 */
	public function save_meta_for_taxonomy( $term_id, $term_taxonomy = null ) {
		// To get WP_Term instead term array( from the argument )
		$term  = get_term( $term_id );
		$value = $term->term_id;
		$data  = Mapping::get_by_mapping_value( 'term', $value );
		if ( ! empty( $data ) ) {
			foreach ( $data as $value ) {
				// Get host + path as an url part. Only if mapping is numeric array
				$host_and_path = Helper::get_host_plus_path( $value );
				// Replacing because post key value couldn't be white '.'.
				$host_and_path = str_replace( '.', '_', $host_and_path );
				foreach ( self::$dms_metas as $meta ) {
					$form_identifier = self::$form_prefix . $meta . self::$domain_separator . $host_and_path;
					$meta_identifier = self::$meta_prefix . $meta . self::$domain_separator . $host_and_path;
					if ( isset( $_POST[ $form_identifier ] ) ) {
						update_term_meta( $term_id, $meta_identifier, sanitize_text_field( $_POST[ $form_identifier ] ) );
					}
				}
			}
		}
	}

	/**
	 *
	 * Function for changing title in <head>
	 *
	 * @param string $title
	 *
	 * @return mixed
	 */
	public function change_title( $title ) {
		return ! empty( $this->data['title'] ) ? $this->data['title'] : $title;
	}

	/**
	 *
	 * Function for changing opengraph url
	 *
	 * @return string
	 */
	public function change_og_url( $url, $presentation = null ) {
		$host_and_path = Helper::get_host_plus_path( $this->frontend->mapping_handler->mapping );

		// Replacing only host, cause canonical contains our mapping path
		return str_ireplace( Helper::get_base_host(), $host_and_path, $url );
	}

	/**
	 *
	 * Function for changing canonical url
	 *
	 * @return string
	 */
	public function change_canonical( $canonical, $presentation = null ) {
		$host_and_path = Helper::get_host_plus_path( $this->frontend->mapping_handler->mapping );

		// Replacing only host, cause canonical contains the mapping path
		return str_ireplace( Helper::get_base_host(), $host_and_path, $canonical );
	}

	/**
	 * Function for changing meta description
	 *
	 * @param $desc
	 *
	 * @return mixed
	 */
	public function change_desc( $desc ) {
		return ! empty( $this->data['description'] ) ? $this->data['description'] : $desc;
	}

	/**
	 * Function for changing focusKeyword
	 *
	 * @param string $keyword
	 *
	 * @return mixed
	 */
	public function change_keyword( $keyword ) {
		return ! empty( $this->data['keywords'] ) ? $this->data['keywords'] : $keyword;
	}

	/**
	 * Function for changing facebook title.
	 *
	 * @param string $ogTitile
	 *
	 * @return mixed
	 */
	public function change_og_title( $ogTitile ) {
		return ! empty( $this->data['opengraph-title'] ) ? $this->data['opengraph-title'] : $ogTitile;
	}

	/**
	 *
	 * Function for changing facebook title.
	 *
	 * @param $ogDesc
	 *
	 * @return mixed
	 */
	public function change_og_desc( $ogDesc ) {
		return ! empty( $this->data['opengraph-description'] ) ? $this->data['opengraph-description'] : $ogDesc;
	}

	/**
	 *
	 * Function for changing opengraph image.
	 *
	 * @param string $image
	 *
	 * @return mixed
	 */
	public function change_og_image( $image ) {
		return ! empty( $this->data['opengraph-image'] ) ? $this->data['opengraph-image'] : $image;
	}

	/**
	 * Function for changing twitter title.
	 *
	 * @param $twit_title
	 *
	 * @return mixed
	 */
	public function change_twit_title( $twit_title ) {
		return ! empty( $this->data['twitter-title'] ) ? $this->data['twitter-title'] : $twit_title;
	}

	/**
	 * Function for changing twitter desc.
	 *
	 * @param $twit_desc
	 *
	 * @return mixed
	 */
	public function change_twit_desc( $twit_desc ) {
		return ! empty( $this->data['twitter-description'] ) ? $this->data['twitter-description'] : $twit_desc;
	}

	/**
	 *
	 * Function for changing twitter image.
	 *
	 * @param string $image
	 *
	 * @return mixed
	 */
	public function change_twit_image( $image ) {
		return ! empty( $this->data['twitter-image'] ) ? $this->data['twitter-image'] : $image;
	}

	/**
	 * Get flag for options per domain activity
	 *
	 * @return string
	 */
	public function get_options_per_domain() {
		return $this->options_per_domain;
	}

	/**
	 * Get flag for sitemap per domain activity
	 *
	 * @return string
	 */
	public function get_sitemap_per_domain() {
		return $this->sitemap_per_domain;
	}

	/**
	 * Checks weather sitemap requested
	 *
	 * @return bool
	 */
	public function is_sitemap_requested() {
		return  Helper::ends_with( $this->frontend->request_params->get_path(), '/' . self::SITEMAP_FILENAME )
		       || preg_match( self::SITEMAP_OBJECT_PATTERN, $this->frontend->request_params->get_path() );
	}

	/**
	 * @return bool
	 */
	public function is_interacting_with_sitemap() {
		return $this->interacting_with_sitemap;
	}

	public static function ends_with( $haystack, $needle ) {
		$length = strlen( $needle );
		if ( $length == 0 ) {
			return true;
		}

		return ( substr( $haystack, - $length ) === $needle );
	}
}