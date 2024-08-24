<?php
namespace WPAICG;
if ( ! defined( 'ABSPATH' ) ) exit;
if(!class_exists('\\WPAICG\\WPAICG_PDF')) {
    class WPAICG_PDF
    {
        private static $instance = null;

        public static function get_instance()
        {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function __construct()
        {
            add_action('wp_ajax_wpaicg_pdf_embedding',[$this,'wpaicg_pdf_embedding']);
            add_action('wp_ajax_wpaicg_admin_pdf',[$this,'wpaicg_admin_pdf']);
            add_action('wp_ajax_nopriv_wpaicg_pdf_embedding',[$this,'wpaicg_pdf_embedding']);
            add_action('wp_ajax_wpaicg_example_questions',[$this,'wpaicg_example_questions']);
            add_action('wp_ajax_nopriv_wpaicg_example_questions',[$this,'wpaicg_example_questions']);
            add_action( 'admin_enqueue_scripts', [$this,'wpaicg_enqueue_scripts'],1);
            add_action( 'wp_enqueue_scripts', [$this,'wpaicg_enqueue_scripts'],1);
            add_action('wp_ajax_wpaicg_pdfs_delete',[$this,'wpaicg_pdfs_delete']);
        }

        // backend pdf upload
        public function wpaicg_admin_pdf()
        {
            $wpaicg_result = array('status' => 'error');
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-action' ) ) {
                $wpaicg_result['msg'] = esc_html__('Nonce verification failed','gpt3-ai-content-generator');
                wp_send_json($wpaicg_result);
            }
            $content = sanitize_text_field($_REQUEST['content']);
            $page = sanitize_text_field($_REQUEST['page']);
            $filename = sanitize_text_field($_REQUEST['filename']);
            $openai = WPAICG_Util::get_instance()->initialize_ai_engine();

            $wpaicg_pinecone_api = get_option('wpaicg_pinecone_api','');
            $wpaicg_pinecone_environment = get_option('wpaicg_pinecone_environment','');

            // Determine the model based on the provider
            $wpaicg_provider = get_option('wpaicg_provider', 'OpenAI');
            // Retrieve the embedding model based on the provider
            switch ($wpaicg_provider) {
                case 'OpenAI':
                    $wpaicg_model = get_option('wpaicg_openai_embeddings', 'text-embedding-ada-002');
                    break;
                case 'Azure':
                    $wpaicg_model = get_option('wpaicg_azure_embeddings', '');
                    break;
                case 'Google':
                    $wpaicg_model = get_option('wpaicg_google_embeddings', 'embedding-001');
                    break;
                default:
                    $wpaicg_model = 'text-embedding-ada-002'; // Default fallback model
                    break;
            }

            $wpaicg_main_embedding_model = get_option('wpaicg_main_embedding_model', '');
            if (!empty($wpaicg_main_embedding_model)) {
                $wpaicg_main_embedding_model = explode(':', $wpaicg_main_embedding_model);
                $wpaicg_embedding_provider = $wpaicg_main_embedding_model[0];
                $wpaicg_model = $wpaicg_main_embedding_model[1];
                try {
                    $openai = WPAICG_Util::get_instance()->initialize_embedding_engine($wpaicg_embedding_provider, $wpaicg_provider);
                } catch (\Exception $e) {
                    $wpaicg_result['msg'] = $e->getMessage();
                    wp_send_json($wpaicg_result);
                }
            } 

            // Prepare the API call parameters
            $apiParams = [
                'input' => $content,
                'model' => $wpaicg_model
            ];

            // Make the API call
            $response = $openai->embeddings($apiParams);

            $response = json_decode($response,true);
            if(isset($response['error']) && !empty($response['error'])) {
                $wpaicg_result['msg'] = $response['error']['message'];
                if(empty($wpaicg_result['msg']) && isset($response['error']['code']) && $response['error']['code'] == 'invalid_api_key'){
                    $wpaicg_result['msg'] = 'Incorrect API key provided. You can find your API key at https://platform.openai.com/account/api-keys.';
                }
            }
            else{
                $embedding = $response['data'][0]['embedding'];
                if(empty($embedding)){
                    $wpaicg_result['msg'] = esc_html__('No data returned','gpt3-ai-content-generator');
                }
                else{
                    $headers = array(
                        'Content-Type' => 'application/json',
                        'Api-Key' => $wpaicg_pinecone_api
                    );
                    $wpaicg_vector_db_provider = get_option('wpaicg_vector_db_provider', 'pinecone');

                    // Determine the appropriate database environment or collection name and API key based on the provider
                if ($wpaicg_vector_db_provider === 'pinecone') {
                    $wpaicg_pinecone_environment = get_option('wpaicg_pinecone_environment', '');
                    $post_excerpt = $wpaicg_pinecone_environment;
                } elseif ($wpaicg_vector_db_provider === 'qdrant') {
                    $wpaicg_qdrant_default_collection = get_option('wpaicg_qdrant_default_collection', '');
                    $post_excerpt = $wpaicg_qdrant_default_collection;
                }


                    $embedding_data = array(
                        'post_type' => 'wpaicg_pdfadmin',
                        'post_title' => $filename.' - Page: '.$page,
                        'post_content' => $content,
                        'post_excerpt' => $post_excerpt,
                        'post_status' => 'publish'
                    );
                    $embeddings_id = wp_insert_post($embedding_data,true);
                                                    
                    $wpaicg_emb_index = get_option('wpaicg_pinecone_environment', '');
                    if ($wpaicg_vector_db_provider === 'qdrant') {
                        $wpaicg_emb_index = get_option('wpaicg_qdrant_default_collection', '');
                    }
                    $wpaicg_emb_model = $wpaicg_provider === 'OpenAI' ? get_option('wpaicg_openai_embeddings', 'text-embedding-ada-002') : ($wpaicg_provider === 'Google' ? get_option('wpaicg_google_embeddings', 'embedding-001') : get_option('wpaicg_azure_embeddings', 'text-embedding-ada-002'));
                    
                    $wpaicg_main_embedding_model = get_option('wpaicg_main_embedding_model', '');
                    if (!empty($wpaicg_main_embedding_model)) {
                        $wpaicg_main_embedding_model = explode(':', $wpaicg_main_embedding_model);
                        $wpaicg_emb_model = $wpaicg_main_embedding_model[1];
                        $wpaicg_provider = $wpaicg_main_embedding_model[0];
                    }
                    
                    if(is_wp_error($embeddings_id)){
                        $wpaicg_result['msg'] = $embeddings_id->get_error_message();
                    }
                    else{
                        update_post_meta($embeddings_id,'wpaicg_start',time());
                        $usage_tokens = $response['usage']['total_tokens'];
                        add_post_meta($embeddings_id, 'wpaicg_embedding_token', $usage_tokens);
                        add_post_meta($embeddings_id, 'wpaicg_provider', $wpaicg_provider);
                        add_post_meta($embeddings_id, 'wpaicg_index', $wpaicg_emb_index);
                        add_post_meta($embeddings_id, 'wpaicg_model', $wpaicg_emb_model);
                        $vectors = array(
                            array(
                                'id' => (string)$embeddings_id,
                                'values' => $embedding
                            )
                        );

                        $wpaicg_vector_db_provider = get_option('wpaicg_vector_db_provider', 'pinecone');
                        if ($wpaicg_vector_db_provider === 'pinecone') {
                            $pinecone_url = 'https://' . $wpaicg_pinecone_environment . '/vectors/upsert';

                            $response = wp_remote_post($pinecone_url, array(
                                'headers' => $headers,
                                'body' => json_encode(array('vectors' => $vectors))
                            ));
                            if(is_wp_error($response)){
                                $wpaicg_result['msg'] = $response->get_error_message();
                                wp_delete_post($embeddings_id);
                            }
                            else{
                                update_post_meta($embeddings_id,'wpaicg_complete',time());
                                $wpaicg_result['status'] = 'success';
                            }
                        } else {
                            $qdrant_endpoint = rtrim(get_option('wpaicg_qdrant_endpoint', ''), '/') . '/collections';
                            $qdrant_default_collection = get_option('wpaicg_qdrant_default_collection', '');
                            $qdrant_url = $qdrant_endpoint . '/' . $qdrant_default_collection . '/points?wait=true';
                            $qdrant_api_key = get_option('wpaicg_qdrant_api_key', '');

                            $group_id = 'default'; // Default group ID

                            // Format for Qdrant
                            $formatted_vector = array(
                                'id' => $embeddings_id,
                                'vector' => $embedding,
                                'payload' => array('group_id' => $group_id)
                            );

                            $vectors = array('points' => array($formatted_vector));
                        
                            // Prepare the request for Qdrant
                            $response = wp_remote_request($qdrant_url, array(
                                'method'    => 'PUT',
                                'headers' => ['api-key' => $qdrant_api_key, 'Content-Type' => 'application/json'],
                                'body'      => json_encode($vectors)
                            ));

                            if(is_wp_error($response)){
                                $wpaicg_result['msg'] = $response->get_error_message();
                                wp_delete_post($embeddings_id);
                            }
                            else{
                                update_post_meta($embeddings_id,'wpaicg_complete',time());
                                $wpaicg_result['status'] = 'success';
                            }
                        }

                    }
                }
            }


            wp_send_json($wpaicg_result);
        }

        public function wpaicg_pdfs_delete()
        {
            $wpaicg_result = array('status' => 'error');
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['msg'] = esc_html__('Nonce verification failed','gpt3-ai-content-generator');
                wp_send_json($wpaicg_result);
            }
            $type = 'wpaicg_pdfembed';
            if(isset($_REQUEST['type']) && !empty($_REQUEST['type'])){
                $type = sanitize_text_field($_REQUEST['type']);
            }
            $ids = wpaicg_util_core()->sanitize_text_or_array_field($_REQUEST['ids']);
            $this->wpaicg_delete_embeddings_ids($ids,$type);
            $wpaicg_result['status'] = 'success';
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_delete_embeddings_ids($ids, $type = 'wpaicg_pdfembed')
        {
            $posts = new \WP_Query([
                'post__in' => $ids,
                'posts_per_page' => -1,
                'post_type' => $type
            ]);
        
            if ($posts->post_count) {
                // Pinecone settings
                $wpaicg_pinecone_api = get_option('wpaicg_pinecone_api', '');
                $wpaicg_pinecone_environment = get_option('wpaicg_pinecone_environment', '');
                // Qdrant settings
                $wpaicg_qdrant_api_key = get_option('wpaicg_qdrant_api_key', '');
                $wpaicg_qdrant_endpoint = get_option('wpaicg_qdrant_endpoint', '') . '/collections';
        
                foreach ($posts->posts as $post) {
                    $wpaicg_index = get_post_meta($post->ID, 'wpaicg_index', true);
        
                    if (empty($wpaicg_index) || strpos($wpaicg_index, 'pinecone.io') !== false) {
                        // Pinecone deletion logic
                        if (!empty($post->post_excerpt)) {
                            $wpaicg_pinecone_environment = $post->post_excerpt;
                        }
                        // Determine index host
                        $index_host = '';
                        if (!empty($wpaicg_index) && strpos($wpaicg_index, 'pinecone.io') !== false) {
                            $index_host = $wpaicg_index;
                        } else {
                            $index_host = $wpaicg_pinecone_environment;
                        }

                        $index_host_url = 'https://' . $index_host . '/vectors/delete';

                        try {
                            $headers = [
                                'Content-Type' => 'application/json',
                                'Api-Key' => $wpaicg_pinecone_api
                            ];
                            $pinecone_id = $post->ID;
                            // convert to string
                            $pinecone_id = strval($pinecone_id);
                            $body = json_encode([
                                'deleteAll' => 'false',
                                'ids' => [$pinecone_id]
                            ]);
                            $response = wp_remote_post($index_host_url, [
                                'headers' => $headers,
                                'body' => $body
                            ]);

                        } catch (\Exception $exception) {
                            // Handle exception
                        }
                    } else {
                        // Qdrant deletion logic
                        $collection_name = $wpaicg_index;
                        $endpoint = $wpaicg_qdrant_endpoint . '/' . $collection_name . '/points/delete?wait=true';

                        $id = $post->ID;
                        $id = intval($id);
                        $points = json_encode(['points' => [$id]]);
        
                        $response = wp_remote_request($endpoint, [
                            'method' => 'POST',
                            'headers' => ['api-key' => $wpaicg_qdrant_api_key, 'Content-Type' => 'application/json'],
                            'body' => $points,
                        ]);
        
                        // Check if response is a WP_Error
                        if (is_wp_error($response)) {
                            // Log WP_Error details
                            error_log('WP_Error: ' . print_r($response->get_error_messages(), true));
                        } else {
                            // Extract the response code and check for error status codes (e.g., 4xx, 5xx)
                            $response_code = wp_remote_retrieve_response_code($response);
                            if ($response_code >= 400) {
                                // Log the error message from the response body
                                $error_message = wp_remote_retrieve_body($response);
                                error_log("API Error Response (Code $response_code): " . $error_message);
                            }
                        }
                    }
        
                    // Delete post after vector deletion
                    wp_delete_post($post->ID);
                }
            }
        }
        

        public function wpaicg_enqueue_scripts()
        {
            $wpaicg_settings = get_option('wpaicg_chat_shortcode_options', array());
            $wpaicg_chat_widget = get_option('wpaicg_chat_widget', array());
        
            $is_pdf_enabled_in_shortcodes = isset($wpaicg_settings['embedding_pdf']) && $wpaicg_settings['embedding_pdf'];
            $is_pdf_enabled_in_widgets = isset($wpaicg_chat_widget['embedding_pdf']) && $wpaicg_chat_widget['embedding_pdf'];
            $is_pdf_enabled_in_chatbots = false;
        
            // If PDF embedding is not enabled in shortcodes or widgets, then check chatbot posts
            if (!$is_pdf_enabled_in_shortcodes && !$is_pdf_enabled_in_widgets) {
                // Query chatbot posts
                $args = array(
                    'post_type' => 'wpaicg_chatbot',
                    'posts_per_page' => -1
                );
                $chatbot_posts = get_posts($args);
        
                // Loop through chatbot posts and check if PDF embedding is enabled in any of them
                foreach ($chatbot_posts as $post) {
                    $chatbot_data = json_decode($post->post_content, true);
        
                    if (isset($chatbot_data['embedding_pdf']) && $chatbot_data['embedding_pdf']) {
                        $is_pdf_enabled_in_chatbots = true;
                        break;
                    }
                }
            }
        
            if ($is_pdf_enabled_in_shortcodes || $is_pdf_enabled_in_widgets || $is_pdf_enabled_in_chatbots || is_admin()) {
                wp_enqueue_script('wpaicg-pdf', WPAICG_PLUGIN_URL.'lib/js/pdf.js', array(), null, true);
            }
        
            wp_enqueue_script('wpaicg-chat-pro', WPAICG_PLUGIN_URL.'lib/js/wpaicg-chat-pro.js', array(), null, true);
        } 
        
        public function wpaicg_example_questions()
        {
            $wpaicg_result = array('status' => 'error');
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-chatbox' ) ) {
                $wpaicg_result['msg'] = esc_html__('Nonce verification failed','gpt3-ai-content-generator');
                wp_send_json($wpaicg_result);
            }

            // Get the default provider option
            $default_provider = get_option('wpaicg_provider', 'OpenAI');
            $wpaicg_provider = $default_provider;
            $model_name = get_option('wpaicg_ai_model', 'gpt-3.5-turbo-16k');
            $default_model = $model_name;

            // Check for bot_id first
            if (isset($_REQUEST['bot_id']) && intval($_REQUEST['bot_id']) > 0) {
                $bot_id = intval($_REQUEST['bot_id']);
                $post = get_post($bot_id);
                if ($post) {
                    $post_content = $post->post_content;
                    $post_content_json = json_decode($post_content, true);
                    $wpaicg_provider = isset($post_content_json['provider']) && !empty($post_content_json['provider']) ? sanitize_text_field($post_content_json['provider']) : $default_provider;
                    $model_name = isset($post_content_json['model']) && !empty($post_content_json['model']) ? sanitize_text_field($post_content_json['model']) : $default_model;
                }
            } elseif (isset($_REQUEST['type'])) {
                $chatbot_identity = sanitize_text_field($_REQUEST['type']);
                if ($chatbot_identity === 'shortcode') {
                    $shortcode_options = get_option('wpaicg_chat_shortcode_options');
                    $wpaicg_provider = isset($shortcode_options['provider']) ? sanitize_text_field($shortcode_options['provider']) : $default_provider;
                    switch ($wpaicg_provider) {
                        case 'OpenAI':
                            $model_name = isset($shortcode_options['model']) && !empty($shortcode_options['model']) ? sanitize_text_field($shortcode_options['model']) : 'gpt-3.5-turbo';
                            break;
                        case 'OpenRouter':
                            $model_name = isset($shortcode_options['model']) && !empty($shortcode_options['model']) ? sanitize_text_field($shortcode_options['model']) : 'openrouter/auto';
                            break;
                        case 'Azure':
                            $model_name = get_option('wpaicg_azure_deployment', $default_model);
                            break;
                        case 'Google':
                            $model_name = get_option('wpaicg_shortcode_google_model', 'gemini-pro');
                            break;
                    }
                } elseif ($chatbot_identity === 'widget') {
                    if (isset($_POST['wpaicg_chat_widget']['provider']) && !empty($_POST['wpaicg_chat_widget']['provider'])) {
                        $wpaicg_provider = sanitize_text_field($_POST['wpaicg_chat_widget']['provider']);
                    } else {
                        $widget_options = get_option('wpaicg_chat_widget');
                        $wpaicg_provider = isset($widget_options['provider']) ? sanitize_text_field($widget_options['provider']) : $default_provider;
                    }
                    switch ($wpaicg_provider) {
                        case 'OpenAI':
                            $model_name = get_option('wpaicg_chat_model', 'gpt-3.5-turbo');
                            break;
                        case 'OpenRouter':
                            $model_name = get_option('wpaicg_widget_openrouter_model', 'openrouter/auto');
                            break;
                        case 'Azure':
                            $model_name = get_option('wpaicg_azure_deployment', $default_model);
                            break;
                        case 'Google':
                            $model_name = get_option('wpaicg_widget_google_model', 'gemini-pro');
                            break;
                    }
                } else {
                    // Handle other custom chatbot identities if needed
                    $wpaicg_provider = $default_provider;
                }
            } else {
                // Fallback to default provider if no specific identity or bot_id is found
                $wpaicg_provider = $default_provider;
            }
            
            $openai = WPAICG_OpenAI::get_instance()->openai();

            // Determine AI engine based on provider
            switch ($wpaicg_provider) {
                case 'Google':
                    $openai = WPAICG_Google::get_instance();
                    break;
                case 'Azure':
                    $openai = WPAICG_AzureAI::get_instance()->azureai();
                    break;
                case 'OpenAI':
                    $openai = WPAICG_OpenAI::get_instance()->openai();
                    break;
                case 'OpenRouter':
                    $openai = WPAICG_OpenRouter::get_instance()->openai();
                    break;
                default:
                    $openai = WPAICG_OpenAI::get_instance()->openai();
                    break;
            }
            
            if($openai){
                $type = isset($_REQUEST['type']) && !empty($_REQUEST['type']) ? sanitize_text_field($_REQUEST['type']) : 'shortcode';
                $bot_id = isset($_REQUEST['bot_id']) && !empty($_REQUEST['bot_id']) ? sanitize_text_field($_REQUEST['bot_id']) : 0;
                $content = sanitize_text_field($_REQUEST['content']);
                $language = 'en';
                $embedding_pdf_message = "Congrats! Your PDF is uploaded now! You can ask questions about your document.\nExample Questions:[questions]";
                if($type == 'shortcode'){
                    $wpaicg_chat_shortcode_options = get_option('wpaicg_chat_shortcode_options',[]);
                    if(isset($wpaicg_chat_shortcode_options['embedding_pdf_message']) && !empty($wpaicg_chat_shortcode_options['embedding_pdf_message'])){
                        $embedding_pdf_message = $wpaicg_chat_shortcode_options['embedding_pdf_message'];
                    }
                    if(isset($wpaicg_chat_shortcode_options['language']) && !empty($wpaicg_chat_shortcode_options['language'])){
                        $language = $wpaicg_chat_shortcode_options['language'];
                    }
                }
                else{
                    $wpaicg_chat_widget = get_option('wpaicg_chat_widget',[]);
                    $language = get_option('wpaicg_chat_language','en');
                    if(isset($wpaicg_chat_widget['embedding_pdf_message']) && $wpaicg_chat_widget['embedding_pdf_message']){
                        $embedding_pdf_message = $wpaicg_chat_widget['embedding_pdf_message'];
                    }
                }
                if($bot_id){
                    $bot = get_post($bot_id);
                    if(strpos($bot->post_content,'\"') !== false) {
                        $bot->post_content = str_replace('\"', '&quot;', $bot->post_content);
                    }
                    if(strpos($bot->post_content,"\'") !== false) {
                        $bot->post_content = str_replace('\\', '', $bot->post_content);
                    }
                    $bot_config = json_decode($bot->post_content,true);
                    if(isset($bot_config['embedding_pdf_message']) && !empty($bot_config['embedding_pdf_message'])){
                        $embedding_pdf_message = $bot_config['embedding_pdf_message'];
                    }
                    if(isset($bot_config['language']) && !empty($bot_config['language'])){
                        $language = $bot_config['language'];
                    }
                }
                $generator = WPAICG_Generator::get_instance();
                if ($wpaicg_provider == 'openrouter') {
                    $generator->openai(WPAICG_OpenRouter::get_instance());
                } else {
                    $generator->openai($openai);
                }
                $wpaicg_language_file = WPAICG_PLUGIN_DIR . 'admin/chat/languages/' . $language . '.json';
                if (!file_exists($wpaicg_language_file)) {
                    $wpaicg_language_file = WPAICG_PLUGIN_DIR . 'admin/chat/languages/en.json';
                }
                $wpaicg_language_json = file_get_contents($wpaicg_language_file);
                $wpaicg_languages = json_decode($wpaicg_language_json, true);
                $prompt = "Give me 3 questions about this text: ".$content;
                if(isset($wpaicg_languages['question_prompt']) && !empty($wpaicg_languages['question_prompt'])){
                    $prompt = sprintf($wpaicg_languages['question_prompt'],$content);
                }

                $opts = array(
                    "model" => $model_name,
                    "temperature" => 1,
                    "max_tokens" => 1500,
                    "frequency_penalty" => 0,
                    "presence_penalty" => 0,
                    "top_p" => 0.01,
                    "prompt" => $prompt
                );

                $result = $generator->wpaicg_request($opts, $wpaicg_provider);
                if($result['status'] == 'error'){
                    $wpaicg_result['msg'] = $result['msg'];
                }
                else{
                    $data = $result['data'];
                    $embedding_pdf_message = str_replace('[questions]',"\n".$data,$embedding_pdf_message);
                    $wpaicg_result['status'] = 'success';
                    $wpaicg_result['data'] = $embedding_pdf_message;
                    $wpaicg_result['prompt'] = $prompt;
                }

            }
            else{
                $wpaicg_result['msg'] = esc_html__('Missing OpenAI API Settings','gpt3-ai-content-generator');
            }
            wp_send_json($wpaicg_result);
        }

        // frontend pdf upload
        public function wpaicg_pdf_embedding()
        {
            $wpaicg_result = array('status' => 'error');
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-chatbox' ) ) {
                $wpaicg_result['msg'] = esc_html__('Nonce verification failed','gpt3-ai-content-generator');
                wp_send_json($wpaicg_result);
            }
            $type = isset($_REQUEST['type']) && !empty($_REQUEST['type']) ? sanitize_text_field($_REQUEST['type']) : 'shortcode';
            $bot_id = isset($_REQUEST['bot_id']) && !empty($_REQUEST['bot_id']) ? sanitize_text_field($_REQUEST['bot_id']) : 0;
            $filename = sanitize_text_field($_REQUEST['filename']);
            $page = sanitize_text_field($_REQUEST['page']);
            $content = sanitize_text_field($_REQUEST['content']);
            $namespace = sanitize_text_field($_REQUEST['namespace']);

            $use_embedding = false;
            $vectordb = 'pinecone';
            $qdrant_collection = '';
            $use_default_embedding_model = true;
            $selected_embedding_model = '';
            $selected_embedding_provider = '';

            $wpaicg_pinecone_api = get_option('wpaicg_pinecone_api','');
            $wpaicg_pinecone_environment = get_option('wpaicg_pinecone_environment','');
            $wpaicg_provider = get_option('wpaicg_provider', 'OpenAI');
            if($type == 'shortcode'){
                $wpaicg_chat_shortcode_options = get_option('wpaicg_chat_shortcode_options',[]);
                $use_default_embedding_model = isset($wpaicg_chat_shortcode_options['use_default_embedding']) ? $wpaicg_chat_shortcode_options['use_default_embedding'] : true;
                $selected_embedding_model = isset($wpaicg_chat_shortcode_options['embedding_model']) ? $wpaicg_chat_shortcode_options['embedding_model'] : '';
                $selected_embedding_provider = isset($wpaicg_chat_shortcode_options['embedding_provider']) ? $wpaicg_chat_shortcode_options['embedding_provider'] : '';
                $wpaicg_provider = isset($wpaicg_chat_shortcode_options['provider']) ? $wpaicg_chat_shortcode_options['provider'] : 'OpenAI';
                if(isset($wpaicg_chat_shortcode_options['embedding']) && $wpaicg_chat_shortcode_options['embedding']){
                    $use_embedding = true;
                }
                if(isset($wpaicg_chat_shortcode_options['embedding_index']) && !empty($wpaicg_chat_shortcode_options['embedding_index'])){
                    $wpaicg_pinecone_environment = $wpaicg_chat_shortcode_options['embedding_index'];
                }
                if(isset($wpaicg_chat_shortcode_options['qdrant_collection']) && !empty($wpaicg_chat_shortcode_options['qdrant_collection'])){
                    $qdrant_collection = $wpaicg_chat_shortcode_options['qdrant_collection'];
                }
                if(isset($wpaicg_chat_shortcode_options['vectordb']) && !empty($wpaicg_chat_shortcode_options['vectordb'])){
                    $vectordb = $wpaicg_chat_shortcode_options['vectordb'];
                }
            }
            else{
                $wpaicg_chat_widget = get_option('wpaicg_chat_widget',[]);
                $use_default_embedding_model = isset($wpaicg_chat_widget['use_default_embedding']) ? $wpaicg_chat_widget['use_default_embedding'] : true;
                $selected_embedding_model = isset($wpaicg_chat_widget['embedding_model']) ? $wpaicg_chat_widget['embedding_model'] : '';
                $selected_embedding_provider = isset($wpaicg_chat_widget['embedding_provider']) ? $wpaicg_chat_widget['embedding_provider'] : '';
                $wpaicg_chat_embedding = get_option('wpaicg_chat_embedding',false);
                $wpaicg_provider = isset($wpaicg_chat_widget['provider']) ? $wpaicg_chat_widget['provider'] : 'OpenAI';
                if($wpaicg_chat_embedding){
                    $use_embedding = true;
                }
                if(isset($wpaicg_chat_widget['embedding_index']) && !empty($wpaicg_chat_widget['embedding_index'])){
                    $wpaicg_pinecone_environment = $wpaicg_chat_widget['embedding_index'];
                }
                // check wpaicg_widget_qdrant_collection in options table
                $qdrant_collection = get_option('wpaicg_widget_qdrant_collection', '');
                $vectordb = get_option('wpaicg_chat_vectordb', 'pinecone');
            }
            if($bot_id){
                $bot = get_post($bot_id);
                if(strpos($bot->post_content,'\"') !== false) {
                    $bot->post_content = str_replace('\"', '&quot;', $bot->post_content);
                }
                if(strpos($bot->post_content,"\'") !== false) {
                    $bot->post_content = str_replace('\\', '', $bot->post_content);
                }
                $bot_config = json_decode($bot->post_content,true);
                if(isset($bot_config['embedding']) && !empty($bot_config['embedding'])){
                    $use_embedding = true;
                }
                else{
                    $use_embedding = false;
                }
                if(isset($bot_config['embedding_index']) && !empty($bot_config['embedding_index'])){
                    $wpaicg_pinecone_environment = $bot_config['embedding_index'];
                }
                // get vectordb and qdrant collection from bot config
                if(isset($bot_config['vectordb']) && !empty($bot_config['vectordb'])){
                    $vectordb = $bot_config['vectordb'];
                }
                if(isset($bot_config['qdrant_collection']) && !empty($bot_config['qdrant_collection'])){
                    $qdrant_collection = $bot_config['qdrant_collection'];
                }
                if(isset($bot_config['use_default_embedding']) && !empty($bot_config['use_default_embedding'])){
                    $use_default_embedding_model = $bot_config['use_default_embedding'];
                }
                if(isset($bot_config['embedding_model']) && !empty($bot_config['embedding_model'])){
                    $selected_embedding_model = $bot_config['embedding_model'];
                }
                if(isset($bot_config['embedding_provider']) && !empty($bot_config['embedding_provider'])){
                    $selected_embedding_provider = $bot_config['embedding_provider'];
                }
                // get provider
                if(isset($bot_config['provider']) && !empty($bot_config['provider'])){
                    $wpaicg_provider = $bot_config['provider'];
                }
            }
            if($use_embedding){

                $openai = WPAICG_OpenAI::get_instance()->openai();

                // Determine AI engine based on provider
                switch ($wpaicg_provider) {
                    case 'Google':
                        $openai = WPAICG_Google::get_instance();
                        break;
                    case 'Azure':
                        $openai = WPAICG_AzureAI::get_instance()->azureai();
                        break;
                    case 'OpenAI':
                        $openai = WPAICG_OpenAI::get_instance()->openai();
                        break;
                    default:
                        $openai = WPAICG_OpenAI::get_instance()->openai();
                        break;
                }

                $wpaicg_model = 'text-embedding-ada-002'; // default model

                if (isset($use_default_embedding_model) && $use_default_embedding_model != 1) {
                    // Custom embedding logic
                    if (!empty($selected_embedding_model) && !empty($selected_embedding_provider)) {
                        $wpaicg_model = $selected_embedding_model;
                        try {
                            $openai = WPAICG_Util::get_instance()->initialize_embedding_engine($selected_embedding_provider, $wpaicg_provider);
                        } catch (\Exception $e) {
                            $result['msg'] = $e->getMessage();
                            return $result;
                        }
                    }
                } else {
                    // Use default embedding logic
                    
                    switch ($wpaicg_provider) {
                        case 'Azure':
                            $wpaicg_model = get_option('wpaicg_azure_embeddings', 'text-embedding-ada-002');
                            break;
                        case 'Google':
                            $wpaicg_model = get_option('wpaicg_google_embeddings', 'embedding-001');
                            break;
                        default:
                            $wpaicg_model = get_option('wpaicg_openai_embeddings', 'text-embedding-3-small');
                            break;
                    }
            
                    $main_embedding_model = get_option('wpaicg_main_embedding_model', '');
                    if (!empty($main_embedding_model)) {
                        $model_parts = explode(':', $main_embedding_model);
                        if (count($model_parts) === 2) {
                            $wpaicg_model = $model_parts[1];
                            try {
                                $openai = WPAICG_Util::get_instance()->initialize_embedding_engine($model_parts[0], $wpaicg_provider);
                            } catch (\Exception $e) {
                                $result['msg'] = $e->getMessage();
                                return $result;
                            }
                        }
                    }
                }

                $response = $openai->embeddings(array(
                    'input' => $content,
                    'model' => $wpaicg_model
                ));
                $response = json_decode($response,true);
                if(isset($response['error']) && !empty($response['error'])) {
                    $wpaicg_result['msg'] = $response['error']['message'];
                    if(empty($wpaicg_result['msg']) && isset($response['error']['code']) && $response['error']['code'] == 'invalid_api_key'){
                        $wpaicg_result['msg'] = 'Incorrect API key provided. You can find your API key at https://platform.openai.com/account/api-keys.';
                    }
                }
                else{
                    $embedding = $response['data'][0]['embedding'];
                    if(empty($embedding)){
                        $wpaicg_result['msg'] = esc_html__('No data returned','gpt3-ai-content-generator');
                    }
                    else{
                        
                        $headers = array(
                            'Content-Type' => 'application/json',
                            'Api-Key' => $wpaicg_pinecone_api
                        );
                        $embedding_data = array(
                            'post_type' => 'wpaicg_pdfembed',
                            'post_title' => $filename.' - Page: '.$page,
                            'post_content' => $content,
                            'post_status' => 'publish'
                        );
                        // Conditionally set the post_excerpt based on the vectordb value
                        if ($vectordb === 'qdrant') {
                            $embedding_data['post_excerpt'] = $qdrant_collection;
                        } else {
                            $embedding_data['post_excerpt'] = $wpaicg_pinecone_environment;
                        }
                        $embeddings_id = wp_insert_post($embedding_data);
                        if(is_wp_error($embeddings_id)){
                            $wpaicg_result['msg'] = $embeddings_id->get_error_message();
                        }
                        else{
                            update_post_meta($embeddings_id,'wpaicg_start',time());
                            $usage_tokens = $response['usage']['total_tokens'];
                            add_post_meta($embeddings_id, 'wpaicg_embedding_token', $usage_tokens);

                            // wpaicg_provider
                            $wpaicg_provider = get_option('wpaicg_provider', 'OpenAI');
                            // if $use_default_embedding_model exists and not equal to 1, then use the selected provider
                            if (isset($use_default_embedding_model) && $use_default_embedding_model != 1) {
                                $wpaicg_provider = $selected_embedding_provider;
                            } else {
                                $wpaicg_provider = get_option('wpaicg_provider', 'OpenAI');
                                // if $main_embedding_model exists, then use the selected provider
                                if (!empty($main_embedding_model)) {
                                    $model_parts = explode(':', $main_embedding_model);
                                    if (count($model_parts) === 2) {
                                        $wpaicg_provider = $model_parts[0];
                                    }
                                }
                            }    
                            add_post_meta($embeddings_id, 'wpaicg_provider', $wpaicg_provider);
                            // wpaicg_emb_index
                            $wpaicg_emb_index = $wpaicg_pinecone_environment;
                            if ($vectordb === 'qdrant') {
                                $wpaicg_emb_index = $qdrant_collection;
                            }
                            add_post_meta($embeddings_id, 'wpaicg_index', $wpaicg_emb_index);
                            // wpaicg_emb_model
                            $wpaicg_emb_model = $wpaicg_provider === 'OpenAI' ? get_option('wpaicg_openai_embeddings', 'text-embedding-ada-002') : ($wpaicg_provider === 'Google' ? get_option('wpaicg_google_embeddings', 'embedding-001') : get_option('wpaicg_azure_embeddings', 'text-embedding-ada-002'));
                            // if $use_default_embedding_model exists and not equal to 1, then use the selected model
                            if (isset($use_default_embedding_model) && $use_default_embedding_model != 1) {
                                $wpaicg_emb_model = $selected_embedding_model;
                            } else {
                                $wpaicg_emb_model = $wpaicg_provider === 'OpenAI' ? get_option('wpaicg_openai_embeddings', 'text-embedding-ada-002') : ($wpaicg_provider === 'Google' ? get_option('wpaicg_google_embeddings', 'embedding-001') : get_option('wpaicg_azure_embeddings', 'text-embedding-ada-002'));
                                // if $main_embedding_model exists, then use the selected model
                                if (!empty($main_embedding_model)) {
                                    $model_parts = explode(':', $main_embedding_model);
                                    if (count($model_parts) === 2) {
                                        $wpaicg_emb_model = $model_parts[1];
                                    }
                                }
                            }
                            
                            add_post_meta($embeddings_id, 'wpaicg_model', $wpaicg_emb_model);
                            $vectors = array(
                                array(
                                    'id' => (string)$embeddings_id,
                                    'values' => $embedding
                                )
                            );

                            $pinecone_url = 'https://' . $wpaicg_pinecone_environment . '/vectors/upsert';
                            if ($vectordb === 'pinecone') {
                                $response = wp_remote_post($pinecone_url, array(
                                    'headers' => $headers,
                                    'body' => json_encode(array('vectors' => $vectors,'namespace' => $namespace))
                                ));
                                if(is_wp_error($response)){
                                    $wpaicg_result['msg'] = $response->get_error_message();
                                    wp_delete_post($embeddings_id);
                                }
                                else{
                                    $wpaicg_result['status'] = 'success';
                                }
                            }
                            else {
                                $qdrant_endpoint = rtrim(get_option('wpaicg_qdrant_endpoint', ''), '/') . '/collections';
                                $qdrant_url = $qdrant_endpoint . '/' . $qdrant_collection . '/points?wait=true';
                                $qdrant_api_key = get_option('wpaicg_qdrant_api_key', '');

                                $group_id = $namespace; 

                                // Format for Qdrant
                                $formatted_vector = array(
                                    'id' => $embeddings_id,
                                    'vector' => $embedding,
                                    'payload' => array('group_id' => $group_id)
                                );

                                $vectors = array('points' => array($formatted_vector));
                            
                                // Prepare the request for Qdrant
                                $response = wp_remote_request($qdrant_url, array(
                                    'method'    => 'PUT',
                                    'headers' => ['api-key' => $qdrant_api_key, 'Content-Type' => 'application/json'],
                                    'body'      => json_encode($vectors)
                                ));
                                if(is_wp_error($response)){
                                    $wpaicg_result['msg'] = $response->get_error_message();
                                    wp_delete_post($embeddings_id);
                                }
                                else{
                                    $wpaicg_result['status'] = 'success';
                                }
                            }

                        }
                    }
                }
            }
            else{
                $wpaicg_result['status'] = 'no_embedding';
            }

            wp_send_json($wpaicg_result);
        }

    }
    WPAICG_PDF::get_instance();
}
