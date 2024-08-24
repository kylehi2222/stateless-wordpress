<?php

abstract class PeepSoBlockAbstract
{
	abstract protected function get_slug();

	public function __construct() {
		$slug = $this->get_slug();

		wp_register_script(
			"peepso-block-{$slug}-editor",
			PeepSo::get_asset("js/blocks/{$slug}-editor.js"),
			['wp-blocks', 'wp-i18n', 'wp-element'],
			PeepSo::PLUGIN_VERSION,
			TRUE
		);

		$attributes = array_merge($this->get_attributes(), [
			'__psBlockId' => [ 'type' => 'string', 'default' => '' ],
			'__psSidebarId' => [ 'type' => 'string', 'default' => '' ],
		]);

		$dataKey = implode('', array_map('ucfirst', explode('-', $slug)));
		$dataKey = "peepsoBlock{$dataKey}EditorData";
		wp_localize_script("peepso-block-{$slug}-editor", $dataKey, [
			'attributes' => $attributes
		]);

		register_block_type("peepso/{$slug}", [
			'attributes' => $attributes,
			'editor_script' => "peepso-block-{$slug}-editor",
			'render_callback' => [$this, 'render_component'],
		]);
	}

	protected function get_attributes() {
		return [];
	}

	protected function get_render_args($attributes, $preview) {
		return [];
	}

	public function render_component($attributes) {
		$preview = isset($_GET['context']) && 'edit' === $_GET['context'];
		$widget_instance = $this->widget_instance($attributes);
		$args = ['attributes' => $attributes, 'preview' => $preview, 'widget_instance' => $widget_instance];
		$args =  array_merge($args, $this->get_render_args($attributes, $preview));
		$html = PeepSoTemplate::exec_template('blocks', $this->get_slug(), $args, TRUE);

		if ($preview && trim($html)) {
			$class = $widget_instance ? 'ps-widget--preview' : '';
			$html = sprintf('<div class="%2$s" style="position:relative">
				%1$s
				<div class="ps-widget__disabler" style="position:absolute; top:0; left:0; right:0; bottom:0"></div>
			</div>', $html, $class);
		}

		return $html;
	}

	/**
	 * For backward compatibility when the block is rendered as a widget, inside a "sidebar".
	 */
	protected function widget_instance($attributes) {
		if (isset($attributes['__psSidebarId'])) {
			global $wp_registered_sidebars;

			$sidebar_id = $attributes['__psSidebarId'];
			if (isset($wp_registered_sidebars[$sidebar_id])) {
				return $wp_registered_sidebars[$sidebar_id];
			}
		}

		return NULL;
	}

	/**
	 * Cannot remove enclosing `before_widget` wrapper from here,
	 * so we do it on the client side as the last resort.
	 */
	protected function widget_empty_content() {
		$id = uniqid('ps_widget_empty_');
		return '<div data-widget-empty id="' . $id . '"><script>try{
			document.getElementById("' . $id . '").closest(".widget").remove();
		}catch(e){}</script></div>';
	}
}
