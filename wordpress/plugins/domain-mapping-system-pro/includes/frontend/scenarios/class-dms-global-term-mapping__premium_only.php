<?php

namespace DMS\Includes\Frontend\Scenarios;

use DMS\Includes\Data_Objects\Mapping;
use DMS\Includes\Data_Objects\Mapping_Value;
use DMS\Includes\Exceptions\DMS_Exception;
use DMS\Includes\Frontend\Frontend;
use DMS\Includes\Frontend\Handlers\Force_Redirection_Handler;
use DMS\Includes\Frontend\Handlers\Mapping_Handler;
use DMS\Includes\Frontend\Services\Request_Params;
use DMS\Includes\Utils\Helper;

class Global_Term_Mapping implements Mapping_Scenario_Interface {

	/**
	 * Check the scenario and return the corresponding mapping value
	 * if not the following scenario return null
	 *
	 * @param Mapping_Handler $mapping_handler Mapping handler instance
	 * @param Request_Params $request_params Request params instance
	 *
	 * @return null|Mapping_Value
	 */
	function object_mapped( Mapping_Handler $mapping_handler, Request_Params $request_params ): ?Mapping_Value {
		if ( ! empty( $mapping_handler->matching_mapping_value ) || ! Frontend::get_instance()->global_archive_mapping ) {
			return null;
		}
		$mapping = $mapping_handler->mapping;
		foreach ( $mapping_handler->mapping_values as $value ) {
			$value->object_id = (int) $value->object_id;
			if ( $value->object_type === 'term' ) {
				$term      = get_term( $value->object_id );
				$post_type = get_taxonomy( $term->taxonomy )->object_type[0];
				$post_path = str_replace( $mapping->path, '', $request_params->path );
				$post_path = explode( '/', $post_path );
				$post_path = $post_path[ count( $post_path ) - 1 ];
				$post      = get_page_by_path( $post_path, OBJECT, $post_type );

				if ( ! empty( $post ) ) {
					$permalink = wp_parse_url( get_permalink( $post->ID ), PHP_URL_PATH );
					$permalink = trim( $permalink, '/' );
					if ( str_contains( $request_params->path, $permalink ) ) {
						return $mapping_handler->prepare_value_instance( $post->ID, 'post' );
					}
				}
			}
		}

		return null;
	}

	/**
	 * Check the force redirection scenario and redirect to the corresponding url
	 * otherwise return false
	 *
	 * @param Force_Redirection_Handler $force_redirection_handler Force redirection handler instance
	 * @param Request_Params $request_params Request Params instance
	 *
	 * @return null|string
	 * @throws DMS_Exception
	 */
	function force_redirection__premium_only( Force_Redirection_Handler $force_redirection_handler, Request_Params $request_params ): ?string {
		if ( $this->should_redirect( $force_redirection_handler ) ) {
			$categories = $this->get_all_terms( $force_redirection_handler->object_id );
			if ( ! empty( $categories ) ) {
				foreach ( $categories as $category ) {
					$mapping_values = Mapping_Value::where( [
						'object_id'   => $category->term_id,
						'object_type' => 'term'
					] );
					if ( ! empty( $mapping_values ) ) {
						$mapping_value = $mapping_values[0];
						$mapping       = Mapping::find( $mapping_value->mapping_id );
						$path          = trim( str_replace( $mapping->path, '', $request_params->path ), '/' );
						$path          = $mapping->path == '' ? $path : ( $mapping->path . '/' . $path );

						return Helper::generate_url( $mapping->host, $path );
					}
				}
			}
		}

		return false;
	}

	/**
	 * Check whether current parameters correspond
	 *
	 * @param $force_redirection_handler
	 *
	 * @return bool
	 */
	private function should_redirect( $force_redirection_handler ) {
		// Check if object_id is not empty
		if ( empty( $force_redirection_handler->object_id ) ) {
			return false;
		}

		// Check if the object_type is 'post'
		if ( $force_redirection_handler->object_type !== 'post' ) {
			return false;
		}

		// Check if there is a global archive mapping in the Frontend instance
		if ( ! Frontend::get_instance()->global_archive_mapping ) {
			return false;
		}

		// If all checks pass, return true
		return true;

	}

	/**
	 * Get all terms related to the post
	 *
	 * @param $post_id
	 *
	 * @return array
	 */
	private function get_all_terms( $post_id ): array {
		$taxonomies = get_object_taxonomies( get_post_type( $post_id ), 'objects' );
		$all_terms  = [];
		foreach ( $taxonomies as $taxonomy => $taxonomy_obj ) {
			$terms = get_the_terms( $post_id, $taxonomy );

			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				$all_terms = array_merge( $terms, $all_terms );
			}
		}

		return $all_terms;
	}
}