<?php
/**
 * Plugin Email Service
 * Called when the user is using Email model for 2FA
 *
 * @file  The Email Model file
 * @package HMWPP/EmailModel
 * @since 1.0.0
 */
defined('ABSPATH') || die('Cheatin\' uh?');

class HMWPP_Models_Services_Email extends HMWPP_Models_Abstract_Provider {

    /** @var string The user meta key for email token */
    const TOKEN_META_KEY = '_hmwp_email_token';

    /** @var string The user meta key for email address */
    const EMAIL_META_KEY = '_hmwp_email_address';

    /** @var string Token timestamp */
    const TOKEN_META_KEY_TIMESTAMP = '_hmwp_email_token_timestamp';

    /**
     * The user meta key for the last successful TOTP token timestamp logged in with.
     *
     * @var string
     */
    const LAST_SUCCESSFUL_LOGIN_META_KEY = '_hmwp_email_last_login';

    /** @var string email code  */
    const INPUT_NAME_RESEND_CODE = 'hmwp-email-code-resend';

    /**
     * Verify whether the current service is active and operational for the present user.
     *
     * @param WP_User $user The WP_User instance representing the currently logged-in user.
     * @return bool
     */
    public function isServiceActive( $user ){

        if( ! HMWPP_Classes_Tools::getOption('hmwp_2fa_email') ){
            return false;
        }

        if(!HMWPP_Classes_Tools::getUserMeta(self::EMAIL_META_KEY, $user->ID)){
            return false;
        }

        return true;
    }

    /**
     * Whether this Two Factor provider is configured and available for the user specified.
     *
     * @param WP_User $user The WP_User instance representing the currently logged-in user.
     * @return boolean
     */
    public function isAvailableForUser( $user ) {
        return true;
    }

    /**
     * Get the Token key for a user.
     *
     * @param  int $user_id User ID.
     *
     * @return string
     */
    public function getUserToken( $user_id ) {

        $hashed_token =  HMWPP_Classes_Tools::getUserMeta(self::TOKEN_META_KEY, $user_id);

        if ( ! empty( $hashed_token ) && is_string( $hashed_token ) ) {
            return $hashed_token;
        }

        return false;
    }

    /**
     * Set the Token key for a user.
     *
     * @param int    $user_id User ID.
     * @param string $key Email token key.
     *
     * @return boolean If the key was stored successfully.
     */
    public function setUserToken( $user_id, $key ) {
        return HMWPP_Classes_Tools::saveUserMeta(self::TOKEN_META_KEY, $key, $user_id);
    }

    /**
     * Set the Email for a user.
     *
     * @param int    $user_id User ID.
     * @param string $email Email address.
     *
     * @return boolean If the key was stored successfully.
     */
    public function setUserEmail( $user_id, $email ) {
        return HMWPP_Classes_Tools::saveUserMeta(self::EMAIL_META_KEY, $email, $user_id);
    }

    /**
     * Delete the Email for a user.
     *
     * @param int    $user_id User ID.
     *
     * @return boolean If the key was deleted successfully.
     */
    public function deleteUserEmail( $user_id) {
        return HMWPP_Classes_Tools::deleteUserMeta(self::EMAIL_META_KEY, $user_id);
    }

    /**
     * Delete the Token key for a user.
     *
     * @param  int $user_id User ID.
     *
     * @return boolean If the key was deleted successfully.
     */
    public function deleteUserToken( $user_id ) {
        return HMWPP_Classes_Tools::deleteUserMeta(self::TOKEN_META_KEY, $user_id);
    }

    /**
     * Generate the user token.
     *
     * @param int $user_id User ID.
     * @return string
     */
    public function generateToken( $user_id ) {

        $token = $this->getCode();

        HMWPP_Classes_Tools::saveUserMeta(self::TOKEN_META_KEY_TIMESTAMP, time(), $user_id);
        HMWPP_Classes_Tools::saveUserMeta(self::TOKEN_META_KEY, wp_hash( $token ), $user_id);

        return $token;
    }

    /**
     * Check if user has a valid token already.
     *
     * @param  int $user_id User ID.
     * @return boolean      If user has a valid email token.
     */
    public function isUserToken( $user_id ) {
        $hashed_token = $this->getUserToken( $user_id );

        if ( ! empty( $hashed_token ) ) {
            return true;
        }

        return false;
    }

    /**
     * Validate the user token.
     *
     * @param int    $user_id User ID.
     * @param string $token User token.
     * @return boolean
     */
    public function isValidToken( $user_id, $token ) {

        $hashed_token = self::getUserToken( $user_id );

        // Bail if token is empty or it doesn't match.
        if ( empty( $hashed_token ) || ! hash_equals( wp_hash( $token ), $hashed_token ) ) {
            return false;
        }

        if ( $this->isUserTokenExpired( $user_id ) ) {
            return false;
        }

        // Ensure the token can be used only once.
        $this->deleteUserToken( $user_id );

        // Save last successful login
        HMWPP_Classes_Tools::saveUserMeta(self::LAST_SUCCESSFUL_LOGIN_META_KEY, time(), $user_id);

        return true;

    }

    /**
     * Get the last login timestamp of the current user
     *
     * @param int $user_id The currently logged-in user ID.
     *
     * @return int timestamp of the last login
     */
    public function getLastLoginTimestamp( $user_id ){

        $last_totp_login = (int) HMWPP_Classes_Tools::getUserMeta(self::LAST_SUCCESSFUL_LOGIN_META_KEY, $user_id);

        if($last_totp_login > 0){
            return $last_totp_login;
        }

        return false;
    }

    /**
     * Has the user token validity timestamp expired.
     *
     * @param integer $user_id User ID.
     *
     * @return boolean
     */
    public function isUserTokenExpired( $user_id ) {
        $token_lifetime = $this->userTokenLifetime( $user_id );
        $token_ttl      = $this->userTokenTtl( $user_id );

        // Invalid token lifetime is considered an expired token.
        if ( is_int( $token_lifetime ) && $token_lifetime <= $token_ttl ) {
            return false;
        }

        return true;
    }

    /**
     * Get the lifetime of a user token in seconds.
     *
     * @param integer $user_id User ID.
     *
     * @return integer|null Return `null` if the lifetime can't be measured.
     */
    public function userTokenLifetime( $user_id ) {

        $hashed_token =  HMWPP_Classes_Tools::getUserMeta(self::TOKEN_META_KEY_TIMESTAMP, $user_id);
        $timestamp = intval( $hashed_token );

        if ( ! empty( $timestamp ) ) {
            return time() - $timestamp;
        }

        return null;
    }

    /**
     * Return the token time-to-live for a user.
     *
     * @param integer $user_id User ID.
     *
     * @return integer
     */
    public function userTokenTtl( $user_id ) {
        $token_ttl = 15 * MINUTE_IN_SECONDS;

        return (int) apply_filters( 'hmwp_token_ttl', $token_ttl, $user_id );
    }

    /**
     * Generate and email the user token.
     *
     * @param WP_User $user The WP_User instance representing the currently logged-in user.
     * @return bool Whether the email contents were sent successfully.
     */
    public function sendToken( $user, $token ) {

        $subject = wp_strip_all_tags( sprintf( __( 'Your login confirmation code for %s', 'hide-my-wp-pack' ), wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) ) );
        $message = wp_strip_all_tags( sprintf( __( 'Enter %s to log in.', 'hide-my-wp-pack' ), $token ) );

        $subject = apply_filters( 'hmwp_email_subject', $subject, $user->ID );
        $message = apply_filters( 'hmwp_email_message', $message, $token, $user->ID );

        if($email =  HMWPP_Classes_Tools::getUserMeta(self::EMAIL_META_KEY, $user->ID)){
            //send email with the token to this user
            return wp_mail( $email, $subject, $message );
        }

    }

    /**
     * Prints the form that prompts the user to authenticate.
     *
     * @param WP_User $user The WP_User instance representing the currently logged-in user.
     */
    public function authenticationPage( $user ) {
        if ( ! $user ) {
            return;
        }

        if ( ! $this->isUserToken( $user->ID ) || $this->isUserTokenExpired( $user->ID ) ) {
            $token = $this->generateToken( $user->ID );
            $this->sendToken( $user, $token);
        }

        require_once ABSPATH . '/wp-admin/includes/template.php';
        ?>
        <p class="hmwp-prompt"><?php esc_html_e( 'A verification code has been sent to the email address associated with your account.', 'hide-my-wp-pack' ); ?></p>
        <p>
            <label for="authcode"><?php esc_html_e( 'Verification Code:', 'hide-my-wp-pack' ); ?></label>
            <input type="text" inputmode="numeric" name="authcode" id="authcode" class="input authcode" value="" size="20" pattern="[0-9 ]*" placeholder="1234 5678" data-digits="8" />
            <?php submit_button( __( 'Log In', 'hide-my-wp-pack' ) ); ?>
        </p>
        <p class="two-factor-email-resend">
            <input type="submit" class="button" name="<?php echo esc_attr( self::INPUT_NAME_RESEND_CODE ); ?>" value="<?php esc_attr_e( 'Resend Code', 'hide-my-wp-pack' ); ?>" />
        </p>
        <script type="text/javascript">
            setTimeout( function(){
                var d;
                try{
                    d = document.getElementById('authcode');
                    d.value = '';
                    d.focus();
                } catch(e){}
            }, 200);
        </script>
        <?php
    }

    /**
     * Send the email code if missing or requested. Stop the authentication
     * validation if a new token has been generated and sent.
     *
     * @param  WP_USer $user The WP_User instance representing the currently logged-in user.
     * @return boolean
     */
    public function preAuthentication( $user ) {

        if ( isset( $user->ID ) && HMWPP_Classes_Tools::getValue(self::INPUT_NAME_RESEND_CODE) ) {

            $token = $this->generateToken( $user->ID );
            $this->sendToken( $user, $token );
            return true;

        }

        return false;
    }

    /**
     * Validates the users input token.
     *
     * @since 0.1-dev
     *
     * @param WP_User $user The WP_User instance representing the currently logged-in user.
     * @return boolean
     */
    public function validateAuthentication( $user ) {

        $code = $this->sanitizeCodeFromRequest( 'authcode' );

        if ( ! isset( $user->ID ) || ! $code ) {
            return false;
        }

        return $this->isValidToken( $user->ID, $code );
    }

    /**
     * Inserts markup at the end of the user profile field for this provider.
     *
     * @param WP_User $user The WP_User instance representing the currently logged-in user.
     *
     * @return array Options for the UsersSettings View
     */
    public function getEmailOption( $user ) {

        return array(
            'user' => $user,
            'placeholder' => $user->user_email,
            'email' => HMWPP_Classes_Tools::getUserMeta(self::EMAIL_META_KEY, $user->ID),
        );
    }

}
