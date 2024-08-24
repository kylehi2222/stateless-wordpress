<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="nice-form-group">


    <?php
    // Retrieve options from the database.
    $wpaicg_crojob_sheets_last_time = get_option('wpaicg_crojob_sheets_last_time', '');
    $wpaicg_cron_sheets_added = get_option('wpaicg_cron_sheets_added', '');

    // Handle POST request to restart the cron job.
    if (isset($_POST['wpaicg_delete_sheets_running']) && check_admin_referer('wpaicg_delete_sheets_running', 'wpaicg_delete_running_nonce')) {
        update_option('wpaicg_crojob_sheets_last_time', time());
        @unlink(WPAICG_PLUGIN_DIR . 'wpaicg_sheets.txt');
        echo '<script>window.location.reload()</script>';
        exit;
    }

    // Check if there is a last cron job run time.
    if (!empty($wpaicg_crojob_sheets_last_time)) {
        $wpaicg_current_timestamp = time();
        $wpaicg_timestamp_diff = $wpaicg_current_timestamp - $wpaicg_crojob_sheets_last_time;
        $wpaicg_time_diff = human_time_diff($wpaicg_crojob_sheets_last_time, $wpaicg_current_timestamp);

        // Prepare the time difference message.
        $search = ['hours', 'hour', 'days', 'day', 'minutes', 'minute'];
        $replace = array_map(function ($item) { return esc_html__($item, 'gpt3-ai-content-generator'); }, $search);
        $wpaicg_output = str_replace($search, $replace, $wpaicg_time_diff);
        
        // Display the last cron job run time and restart button in one line.
        ?>
        <div class="nice-form-group" style="display: flex; align-items: center;">
            <p style="margin: 0; padding:0.4em"><small>Last cron job run: <?= date('Y-m-d H:i:s', $wpaicg_crojob_sheets_last_time) ?> (<?= $wpaicg_output ?> ago)</small></p>
            <form action="" method="post" style="display: inline;">
                <?php wp_nonce_field('wpaicg_delete_sheets_running', 'wpaicg_delete_running_nonce'); ?>
                <button name="wpaicg_delete_sheets_running" class="button button-primary"><?= esc_html__('Restart Queue', 'gpt3-ai-content-generator') ?></button>
            </form>
        </div>
        <?php
    }
    ?>

</div>

