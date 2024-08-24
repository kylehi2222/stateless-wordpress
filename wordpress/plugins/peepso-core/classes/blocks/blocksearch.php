<?php

class PeepSoBlockSearch extends PeepSoBlockAbstract
{
	protected function get_slug() {
		return 'search';
	}

	protected function get_attributes() {
		$attributes = [
			'title' => [ 'type' => 'string', 'default' => __('Search', 'peepso-core') ],
		];

		return apply_filters('peepso_block_attributes', $attributes, $this->get_slug());
	}
}
