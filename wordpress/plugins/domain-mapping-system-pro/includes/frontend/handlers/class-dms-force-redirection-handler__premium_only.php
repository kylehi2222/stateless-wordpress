<?php

namespace DMS\Includes\Frontend\Handlers;

use DMS\Includes\Frontend\Frontend;
use DMS\Includes\Frontend\Services\Request_Params;
use DMS\Includes\Utils\Helper;
use WP;
use WP_Post;
use WP_Term;

class Force_Redirection_Handler {

	/**
	 * Global wp variable
	 *
	 * @var mixed
	 */
	public ?WP $wp;

	/**
	 * The current queried object
	 *
	 * @var null|object
	 */
	public ?object $object;

	/**
	 * The current queried object id
	 *
	 * @var null|integer
	 */
	public ?int $object_id;

	/**
	 * The current object type
	 *
	 * @var null|string
	 */
	public ?string $object_type;

	/**
	 * The frontend class instance
	 *
	 * @var Frontend
	 */
	public Frontend $frontend;

	/**
	 * Taxonomies of the website
	 *
	 * @var array
	 */
	public array $taxonomies;

	/**
	 * Request params instance
	 *
	 * @var Request_Params
	 */
	public Request_Params $request_params;

	/**
	 * Constructor
	 *
	 * @param Request_Params $request_params Request params instance
	 * @param Frontend $frontend Frontend class instance
	 */
	public function __construct( Request_Params $request_params, Frontend $frontend ) {
		if ( $frontend->force_site_visitors ) {
			$this->frontend       = $frontend;
			$this->request_params = $request_params;
			$this->define_hooks();
		}
	}

	/**
	 * Define hooks
	 *
	 * @return void
	 */
	public function define_hooks():void {
		add_action( 'template_redirect', array( $this, 'init' ) );
	}

	/**
	 * Initialize
	 *
	 * @return void
	 */
	public function init(): void {
		if ( empty( $this->frontend->mapping_handler->mapped ) && get_queried_object() !== null && ! is_admin() ) {
			$this->set_properties();
			$uri = $this->frontend->mapping_scenarios->run_force_redirection_scenario__premium_only( $this, $this->request_params );
			if ( ! $this->prevent_redirection_loop( $uri ) ) {
				Helper::redirect_to( $uri );
			}
		}
	}

	/**
	 * Set properties
	 *
	 * @return void
	 */
	public function set_properties():void {
		global $wp;
		$this->wp          = $wp;
		$this->object      = get_queried_object();
		if ( $this->object instanceof WP_Term || $this->object instanceof WP_Post ) {
			$this->object_type = $this->object instanceof WP_Term ? 'term' : 'post';
			$this->object_id   = $this->object instanceof WP_Term ? $this->object->term_id : $this->object->ID;
			$this->taxonomies  = get_taxonomies();
		}
	}

	/**
	 * Check whether the current URL and the given URL are the same or not
	 *
	 * @param string|null $url The url to which the website should be redirected
	 *
	 * @return bool
	 */
	public function prevent_redirection_loop( ?string $url ): bool {
		// There are places where the $wp is not defined, that's why we use $_SERVER['REQUEST_URI']
		return $url == Helper::generate_url( $this->request_params->domain, trim( $_SERVER['REQUEST_URI'], '/' ) );
	}

	/**
	 * Gets the top level parent of the page
	 *
	 * @param int $post_id The id of the page
	 *
	 * @return WP_Post
	 */
	public function get_top_level_parent( int $post_id ): WP_Post {
		$post = get_post( $post_id );
		if ( $post && $post->post_parent ) {
			return $this->get_top_level_parent( $post->post_parent );
		}

		return $post;
	}
}
