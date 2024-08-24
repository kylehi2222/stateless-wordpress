<?php
defined('ABSPATH') || die('Cheatin\' uh?');
if(!isset($view)) return;
?>
<table class="form-table" id="hmwp_two_factor_options">
    <tr>
        <th rowspan="2">
            <div class="hmwp_title"><?php echo esc_html__( '2FA Setup', 'hide-my-wp-pack' ); ?></div>
            <div class="hmwp_description" style="margin-top: 5px; font-size: x-small; color: #aaaaaa"><?php echo _HMWPP_PLUGIN_FULL_NAME_; ?> </div>
        </th>
        <td id="hmwp_totp_options">
            <?php do_action( 'hmwp_two_factor_user_options' );?>
        </td>
    </tr>
</table>