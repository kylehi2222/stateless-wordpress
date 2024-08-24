<?php

class PeepSoBlockLogin extends PeepSoBlockAbstract
{
	protected function get_slug() {
		return 'login';
	}

	protected function get_attributes() {
		$attributes = [
			'title' => [ 'type' => 'string', 'default' => '' ],
			'view_option' => [ 'type' => 'string', 'default' => 'vertical' ],
		];

		return apply_filters('peepso_block_attributes', $attributes, $this->get_slug());
	}

	public function render_component($attributes) {
		if (is_user_logged_in()) {
			$preview = isset($_GET['context']) && 'edit' === $_GET['context'];
			if (!$preview) {
				$widget_instance = $this->widget_instance($attributes);
				return $widget_instance ? $this->widget_empty_content() : '';
			}
		}

		return parent::render_component($attributes);
	}
}
