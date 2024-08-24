<?php

namespace FluentSupportPro\App\Services;

use FluentSupport\App\Services\Helper;

class ProHelper
{
    /**
     * getTicketFormConfig method will return the configuration for the ticket form
     * @return mixed
     */
    public static function getTicketFormConfig()
    {
        static $settings;
        if ($settings) {
            return $settings;
        }

        //Get all options for _ticket_form_settings from fs_meta table
        $settings = Helper::getOption('_ticket_form_settings', []);

        //Default settings for TicketFormConfig
        $defaults = [
            'enable_docs'        => 'no',
            'docs_post_types'    => [],
            'post_limits'        => 5,
            'disable_rich_text'  => 'no',
            'disabled_fields'    => [],
            'submitter_type'     => 'logged_in_users',
            'allowed_user_roles' => [],
            'field_labels'       => [
                'subject'           => __('Subject', 'fluent-support-pro'),
                'ticket_details'    => __('Ticket Details', 'fluent-support-pro'),
                'details_help'      => __('Please provide details about your problem', 'fluent-support-pro'),
                'product_services'  => __('Related Product/Service', 'fluent-support-pro'),
                'priority'          => __('Priority', 'fluent-support-pro'),
                'btn_text'          => __('Create Ticket', 'fluent-support-pro'),
                'submit_heading'    => __('Submit a Support Ticket', 'fluent-support-pro'),
                'create_ticket_cta' => __('Create a New Ticket', 'fluent-support-pro')
            ],
            'enable_woo_menu'    => 'yes'
        ];

        $settings = wp_parse_args($settings, $defaults);
        return $settings;
    }

    public static function hasDocIntegration()
    {
        $config = self::getTicketFormConfig();
        return $config['enable_docs'] == 'yes' && !empty($config['docs_post_types']);
    }

    public static function getAdvancedFilterOptions()
    {
        $groups = [
            'tickets'  => [
                'label'    => 'Tickets',
                'value'    => 'tickets',
                'children' => [
                    [
                        'label' => 'Title',
                        'value' => 'title',
                    ],
                    [
                        'label' => 'Content',
                        'value' => 'content',
                    ],
                    [
                        'label' => 'Conversation Content',
                        'value' => 'conversation_content',
                    ],
                    [
                        'label'             => 'Status',
                        'value'             => 'status',
                        'type'              => 'selections',
                        'component'         => 'options_selector',
                        'option_key'        => 'ticket_statuses',
                        'is_multiple'       => true,
                        'is_singular_value' => true
                    ],
                    [
                        'label'             => 'Client Priority',
                        'value'             => 'client_priority',
                        'type'              => 'selections',
                        'component'         => 'options_selector',
                        'option_key'        => 'client_priorities',
                        'is_multiple'       => false,
                        'is_singular_value' => true
                    ],
                    [
                        'label'             => 'Admin Priority',
                        'value'             => 'priority',
                        'type'              => 'selections',
                        'component'         => 'options_selector',
                        'option_key'        => 'admin_priorities',
                        'is_multiple'       => true,
                        'is_singular_value' => true
                    ],
                    [
                        'label'       => 'Tags',
                        'value'       => 'tags',
                        'type'        => 'selections',
                        'component'   => 'options_selector',
                        'option_key'  => 'ticket_tags',
                        'is_multiple' => true,
                    ],
                    [
                        'label'             => 'Products',
                        'value'             => 'product',
                        'type'              => 'selections',
                        'component'         => 'options_selector',
                        'option_key'        => 'support_products',
                        'is_multiple'       => true,
                        'is_singular_value' => true
                    ],
                    [
                        'label'             => 'Waiting For Reply',
                        'value'             => 'waiting_for_reply',
                        'type'              => 'selections',
                        'option_key'        => 'waiting_for_reply',
                        'is_singular_value' => true,
                        'options'           => [
                            'yes' => 'Yes',
                            'no'  => 'No'
                        ]
                    ],
                    [
                        'label'             => 'Assigned Agent',
                        'value'             => 'agent_id',
                        'type'              => 'selections',
                        'component'         => 'options_selector',
                        'option_key'        => 'support_agents',
                        'is_singular_value' => true,
                    ],
                    [
                        'label'             => 'Ticket Mailbox',
                        'value'             => 'mailbox_id',
                        'type'              => 'selections',
                        'component'         => 'options_selector',
                        'option_key'        => 'mailboxes',
                        'is_singular_value' => true,
                        'is_multiple'       => true,
                    ],
                    [
                        'label' => 'Ticket Created',
                        'value' => 'created_at',
                        'type'  => 'dates'
                    ],
                    [
                        'label' => 'Last Response',
                        'value' => 'updated_at',
                        'type'  => 'dates'
                    ],
                    [
                        'label' => 'Customer Waiting From',
                        'value' => 'waiting_since',
                        'type'  => 'dates'
                    ],
                    [
                        'label' => 'Last Agent Response',
                        'value' => 'last_agent_response',
                        'type'  => 'dates'
                    ],
                    [
                        'label' => 'Last Customer Response',
                        'value' => 'last_customer_response',
                        'type'  => 'dates'
                    ],
                ],
            ],
            'customer' => [
                'label'    => 'Customer',
                'value'    => 'customer',
                'children' => [
                    [
                        'label' => 'First Name',
                        'value' => 'first_name',
                    ],
                    [
                        'label' => 'Last Name',
                        'value' => 'last_name',
                    ],
                    [
                        'label' => 'Email',
                        'value' => 'email',
                    ],
                    [
                        'label' => 'Address Line 1',
                        'value' => 'address_line_1',
                    ],
                    [
                        'label' => 'Address Line 2',
                        'value' => 'address_line_2',
                    ],
                    [
                        'label' => 'City',
                        'value' => 'city',
                    ],
                    [
                        'label' => 'State',
                        'value' => 'state',
                    ],
                    [
                        'label' => 'Postal Code',
                        'value' => 'postal_code',
                    ],
//                    [
//                        'label'             => 'Country',
//                        'value'             => 'country',
//                        'type'              => 'selections',
//                        'component'         => 'options_selector',
//                        'option_key'        => 'countries',
//                        'is_multiple'       => true,
//                        'is_singular_value' => true
//                    ],
                    [
                        'label' => 'Phone',
                        'value' => 'phone',
                    ],
                ],
            ],
            'agent'    => [
                'label'    => 'Agent',
                'value'    => 'agent',
                'children' => [
                    [
                        'label' => 'First Name',
                        'value' => 'first_name',
                    ],
                    [
                        'label' => 'Last Name',
                        'value' => 'last_name',
                    ],
                    [
                        'label' => 'Email',
                        'value' => 'email',
                    ]
                ],
            ]
        ];

        $groups = apply_filters('fluent_support/advanced_filter_options', $groups);

        return array_values($groups);
    }

    public static function sanitizeMessageId($text)
    {
        if(!$text) {
            return $text;
        }
        $messageId = str_replace(['<', '>'], ['[', '}'], $text);
        $messageId = sanitize_textarea_field($messageId);
        return str_replace(['[', ']'], ['<', '>'], $messageId);
    }

    public static function generateMessageID($email)
    {
        $emailParts = explode('@', $email);
        if(count($emailParts) != 2) {
            return false;
        }
        $emailDomain = $emailParts[1];
        try {
            return sprintf(
                "<%s.%s@%s>",
                base_convert(microtime(), 10, 36),
                base_convert(bin2hex(openssl_random_pseudo_bytes(8)), 16, 36),
                $emailDomain
            );
        } catch (\Exception $exception) {
            return false;
        }
    }


    public static function encryptKey($value)
    {
        if (!$value) {
            return $value;
        }

        if (!extension_loaded('openssl')) {
            return $value;
        }

        $salt = (defined('LOGGED_IN_SALT') && '' !== LOGGED_IN_SALT) ? LOGGED_IN_SALT : 'this-is-a-fallback-salt-but-not-secure';

        if (defined('FLUENT_SUPPORT_ENCRYPTION_KEY')) {
            $key = FLUENT_SUPPORT_ENCRYPTION_KEY;
        } else {
            $key = (defined('LOGGED_IN_KEY') && '' !== LOGGED_IN_KEY) ? LOGGED_IN_KEY : 'this-is-a-fallback-key-but-not-secure';
        }

        $method = 'aes-256-ctr';
        $ivlen = openssl_cipher_iv_length($method);
        $iv = openssl_random_pseudo_bytes($ivlen);

        $raw_value = openssl_encrypt($value . $salt, $method, $key, 0, $iv);
        if (!$raw_value) {
            return false;
        }

        return base64_encode($iv . $raw_value); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
    }

    public static function decryptKey($raw_value)
    {
        if (!$raw_value) {
            return $raw_value;
        }

        if (!extension_loaded('openssl')) {
            return $raw_value;
        }

        $raw_value = base64_decode($raw_value, true); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode

        $method = 'aes-256-ctr';
        $ivlen = openssl_cipher_iv_length($method);
        $iv = substr($raw_value, 0, $ivlen);

        $raw_value = substr($raw_value, $ivlen);

        if (defined('FLUENT_SUPPORT_ENCRYPTION_KEY')) {
            $key = FLUENT_SUPPORT_ENCRYPTION_KEY;
        } else {
            $key = (defined('LOGGED_IN_KEY') && '' !== LOGGED_IN_KEY) ? LOGGED_IN_KEY : 'this-is-a-fallback-key-but-not-secure';
        }

        $salt = (defined('LOGGED_IN_SALT') && '' !== LOGGED_IN_SALT) ? LOGGED_IN_SALT : 'this-is-a-fallback-salt-but-not-secure';

        $value = openssl_decrypt($raw_value, $method, $key, 0, $iv);
        if (!$value || substr($value, -strlen($salt)) !== $salt) {
            return false;
        }

        return substr($value, 0, -strlen($salt));
    }
}
