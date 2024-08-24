<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$wpaicg_cron_tweet_last_time = get_option('wpaicg_cron_tweet_last_time','');
$wpaicg_cron_tweet_added = get_option('wpaicg_cron_tweet_added','');
if (isset($_POST['wpaicg_delete_tweet_running']) && check_admin_referer('wpaicg_delete_tweet_running', 'wpaicg_delete_running_nonce')) {
    update_option('wpaicg_cron_tweet_last_time', time());
    @unlink(WPAICG_PLUGIN_DIR.'wpaicg_tweet.txt');
    echo '<script>window.location.reload()</script>';
    exit;
}
?>
<div class="wpaicg-alert">
    <?php
    if(empty($wpaicg_cron_tweet_added)): ?>
        <div class="wpaicg_sheets_cron_error_msg">
            <p>Cron job is not active.</p>
        </div>
        <div class="wpaicg_sheets_cron_error_msg">
            <p>To activate it, copy the code below and paste it into the crontab in your server. Read the detailed guide <a href="https://docs.aipower.org/docs/AutoGPT/gpt-agents#cron-job-setup" target="_blank">here</a>.
        </div>
        <div class="nice-form-group">
            <div class="toggle-shortcode-sheets">
                <p>* * * * * php <?php echo esc_html(ABSPATH)?>index.php -- wpaicg_tweet=yes</p>
            </div>
        </div>
    <?php else: ?>
        <div class="wpaicg_sheets_cron_msg"><?= esc_html__('Your cron job is running properly.', 'gpt3-ai-content-generator') ?></div>
    <?php endif; ?>
</div>
