<?php
defined('ABSPATH') || die('Cheatin\' uh?');
if(!isset($view)) return;
?>

<?php if(!empty($view->options)){?>
    <?php  if (isset($view->options['url']) && $view->options['url'] <> '') { ?>
        <table>
            <td>
                <div id="hmwp_qr_code">
                    <a href="<?php echo $view->options['url']; ?>">
                        Loading...
                        <img src="<?php echo esc_url( admin_url( 'images/spinner.gif' ) ); ?>" alt="" />
                    </a>
                </div>
            </td>
            <td>
                <ol>
                    <li>
                        <p><?php esc_html_e( 'Download and start the application of your choice.', 'hide-my-wp-pack' ); ?></p>
                        <p class="hmwp_description"><?php esc_html_e( 'Click on the icon of the app that you are using for a detailed guide on how to set it up.', 'hide-my-wp-pack' ); ?> </p>
                        <div class="hmwp_apps_wrapper">
                            <a href="<?php echo esc_url(HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/setting-up-two-factor-authentication-2fa-for-wordpress-using-mobile-apps/#google-authenticator-380cd71a-1460-489f-95c2-d65549b1730f') ?>" target="_blank" class="hmwp_app_logo"><img src="<?php echo _HMWPP_ASSETS_URL_ ?>img/google-logo.png"></a>
                            <a href="<?php echo esc_url(HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/setting-up-two-factor-authentication-2fa-for-wordpress-using-mobile-apps/#authy-ec1a725e-f8aa-47a4-a2b6-9b346e6b5640') ?>" target="_blank" class="hmwp_app_logo"><img src="<?php echo _HMWPP_ASSETS_URL_ ?>img/authy-logo.png"></a>
                            <a href="<?php echo esc_url(HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/setting-up-two-factor-authentication-2fa-for-wordpress-using-mobile-apps/#microsoft-authenticator-3c347c3d-ad60-4589-8a93-d424d82a373d') ?>" target="_blank" class="hmwp_app_logo"><img src="<?php echo _HMWPP_ASSETS_URL_ ?>img/microsoft-logo.png"></a>
                            <a href="<?php echo esc_url(HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/setting-up-two-factor-authentication-2fa-for-wordpress-using-mobile-apps/#lastpass-authenticator-c76630c2-dfbb-476c-aad3-e7ccaf597803') ?>" target="_blank" class="hmwp_app_logo"><img src="<?php echo _HMWPP_ASSETS_URL_ ?>img/lastpass-logo.png"></a>
                        </div>
                    </li>
                    <li>
                        <p><?php esc_html_e( 'Scan the provided code using your authenticator app to link this account.', 'hide-my-wp-pack' ); ?></p>
                        <p><?php esc_html_e( 'Some authenticator apps permit you to manually input the text version.', 'hide-my-wp-pack' ); ?></p>
                        <p class="htmp_app_key_wrapper"><code><?php echo esc_html( $view->options['key'] ); ?></code></p>
                    </li>
                    <li>
                        <?php esc_html_e( 'Type in the one-time code from your chosen authentication app to finalize the setup.', 'hide-my-wp-pack' ); ?>
                        <p class="htmp_app_auth_wrapper">
                            <input type="hidden" name="hmwp_totp_nonce" value="<?php echo wp_create_nonce( 'hmwpp_totp_submit' )?>"/>
                            <input type="hidden" name="hmwp_totp_referer" value="<?php echo esc_url( remove_query_arg( '_wp_http_referer' ) ); ?>" />
                            <input type="hidden" name="hmwp_totp_action" value="hmwpp_totp_submit"/>
                            <input type="hidden" name="hmwp_totp_key" value="<?php echo esc_attr( $view->options['key'] ); ?>" />
                            <input type="hidden" name="hmwp_totp_user_id" value="<?php echo esc_attr( $view->options['user']->ID ); ?>" />
                            <label for="hmwp_totp_authcode">
                                <?php echo esc_html__( 'Authentication Code:', 'hide-my-wp-pack' ); ?>
                                <input type="tel" name="hmwp_totp_authcode" id="hmwp_totp_authcode" class="input" value="" size="20" pattern="[0-9 ]*" placeholder="<?php echo esc_attr( sprintf( __( 'eg. %s', 'hide-my-wp-pack' ), '123456' ) ); ?>" />
                            </label>
                            <input id="hmwpp_totp_submit" type="button" class="button button-primary" value="<?php echo esc_attr__( 'Submit', 'hide-my-wp-pack' ); ?>" />
                        </p>
                    </li>
                </ol>
            </td>
        </table>

    <?php }elseif ( isset($view->options['user']) && $view->options['user'] <> ''){ ?>
        <div class="hmwp_title">
            <?php esc_html_e( 'Secret key is configured and registered.', 'hide-my-wp-pack' ); ?>
        </div>
        <div class="hmwp_description">
            <?php esc_html_e( 'It is not possible to view it again for security reasons.', 'hide-my-wp-pack' ); ?>
        </div>
        <div class="hmwp_description">
            <?php esc_html_e( 'You will have to re-scan the QR code on all devices as the previous codes will stop working.', 'hide-my-wp-pack' ); ?>
        </div>
        <div style="margin: 10px 0">
            <input type="hidden" name="hmwp_totp_nonce" value="<?php echo wp_create_nonce( 'hmwpp_totp_reset' ); ?>"/>
            <input type="hidden" name="hmwp_totp_referer" value="<?php echo esc_url( remove_query_arg( '_wp_http_referer' ) ); ?>" />
            <input type="hidden" name="hmwp_totp_action" value="hmwpp_totp_reset"/>
            <input type="hidden" name="hmwp_totp_user_id" value="<?php echo esc_attr( $view->options['user']->ID ); ?>" />

            <input id="hmwpp_totp_reset" type="button" class="button" value="<?php echo esc_attr__( 'Reset Key', 'hide-my-wp-pack' ); ?>" />

            <?php
            //Show the Codes block
            $view->show('blocks/Codes');
            ?>
        </div>
    <?php } ?>
<?php }?>