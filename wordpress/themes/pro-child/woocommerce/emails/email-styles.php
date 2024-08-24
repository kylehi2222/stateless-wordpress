<?php
/**
 * Email Styles
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-styles.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 *
 * @author  themes.email
 * @url     yourtheme/woocommerce/emails/email-styles.php
 * @package yourtheme/woocommerce/emails/
 * @version 2022
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load colors.
$bg              = get_option( 'woocommerce_email_background_color' ); // Predefined #f5f5f5
$body            = get_option( 'woocommerce_email_body_background_color' ); // Predefined #ffffff
$base            = get_option( 'woocommerce_email_base_color' ); // Predefined #00237e
$base_text       = wc_light_or_dark( $base, '#202020', '#ffffff' );
$text            = get_option( 'woocommerce_email_text_color' ); // Predefined #666666

// Pick a contrasting color for links.
$link = wc_hex_is_light( $base ) ? $base : $base_text;
if ( wc_hex_is_light( $body ) ) {
	$link = wc_hex_is_light( $base ) ? $base_text : $base;
}

$bg_darker_10    = wc_hex_darker( $bg, 10 );
$body_darker_10  = wc_hex_darker( $body, 10 );
$base_lighter_20 = wc_hex_lighter( $base, 20 );
$base_lighter_40 = wc_hex_lighter( $base, 40 );
$text_lighter_20 = wc_hex_lighter( $text, 20 );

// Add Google Font 'Epilogue'
$font_family = 'Epilogue';
$font_url = 'https://fonts.googleapis.com/css2?family=' . urlencode( $font_family ) . ':wght@300;400;700&display=swap';

?>
#wrapper {
	background-color: <?php echo esc_attr( $bg ); ?>;
	margin: 0;
	padding: 70px 0 70px 0;
	-webkit-text-size-adjust: none !important;
	width: 100%;
}

#template_container {
	box-shadow: 0 1px 4px rgba(0,0,0,0.1) !important;
	background-color: <?php echo esc_attr( $body ); ?>;
	border: 1px solid <?php echo esc_attr( $bg_darker_10 ); ?>;
	border-radius: 3px !important;
}

#template_header {
	background-color: <?php echo esc_attr( $base ); ?>;
	border-radius: 3px 3px 0 0 !important;
	color: <?php echo esc_attr( $base_text ); ?>;
	border-bottom: 0;
	font-weight: bold;
	line-height: 100%;
	vertical-align: middle;
	font-family: 'Epilogue', Arial, Helvetica, sans-serif;
}

#template_header h1,
#template_header h1 a {
	color: <?php echo esc_attr( $base_text ); ?>;
}

#template_footer td {
	padding: 0;
	-webkit-border-radius: 6px;
}

#template_footer #credit {
	border:0;
	color: <?php echo esc_attr( $base_lighter_40 ); ?>;
	font-family: 'Epilogue', Arial, Helvetica, sans-serif;
	font-size:12px;
	line-height:125%;
	text-align:center;
	padding: 0 48px 48px 48px;
}

#body_content {
	background-color: <?php echo esc_attr( $body ); ?>;
}

#body_content table td {
	padding: 48px 48px 0;
}

#body_content table td td {
	padding: 12px;
}

#body_content table td th {
	padding: 12px;
}

#body_content td ul.wc-item-meta {
	font-size: small;
	margin: 1em 0 0;
	padding: 0;
	list-style: none;
}

#body_content td ul.wc-item-meta li {
	margin: 0.5em 0 0;
	padding: 0;
}

#body_content td ul.wc-item-meta li p {
	margin: 0;
}

#body_content p {
	margin: 0 0 16px;
}

#body_content_inner {
	color: <?php echo esc_attr( $text_lighter_20 ); ?>;
	font-family: 'Epilogue', Arial, Helvetica, sans-serif;
	font-size: 13px;
	line-height: 150%;
	text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
}

.td {
	color: <?php echo esc_attr( $text_lighter_20 ); ?>;
	//border: 1px solid <?php echo esc_attr( $body_darker_10 ); ?>;
	vertical-align: middle;
}

.address {
	// padding:12px 12px 0;
	color: <?php echo esc_attr( $text_lighter_20 ); ?>;
	// border: 1px solid <?php echo esc_attr( $body_darker_10 ); ?>;
}

.text {
	color: <?php echo esc_attr( $text ); ?>;
	font-family: 'Epilogue', Arial, Helvetica, sans-serif;
}

.link {
	color: <?php echo esc_attr( $base ); ?>;
}

#header_wrapper {
	padding: 36px 48px;
	display: block;
}

h1 {
	color: <?php echo esc_attr( $base ); ?>;
	font-family: 'Epilogue', Arial, Helvetica, sans-serif;
	font-size: 30px;
	font-weight: 300;
	line-height: 150%;
	margin: 0;
	text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
	text-shadow: 0 1px 0 <?php echo esc_attr( $base_lighter_20 ); ?>;
}

h2 {
	color: <?php echo esc_attr( $base ); ?>;
	display: block;
	font-family: 'Epilogue', Arial, Helvetica, sans-serif;
	font-size: 18px;
	font-weight: bold;
	line-height: 130%;
	margin: 30px 0px 15px 0px;
	text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
}

h3 {
	color: <?php echo esc_attr( $base ); ?>;
	display: block;
	font-family: 'Epilogue', Arial, Helvetica, sans-serif;
	font-size: 16px;
	font-weight: bold;
	line-height: 130%;
	margin: 16px 0 8px;
	text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
}

a {
	color: <?php echo esc_attr( $link ); ?>;
	font-weight: normal;
	text-decoration: underline;
}

img { border: none;
	display: inline-block;
	font-size: 14px;
	font-weight: bold;
	height: auto;
	outline: none;
	text-decoration: none;
	text-transform: capitalize;
	vertical-align: middle;
	margin-<?php echo is_rtl() ? 'left' : 'right'; ?>: 10px;
}

#emailcontent thead th {
    border-bottom: 1px solid #ebebeb;
}

#emailcontent tbody td {
    border-bottom: 1px solid #ebebeb;
}

#emailcontent tbody tr.order_item td:first-child {
   font-weight:bold;
}

#emailcontent tfoot {
    background-color: #f5f5f5;
}
#emailcontent tfoot th,
#emailcontent tfoot td {
    padding-top: 8px !important;
    padding-bottom: 8px !important;
}

<?php
// Add Google Font 'Epilogue' to the head of the email
if ( ! function_exists( 'add_epilogue_font_to_email_head' ) ) {
	function add_epilogue_font_to_email_head() {
		global $font_url;
		echo '<link href="' . esc_url( $font_url ) . '" rel="stylesheet" type="text/css" />' . "\n";
	}
}

add_action( 'woocommerce_email_header', 'add_epilogue_font_to_email_head' );