<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$wpaicg_google_credentials_json = get_option('wpaicg_google_credentials_json',[]);
?>
<style>
    .wpaicg_sheets_error_msg{}
    .wpaicg_sheets_error_msg > div{
        color: #F44337;
    }
</style>

<form action="" method="post" class="wpaicg_sheets_settings" enctype="multipart/form-data">
    <?php
    wp_nonce_field('wpaicg_google_sheets_setting','nonce');
    ?>
    <input type="hidden" name="action" value="wpaicg_google_sheet_save">
    <div class="wpaicg_sheets_error_msg"></div>
    <div class="wpaicg_sheets_settings_fields">
        <?php
        if($wpaicg_google_credentials_json && is_array($wpaicg_google_credentials_json) && count($wpaicg_google_credentials_json)):
            include __DIR__.'/fields.php';
        endif;
        ?>
    </div>
    <?php
    // Now check if the credentials should be displayed outside the previous div
    if(!$wpaicg_google_credentials_json || !is_array($wpaicg_google_credentials_json) || !count($wpaicg_google_credentials_json)):
    ?>
    <div class="first_cred_container">
        <p>You need to upload your Google Sheets credentials in a JSON file. Read the tutorial <a href="https://docs.aipower.org/docs/AutoGPT/auto-content-writer/google-sheets" target="_blank">here</a>.</p>
        <div class="nice-form-group">
            <input type="file" name="file" class="wpaicg_sheets_first_credentials">
        </div>
        <div class="nice-form-group">
            <button class="button button-primary wpaicg_sheets_save"><?php echo esc_html__('Save', 'gpt3-ai-content-generator') ?></button>
        </div>
    </div>
    <?php endif; ?>
</form>
<script>
    jQuery(document).ready(function ($){
        function wpaicgLoading(btn){
            btn.attr('disabled','disabled');
            if(!btn.find('spinner').length){
                btn.append('<span class="spinner"></span>');
            }
            btn.find('.spinner').css('visibility','unset');
        }
        function wpaicgRmLoading(btn){
            btn.removeAttr('disabled');
            btn.find('.spinner').remove();
        }
        $('.wpaicg_sheets_settings').on('submit', function (e){
            e.preventDefault();
            let form = $(this);
            let btn = form.find('.wpaicg_sheets_save');
            let data = new FormData(form[0]);
            let has_error = false;
            if(form.find('.wpaicg_sheets_first_credentials').length > 0 && form.find('.wpaicg_sheets_first_credentials')[0].files.length === 0){
                has_error = '<?php echo esc_html__('Please upload your credentials','gpt3-ai-content-generator')?>';
            }
            if(!has_error && form.find('.wpaicg_google_sheets_url').length && form.find('.wpaicg_google_sheets_url').val() === ''){
                has_error = '<?php echo esc_html__('Please add Google Sheets URL','gpt3-ai-content-generator')?>';
            }
            if(!has_error) {
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php')?>',
                    data: data,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'JSON',
                    type: 'POST',
                    beforeSend: function () {
                        wpaicgLoading(btn);
                    },
                    success: function (res) {
                        wpaicgRmLoading(btn);
                        if(res.status === 'success'){
                            $('.wpaicg_sheets_success_msg').empty();
                            // change wpaicg_sheets_success_msg from hide to block
                            $('.wpaicg_sheets_success_msg').css('display','block');
                            $('.wpaicg_sheets_success_msg').html('<div><?php echo esc_html__('Record updated successfully','gpt3-ai-content-generator')?></div>');
                            $('.wpaicg_sheets_settings_fields').html(res.html);
                            // hide first_cred_container
                            $('.first_cred_container').hide();
                        }
                        else{
                            $('.wpaicg_sheets_error_msg').empty();
                            $('.wpaicg_sheets_error_msg').html('<div>'+res.msg+'</div>');
                        }
                    }
                })
                return false;
            }
            else{
                alert(has_error);
            }
        })
    })
</script>
