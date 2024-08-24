<?php
defined('ABSPATH') || die('Cheatin\' uh?');
if(!isset($view)) return;
?>

<?php if(!empty($view->options)){?>
    <?php  if ($view->options['email'] == '') { ?>
        <div class="hmwp_title"><?php esc_html_e( 'Specify the email address to receive the authentication code during the login process for 2FA.', 'hide-my-wp-pack' ); ?></div>
        <div class="hmwp_row">
            <input type="hidden" name="hmwp_email_nonce" value="<?php echo wp_create_nonce( 'hmwpp_email_submit' )?>"/>
            <input type="hidden" name="hmwp_email_referer" value="<?php echo esc_url( remove_query_arg( '_wp_http_referer' ) ); ?>" />
            <input type="hidden" name="hmwp_email_action" value="hmwpp_email_submit"/>
            <input type="hidden" name="hmwp_email_user_id" value="<?php echo esc_attr( $view->options['user']->ID ); ?>" />
            <label for="hmwp_user_email">
                <?php echo esc_html__( 'Email:', 'hide-my-wp-pack' ); ?>
                <input type="text" name="hmwp_user_email" id="hmwp_user_email" size="32" class="input" value="<?php echo esc_attr( $view->options['placeholder'] ); ?>" placeholder="<?php echo esc_attr( $view->options['placeholder'] ); ?>" />
            </label>
            <input id="hmwpp_email_submit" type="button" class="button button-primary" value="<?php echo esc_attr__( 'Submit', 'hide-my-wp-pack' ); ?>" />
        </div>
    <?php }else{?>
        <div class="hmwp_title"><?php esc_html_e( 'The email address to which the authentication code will be sent is', 'hide-my-wp-pack' ); ?>:</div>
        <div class="hmwp_row"><?php echo esc_attr( $view->options['email'] ); ?></div>
        <div class="hmwp_row">
            <input type="hidden" name="hmwp_email_nonce" value="<?php echo wp_create_nonce( 'hmwpp_email_reset' )?>"/>
            <input type="hidden" name="hmwp_email_referer" value="<?php echo esc_url( remove_query_arg( '_wp_http_referer' ) ); ?>" />
            <input type="hidden" name="hmwp_email_action" value="hmwpp_email_reset"/>
            <input type="hidden" name="hmwp_email_user_id" value="<?php echo esc_attr( $view->options['user']->ID ); ?>" />
            <input id="hmwpp_email_reset" type="button" class="button" value="<?php echo esc_attr__( 'Reset Email', 'hide-my-wp-pack' ); ?>" />

            <?php
            //Show the Codes block
            $view->show('blocks/Codes');
            ?>
        </div>
    <?php }?>
<?php }?>