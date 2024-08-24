<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$success = false;
if(isset($_POST['wpaicg_chat_save'])){
    // Check the nonce
    if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'wpaicg_chat_nonce' ) ) {
        wp_die( __('Nonce verification failed.', 'gpt3-ai-content-generator') );
    }
    if (isset($_POST['wpaicg_chat_enable_sale']) && !empty($_POST['wpaicg_chat_enable_sale'])) {
        update_option('wpaicg_chat_enable_sale', sanitize_text_field($_POST['wpaicg_chat_enable_sale']));
    } else {
        delete_option('wpaicg_chat_enable_sale');
    }
    if (isset($_POST['wpaicg_elevenlabs_hide_error']) && !empty($_POST['wpaicg_elevenlabs_hide_error'])) {
        update_option('wpaicg_elevenlabs_hide_error', sanitize_text_field($_POST['wpaicg_elevenlabs_hide_error']));
    } else {
        delete_option('wpaicg_elevenlabs_hide_error');
    }
    if (isset($_POST['wpaicg_google_api_key']) && !empty($_POST['wpaicg_google_api_key'])) {
        update_option('wpaicg_google_api_key', sanitize_text_field($_POST['wpaicg_google_api_key']));
    } else {
        delete_option('wpaicg_google_api_key');
    }
    if (isset($_POST['wpaicg_google_search_engine_id']) && !empty($_POST['wpaicg_google_search_engine_id'])) {
        update_option('wpaicg_google_search_engine_id', sanitize_text_field($_POST['wpaicg_google_search_engine_id']));
    } else {
        delete_option('wpaicg_google_search_engine_id');
    }
    if (isset($_POST['wpaicg_google_search_country']) && !empty($_POST['wpaicg_google_search_country'])) {
        update_option('wpaicg_google_search_country', sanitize_text_field($_POST['wpaicg_google_search_country']));
    } else {
        delete_option('wpaicg_google_search_country');
    }
    if (isset($_POST['wpaicg_google_search_language']) && !empty($_POST['wpaicg_google_search_language'])) {
        update_option('wpaicg_google_search_language', sanitize_text_field($_POST['wpaicg_google_search_language']));
    } else {
        delete_option('wpaicg_google_search_language');
    }
    if (isset($_POST['wpaicg_google_search_num']) && !empty($_POST['wpaicg_google_search_num'])) {
        update_option('wpaicg_google_search_num', sanitize_text_field($_POST['wpaicg_google_search_num']));
    } else {
        update_option('wpaicg_google_search_num', 10); // Default to 10 if not set or invalid
    }
    
    if (isset($_POST['wpaicg_elevenlabs_api']) && !empty($_POST['wpaicg_elevenlabs_api'])) {
        update_option('wpaicg_elevenlabs_api', sanitize_text_field($_POST['wpaicg_elevenlabs_api']));
    } else {
        delete_option('wpaicg_elevenlabs_api');
        delete_option('wpaicg_chat_to_speech');
    }
    if (isset($_POST['wpaicg_banned_ips']) && !empty($_POST['wpaicg_banned_ips'])) {
        update_option('wpaicg_banned_ips', sanitize_text_field($_POST['wpaicg_banned_ips']));
    } else {
        delete_option('wpaicg_banned_ips');
    }
    // Handling the form submission to save banned words
    if (isset($_POST['wpaicg_banned_words']) && !empty($_POST['wpaicg_banned_words'])) {
        update_option('wpaicg_banned_words', sanitize_text_field($_POST['wpaicg_banned_words']));
    } else {
        delete_option('wpaicg_banned_words');
    }
    // Handling the new "User Uploads Preference" option
    if (isset($_POST['wpaicg_user_uploads']) && in_array($_POST['wpaicg_user_uploads'], ['filesystem', 'media_library'])) {
        update_option('wpaicg_user_uploads', sanitize_text_field($_POST['wpaicg_user_uploads']));
    } else {
        // Set default value if not set or invalid
        update_option('wpaicg_user_uploads', 'filesystem');
    }
    if (isset($_POST['wpaicg_img_processing_method']) && in_array($_POST['wpaicg_img_processing_method'], ['url', 'base64'])) {
        update_option('wpaicg_img_processing_method', sanitize_text_field($_POST['wpaicg_img_processing_method']));
    } else {
        // Set default value if not set or invalid
        update_option('wpaicg_img_processing_method', 'url');
    }
    if (isset($_POST['wpaicg_img_vision_quality']) && in_array($_POST['wpaicg_img_vision_quality'], ['auto', 'low', 'high'])) {
        update_option('wpaicg_img_vision_quality', sanitize_text_field($_POST['wpaicg_img_vision_quality']));
    } else {
        // Set default value if not set or invalid
        update_option('wpaicg_img_vision_quality', 'auto');
    }
    if (isset($_POST['wpaicg_typewriter_effect']) && !empty($_POST['wpaicg_typewriter_effect'])) {
        update_option('wpaicg_typewriter_effect', sanitize_text_field($_POST['wpaicg_typewriter_effect']));
    } else {
        delete_option('wpaicg_typewriter_effect');
    }
    if (isset($_POST['wpaicg_typewriter_effect']) && !empty($_POST['wpaicg_typewriter_effect']) && isset($_POST['wpaicg_typewriter_speed'])) {
        update_option('wpaicg_typewriter_speed', sanitize_text_field($_POST['wpaicg_typewriter_speed']));
    } elseif(empty($_POST['wpaicg_typewriter_effect'])) {
        delete_option('wpaicg_typewriter_speed');
    }
    if (isset($_POST['wpaicg_delete_image']) && !empty($_POST['wpaicg_delete_image'])) {
        update_option('wpaicg_delete_image', sanitize_text_field($_POST['wpaicg_delete_image']));
    } else {
        delete_option('wpaicg_delete_image');
    }
    // Handling the new "Enable Assistants" option
    $wpaicg_assistant_feature = isset($_POST['wpaicg_assistant_feature']) ? 1 : 0;
    update_option('wpaicg_assistant_feature', $wpaicg_assistant_feature);
    // Handling autoload past conversations.
    $wpaicg_autoload_chat_conversations = isset($_POST['wpaicg_autoload_chat_conversations']) ? 1 : 0;
    update_option('wpaicg_autoload_chat_conversations', $wpaicg_autoload_chat_conversations, 'no');
    // Ensure delete image option is false if processing method is URL
    if (get_option('wpaicg_img_processing_method', 'url') === 'url') {
        delete_option('wpaicg_delete_image');
    }
    $success = true;
}
$autoload_chat = get_option('wpaicg_autoload_chat_conversations', false);
// delete if not set or 0
if(!$autoload_chat || $autoload_chat == 0){
    delete_option('wpaicg_autoload_chat_conversations');
}

$wpaicg_chat_enable_sale = get_option('wpaicg_chat_enable_sale', false);
$wpaicg_elevenlabs_hide_error = get_option('wpaicg_elevenlabs_hide_error', false);
$wpaicg_elevenlabs_api = get_option('wpaicg_elevenlabs_api', '');
$wpaicg_google_api_key = get_option('wpaicg_google_api_key', '');
$wpaicg_google_search_engine_id = get_option('wpaicg_google_search_engine_id', '');
$wpaicg_google_search_country = get_option('wpaicg_google_search_country', '');
$wpaicg_google_search_language = get_option('wpaicg_google_search_language', '');
$wpaicg_google_search_num = get_option('wpaicg_google_search_num', 10); // Default to 10
// Array of countries for the dropdown
$countries = \WPAICG\WPAICG_Util::get_instance()->wpaicg_countries;
$search_languages = \WPAICG\WPAICG_Util::get_instance()->search_languages;
$wpaicg_typewriter_effect = get_option('wpaicg_typewriter_effect', false);
$wpaicg_typewriter_speed = get_option('wpaicg_typewriter_speed', 1);
if($success){
    echo '<div class="notice notice-success is-dismissible"><p>'.esc_html__('Settings saved successfully!','gpt3-ai-content-generator').'</p></div>';
    
}
?>
<div id="wpaicg_message" style="display: none;"></div>
<form action="" method="post">
    <?php wp_nonce_field('wpaicg_chat_nonce'); ?>
    <h3>
        <svg  xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-key"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16.555 3.843l3.602 3.602a2.877 2.877 0 0 1 0 4.069l-2.643 2.643a2.877 2.877 0 0 1 -4.069 0l-.301 -.301l-6.558 6.558a2 2 0 0 1 -1.239 .578l-.175 .008h-1.172a1 1 0 0 1 -.993 -.883l-.007 -.117v-1.172a2 2 0 0 1 .467 -1.284l.119 -.13l.414 -.414h2v-2h2v-2l2.144 -2.144l-.301 -.301a2.877 2.877 0 0 1 0 -4.069l2.643 -2.643a2.877 2.877 0 0 1 4.069 0z" /><path d="M15 9h.01" /></svg>
        <?php echo esc_html__('Integrations','gpt3-ai-content-generator')?>
    </h3>
    <div class="nice-form-group">
        <label><?php echo esc_html__('Google API Key','gpt3-ai-content-generator')?></label>
        <input style="width: 400px;" type="text" class="wpaicg_google_api_key" value="<?php echo esc_html($wpaicg_google_api_key)?>" name="wpaicg_google_api_key">
        <a href="https://console.cloud.google.com/" target="_blank"><?php echo esc_html__('Get API Key','gpt3-ai-content-generator')?></a>
    </div>
    <div class="nice-form-group">
        <label><?php echo esc_html__('ElevenLabs API Key','gpt3-ai-content-generator')?></label>
        <input style="width: 400px;" type="text" class="wpaicg_elevenlabs_api" value="<?php echo esc_html($wpaicg_elevenlabs_api)?>" name="wpaicg_elevenlabs_api">
        <a href="https://beta.elevenlabs.io/speech-synthesis" target="_blank"><?php echo esc_html__('Get API Key','gpt3-ai-content-generator')?></a>
    </div>
    <hr>
    <h3>
        <svg  xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-world-www"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M19.5 7a9 9 0 0 0 -7.5 -4a8.991 8.991 0 0 0 -7.484 4" /><path d="M11.5 3a16.989 16.989 0 0 0 -1.826 4" /><path d="M12.5 3a16.989 16.989 0 0 1 1.828 4" /><path d="M19.5 17a9 9 0 0 1 -7.5 4a8.991 8.991 0 0 1 -7.484 -4" /><path d="M11.5 21a16.989 16.989 0 0 1 -1.826 -4" /><path d="M12.5 21a16.989 16.989 0 0 0 1.828 -4" /><path d="M2 10l1 4l1.5 -4l1.5 4l1 -4" /><path d="M17 10l1 4l1.5 -4l1.5 4l1 -4" /><path d="M9.5 10l1 4l1.5 -4l1.5 4l1 -4" /></svg>
        <?php echo esc_html__('Internet Browsing','gpt3-ai-content-generator')?>
    </h3>
    <a href="https://docs.aipower.org/docs/ChatGPT/advanced-setup/internet-browsing" target="_blank"><?php echo esc_html__('Read Documentation','gpt3-ai-content-generator')?></a>
    <div class="nice-form-group">
        <label><?php echo esc_html__('Google Custom Search Engine ID','gpt3-ai-content-generator')?></label>
        <input style="width: 400px;" type="text" class="wpaicg_google_search_engine_id" value="<?php echo esc_html($wpaicg_google_search_engine_id)?>" name="wpaicg_google_search_engine_id">
        <a href="https://programmablesearchengine.google.com/" target="_blank"><?php echo esc_html__('Get Search Engine ID','gpt3-ai-content-generator')?></a>
    </div>
    <div class="nice-form-group" style="display: flex; justify-content: flex-start; gap: 10px;">
        <div class="nice-form-group">
            <label><?php echo esc_html__('Region','gpt3-ai-content-generator')?></label>
            <select name="wpaicg_google_search_country" style="width: 130px;">
                <?php foreach ($countries as $value => $label): ?>
                    <option value="<?php echo esc_attr($value); ?>" <?php selected($wpaicg_google_search_country, $value); ?>>
                        <?php echo esc_html($label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="nice-form-group">
            <label><?php echo esc_html__('Language','gpt3-ai-content-generator')?></label>
            <select name="wpaicg_google_search_language" style="width: 130px;">
                <?php foreach ($search_languages as $value => $label): ?>
                    <option value="<?php echo esc_attr($value); ?>" <?php selected($wpaicg_google_search_language, $value); ?>>
                        <?php echo esc_html($label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="nice-form-group">
            <label><?php echo esc_html__('Search Results','gpt3-ai-content-generator')?></label>
            <select name="wpaicg_google_search_num" style="width: 130px;">
                <?php for ($i = 1; $i <= 10; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php selected($wpaicg_google_search_num, $i); ?>>
                        <?php echo $i; ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>
    </div>
    <hr>
    <h3>
        <svg  xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-microphone"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 2m0 3a3 3 0 0 1 3 -3h0a3 3 0 0 1 3 3v5a3 3 0 0 1 -3 3h0a3 3 0 0 1 -3 -3z" /><path d="M5 10a7 7 0 0 0 14 0" /><path d="M8 21l8 0" /><path d="M12 17l0 4" /></svg>
        <?php echo esc_html__('Text to Speech','gpt3-ai-content-generator')?>
    </h3>
    <div class="nice-form-group" style="display: flex; justify-content: flex-start; gap: 10px;margin-top: -1em;">
        <div class="nice-form-group">
            <button class="button button-primary wpaicg_sync_voices" type="button"><?php echo esc_html__('Sync ElevenLabs Voices','gpt3-ai-content-generator')?></button>
        </div>
        <div class="nice-form-group">
            <button class="button button-primary wpaicg_sync_models" type="button"><?php echo esc_html__('Sync ElevenLabs Models','gpt3-ai-content-generator')?></button>
        </div>
        <div class="nice-form-group">
            <button class="button button-primary wpaicg_sync_google_voices" type="button"><?php echo esc_html__('Sync Google Voices','gpt3-ai-content-generator')?></button>
        </div>
    </div>
    <hr>
    <h3>
        <svg  xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-shield-lock"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3" /><path d="M12 11m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M12 12l0 2.5" /></svg>
        <?php echo esc_html__('Security','gpt3-ai-content-generator')?>
    </h3>
    <div class="nice-form-group">
        <label><?php echo esc_html__('Banned IP Addresses','gpt3-ai-content-generator')?></label>
        <input style="width: 400px;" type="text" value="<?php echo esc_attr(get_option('wpaicg_banned_ips', ''))?>" name="wpaicg_banned_ips" placeholder="e.g., 123.456.789.0, 987.654.321.0">
        <p class="description"><?php echo esc_html__('Enter IP addresses separated by commas.','gpt3-ai-content-generator')?></p>
    </div>
    <div class="nice-form-group">
        <label><?php echo esc_html__('Banned Words','gpt3-ai-content-generator')?></label>
        <input style="width: 400px;" type="text" value="<?php echo esc_attr(get_option('wpaicg_banned_words', ''))?>" name="wpaicg_banned_words" placeholder="e.g., badword1, badword2">
        <p class="description"><?php echo esc_html__('Enter words separated by commas.','gpt3-ai-content-generator')?></p>
    </div>
    <hr>
    <h3>
        <svg  xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-photo"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 8h.01" /><path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z" /><path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5" /><path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3" /></svg>
        <?php echo esc_html__('Images','gpt3-ai-content-generator')?>
    </h3>
    <table class="form-table">
        <tr>
            <th><?php echo esc_html__('User Upload','gpt3-ai-content-generator')?></th>
            <td>
                <?php $wpaicg_user_uploads = get_option('wpaicg_user_uploads', 'filesystem'); ?>
                <select name="wpaicg_user_uploads">
                    <option value="filesystem" <?php selected($wpaicg_user_uploads, 'filesystem'); ?>><?php echo esc_html__('Filesystem', 'gpt3-ai-content-generator'); ?></option>
                    <option value="media_library" <?php selected($wpaicg_user_uploads, 'media_library'); ?>><?php echo esc_html__('Media Library', 'gpt3-ai-content-generator'); ?></option>
                </select>
                <a href="https://docs.aipower.org/docs/ChatGPT/advanced-setup/image-upload#image-upload-settings" target="_blank">?</a>
            </td>
        </tr>
    </table>
    <table class="form-table">
        <tr>
            <th><?php echo esc_html__('Image Processing Method','gpt3-ai-content-generator')?></th>
            <td>
                <?php $wpaicg_img_processing_method = get_option('wpaicg_img_processing_method', 'url'); ?>
                <select name="wpaicg_img_processing_method">
                    <option value="base64" <?php selected($wpaicg_img_processing_method, 'base64'); ?>><?php echo esc_html__('Base64', 'gpt3-ai-content-generator'); ?></option>
                    <option value="url" <?php selected($wpaicg_img_processing_method, 'url'); ?>><?php echo esc_html__('URL', 'gpt3-ai-content-generator'); ?></option>
                </select>
                <a href="https://docs.aipower.org/docs/ChatGPT/advanced-setup/image-upload#image-upload-settings" target="_blank">?</a>
            </td>
        </tr>
    </table>
    <table class="form-table">
        <tr>
            <th><?php echo esc_html__('Image Quality','gpt3-ai-content-generator')?></th>
            <td>
                <?php $wpaicg_img_vision_quality = get_option('wpaicg_img_vision_quality', 'auto'); ?>
                <select name="wpaicg_img_vision_quality">
                    <option value="auto" <?php selected($wpaicg_img_vision_quality, 'auto'); ?>><?php echo esc_html__('Auto', 'gpt3-ai-content-generator'); ?></option>
                    <option value="low" <?php selected($wpaicg_img_vision_quality, 'low'); ?>><?php echo esc_html__('Low', 'gpt3-ai-content-generator'); ?></option>
                    <option value="high" <?php selected($wpaicg_img_vision_quality, 'high'); ?>><?php echo esc_html__('High', 'gpt3-ai-content-generator'); ?></option>
                </select>
                <a href="https://docs.aipower.org/docs/ChatGPT/advanced-setup/image-upload#image-upload-settings" target="_blank">?</a>
            </td>
        </tr>
    </table>
    <hr>
    <h3>
        <svg  xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-settings"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /></svg>
        <?php echo esc_html__('Other Options','gpt3-ai-content-generator')?>
    </h3>
    <fieldset class="nice-form-group">
        <div class="nice-form-group">
            <input <?php echo $wpaicg_chat_enable_sale ? ' checked':''?> type="checkbox" class="wpaicg_chat_enable_sale" value="1" name="wpaicg_chat_enable_sale">
            <label><?php echo esc_html__('Enable Token Purchase','gpt3-ai-content-generator')?></label>
            <a href="https://docs.aipower.org/docs/user-management-token-sale" target="_blank">?</a>
        </div>
        <div class="nice-form-group">
            <input <?php echo $wpaicg_elevenlabs_hide_error ? ' checked':''?> type="checkbox" class="wpaicg_elevenlabs_hide_error" value="1" name="wpaicg_elevenlabs_hide_error">
            <label><?php echo esc_html__('Hide API Errors in Chat','gpt3-ai-content-generator')?></label>
            <a href="https://docs.aipower.org/docs/ChatGPT/advanced-setup/voice-chat#hide-api-errors-in-chat" target="_blank">?</a>
        </div>
        <div class="nice-form-group">
            <?php
            $wpaicg_assistant_feature = get_option('wpaicg_assistant_feature', 0);
            ?>
            <input type="checkbox" name="wpaicg_assistant_feature" value="1" <?php checked(1, $wpaicg_assistant_feature, true); ?>>
            <label><?php echo esc_html__('Enable Assistants','gpt3-ai-content-generator')?></label>
        </div>
        <div class="nice-form-group">
            <input <?php echo $wpaicg_typewriter_effect ? ' checked':''?> type="checkbox" id="wpaicg_typewriter_effect" class="wpaicg_typewriter_effect" value="1" name="wpaicg_typewriter_effect">
            <label><?php echo esc_html__('Use Typewriter Effect','gpt3-ai-content-generator')?></label>
            <a href="https://docs.aipower.org/docs/ChatGPT/advanced-setup/style#type-writer-effect" target="_blank">?</a>
        </div>
        <div class="nice-form-group">
            <input <?php echo get_option('wpaicg_autoload_chat_conversations', 0) ? ' checked':''?> type="checkbox" id="wpaicg_autoload_chat_conversations" class="wpaicg_autoload_chat_conversations" value="1" name="wpaicg_autoload_chat_conversations">
            <label><?php echo esc_html__('Don’t Load Past Chats','gpt3-ai-content-generator')?></label>
            <a href="https://docs.aipower.org/docs/ChatGPT/advanced-setup/context#conversation-starters" target="_blank">?</a>
        </div>
        <div class="nice-form-group">
            <input type="checkbox" id="wpaicg_delete_image" name="wpaicg_delete_image" value="1" <?php checked(1, get_option('wpaicg_delete_image', 0)); ?>>
            <label for="wpaicg_delete_image"><?php echo esc_html__('Delete Images After Processing', 'gpt3-ai-content-generator'); ?></label>
            <a href="https://docs.aipower.org/docs/ChatGPT/advanced-setup/image-upload#deleting-images-after-processing" target="_blank">?</a>
        </div>
    </fieldset>
    <table class="form-table">
        <tr id="wpaicg_typewriter_speed_row" style="display: none;">
            <th><?php echo esc_html__('Typewriter Speed','gpt3-ai-content-generator')?></th>
            <td>
                <select name="wpaicg_typewriter_speed" id="wpaicg_typewriter_speed">
                    <?php for ($i = 1; $i <= 10; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php echo get_option('wpaicg_typewriter_speed', 1) == $i ? 'selected' : ''; ?>>
                            <?php echo $i; ?> <?php echo $i == 1 ? ' - Fastest' : ($i == 10 ? ' - Slowest' : ''); ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </td>
        </tr>
    </table>
    <p class="submit"><button class="button button-primary" name="wpaicg_chat_save"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button></p>
</form>
<script>
    jQuery(document).ready(function($){

        // Initial check
        toggleTypewriterSpeedDisplay();
        toggleDeleteImageOption();

        // On change of the checkbox
        $('#wpaicg_typewriter_effect').change(function() {
            toggleTypewriterSpeedDisplay();
        });

        $('#wpaicg_img_processing_method').change(function() {
            toggleDeleteImageOption();
        });

        function toggleTypewriterSpeedDisplay() {
            if ($('#wpaicg_typewriter_effect').is(':checked')) {
                $('#wpaicg_typewriter_speed_row').show();
            } else {
                $('#wpaicg_typewriter_speed_row').hide();
            }
        }

        function toggleDeleteImageOption() {
            if ($('#wpaicg_img_processing_method').val() === 'url') {
                $('#wpaicg_delete_image').prop('checked', false);
                $('#wpaicg_delete_image').attr('disabled', 'disabled');
            } else {
                $('#wpaicg_delete_image').removeAttr('disabled');
            }
        }

        function showMessageSuccess(message) {
            $("#wpaicg_message").css({
                'color': 'green',
            }).text(message).fadeIn().delay(10000).fadeOut();
        }

        function showMessageError(message) {
            $("#wpaicg_message").css({
                'color': 'red',
            }).text(message).fadeIn().delay(10000).fadeOut();
        }
        $('.wpaicg_sync_voices').click(function(){
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php')?>',
                data: {action: 'wpaicg_sync_voices',nonce: '<?php echo wp_create_nonce('wpaicg_sync_voices')?>'},
                dataType: 'json',
                type: 'post',
                beforeSend: function(){
                    $('.wpaicg_sync_voices').attr('disabled','disabled');
                    $('.wpaicg_sync_voices').text('<?php echo esc_html__('Syncing voices...Please wait...','gpt3-ai-content-generator')?>');
                },
                success: function(res){
                $('.wpaicg_sync_voices').removeAttr('disabled');
                $('.wpaicg_sync_voices').text('<?php echo esc_html__('Sync ElevenLabs Voices','gpt3-ai-content-generator')?>');
                if(res.status === 'success') {
                    showMessageSuccess('<?php echo esc_html__('Voices synced successfully!','gpt3-ai-content-generator')?>');
                } else {
                    showMessageError(res.message);
                }
            }


            })
        })
        $('.wpaicg_sync_google_voices').click(function(){
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php')?>',
                data: {action: 'wpaicg_sync_google_voices',nonce: '<?php echo wp_create_nonce('wpaicg_sync_google_voices')?>'},
                dataType: 'json',
                type: 'post',
                beforeSend: function(){
                    $('.wpaicg_sync_google_voices').attr('disabled','disabled');
                    $('.wpaicg_sync_google_voices').text('<?php echo esc_html__('Syncing voices...Please wait...','gpt3-ai-content-generator')?>');
                },
                success: function(res){
                    $('.wpaicg_sync_google_voices').removeAttr('disabled');
                    $('.wpaicg_sync_google_voices').text('<?php echo esc_html__('Sync Google Voices','gpt3-ai-content-generator')?>');
                    if(res.status === 'success'){
                        showMessageSuccess('<?php echo esc_html__('Voices synced successfully!','gpt3-ai-content-generator')?>');
                    }else{
                        showMessageError(res.msg);
                    }
                }


            });
        })
        $('.wpaicg_sync_models').click(function(){
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php')?>',
                data: {action: 'wpaicg_sync_models',nonce: '<?php echo wp_create_nonce('wpaicg_sync_models')?>'},
                dataType: 'json',
                type: 'post',
                beforeSend: function(){
                    $('.wpaicg_sync_models').attr('disabled','disabled');
                    $('.wpaicg_sync_models').text('<?php echo esc_html__('Syncing models...Please wait...','gpt3-ai-content-generator')?>');
                },
                success: function(res){
                $('.wpaicg_sync_models').removeAttr('disabled');
                $('.wpaicg_sync_models').text('<?php echo esc_html__('Sync ElevenLabs Models','gpt3-ai-content-generator')?>');
                if(res.status === 'success') {
                    showMessageSuccess('<?php echo esc_html__('Models synced successfully!','gpt3-ai-content-generator')?>');
                } else {
                    showMessageError(res.message);
                }
            }

            });
        });

    })
</script>
