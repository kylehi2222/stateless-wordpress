<?php

// Add preference values for code editor
add_filter("cs_app_preference_defaults", function($defaults) {
  $defaults['code_editor_theme'] = apply_filters("cs_code_editor_default_theme", "tco");
  $defaults['code_editor_keymap'] = apply_filters("cs_code_editor_default_keymap", "sublime");
  $defaults['code_editor_fontsize'] = apply_filters("cs_code_editor_default_fontsize", "14");
  $defaults['code_editor_wrap'] = apply_filters("cs_code_editor_default_wrap", true);

  return $defaults;
});

// Add code mirror controls like theme and keymaps
// to
add_filter("cs_preference_controls", function($controls) {
  $themePath = __DIR__ . "/../config/codemirror-themes.json";

  // No file
  if (!file_exists($themePath)) {
    trigger_error("No code mirror theme config file");
    return $controls;
  }

  $themes = json_decode(file_get_contents($themePath));
  $themesAsSelect = [
    [
      'value' => 'tco',
      'label' => 'Themeco',
    ],
  ];

  foreach ($themes as $theme) {
    $label = ucwords(str_replace("-", " ", $theme));
    $themesAsSelect[] = [
      'value' => $theme,
      'label' => $label,
    ];
  }

  // Filter to add more
  $themesAsSelect = apply_filters("cs_code_editor_themes", $themesAsSelect);

  $controls[] = [
    'type'     => 'group',
    'label'    => __( 'Code Editor', 'cornerstone' ),
    'controls' => [
      [
        'key'     => 'code_editor_theme',
        'type'    => 'select',
        'label'   => __('Theme', 'cornerstone'),
        'options' => [
          'choices' => $themesAsSelect,
        ],
      ],

      cs_recall('control_mixin_font_size', [
        'key' => 'code_editor_fontsize',
      ]),

      [
        'key' => 'code_editor_wrap',
        'label' => __('Line Wrap', 'cornerstone'),
        'description' => __('Instead of going off screen to a scroll bar, wrap the text inside the code editor frame', 'cornerstone'),
        'type' => 'toggle',
      ],

      [
        'key'     => 'code_editor_keymap',
        'type'    => 'select',
        'label'   => __('Key Map', 'cornerstone'),
        'options' => [
          'choices' => [

            [
              'value' => 'sublime',
              'label' => 'Sublime',
            ],

            [
              'value' => 'default',
              'label' => 'Code Mirror Default',
            ],

            [
              'value' => 'vim',
              'label' => 'Vim',
            ],

            [
              'value' => 'emacs',
              'label' => 'Emacs',
            ],

          ],
        ],
      ],

    ],
  ];

  return $controls;
});
