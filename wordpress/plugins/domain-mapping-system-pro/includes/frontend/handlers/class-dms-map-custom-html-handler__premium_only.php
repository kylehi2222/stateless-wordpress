<?php

namespace DMS\Includes\Frontend\Handlers;

class Map_Html_Handler {

	/**
	 * Custom html of the mapping which must be added to head tag
	 *
	 * @var null|string
	 */
	public ?string $map_custom_html;

	/**
	 * Constructor
	 *
	 * @param null|string $html
	 */
	public function __construct( ?string $html ) {
		$this->map_custom_html = $html;
	}

	/**
	 * Override the head adding the custom html
	 *
	 * @return void
	 */
	public function override(): void {
		if ( ! empty( $this->map_custom_html ) ) {
			printf( stripslashes( $this->map_custom_html ) );
		}
	}
}