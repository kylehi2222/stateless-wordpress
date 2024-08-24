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
use WP_Post;

class Short_Child_Page_Mapping implements Mapping_Scenario_Interface {

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
		if ( $mapping_handler->matching_mapping_value || ! Frontend::get_instance()->short_child_page_urls ) {
			return null;
		}
		$mapping              = $mapping_handler->mapping;
		$request_params->path = str_replace( $mapping->path, '', $request_params->path );
		$matched_mapping_value = null;
		foreach ( $mapping_handler->mapping_values as $value ) {
			if ( $value->object_type == 'post' ) {
				$post = get_post( $value->object_id );
				if ( $post->post_type == 'page' ) {
					$child = $this->child_search( $post->ID, $request_params->path );
					if ( $child ) {
						$matched_mapping_value = $mapping_handler->prepare_value_instance( $child->ID, 'post' );
					}
				}
			}
		}

		return $matched_mapping_value;
	}

	/**
	 * Recursively search child pages
	 *
	 * @param int|null $parentId
	 * @param string|null $path
	 *
	 * @return array|int|WP_Post
	 */
	private function child_search( ?int $parentId, ?string $path ) {
		$children = get_children( array(
			'post_type'   => 'page',
			'post_parent' => $parentId
		) );

		foreach ( $children as $child ) {
			$permalink = get_permalink( $child->ID );
			if ( str_contains( $permalink, $path ) ) {
				return $child;
			} else {
				$nested_child = self::child_search( $child->ID, $path );
				if ( $nested_child ) {
					return $nested_child;
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
		if ( ! empty( $force_redirection_handler->object_id ) && $force_redirection_handler->object->post_type == 'page' && $force_redirection_handler->object->post_parent && Frontend::get_instance()->short_child_page_urls ) {
			$top_level_parent      = $force_redirection_handler->get_top_level_parent( $force_redirection_handler->object->ID );
			$parent_mapping_values = Mapping_Value::where( [ 'object_id' => $top_level_parent->ID ] );
			if ( ! empty( $parent_mapping_values ) ) {
				$mapping_value = $parent_mapping_values[0];
				$mapping       = Mapping::find( $mapping_value->mapping_id );
				$path          = $mapping->path == '' ? $request_params->path : ( $mapping->path . '/' . $request_params->path );
				$parent_path   = trim( wp_parse_url( get_permalink( $top_level_parent->ID ), PHP_URL_PATH ), '/' );
				$path          = trim( str_replace( $parent_path, '', $path ), '/' );
				$path          = str_replace( '//', '/', $path );

				return Helper::generate_url( $mapping->host, $path );
			}

		}

		return null;
	}
}