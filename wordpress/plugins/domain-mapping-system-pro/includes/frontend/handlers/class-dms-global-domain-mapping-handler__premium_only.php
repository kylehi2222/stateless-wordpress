<?php

namespace DMS\Includes\Frontend\Handlers;

use DMS\Includes\Data_Objects\Mapping;
use DMS\Includes\Freemius;
use DMS\Includes\Frontend\Frontend;
use DMS\Includes\Frontend\Services\Request_Params;
use DMS\Includes\Utils\Helper;
use Exception;

class Global_Domain_Mapping_Handler {

	/**
	 * Request params instance
	 *
	 * @var Request_Params
	 */
	public Request_Params $request_params;

	/**
	 * Frontend Instance
	 *
	 * @var Frontend
	 */
	public Frontend $frontend;

	/**
	 * Flag for checking was there global domain mapping or not
	 *
	 * @var null|bool
	 */
	public ?bool $mapped = false;

	/**
	 * Force redirection handler instance
	 *
	 * @var Force_Redirection_Handler
	 */
	public Force_Redirection_Handler $force_redirection_handler;

	/**
	 * Freemius instance
	 *
	 * @var \Freemius|null
	 */
	public ?\Freemius $fs;

	/**
	 * Constructor
	 *
	 * @param Request_Params $request_params Request params instance
	 * @param Force_Redirection_Handler $force_redirection_handler Force redirection handler instance
	 * @param Frontend $frontend Frontend instance
	 */
	public function __construct( Request_Params $request_params, Force_Redirection_Handler $force_redirection_handler, Frontend $frontend ) {
		$this->request_params            = $request_params;
		$this->frontend                  = $frontend;
		$this->force_redirection_handler = $force_redirection_handler;
		$this->fs                        = Freemius::getInstance()->get_freemius();
	}

	/**
	 * Checks global mapping existence and redirects to the global mapping host
	 *
	 * @return void
	 */
	public function init(): void {
		try {
			if ( empty( $this->request_params->path )
			     || empty( $this->frontend->global_domain_mapping )
			     || is_admin()
			     || empty( $this->frontend->main_mapping )
			     || str_contains( $this->request_params->path, 'wp-admin' )
			     || str_contains( $this->request_params->path, 'wp-login' )
			     || str_contains( $this->request_params->path, 'wp-json' ) ) {
				return;
			}

			$mappings = Mapping::where( [ 'host' => $this->request_params->domain ] );
			if ( ! empty( $mappings ) ) {
				$mappings = array_values( array_filter( $mappings, function ( $item ) {
					return str_contains( $this->request_params->path, $item->path );
				} ) );

				if ( ! empty( $mappings ) && !empty($this->frontend->main_mapping) && $mappings[0]->id != $this->frontend->main_mapping->id ) {
					return;
				}
			}

			$main_mapping = $this->frontend->main_mapping;

			$real_path = str_starts_with( $this->request_params->path, (string) $main_mapping->path )
				? trim( Helper::str_replace_once( $main_mapping->path, '', $this->request_params->path ), '/' )
				: null;

			if ( $this->frontend->force_site_visitors ) {
				$req_params = ltrim( str_replace( $main_mapping->path, '', $this->request_params->path ), '/' );
				$path       = ! empty( $main_mapping->path ) ? rtrim( $main_mapping->path, '/' ) . '/' : '';
				$path       .= $req_params;

				$target_url  = $main_mapping->host . '/' . ltrim( $path, '/' );
				$current_url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

				if ( trim( $target_url, '/' ) !== trim( $current_url, '/' ) ) {
					$url = Helper::generate_url( $main_mapping->host, $path );
					Helper::redirect_to( $url );
				}
			}

			$_SERVER['PATH_INFO'] = $real_path;
			$this->mapped         = true;

			add_filter( 'get_site_icon_url', array( new Favicon_Handler( $main_mapping->attachment_id ), 'override' ) );
			add_action( 'wp_head', array( new Map_Html_Handler( $main_mapping->custom_html ), 'override' ) );
		} catch ( Exception $e ) {
			Helper::log( $e, __METHOD__ );
		}
	}
}
