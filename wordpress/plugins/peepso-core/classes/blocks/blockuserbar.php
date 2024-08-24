<?php

class PeepSoBlockUserBar extends PeepSoBlockAbstract
{
	protected function get_slug() {
		return 'user-bar';
	}

	protected function get_attributes() {
		$attributes = [
			'content_position' => [ 'type' => 'string', 'default' => 'left' ],
			'guest_behavior' => [ 'type' => 'string', 'default' => 'hide' ],
			'show_name' => [ 'type' => 'integer', 'default' => 1 ],
			'compact_mode' => [ 'type' => 'integer', 'default' => 1 ],
			'show_avatar' => [ 'type' => 'integer', 'default' => 1 ],
			'show_notifications' => [ 'type' => 'integer', 'default' => 1 ],
			'show_usermenu' => [ 'type' => 'integer', 'default' => 1 ],
			'show_logout' => [ 'type' => 'integer', 'default' => 0 ],
			'show_vip' => [ 'type' => 'integer', 'default' => 0 ],
			'show_badges' => [ 'type' => 'integer', 'default' => 0 ],
		];

		return apply_filters('peepso_block_attributes', $attributes, $this->get_slug());
	}

	protected function get_render_args($attributes, $preview) {
		$user_id = get_current_user_id();
		$user = PeepSoUser::get_instance($user_id);
		$toolbar = $this->toolbar();
		$links = apply_filters('peepso_navigation_profile', array('_user_id' => get_current_user_id()));

		return [
			'user_id' => $user_id,
			'user' => $user,
			'toolbar' => $toolbar,
			'links' => $links
		];
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
