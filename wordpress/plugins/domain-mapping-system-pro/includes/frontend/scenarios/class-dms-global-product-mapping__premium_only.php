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

class Global_Product_Mapping implements Mapping_Scenario_Interface {

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
		if ( $mapping_handler->matching_mapping_value || ! Frontend::get_instance()->global_shop_mapping ) {
			return null;
		}
		$mapping      = $mapping_handler->mapping;
		$shop_page    = wc_get_page_id( 'shop' );
		$shop_mapping = array_filter( $mapping_handler->mapping_values, function ( $item ) use ( $shop_page ) {
			return $item->object_type == 'post' && $shop_page == $item->object_id;
		} );
		if ( ! empty( $shop_mapping ) ) {
			$permalink            = str_replace( $mapping->path, '', $request_params->path );
			$permalink            = explode( '/', $permalink );
			$request_params->path = $permalink[ count( $permalink ) - 1 ];
			$product              = get_page_by_path( $request_params->path, OBJECT, 'product' );

			if ( ! empty( $product ) ) {
				return $mapping_handler->prepare_value_instance( $product->ID, 'product' );
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
		if ( Frontend::get_instance()->global_shop_mapping ) {
			if ( ! empty( $force_redirection_handler->object->post_type ) && $force_redirection_handler->object->post_type == 'product' ) {
				$shop_page_id            = wc_get_page_id( 'shop' );
				$shop_page_mapping_value = Mapping_Value::where( [ 'object_id' => $shop_page_id ] );
				if ( ! empty( $shop_page_mapping_value ) ) {
					$mapping_value = $shop_page_mapping_value[0];
					$mapping       = Mapping::find( $mapping_value->mapping_id );
					$path          = $mapping->path == '' ? $request_params->path : ( $mapping->path . '/' . $request_params->path );

					return Helper::generate_url( $mapping->host, $path );
				}
			}
		}

		return null;
	}
}