<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
namespace Uncanny_Automator_Pro;

use Uncanny_Automator\Recipe;

/**
 * Class AMELIA_USER_APPOINTMENT_BOOKED_SERVICE_RESCHEDULED
 *
 * @package Uncanny_Automator
 */
class AMELIA_USER_APPOINTMENT_BOOKED_SERVICE_RESCHEDULED {

	use Recipe\Triggers;

	/**
	 * Trigger code.
	 *
	 * @var string
	 */
	const TRIGGER_CODE = 'AMELIA_USER_APPOINTMENT_BOOKED_SERVICE_RESCHEDULED';

	/**
	 * Trigger meta.
	 *
	 * @var string
	 */
	const TRIGGER_META = 'AMELIA_USER_APPOINTMENT_BOOKED_SERVICE_RESCHEDULED_META';

	/**
	 * Set up Automator trigger constructor.
	 */
	public function __construct() {

		// Only enable in Amelia Pro.
		if ( ! defined( 'AMELIA_LITE_VERSION' ) ) {

			$this->setup_trigger();

		}

	}

	/**
	 * Define and register the trigger by pushing it into the Automator object
	 */
	public function setup_trigger() {

		// Bailout if helpers from base Automator is not found.
		if ( is_null( Automator()->helpers->recipe->ameliabooking ) ) {
			return;
		}

		$this->set_integration( 'AMELIABOOKING' );
		$this->set_trigger_code( self::TRIGGER_CODE );
		$this->set_trigger_meta( self::TRIGGER_META );
		$this->set_is_pro( true );
		$this->set_is_login_required( true );

		// The action hook to attach this trigger into.
		$this->add_action( 'AmeliaBookingTimeUpdated' );

		// The number of arguments that the action hook accepts.
		$this->set_action_args_count( 3 );

		$this->set_sentence(
			sprintf(
			/* Translators: Trigger sentence */
				esc_html__( "A user's booking of an appointment for {{a specific service:%1\$s}} is rescheduled", 'uncanny-automator' ),
				$this->get_trigger_meta()
			)
		);

		$this->set_readable_sentence(
		/* Translators: Trigger sentence */
			esc_html__( "A user's booking of an appointment for {{a specific service}} is rescheduled", 'uncanny-automator' )
		);

		// Set the options field group.
		$this->set_options_callback( array( $this, 'load_options' ) );

		// Register the trigger.
		$this->register_trigger();

	}

	/**
	 * @return array
	 */
	public function load_options() {
		return Automator()->utilities->keep_order_of_options(
			array(
				'options_group' => $this->get_trigger_option_fields(),
			)
		);
	}

	/**
	 * Validate the trigger.
	 *
	 * Return false if returned booking data is empty.
	 */
	public function validate_trigger( ...$args ) {

		return Automator()->helpers->recipe->ameliabooking->options->validate_trigger( $args );

	}

	/**
	 * Prepare to run.
	 *
	 * Sets the conditional trigger to true.
	 *
	 * @return void.
	 */
	public function prepare_to_run( $data ) {

		// Maybe update current user ID.
		$user_id = Ameliabooking_Pro_Helpers::get_reservation_wp_user_id( $data[0], $data[2] );
		if ( $user_id && $user_id !== $this->get_user_id() ) {
			$this->set_user_id( $user_id );
		}

		$this->set_conditional_trigger( true );

	}

	/**
	 * Trigger conditions.
	 *
	 * Only run the trigger if service is set to 'Any' or if service id is equals to the one set in the recipe.
	 *
	 * @return void.
	 */
	protected function trigger_conditions( $args ) {

		// Grab the returned booking data.
		$booking_data = $args[0];

		// Match 'Any services' condition.
		$this->do_find_any( true );

		// Match specific condition.
		$this->do_find_this( $this->get_trigger_meta() );

		$this->do_find_in( array( $booking_data['serviceId'] ) );

	}

	/**
	 * The trigger options fields.
	 *
	 * @return array The field options.
	 */
	public function get_trigger_option_fields() {

		return Automator()->helpers->recipe->ameliabooking->options->get_option_fields(
			$this->get_trigger_code(),
			$this->get_trigger_meta()
		);

	}

}
