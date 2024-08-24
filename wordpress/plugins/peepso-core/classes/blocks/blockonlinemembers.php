<?php

class PeepSoBlockOnlineMembers extends PeepSoBlockAbstract
{
	protected function get_slug() {
		return 'online-members';
	}

	protected function get_attributes() {
		$attributes = [
			'title' => [ 'type' => 'string', 'default' => __('Online Members', 'peepso-core') ],
			'limit' => [ 'type' => 'integer', 'default' => 12 ],
			'hide_empty' => [ 'type' => 'integer', 'default' => 0 ],
			'show_total_online' => [ 'type' => 'integer', 'default' => 0 ],
			'show_total_members' => [ 'type' => 'integer', 'default' => 0 ],
		];

		return apply_filters('peepso_block_attributes', $attributes, $this->get_slug());
	}
}
