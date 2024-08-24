<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$wpaicg_google_sheets_url = get_option('wpaicg_google_sheets_url','');
$wpaicg_google_sheets_status = get_option('wpaicg_google_sheets_status','');
$wpaicg_google_sheets_cron = get_option('wpaicg_google_sheets_cron','yes');
$wpaicg_cron_sheets_added = get_option('wpaicg_cron_sheets_added','');
$wpaicg_google_sheets_limitation = get_option('wpaicg_google_sheets_limitation',60);
$wpaicg_google_credentials_json = get_option('wpaicg_google_credentials_json',[]);
?>
<div class="nice-form-group">
    <?php if($wpaicg_google_credentials_json && is_array($wpaicg_google_credentials_json) && count($wpaicg_google_credentials_json)): ?>
        <p style="color: green; padding:0.4em; display:flex;"><small>Credentials: OK</small>
        <label onclick="document.getElementById('wpaicg_file_upload').style.display='block';" style="color: #2271B1;margin: 0 1em;"><?php echo esc_html__('Re-upload', 'gpt3-ai-content-generator'); ?></label>
        </p>
        <input type="file" name="file" id="wpaicg_file_upload" style="display:none;">
            <?php if (empty($wpaicg_cron_sheets_added)): ?>
                <div class="wpaicg_sheets_cron_error_msg">
                    <p>Cron job is not active.</p>
                </div>
                <div class="wpaicg_sheets_cron_error_msg">
                    <p>To activate it, copy the code below and paste it into the crontab in your server. Read the detailed guide <a href="https://docs.aipower.org/docs/AutoGPT/gpt-agents#cron-job-setup" target="_blank">here</a>.
                </div>
                <div class="nice-form-group">
                    <div class="toggle-shortcode-sheets">
                        <p>* * * * * php <?php echo esc_html(ABSPATH)?>index.php -- wpaicg_sheets=yes</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="wpaicg_sheets_cron_msg"><?= esc_html__('Your cron job is running properly.', 'gpt3-ai-content-generator') ?></div>
            <?php endif; ?>
    <?php else: ?>
        <!-- If credentials are not present, directly show the upload option -->
        <label><?php echo esc_html__('Upload Credentials', 'gpt3-ai-content-generator'); ?></label>
        <input type="file" name="file">
    <?php endif; ?>
</div>
<table class="form-table">
<tr>
    <th><?php echo esc_html__('Google Sheet URL','gpt3-ai-content-generator')?></th>
    <td>
        <input class="wpaicg_google_sheets_url" type="text" name="url" value="<?php echo isset($wpaicg_google_sheets_url) ? esc_html($wpaicg_google_sheets_url) : ''?>">
        <?php
        if($wpaicg_google_sheets_status == 'accessible'):
            ?>
            <span style="display: inline-block;width: 20px;height: 20px;background: #24A148;position: relative;top: 5px;border-radius: 50%;"></span> <?php echo esc_html__('File is accessible!','gpt3-ai-content-generator')?>
            <a href="<?php echo esc_url($wpaicg_google_sheets_url); ?>" target="_blank" style="margin-left: 10px;"><?php echo esc_html__('Open','gpt3-ai-content-generator')?></a>
        <?php else:
            ?>
            <span style="display: inline-block;width: 20px;height: 20px;background: #F44337;position: relative;top: 5px;border-radius: 50%;"></span><span style="display: block;font-style: italic;font-size: 12px;margin-top: 2px"> <?php echo esc_html__('Your Google Sheet is not accessible. Check permissions or URL.','gpt3-ai-content-generator')?></span>
        <?php
        endif;
        ?>
        <span style="display: block;font-style: italic;font-size: 12px;margin-top: 2px"><?php echo esc_html__('Example: https://docs.google.com/spreadsheets/d/xxxxxxxxxxxxxxx/edit','gpt3-ai-content-generator')?></span>
    </td>
</tr>
<tr>
    <th><?php echo esc_html__('Row Update per Minute','gpt3-ai-content-generator')?></th>
    <td>
        <input type="number" value="<?php echo esc_html($wpaicg_google_sheets_limitation)?>" name="limitation">
        <span style="display: block;font-style: italic;font-size: 12px;margin-top: 2px"><?php echo esc_html__('Default: 60 - Do not change this value unless you have a special quota from','gpt3-ai-content-generator')?> <a href="https://cloud.google.com/docs/quota#requesting_higher_quota" target="_blank">Google</a></span>
    </td>
</tr>
<tr>
    <th><?php echo esc_html__('Constant Crawling','gpt3-ai-content-generator')?></th>
    <td>
        <select name="status">
            <option <?php echo isset($wpaicg_google_sheets_cron) && $wpaicg_google_sheets_cron == 'yes' ? ' selected':''?> value="yes"><?php echo esc_html__('Yes','gpt3-ai-content-generator')?></option>
            <option <?php echo isset($wpaicg_google_sheets_cron) && $wpaicg_google_sheets_cron == 'no' ? ' selected':''?> value="no"><?php echo esc_html__('No','gpt3-ai-content-generator')?></option>
        </select>
    </td>
</tr>
<tr>
    <th></th>
    <td><button class="button button-primary wpaicg_sheets_save"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button></td>
</tr>
</table>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var copyCode = document.querySelector('.toggle-shortcode-sheets');
        
        if (copyCode) { // Check if the element exists
            copyCode.addEventListener('click', function() {
            // Copy text
            navigator.clipboard.writeText(this.textContent).then(() => {
                // Temporarily change the text to indicate copy
                const originalText = this.textContent;
                this.textContent = 'Copied!';
                setTimeout(() => {
                this.textContent = originalText;
                }, 2000); // Reset text after 2 seconds
            }).catch(err => {
                console.error('Failed to copy: ', err);
            });
            });
        } else {
            console.warn('Element with class .toggle-shortcode-sheets not found.');
        }
    });
</script>