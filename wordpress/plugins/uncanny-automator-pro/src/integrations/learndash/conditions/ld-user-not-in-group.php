<?php

namespace Uncanny_Automator_Pro;

/**
 * Class LD_USER_NOT_IN_GROUP
 *
 * @package Uncanny_Automator_Pro
 */
class LD_USER_NOT_IN_GROUP extends Action_Condition {

	/**
	 * Define_condition
	 *
	 * @return void
	 */
	public function define_condition() {

		$this->integration = 'LD';
		/*translators: Token */
		$this->name = __( 'The user is not a member of {{a group}}', 'uncanny-automator-pro' );
		$this->code = 'NOT_MEMBER_OF_GROUP';
		// translators: A token matches a value
		$this->dynamic_name  = sprintf( esc_html__( 'The user is not a member of {{a group:%1$s}}', 'uncanny-automator-pro' ), 'GROUP' );
		$this->is_pro        = true;
		$this->requires_user = true;
	}

	/**
	 * Fields
	 *
	 * @return array
	 */
	public function fields() {

		$groups_field_args = array(
			'option_code'           => 'GROUP',
			'label'                 => esc_html__( 'Group', 'uncanny-automator-pro' ),
			'required'              => true,
			'options'               => $this->ld_groups_options(),
			'supports_custom_value' => true,
		);

		return array(
			// Course field
			$this->field->select_field_args( $groups_field_args ),
		);
	}

	/**
	 * Load options
	 *
	 * @return array[]
	 */
	public function ld_groups_options() {
		$args      = array(
			'post_type'      => 'groups',
			'posts_per_page' => 9999, //phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
			'orderby'        => 'title',
			'order'          => 'ASC',
			'post_status'    => 'publish',
		);
		$ld_groups = array(
			array(
				'value' => '-1',
				'text'  => esc_html__( 'Any group', 'uncanny-automator-pro' ),
			),
		);
		$groups    = Automator()->helpers->recipe->options->wp_query( $args, false, false );
		if ( empty( $groups ) ) {
			return array();
		}
		foreach ( $groups as $group_id => $group_title ) {
			$ld_groups[] = array(
				'value' => $group_id,
				'text'  => $group_title,
			);
		}

		return $ld_groups;
	}

	/**
	 * Evaluate_condition
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function evaluate_condition() {

		$user_groups = learndash_get_users_group_ids( $this->user_id, true );
		if ( ! empty( $user_groups ) ) {
			$parsed_group = $this->get_parsed_option( 'GROUP' );

			// Any group.
			if ( '-1' === (string) $parsed_group ) {

				$message = __( 'User is a member of a group.', 'uncanny-automator-pro' );
				$this->condition_failed( $message );

			} else {

				// Specific group.
				$user_in_group = array_intersect( $user_groups, array( $parsed_group ) );
				// Check if the user is enrolled in the group here
				if ( ! empty( $user_in_group ) ) {
					$message = __( 'User is a member of ', 'uncanny-automator-pro' ) . $this->get_option( 'GROUP_readable' );
					$this->condition_failed( $message );
				}
			}
		}
	}
}
