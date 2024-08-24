<?php

namespace Cornerstone\TwigIntegration;

const TWIG_ENABLED = 'cs_twig_enabled';
const TWIG_TEMPLATES = 'cs_twig_templates';

/**
 * Add API module controls
 */
add_filter('cs_theme_options_modules', function($modules) {

  $twigEnabledCondition = [
    [
      'key' => TWIG_ENABLED,
      'op' => '==',
      'value' => true,
    ]
  ];

  // API top level group
  $modules[] = [
    'type'  => 'group-sub-module',
    'label' => __( 'Twig (Beta)', 'cornerstone' ),
    'options' => [ 'tag' => 'twig', 'name' => 'x-theme-options:twig' ],
    'controls' => [

      // Twig Enabled
      [
        'key' => TWIG_ENABLED,
        'label' => cs_recall('label_enable'),
        'type' => 'toggle',
      ],

      // Templates
      [
        'type' => 'group',
        'conditions' => $twigEnabledCondition,
        //'label' => __('Endpoints', 'cornerstone'),
        'controls' => [

          // Template list editor
          [
            'label' => __('Templates', 'cornerstone'),
            'key' => TWIG_TEMPLATES,
            'description' => __('You can include these templates through the Dynamic Content UI through Twig > Template (Include)', CS_LOCALIZE),
            'type' => 'list',
            'options' => [
              // Initial object values
              'initial' => [
                'title' => __('My Template', CS_LOCALIZE),
                'template' => __('{# Start your twig adventure today! #}', CS_LOCALIZE),
              ],
              'item_label' => '{{index}}. {{title}}',
            ],
            'controls' => [
              // ID
              [
                'key' => 'id',
                'type' => 'text',
                'label' => __('ID', CS_LOCALIZE),
              ],

              // Title
              [
                'key' => 'title',
                'type' => 'text',
                'label' => __('Title', CS_LOCALIZE),
              ],

              // Template
              [
                'type' => 'code-editor',
                'key' => 'template',
                'options' => [
                  'mode' => 'twig',
                  'height' => 4,
                  'is_draggable' => false,
                  'expandable' => true,
                  'header_label' => __('Twig', 'cornerstone'),
                ],
              ],

            ],
          ],

        ],

      ],

      // Extensions
      [
        'type' => 'group',
        'label' => __('Extensions', CS_LOCALIZE),
        'conditions' => $twigEnabledCondition,
        'controls' => [

          // WordPress Extension
          [
            'key' => 'cs_twig_extension_wordpress',
            'type' => 'toggle',
            'label' => __('WordPress', CS_LOCALIZE),
            'description' => __('Exposes WordPress functions like `get_posts` adding useful filters and functions to Twig', CS_LOCALIZE),
          ],

          // HTML Extra
          [
            'key' => 'cs_twig_extension_html_extra',
            'type' => 'toggle',
            'label' => __('HTML Extra', CS_LOCALIZE),
            'description' => __('Adds the filter `data_uri` and the function `html_classes`. See the Twig docs https://twig.symfony.com', CS_LOCALIZE),
          ],

          // String Extra
          [
            'key' => 'cs_twig_extension_string_extra',
            'type' => 'toggle',
            'label' => __('String Extra', CS_LOCALIZE),
            'description' => __('Add the filter `slug` and unicode helpers like `u.wordwrap(4)`', CS_LOCALIZE),
          ],

          // Directory Loader
          [
            'key' => 'cs_twig_extension_directory_loader',
            'type' => 'toggle',
            'label' => __('Directory Loader', CS_LOCALIZE),
            'description' => __('Adds the ability to load in directories from your server. Adds the /twig folder in your child theme by default. See our Twig docs for notes on how to extend https://theme.co/docs/twig', CS_LOCALIZE),
          ],

          // Advanced extension
          [
            'key' => 'cs_twig_extension_advanced',
            'type' => 'toggle',
            'label' => __('Advanced', CS_LOCALIZE),
            'description' => __('Adds in the ability to call any function (function and fn), and use WordPress actions. Also adds in the StringLoaderExtension. Be careful please', CS_LOCALIZE),
          ],

          // Autoescape
          [
            'key' => 'cs_twig_autoescape',
            'type' => 'toggle',
            'label' => __('Autoescape', CS_LOCALIZE),
            'description' => __('Autoescape any HTML sent back from a Twig output statement. When this is enabled you will need to add in the `|raw` filter to any Twig output that you want to output HTML', CS_LOCALIZE),
          ],

          // Cache
          [
            'key' => 'cs_twig_cache',
            'type' => 'toggle',
            'label' => __('Cache', CS_LOCALIZE),
            'description' => __('Will not cache templates when this is disabled. Should be left on in a production environment. Useful if you are developing a Twig Extension', CS_LOCALIZE),
          ],

          // Debug Extension
          [
            'key' => 'cs_twig_extension_debug',
            'type' => 'toggle',
            'label' => __('Debug', CS_LOCALIZE),
            'description' => __('Adds in the function `dump`. Similar to `var_dump`. Will also run Twig debug mode even if you are not in Cornerstone. Useful for debugging data', CS_LOCALIZE),
          ],

        ],
      ],


    ],

  ];

  return $modules;
});


// Register options
cs_stack_register_options([
  TWIG_TEMPLATES => [],
  TWIG_ENABLED => false,
  'cs_twig_extension_wordpress' => true,
  'cs_twig_extension_html_extra' => true,
  'cs_twig_extension_string_extra' => false,
  'cs_twig_extension_directory_loader' => false,
  'cs_twig_extension_advanced' => false,
  'cs_twig_extension_debug' => false,
  'cs_twig_autoescape' => false,
  'cs_twig_cache' => true,
]);
