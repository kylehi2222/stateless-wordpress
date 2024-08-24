<?php

class PeepSoBlockProfile extends PeepSoBlockAbstract
{
	protected function get_slug() {
		return 'profile';
	}

	protected function get_attributes() {
		$attributes = [
			'title' => [ 'type' => 'string', 'default' => '' ],
			'guest_behavior' => [ 'type' => 'string', 'default' => 'login' ],
			'show_notifications' => [ 'type' => 'integer', 'default' => 1 ],
			'show_community_links' => [ 'type' => 'integer', 'default' => 0 ],
			'show_cover' => [ 'type' => 'integer', 'default' => 0 ],
			'show_in_profile' => [ 'type' => 'integer', 'default' => 3 ],
			'timestamp' => [ 'type' => 'integer', 'default' => 0 ]
		];

		return apply_filters('peepso_block_attributes', $attributes, $this->get_slug());
	}

	protected function get_render_args($attributes, $preview) {
		$user_id = get_current_user_id();
		$user = PeepSoUser::get_instance($user_id);
		$toolbar = $this->toolbar();
		$links = apply_filters('peepso_navigation_profile', array('_user_id' => get_current_user_id()));
		$community_links = apply_filters('peepso_navigation', array());
		unset($community_links['profile']);

		return [
			'user_id' => $user_id,
			'user' => $user,
			'toolbar' => $toolbar,
			'links' => $links,
			'community_links' => $community_links
		];
	}

	public function render_component($attributes) {
		if ($this->maybe_hide($attributes)) {
			$widget_instance = $this->widget_instance($attributes);
			return $widget_instance ? $this->widget_empty_content() : '';
		}

		return parent::render_component($attributes);
	}

	private function maybe_hide($attributes) {
		$user_id = get_current_user_id();

		if ($user_id > 0) {
			global $post;

			// Hide from profile page?
			if ($post instanceof  WP_Post) {
				$profile_page = $post->post_type == 'page' && stristr($post->post_content, '[peepso_profile');

				// https://gitlab.com/PeepSo/PeepSo/-/issues/4753
				if (!$profile_page) {
					global $wp_query;

					if ($wp_query instanceof WP_Query && isset($wp_query->post) && $wp_query->post instanceof WP_Post && stristr($wp_query->post->post_content, '[peepso_profile')) {
						$profile_page = TRUE;
					}
				}

				if (!$profile_page && $post->post_type === 'peepso-post') {
					$url = PeepSoUrlSegments::get_instance();
					if ($url->_shortcode === 'peepso_profile') {
						$profile_page = TRUE;
					}
				}

				// 3 = always show
				if ($profile_page && $attributes['show_in_profile'] < 3) {
					// 0 = always hide
					if (0 == $attributes['show_in_profile']) {
						return TRUE;
					}

					$PeepSoProfile = PeepSoProfileShortcode::get_instance();
					$view_id = $PeepSoProfile->get_view_user_id();

					// 1 = show on "mine" and hide on "theirs"
					if (1 == $attributes['show_in_profile'] && $view_id != $user_id) {
						return TRUE;
					}

					// 2 = hide on "mine" and show on "theirs"
					if (2 == $attributes['show_in_profile'] && $view_id == $user_id) {
						return TRUE;
					}
				}
			}
		} else {
			// Non logged-in users.
			if (isset($attributes['guest_behavior']) && 'hide' === $attributes['guest_behavior']) {
				return TRUE;
			}
		}

		return FALSE;


	}

	private function toolbar() {
		$note = PeepSoNotifications::get_instance();
		$unread_notes = $note->get_unread_count_for_user();

		$toolbar = array(
			'notifications' => array(
				'href' => PeepSo::get_page('notifications'),
				'icon' => 'gcis gci-bell',
				'class' => 'ps-notif--general dropdown-notification ps-js-notifications',
				'title' => __('Pending Notifications', 'peepso-core'),
				'count' => $unread_notes,
				'order' => 100
			),
		);

		$toolbar = PeepSoGeneral::get_instance()->get_navigation('notifications');

		ob_start();

		?>
		<?php foreach ($toolbar as $item => $data) { ?>
			<div class="ps-notif <?php echo $data['class']; ?>">
				<a class="ps-notif__toggle" href="<?php echo $data['href']; ?>" title="<?php echo esc_attr($data['label']); ?>">
					<i class="<?php echo $data['icon']; ?>"></i>
					<span class="ps-notif__bubble js-counter ps-js-counter"><?php echo ($data['count'] > 0) ? $data['count'] : ''; ?></span>
				</a>
			</div>
		<?php } ?>
		<?php

		$html = str_replace(PHP_EOL, '', ob_get_clean());

		return $html;
	}
}
