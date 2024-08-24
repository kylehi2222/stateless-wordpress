<?php
/**
 * Email Footer
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-footer.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 *
 * @author  themes.email
 * @url     yourtheme/woocommerce/emails/email-footer.php
 * @package yourtheme/woocommerce/emails/
 * @version 2022
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Load colors.
$bg              = get_option( 'woocommerce_email_background_color' );
$body            = get_option( 'woocommerce_email_body_background_color' );
$base            = get_option( 'woocommerce_email_base_color' );
$text            = get_option( 'woocommerce_email_text_color' );
?>

                                                        <!--  Here Goes Content: End  -->    
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td height="58" style="line-height:0px; font-size:0px;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="font-family: Epilogue, Arial,Helvetica,sans-serif;font-size:13px;letter-spacing:.08em; color:#999999;" align="center">
                                <a href="https://support.humandesign.ai" style=" color:#999999; text-decoration:none;">Support Area</a> |
                                <a href="https://support.humandesign.ai/submit-ticket/" style=" color:#999999; text-decoration:none;">Contact Us</a>
                            </td>
                        </tr>
                        <tr>
                            <td height="24" style="line-height:0px; font-size:0px;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td align="center">
                                <table width="153" border="0" align="center" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td width="11">
                                            <a href="https://instagram.com/humandesign.ai" target="_blank">
                                                <img src="https://s3.amazonaws.com/templates.themes.email/2022/vaio/instagram.png" width="20" height="21" alt="Instagram" style="display:block;max-width:20px;" border="0" />
                                            </a>
                                        </td>
                                        <td width="11">
                                            <a href="https://www.facebook.com/humandesignai" target="_blank">
                                                <img src="https://s3.amazonaws.com/templates.themes.email/2022/vaio/facebook.png" width="11" height="21" alt="Facebook" style="display:block;max-width:11px;" border="0" />
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td height="58" style="line-height:0px; font-size:0px;">&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <!-- footer -->
            <tr>
                <td align="center">
                    <table width="600" border="0" cellspacing="0" cellpadding="0" class="em_wrapper">
                        <tr>
                            <td height="10" style="line-height:0px; font-size:0px;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style=" color:#b2b2b2;font-family: Arial,Helvetica,sans-serif;font-size:10px;" align="center">
                                <?php echo wp_kses_post( wptexturize( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) ) ); ?>
                            </td>
                        </tr>
                        <tr>
                            <td height="10" style="line-height:0px; font-size:0px;">&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <div style="display:none; white-space:nowrap; font:20px courier; background-color:<?php echo esc_attr( $body ); ?>; color:<?php echo esc_attr( $body ); ?>;">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</div>
    </body>
</html>