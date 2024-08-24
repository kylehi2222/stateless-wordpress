<?php

$_preferenceList = [
  [
    'key'         => 'dynamic_content',
    'label'       => __( 'Dynamic Content', 'cornerstone' ),
    'description' => __( 'Show controls to open Dynamic Content wherever it is supported.', 'cornerstone' ),
  ],
  [
    'key'         => 'show_wp_toolbar',
    'label'       => __( 'WordPress Toolbar', 'cornerstone' ),
    'description' => __( 'Allow WordPress to display the toolbar above the app. Requires a page refresh to take effect.', 'cornerstone' ),
  ],
  [
    'key'         => 'context_menu',
    'label'       => __( 'Context Menu', 'cornerstone' ),
    'description' => __( 'Allow context menu to appear when alt-clicking in the live preview.', 'cornerstone' ),
  ],
  [
    'key'         => 'code_editors',
    'label'       => __( 'Code Editors', 'cornerstone' ),
    'description' => __( 'Add custom CSS and JavaScript to your documents or entire site', 'cornerstone' ),
  ],
  [
    'key'         => 'dev_toolkit',
    'label'       => __( 'Dev Toolkit', 'cornerstone' ),
    'description' => __( 'Experimental functionality used by Themeco developers. Use at your own risk.', 'cornerstone' ),
  ],

  // Show all Documents
  [
    'key'         => 'document_list_show_all',
    'label'       => __( 'Show All Documents', 'cornerstone' ),
    'description' => __( 'If enabled, this will show all documents created regardless of if they are Cornerstone Pages', 'cornerstone' ),
  ],

  [
    'key'         => 'expanded_font_family',
    'label'       => __( 'Expanded Font Family', 'cornerstone' ),
    'description' => __( 'The font family display text will sample text or it will display the sample preview as the font family name', 'cornerstone' ),
  ],
];

if (apply_filters('cs_max_enabled', true)) {
  // Max
  $_preferenceList[] = [
    'key'         => 'use_max',
    'label'       => __( 'Enable Max', 'cornerstone' ),
    'disabled'    => !apply_filters("cs_max_enabled", true),
    'description' => __( 'The Best Templates & Training Right In Your Builder.', 'cornerstone' ),
  ];
}

// Main filter for adding to funtionaility list
$_preferenceList = apply_filters("cs_preferences_functionality_list", $_preferenceList);

return [
  [
    'type'     => 'group',
    'label'    => __( 'Interface', 'cornerstone' ),
    'controls' => [
      [
        'key'         => 'ui_theme',
        'type'        => 'choose',
        'title'       => __( 'Theme', 'cornerstone' ),
        'description' => __( 'Select how you would like the application UI to appear.', 'cornerstone' ),
        'condition'   => [ 'user_can:preference.ui_theme.user' => true ],
        'options'     => [
          'choices' => [
            [ 'value' => 'light', 'label' => __( 'Light', 'cornerstone' ) ],
            [ 'value' => 'dark',  'label' => __( 'Dark', 'cornerstone' )  ],
          ],
        ],
      ],
      [
        'key'         => 'workspace_side',
        'type'        => 'choose',
        'title'       => __( 'Workspace', 'cornerstone' ),
        'description' => __( 'Decide which side of the screen you prefer the workspace', 'cornerstone' ),
        'condition'   => [ 'user_can:preference.ui_theme.user' => true ],
        'options'     => [
          'choices' => [
            [ 'value' => 'left',  'label' => __( 'Left', 'cornerstone' )  ],
            [ 'value' => 'right', 'label' => __( 'Right', 'cornerstone' ) ]
          ],
        ],
      ],

      // Status Indicators
      [
        'key'         => 'status_indicators',
        'type'        => 'select',
        'title'       => __( 'Status<br/>Indicators', 'cornerstone' ),
        'description' => __( 'Select the contexts where you would like to see element status indicators.', 'cornerstone' ),
        'condition'   => [ 'user_can:preference.default_layout_element.user' => true ],
        'options'     => [
          'choices' => [
            [ 'value' => 'all',         'label' => __( 'Workspace and Breadcrumbs', 'cornerstone' ) ],
            [ 'value' => 'breadcrumbs', 'label' => __( 'Breadcrumbs Only', 'cornerstone' )          ],
            [ 'value' => 'workspace',   'label' => __( 'Workspace Only', 'cornerstone' )            ],
            [ 'value' => 'off',         'label' => __( 'Off', 'cornerstone' )                       ],
          ],
        ],
      ],

      // Default Element
      [
        'key'         => 'default_layout_element',
        'type'        => 'select',
        'label'       => __( 'Default Element', 'cornerstone' ),
        'description' => __( 'When inserting an element, this is the default element to use', 'cornerstone' ),
        'options'     => [
          'choices' => apply_filters("cs_content_layout_element_options", [
            [ 'value' => 'section',  'label' => __( 'Section', 'cornerstone' ) ],
            [ 'value' => 'layout-row',  'label' => __( 'Row', 'cornerstone' ) ],
            [ 'value' => 'layout-div',  'label' => __( 'Div', 'cornerstone' ) ],
          ]),
        ],
      ],

      // Insert Use Element Library
      [
        'key'         => 'insert_use_element_library',
        'type'        => 'toggle',
        'label'       => __( 'Open Library Default', 'cornerstone' ),
        'description' => __( 'Instead of using the default element, open the Element Library whenever you click on the insert areas. This can also be controlled through CTRL / MOD (Mac) to open the library. When this preference is enabled, this reverses how the modifier works', 'cornerstone' ),
      ],

      //  Preferences in toolbar
      [
        'key'         => 'preferences_in_toolbar',
        'type'        => 'toggle',
        'label'       => __("Preference Toolbar", "cornerstone"),
        'description' => __("Display the preference window button in the App toolbar", "cornerstone"),
      ],

      // Cornerstone Favicon
      [
        'key'         => 'cornerstone_favicon',
        'type'        => 'toggle',
        'title'       => __( 'CS Favicon', 'cornerstone' ),
        'description' => __( 'Use the Cornerstone icon as the favicon when you are viewing this App. Requires reload of Cornerstone', 'cornerstone' ),
      ],
    ],
  ],
  [
    'type'     => 'group',
    'label'    => __( 'Functionality', 'cornerstone' ),
    'controls' => [
      [
        'type'    => 'checkbox-list',
        // 'label'   => '&nbsp;',
        'options' => [
          'list' => $_preferenceList,
        ],
      ],
    ],
  ],
  [
    'type'     => 'group',
    'label'    => __( 'Workflow', 'cornerstone' ),
    'controls' => [
      [
        'type'    => 'checkbox-list',
        // 'label'   => '&nbsp;',
        'options' => [
          'list' => [
            [
              'key'         => 'preview_inset',
              'label'       => __( 'Inset Preview', 'cornerstone' ),
            ],
            [
              'key'         => 'rich_text_default',
              'label'       => __( 'Rich Text Editor Default', 'cornerstone' ),
              'description' => __( 'By default, start text editors in rich text mode whenever possible.', 'cornerstone' ),
            ],
            [
              'key'         => 'preserve_nav_group',
              'label'       => __( 'Preserve Inspector Group', 'cornerstone' ),
              'description' => __( 'When navigating between elements this will keep the same group open if it exists on the subsequent element. Hold cmd/ctrl to invert.', 'cornerstone' ),
            ],
          ],
        ],
      ],
    ],
  ],
];
