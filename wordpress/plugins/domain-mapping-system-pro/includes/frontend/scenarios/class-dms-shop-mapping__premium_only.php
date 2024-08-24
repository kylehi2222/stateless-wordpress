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

class Shop_Mapping implements Mapping_Scenario_Interface {

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
		if ( Frontend::get_instance()->global_shop_mapping ) {
			if ( is_shop() ) {
				$shop_page = Helper::get_shop_page_association();
				if ( $shop_page ) {
					$mapping_value = Mapping_Value::where( [ 'object_id' => $shop_page, 'object_type' => 'post' ] );
					if ( $mapping_value ) {
						$mapping_value = $mapping_value[0];
						$mapping       = Mapping::find( $mapping_value->mapping_id );
						if ( $mapping_value->primary ) {
							$url = Helper::generate_url( $mapping->host, $mapping->path );
						} else {
							$slug = trim( wp_parse_url( get_page_link( $shop_page ), PHP_URL_PATH ), '/' );
							$path = ! empty( $mapping->path ) ? $mapping->path . '/' . $slug : $slug;
							$url  = Helper::generate_url( $mapping->host, $path );
						}

						return $url;
					}
				}
			}
		}

		return null;
	}
}