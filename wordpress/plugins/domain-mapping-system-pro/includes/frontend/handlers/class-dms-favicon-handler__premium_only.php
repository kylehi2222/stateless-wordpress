<?php
namespace DMS\Includes\Frontend\Handlers;

use DMS\Includes\Freemius;

class Favicon_Handler {

	/**
	 * Favicon attachment id
	 *
	 * @var null|int
	 */
	public ?int $favicon_id;

	/**
	 * Freemius
	 *
	 * @var \Freemius|null
	 */
	public ?\Freemius $fs;

	/**
	 * Constructor
	 *
	 * @param null|int $favicon_id
	 */
	public function __construct( ?int $favicon_id ) {
		$this->favicon_id = $favicon_id;
		$this->fs         = Freemius::getInstance()->get_freemius();
	}

	/**
	 * Override Favicon of the page
	 *
	 * @param null|string $url
	 *
	 * @return null|string
	 */
	public function override( ?string $url ): ?string {
		if ( ! empty( $this->favicon_id ) ) {
			$favicon_src = wp_get_attachment_image_url( $this->favicon_id );
			if ( ! empty( $favicon_src ) ) {
				return $favicon_src;
			}
		}

		return $url;
	}
}
