<?php

namespace Uncanny_Automator_Pro;

/**
 * Class BDB_JOINPRIVATEGROUP
 *
 * @package Uncanny_Automator_Pro
 */
class BDB_JOINPRIVATEGROUP {

	/**
	 * Integration code
	 *
	 * @var string
	 */
	public static $integration = 'BDB';

	private $trigger_code;
	private $trigger_meta;

	/**
	 * SetAutomatorTriggers constructor.
	 */

	public function __construct() {
		$this->trigger_code = 'BDBJOINPRIVATEGROUP';
		$this->trigger_meta = 'BDBGROUPS';
		$this->define_trigger();
	}

	/**
	 *
	 */
	public function define_trigger() {

		$trigger = array(
			'author'              => Automator()->get_author_name(),
			'support_link'        => Automator()->get_author_support_link( $this->trigger_code, 'integration/buddyboss/' ),
			'is_pro'              => true,
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			/* translators: Logged-in trigger - BuddyBoss */
			'sentence'            => sprintf( __( 'A user joins {{a private group:%1$s}}', 'uncanny-automator-pro' ), $this->trigger_meta ),
			/* translators: Logged-in trigger - BuddyBoss */
			'select_option_name'  => __( 'A user joins {{a private group}}', 'uncanny-automator-pro' ),
			'action'              => array(
				'groups_membership_accepted',
				'groups_accept_invite',
				'automator_groups_join_group',
			),
			'priority'            => 60,
			'accepted_args'       => 2,
			'validation_function' => array( $this, 'groups_join_group' ),
			'options_callback'    => array( $this, 'load_options' ),
		);

		Automator()->register->trigger( $trigger );
	}

	/**
	 * @return array[]
	 */
	public function load_options() {

		$bp_group_args = array(
			'uo_include_any' => true,
			'uo_any_label'   => __( 'Any private group', 'uncanny-automator-pro' ),
			'status'         => array( 'private' ),
		);

		return Automator()->utilities->keep_order_of_options(
			array(
				'options' => array(
					Automator()->helpers->recipe->buddyboss->options->all_buddyboss_groups(
						__( 'Private group', 'uncanny-automator-pro' ),
						'BDBGROUPS',
						$bp_group_args
					),
				),
			)
		);
	}

	/**
	 * @param $user_id
	 * @param $group_id
	 */

	public function groups_join_group( $user_id, $group_id ) {

		$group = groups_get_group( $group_id, true );

		// Early bail since we only targetting private groups.
		if ( isset( $group->status ) && 'private' !== $group->status ) {
			return;
		}

		$recipes = Automator()->get->recipes_from_trigger_code( $this->trigger_code );
		$group   = Automator()->get->meta_from_recipes( $recipes, 'BDBGROUPS' );

		$matched_recipe_ids = array();

		foreach ( $recipes as $recipe_id => $recipe ) {
			// Match recipe if trigger for Any group '-1', or matching Group ID.
			foreach ( $recipe['triggers'] as $trigger ) {
				$trigger_id = $trigger['ID'];
				if (
					intval( '-1' ) === intval( $group[ $recipe_id ][ $trigger_id ] )
					|| intval( $group_id ) === intval( $group[ $recipe_id ][ $trigger_id ] )

				) {
					$matched_recipe_ids[] = array(
						'recipe_id'  => $recipe_id,
						'trigger_id' => $trigger_id,
					);
				}
			}
		}

		if ( ! empty( $matched_recipe_ids ) ) {
			foreach ( $matched_recipe_ids as $matched_recipe_id ) {
				$args = array(
					'code'             => $this->trigger_code,
					'meta'             => $this->trigger_meta,
					'user_id'          => $user_id,
					'recipe_to_match'  => $matched_recipe_id['recipe_id'],
					'trigger_to_match' => $matched_recipe_id['trigger_id'],
					'ignore_post_id'   => true,
				);

				$args = Automator()->maybe_add_trigger_entry( $args, false );
				// Save trigger meta
				if ( $args ) {
					foreach ( $args as $result ) {
						if ( true === $result['result'] && $result['args']['trigger_id'] && $result['args']['trigger_log_id'] ) {

							$run_number = Automator()->get->trigger_run_number( $result['args']['trigger_id'], $result['args']['trigger_log_id'], $user_id );
							$save_meta  = array(
								'user_id'        => $user_id,
								'trigger_id'     => $result['args']['trigger_id'],
								'run_number'     => $run_number,
								//get run number
								'trigger_log_id' => $result['args']['trigger_log_id'],
								'meta_key'       => 'BDBGROUPS',
								'meta_value'     => $group_id,
							);

							Automator()->insert_trigger_meta( $save_meta );

							Automator()->maybe_trigger_complete( $result['args'] );
						}
					}
				}
			}
		}
	}

}
