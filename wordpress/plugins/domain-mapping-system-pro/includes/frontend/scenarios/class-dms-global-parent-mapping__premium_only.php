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

class Global_Parent_Mapping implements Mapping_Scenario_Interface {

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
		if ( $mapping_handler->matching_mapping_value || ! Frontend::get_instance()->global_parent_mapping ) {
			return null;
		}
		if ( ! empty( $mapping->path ) && $mapping_handler->matching_mapping_value->object_id ) {
			$request_params->path = str_replace( $mapping->path, '', $request_params->path );
		}
		$child_page = get_page_by_path( trim( $request_params->path, '/' ) );

		if ( ! empty( $child_page ) && $child_page->post_parent != 0 ) {
			return $mapping_handler->prepare_value_instance( $child_page->ID, 'post' );
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
			$top_level_parent      = $force_redirection_handler->get_top_level_parent( $force_redirection_handler->object->ID );
			$parent_mapping_values = Mapping_Value::where( [ 'object_id' => $top_level_parent->ID ] );
			if ( ! empty( $parent_mapping_values ) ) {
				$mapping_value = $parent_mapping_values[0];
				$mapping       = Mapping::find( $mapping_value->mapping_id );
				$params_path   = trim( $request_params->path, '/' );
				$path          = $mapping->path == '' ? $params_path : ( $mapping->path . '/' . $params_path );

				return Helper::generate_url( $mapping->host, $path );
			}
		}

		return null;
	}

	/**
	 * Check whether current parameters correspond
	 *
	 * @param $force_redirection_handler
	 *
	 * @return bool
	 */
	public function should_redirect( $force_redirection_handler ) {
		// Check if object_id is not empty
		if ( empty( $force_redirection_handler->object_id ) ) {
			return false;
		}
		// Check if the object is a page
		if ( $force_redirection_handler->object->post_type !== 'page' ) {
			return false;
		}
		// Check if the page has a parent
		if ( empty( $force_redirection_handler->object->post_parent ) ) {
			return false;
		}
		// Check if there is a global parent mapping in the Frontend instance
		if ( ! Frontend::get_instance()->global_parent_mapping ) {
			return false;
		}

		// If all checks pass, return true
		return true;
	}
}
