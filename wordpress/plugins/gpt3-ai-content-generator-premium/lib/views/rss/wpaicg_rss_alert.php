<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$wpaicg_xml_enabled = extension_loaded('xml');
if(!$wpaicg_xml_enabled){
    ?>
    <div class="wpaicg-alert">
        <p style="color: #f00">
            <?php echo esc_html__('Please enable XML php extension','gpt3-ai-content-generator')?>
        </p>
    </div>
    <?php
}
?>
<?php
$wpaicg_crojob_rss_last_time = get_option('_wpaicg_crojob_rss_last_time','');
$wpaicg_cron_rss_added = get_option('_wpaicg_cron_rss_added','');
if (isset($_POST['wpaicg_delete_rss_running']) && check_admin_referer('wpaicg_delete_rss_running', 'wpaicg_delete_running_nonce')) {
    update_option('wpaicg_crojob_sheets_last_time', time());
    @unlink(WPAICG_PLUGIN_DIR.'wpaicg_rss.txt');
    echo '<script>window.location.reload()</script>';
    exit;
}
if(!empty($wpaicg_crojob_rss_last_time)){
    $wpaicg_timestamp_diff = time() - $wpaicg_crojob_rss_last_time;
    if($wpaicg_timestamp_diff > 600){
        ?>
        <div class="wpaicg-alert">
            <p style="color: #f00">
                <?php echo esc_html__('You can use the button below to restart your queue if it is stuck.','gpt3-ai-content-generator')?>
            </p>
            <form action="" method="post">
                <?php wp_nonce_field('wpaicg_delete_rss_running', 'wpaicg_delete_running_nonce'); ?>
                <button name="wpaicg_delete_rss_running" class="button button-primary"><?php echo esc_html__('Force Refresh','gpt3-ai-content-generator')?></button>
            </form>
        </div>
        <?php
    }
}
?>
<div class="nice-form-group">
    <?php if(empty($wpaicg_cron_rss_added)): ?>
        <div class="wpaicg_sheets_cron_error_msg">
        <p>Cron job is not active.</p>
    </div>
    <div class="wpaicg_sheets_cron_error_msg">
        <p>To activate it, copy the code below and paste it into the crontab in your server. Read the detailed guide <a href="https://docs.aipower.org/docs/AutoGPT/gpt-agents#cron-job-setup" target="_blank">here</a>.
    </div>
    <div class="nice-form-group">
        <div class="toggle-shortcode-sheets">
            <p>0 * * * * php <?php echo esc_html(ABSPATH)?>index.php -- wpaicg_rss=yes</p>
        </div>
    </div>
    <?php else: ?>
        <div class="wpaicg_sheets_cron_msg"><?= esc_html__('Your cron job is running properly.', 'gpt3-ai-content-generator') ?></div>
    <?php endif; ?>
</div>