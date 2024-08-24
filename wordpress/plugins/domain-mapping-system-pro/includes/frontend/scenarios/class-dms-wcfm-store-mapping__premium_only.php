<?php

namespace DMS\Includes\Frontend\Scenarios;

use DMS\Includes\Data_Objects\Mapping;
use DMS\Includes\Data_Objects\Mapping_Value;
use DMS\Includes\Exceptions\DMS_Exception;
use DMS\Includes\Frontend\Handlers\Force_Redirection_Handler;
use DMS\Includes\Frontend\Handlers\Mapping_Handler;
use DMS\Includes\Frontend\Services\Request_Params;
use DMS\Includes\Integrations\WCFM\WCFM;
use DMS\Includes\Utils\Helper;

class WCFM_Store_Mapping implements Mapping_Scenario_Interface {

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
		$mapping               = $mapping_handler->mapping;
		$matched_mapping_value = null;
		foreach ( $mapping_handler->mapping_values as $mapping_value ) {
			if ( $mapping_value->object_type == WCFM::SINGULAR_NAME ) {
				$store      = wcfmmp_get_store( $mapping_value->object_id );
				$store_path = trim( wp_parse_url( $store->get_shop_url(), PHP_URL_PATH ), '/' );
				$primary    = $mapping_value->primary || count( $mapping_handler->mapping_values ) == 1;
				if ( $primary && $request_params->domain == $mapping->host && $request_params->path == $mapping->path ) {
					$matched_mapping_value = $mapping_value;
					break;
				} else if ( ! $primary && str_contains( $request_params->path, $store_path ) ) {
					$matched_mapping_value = $mapping_value;
					break;
				}
			}
		}

		return $matched_mapping_value;
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
		$url            = null;
		$mapping_values = Mapping_Value::where( [ 'object_type' => WCFM::SINGULAR_NAME ] );
		if ( ! empty( $mapping_values ) ) {
			foreach ( $mapping_values as $mapping_value ) {
				$store      = wcfmmp_get_store( $mapping_value->object_id );
				$store_path = trim( wp_parse_url( $store->get_shop_url(), PHP_URL_PATH ), '/' );
				if ( $store_path === $request_params->path ) {
					$mapping = Mapping::find( $mapping_value->mapping_id );
					if ( $mapping_value->primary ) {
						$path = $mapping->path;
					} else {
						$path = ! empty( $mapping->path ) ? $mapping->path . '/' . $store_path : $store_path;
					}
					$url = Helper::generate_url( $mapping->host, $path );
					break;
				}
			}
		}


		return $url;
	}
}