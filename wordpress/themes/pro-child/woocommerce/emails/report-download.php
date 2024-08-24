<?php

if (!defined('ABSPATH')) {
	exit;
}

do_action('woocommerce_email_header', $email_heading, $email);

?>

<p><?php printf(esc_html__('Hi %s,', 'bgcrw'), esc_html($order->get_billing_first_name())); ?></p>
<p><?php printf(esc_html__('Thank you for purchasing your %s. Please click the link below to download', 'bgcrw'), wc_get_product($downloadData['product_id'])->get_name()); ?></p>
<p><a href="<?php echo esc_url($downloadData['url']); ?>" class="link"><?php esc_html_e('Download', 'bgcrw'); ?></a></p>

<?php

if ($additional_content) {
	echo wp_kses_post(wpautop(wptexturize($additional_content)));
}

do_action('woocommerce_email_footer', $email);
