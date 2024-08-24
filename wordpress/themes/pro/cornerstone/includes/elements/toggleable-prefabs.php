<?php

/**
 * Toggleable Close
 */
cs_register_prefab_element( 'interactive', 'toggleable-close-button', [
  'type' => 'button',
  'scope'  => [ 'all' ],
  'title' => __('Toggleable Close Button', CS_LOCALIZE),

  'values' => [
    '_label' => __('Toggleable Close Button', CS_LOCALIZE),
    'anchor_bg_color' => 'transparent',

    'anchor_box_shadow_dimensions' => '!0em 0.15em 0.65em 0em',

    'anchor_text' => false,

    'anchor_graphic' => true,

    'anchor_graphic_icon' => 'xmark',

    'custom_atts' => '{"data-x-toggle-close":""}',
  ],
]);

/**
 * Modal (Inner Close)
 */
cs_register_prefab_element( 'interactive', 'layout-modal-inner-close', [
  'type'   => 'layout-modal',
  'scope'  => [ 'all' ],
  'title'  => __('Modal (Inner Close)', CS_LOCALIZE),
  'values' => [
    '_type'    => 'layout-modal',
    '_label'   => __('Modal (Inner Close)', CS_LOCALIZE),
    'modal_close_enabled' => false,

    '_modules' => [
      [
        '_type'    => 'layout-div',
        '_label'   => __('Close Wrapper', CS_LOCALIZE),

        'layout_div_flexbox' => true,
        'layout_div_flex_direction' => 'row',
        'layout_div_flex_justify' => 'flex-end',


        '_modules' => [
          cs_prefab_element_values('interactive', 'toggleable-close-button'),
        ]
      ],

      [
        '_type'    => 'layout-div',
        '_label'   => __('Content Wrapper', CS_LOCALIZE),
      ],
    ]
  ]
]);

/**
 * Off Canvas (Custom Close)
 */
cs_register_prefab_element( 'interactive', 'layout-off-canvas-custom-close', [
  'type'   => 'layout-off-canvas',
  'scope'  => [ 'all' ],
  'title'  => __('Off Canvas (Custom Close)', CS_LOCALIZE),
  'values' => [
    '_type'    => 'layout-off-canvas',
    '_label'   => __('Off Canvas (Custom Close)', CS_LOCALIZE),
    'off_canvas_close_enabled' => false,

    '_modules' => [
      [
        '_type'    => 'layout-div',
        '_label'   => __('Close Wrapper', CS_LOCALIZE),

        'layout_div_flexbox' => true,
        'layout_div_flex_direction' => 'row',
        'layout_div_flex_justify' => 'flex-end',


        '_modules' => [
          cs_prefab_element_values('interactive', 'toggleable-close-button'),
        ]
      ],

      [
        '_type'    => 'layout-div',
        '_label'   => __('Content Wrapper', CS_LOCALIZE),

        'layout_div_padding' => '1em',
      ],
    ]
  ]
]);
