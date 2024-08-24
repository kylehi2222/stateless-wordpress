<?php
/**
 * Admin: Review Affiliate View
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Affiliates
 * @copyright   Copyright (c) 2014, Sandhills Development, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.2
 */

use AffiliateWP\Utils\Icons as Icons;

use function \AffiliateWP\Functions\Affiliates\get_pending_affiliate_count;
use function \AffiliateWP\Functions\Affiliates\get_next_pending_affiliate_id;
use function \AffiliateWP\Admin\Affiliates\Review_Affiliate\get_ai_amount;

$affiliate                    = affwp_get_affiliate( absint( filter_input( INPUT_GET, 'affiliate_id', FILTER_SANITIZE_NUMBER_INT ) ) );
$affiliate_id                 = $affiliate->affiliate_id;
$name                         = affiliate_wp()->affiliates->get_affiliate_name( $affiliate_id );
$user_info                    = get_userdata( $affiliate->user_id );
$user_url                     = $user_info->user_url; // phpcs:ignore -- Not overriding here.
$promotion_method             = get_user_meta( $affiliate->user_id, 'affwp_promotion_method', true );
$payment_email                = $affiliate->payment_email;
$dynamic_coupons_enabled      = affiliate_wp()->settings->get( 'dynamic_coupons' );
$dynamic_coupons              = affwp_get_dynamic_affiliate_coupons( $affiliate_id, false );
$custom_fields                = affwp_get_custom_registration_fields( $affiliate_id, true );
$exclude_affiliates           = array_map( 'absint', array_merge( [ $affiliate_id ], explode( ',', $_REQUEST['undecided'] ?? '' ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- We validate this data later.
$pending_count                = get_pending_affiliate_count( $exclude_affiliates );
$have_more_pending_affiliates = ( $pending_count >= 1 );
$next_pending_affiliate_id    = get_next_pending_affiliate_id( $exclude_affiliates );
$undecided_affiliates         = $_REQUEST['undecided'] ?? ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- We validate this data later.
$suggestion                   = sprintf( '<span style="display:none;" class="recommendation-label">%s</span>', esc_html__( 'Recommendation', 'affiliate-wp' ) );

?>

<div class="wrap">

	<h2><?php esc_html_e( 'Review Affiliate', 'affiliate-wp' ); ?> <?php affwp_admin_link( 'affiliates', __( 'Go Back', 'affiliate-wp' ), array(), array( 'class' => 'button-secondary' ) ); ?></h2>

	<form method="post" id="affwp_review_affiliate" data-affiliate-id="<?php echo absint( $affiliate_id ); ?>">

		<?php

		/**
		 * Fires at the top of the review-affiliate admin screen, just inside of the form element.
		 *
		 * @since 1.2
		 *
		 * @param \AffWP\Affiliate $affiliate Affiliate object.
		 */
		do_action( 'affwp_review_affiliate_top', $affiliate );

		?>

		<table class="form-table">

			<tr class="form-row form-required">

				<th scope="row">
					<?php esc_html_e( 'Name', 'affiliate-wp' ); ?>
				</th>

				<td>
					<?php echo esc_html( $name ); ?>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<?php esc_html_e( 'Username', 'affiliate-wp' ); ?>
				</th>

				<td>
					<?php echo esc_html( $user_info->user_login ); ?>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<?php esc_html_e( 'Email Address', 'affiliate-wp' ); ?>
				</th>

				<td>
					<?php echo esc_html( $user_info->user_email ); ?>
				</td>

			</tr>

			<?php if ( $payment_email ) : ?>
				<tr class="form-row form-required">

					<th scope="row">
						<?php esc_html_e( 'Payment Email', 'affiliate-wp' ); ?>
					</th>

					<td>
						<?php echo esc_html( $payment_email ); ?>
					</td>

				</tr>
			<?php endif; ?>

			<?php if ( $user_url ) : ?>
				<tr class="form-row form-required">

					<th scope="row">
						<?php esc_html_e( 'Website URL', 'affiliate-wp' ); ?>
					</th>

					<td>
						<a href="<?php echo esc_url( $user_url ); ?>" title="<?php esc_html_e( 'Affiliate&#8217;s Website URL', 'affiliate-wp' ); ?>" target="blank"><?php echo esc_url( $user_url ); ?></a>
					</td>

				</tr>
			<?php endif; ?>

			<?php if ( $promotion_method ) : ?>
				<tr class="form-row form-required">

					<th scope="row">
						<?php esc_html_e( 'Promotion Method', 'affiliate-wp' ); ?>
					</th>

					<td>
						<?php echo esc_html( $promotion_method ); ?>
					</td>

				</tr>
			<?php endif; ?>

			<?php foreach ( $custom_fields as $custom_field ):
				if( 'checkbox' === $custom_field['type'] || 'terms_of_use' === $custom_field['type'] ) {
						$value = (bool) $custom_field['meta_value'];
						$custom_field['meta_value'] = true === $value ? _x( 'Yes', 'registration checkbox enabled', 'affiliate-wp' ) : _x( 'No', 'registration checkbox disabled', 'affiliate-wp' );
				}

				if( 'checkbox_multiple' === $custom_field['type'] ) {
					$custom_field['meta_value'] = implode( ', ', $custom_field['meta_value'] );
				}

			?>
				<tr class="form-row">

					<th scope="row">
						<?php echo wp_strip_all_tags( $custom_field['name'] ); ?>
					</th>

					<td>
						<?php echo esc_html( $custom_field['meta_value'] ); ?>
					</td>

				</tr>
			<?php endforeach; ?>

			<?php if ( affwp_dynamic_coupons_is_setup() && empty( $dynamic_coupons ) ) : ?>

				<tr class="form-row">

					<th scope="row">
						<label for="dynamic_coupon"><?php esc_html_e( 'Dynamic Coupon', 'affiliate-wp' ); ?></label>
					</th>

					<td>
						<label class="description" for="dynamic_coupon">
							<input type="checkbox" name="dynamic_coupon" id="dynamic_coupon" value="1" <?php checked( $dynamic_coupons_enabled, true ); ?> />
							<?php esc_html_e( 'Create dynamic coupon for affiliate?', 'affiliate-wp' ); ?>
						</label>
					</td>
				</tr>

			<?php endif; ?>

			<?php

			/**
			 * Fires at the end of the review-affiliate admin screen, prior to the closing table element tag.
			 *
			 * @since 1.2
			 *
			 * @param \AffWP\Affiliate $affiliate Affiliate object.
			 */
			do_action( 'affwp_review_affiliate_end', $affiliate );

			?>
		</table>

		<hr>

		<table class="form-table">

			<tr class="form-row">

				<th scope="row">
					<?php esc_attr_e( __( 'Review Application', 'affiliate-wp' ) ); ?>
				</th>

				<td>

					<div class="review-with-ai">

						<button type="button"
								name="ask-ai"
								class="button button-secondary"
								data-affiliate-id="<?php echo absint( $affiliate_id ); ?>"
						>
							<?php Icons::render( 'sparkles' ); ?>
							<?php esc_attr_e( 'Review with AI', 'affiliate-wp' ); ?>
						</button>

						<div class="decision-container">

							<p class="ai-reason">
								<span class="faux">&nbsp;</span>
								<span class="faux">&nbsp;</span>
							</p>

							<span class="ai-reviews-left">
								<span class="count"><?php echo intval( floor( get_ai_amount( 'available_applications' ) ) ); ?></span>
								<?php esc_html_e( 'AI reviews left', 'affiliate-wp' ); ?>
							</span>

						</div>

					</div>

					<div class="decisions">

						<label for="decision-accept">

							<input
								checked
								type="radio"
								value="accept"
								name="decision"
								id="decision-accept"
							>
								<?php esc_attr_e( 'Accept Affiliate', 'affiliate-wp' ); ?>
								<?php echo $suggestion; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped  -- Escaped earlier. ?>
						</label>

						<label for="decision-reject">

							<input
								type="radio"
								value="reject"
								name="decision"
								id="decision-reject"
							>
								<?php esc_attr_e( 'Reject Affiliate', 'affiliate-wp' ); ?>
								<?php echo $suggestion; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped earlier. ?>
						</label>

						<label for="decision-undecided">

							<input
								type="radio"
								name="decision"
								value="undecided"
								id="decision-undecided"
							>
								<?php esc_attr_e( 'Skip Affiliate', 'affiliate-wp' ); ?>
						</label>

						<input
							name="undecided"
							type="hidden"
							value="<?php echo esc_attr( $undecided_affiliates ); ?>"
						>

						<!-- These are submitted along with the form, and stored in the database. -->
						<input type="hidden" name="ai_status" value="">
						<input type="hidden" name="ai_reason" value="">
					</div>

				</td>

			</tr>

			<tr class="form-row hidden" id="affwp-rejection-reason">

				<th scope="row">
					<?php esc_html_e( 'Rejection Reason', 'affiliate-wp' ); ?>
				</th>

				<td>
					<textarea class="large-text" name="affwp_rejection_reason" rows="10"></textarea>
				</td>

			</tr>

		</table>

		<?php

		/**
		 * Fires at the bottom of the review-affiliate admin screen, just prior to the submit button.
		 *
		 * @since 1.2
		 *
		 * @param \AffWP\Affiliate $affiliate Affiliate object.
		 */
		do_action( 'affwp_review_affiliate_bottom', $affiliate );

		?>

		<input type="hidden" name="affiliate_id" value="<?php echo esc_attr( absint( $affiliate_id ) ); ?>">
		<input type="hidden" name="affwp_action" value="moderate_affiliate">

		<?php wp_nonce_field( 'affwp_moderate_affiliates_nonce', 'affwp_moderate_affiliates_nonce' ); ?>

		<input
			type="submit"
			name="continue"
			value="<?php esc_attr_e( 'Accept Affiliate', 'affiliate-wp' ); ?>" class="button button-primary"
			data-value-reject="<?php esc_attr_e( 'Reject Affiliate', 'affiliate-wp' ); ?>"
			data-value-accept="<?php esc_attr_e( 'Accept Affiliate', 'affiliate-wp' ); ?>"
			data-value-undecided="<?php esc_attr_e( 'Skip Affiliate', 'affiliate-wp' ); ?>"
		>

		<input type="hidden"
			name="next-pending-affiliate-id"
			value="<?php echo absint( $next_pending_affiliate_id ); ?>"
		>

	</form>

</div>
