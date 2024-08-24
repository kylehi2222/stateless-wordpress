<?php

// Notes
// -----
// "Managed" parameter types can be defined here by composing existing types
// and storing them in a key that will be the new type name, with some caveats:
//
// 01. These transforms happen at runtime. This means alterations to the
//     syntax below between releases would be considered breaking changes.
//     Whatever is placed here is a permanent addition to the API. A WordPress
//     filter is intentionally not present because it would prevent the
//     parameters from being portable. We may in the future add functionality
//     to define "managed types" directly on elements which, would help solve
//     that concern.
//
// 02. Shorthand should be avoided. It might work in some cases, but will break
//     if 'params' is supplied to extend the control set.
//
// 03. cs_maybe_recall must be used here to for performance. It prevents all
//     the builder strings from being loaded on the front end.
//
// 04. You may not set the top level control type to a list. For example, don't
//     do this:
//
//     'bg-image-simple' => [ 'type' => 'group' ]
//
//     Instead, that decision should be made by the author when the type is
//     invoked like this:
//
//     "my-param" : { "type" : "bg-image[]" }

namespace Themeco\Cornerstone\Util;

class ManagedParameters {

  protected static $managed;

  public static function managedTypes() {

    // Types
    // -----
    // 01. 'managed-size'
    // 02. 'position'
    // 03. 'bg-image'
    // 04. 'bg-image-simple'
    // 05. 'filter'
    // 06. 'mix-blend-mode'
    // 07. 'input-type'
    // 08. 'font-style'
    // 09. 'text-align'
    // 10. 'text-decoration'
    // 11. 'text-transform'
    // 12. 'list-style'
    // 13. 'border-style'
    // 14. 'overflow'
    // 15. 'text-overflow'
    // 16. 'social-share-networks'
    // 17. 'message-managed-content'
    // 18. 'fade'
    // 19. 'fade-stop'
    // 20. 'phone-make-model'
    // 21. 'text-format' (proto)

    $managed = [

      // 01
      // --

      'managed-size' => [
        'type'    => 'choose',
        'label'   => cs_maybe_recall( 'label_size' ),
        'initial' => 'md',
        'options' => [
          [ 'value' => 'xs', 'label' => cs_maybe_recall( 'label_size_xs' ) ],
          [ 'value' => 'sm', 'label' => cs_maybe_recall( 'label_size_s' )  ],
          [ 'value' => 'md', 'label' => cs_maybe_recall( 'label_size_m' )  ],
          [ 'value' => 'lg', 'label' => cs_maybe_recall( 'label_size_l' )  ],
          [ 'value' => 'xl', 'label' => cs_maybe_recall( 'label_size_xl' ) ],
        ],
      ],


      // 02
      // --

      'position' => [
        'type'    => 'select',
        'label'   => cs_maybe_recall( 'label_position' ),
        'initial' => 'center',
        'options' => [
          [ 'value' => '50% 50%',   'label' => cs_maybe_recall( 'label_center' )       ],
          [ 'value' => '50% 0%',    'label' => cs_maybe_recall( 'label_top' )          ],
          [ 'value' => '50% 100%',  'label' => cs_maybe_recall( 'label_bottom' )       ],
          [ 'value' => '0% 50%',    'label' => cs_maybe_recall( 'label_left' )         ],
          [ 'value' => '100% 50%',  'label' => cs_maybe_recall( 'label_right' )        ],
          [ 'value' => '0% 0%',     'label' => cs_maybe_recall( 'label_top_left' )     ],
          [ 'value' => '100% 0%',   'label' => cs_maybe_recall( 'label_top_right' )    ],
          [ 'value' => '0% 100%',   'label' => cs_maybe_recall( 'label_bottom_left' )  ],
          [ 'value' => '100% 100%', 'label' => cs_maybe_recall( 'label_bottom_right' ) ],
        ],
      ],


      // 03
      // --

      'bg-image' => [
        'type'   => 'group',
        'params' => [
          'src' => [
            'type'    => 'image',
            'label'   => cs_maybe_recall( 'label_source' ),
            'initial' => ''
          ],
          'repeat' => [
            'type'    => 'choose',
            'label'   => cs_maybe_recall( 'label_repeat' ),
            'initial' => 'no-repeat',
            'when'    => "not(eq(src, ''))",
            'options' => [
              [ 'value' => 'no-repeat', 'label' => cs_maybe_recall( 'label_none' ) ],
              [ 'value' => 'repeat-x',  'label' => cs_maybe_recall( 'label_x' )    ],
              [ 'value' => 'repeat-y',  'label' => cs_maybe_recall( 'label_y' )    ],
              [ 'value' => 'repeat',    'label' => cs_maybe_recall( 'label_both' ) ],
            ]
          ],
          'size' => [
            'type'    => 'text',
            'initial' => 'cover',
            'when'    => "not(eq(src, ''))",
          ],
          'position' => [
            'type'    => 'text',
            'initial' => '50% 50%',
            'when'    => "not(eq(src, ''))",
          ],
        ],
      ],


      // 04
      // --

      'bg-image-simple' => [
        'type'   => 'group',
        'params' => [
          'src' => [
            'type'    => 'image',
            'label'   => cs_maybe_recall( 'label_source' ),
            'initial' => ''
          ],
          'type' => [
            'type'    => 'choose',
            'label'   => cs_maybe_recall( 'label_type' ),
            'initial' => 'cover',
            'when'    => "not(eq(src, ''))",
            'options' => [
              [ 'value' => 'cover',   'label' => cs_maybe_recall( 'label_cover' )   ],
              [ 'value' => 'pattern', 'label' => cs_maybe_recall( 'label_pattern' ) ],
            ]
          ],
          'patternWidth' => [
            'type'    => 'size',
            'label'   => cs_maybe_recall( 'label_width' ),
            'initial' => '200px',
            'when'    => "and(not(eq(src, '')), eq(type, 'pattern'))",
          ],
          'position' => [
            'type'    => 'position',
            'initial' => '50% 50%',
            'when'    => "not(eq(src, ''))",
          ],
        ],
      ],


      // 05
      // --

      'filter' => [
        'type'    => 'filter',
        'initial' => '',
        'options' => [
          'placeholder' => 'grayscale(100%)'
        ],
      ],


      // 06
      // --

      'mix-blend-mode' => [
        'type'    => 'select',
        'label'   => cs_maybe_recall( 'label_blend' ),
        'initial' => 'normal',
        'options' => [
          [ 'value' => 'normal',      'label' => cs_maybe_recall( 'label_normal' )      ],
          [ 'value' => 'multiply',    'label' => cs_maybe_recall( 'label_multiply' )    ],
          [ 'value' => 'screen',      'label' => cs_maybe_recall( 'label_screen' )      ],
          [ 'value' => 'overlay',     'label' => cs_maybe_recall( 'label_overlay' )     ],
          [ 'value' => 'darken',      'label' => cs_maybe_recall( 'label_darken' )      ],
          [ 'value' => 'lighten',     'label' => cs_maybe_recall( 'label_lighten' )     ],
          [ 'value' => 'color-dodge', 'label' => cs_maybe_recall( 'label_color_dodge' ) ],
          [ 'value' => 'color-burn',  'label' => cs_maybe_recall( 'label_color_burn' )  ],
          [ 'value' => 'hard-light',  'label' => cs_maybe_recall( 'label_hard_light' )  ],
          [ 'value' => 'soft-light',  'label' => cs_maybe_recall( 'label_soft_light' )  ],
          [ 'value' => 'difference',  'label' => cs_maybe_recall( 'label_difference' )  ],
          [ 'value' => 'exclusion',   'label' => cs_maybe_recall( 'label_exclusion' )   ],
          [ 'value' => 'hue',         'label' => cs_maybe_recall( 'label_hue' )         ],
          [ 'value' => 'saturation',  'label' => cs_maybe_recall( 'label_saturation' )  ],
          [ 'value' => 'color',       'label' => cs_maybe_recall( 'label_color' )       ],
          [ 'value' => 'luminosity',  'label' => cs_maybe_recall( 'label_luminosity' )  ],
        ],
      ],


      // 07
      // --

      'input-type' => [
        'type'    => 'select',
        'initial' => 'normal',
        'options' => [
          // [ 'value' => 'checkbox',       'label' => 'checkbox'       ],
          // [ 'value' => 'datetime-local', 'label' => 'datetime-local' ],
          [ 'value' => 'email',          'label' => 'email'          ],
          // [ 'value' => 'month',          'label' => 'month'          ],
          [ 'value' => 'number',         'label' => 'number'         ],
          // [ 'value' => 'password',       'label' => 'password'       ],
          // [ 'value' => 'radio',          'label' => 'radio'          ],
          // [ 'value' => 'search',         'label' => 'search'         ],
          // [ 'value' => 'select',         'label' => 'select'         ],
          // [ 'value' => 'submit',         'label' => 'submit'         ],
          [ 'value' => 'tel',            'label' => 'tel'            ],
          [ 'value' => 'text',           'label' => 'text'           ],
          [ 'value' => 'textarea',       'label' => 'textarea'       ],
          // [ 'value' => 'time',           'label' => 'time'           ],
          [ 'value' => 'url',            'label' => 'url'            ],
          // [ 'value' => 'week',           'label' => 'week'           ],
        ],
      ],


      // 08
      // --

      'font-style' => [
        'type'    => 'choose',
        'initial' => 'normal',
        'options' => [
          [ 'value' => 'normal', 'label' => cs_maybe_recall( 'label_normal' )                    ],
          [ 'value' => 'italic', 'label' => cs_maybe_recall( 'label_italic' ), 'class' => 'is-i' ],
        ],
      ],


      // 09
      // --

      'text-align' => [
        'type'     => 'choose',
        'initial'  => 'none',
        'offValue' => 'none',
        'options'  => [
          [ 'value' => 'left',    'icon' => 'ui:text-align-left'    ],
          [ 'value' => 'center',  'icon' => 'ui:text-align-center'  ],
          [ 'value' => 'right',   'icon' => 'ui:text-align-right'   ],
          [ 'value' => 'justify', 'icon' => 'ui:text-align-justify' ],
        ],
      ],


      // 10
      // --

      'text-decoration' => [
        'type'     => 'choose',
        'initial'  => 'none',
        'offValue' => 'none',
        'options'  => [
          [ 'value' => 'underline',    'icon' => 'ui:underline'     ],
          [ 'value' => 'line-through', 'icon' => 'ui:strikethrough' ],
        ],
      ],


      // 11
      // --

      'text-transform' => [
        'type'     => 'choose',
        'initial'  => 'none',
        'offValue' => 'none',
        'options'  => [
          [ 'value' => 'uppercase',  'label' => 'TT', 'class' => 'is-tt' ],
          [ 'value' => 'capitalize', 'label' => 'Tt', 'class' => 'is-tt' ],
          [ 'value' => 'lowercase',  'label' => 'tt', 'class' => 'is-tt' ],
        ],
      ],


      // 12
      // --

      'list-style' => [
        'type'    => 'select',
        'options' => [
          [ 'value' => 'circle',               'label' => cs_maybe_recall( 'label_circle' )               ],
          [ 'value' => 'disc',                 'label' => cs_maybe_recall( 'label_disc' )                 ],
          [ 'value' => 'square',               'label' => cs_maybe_recall( 'label_square' )               ],
          [ 'value' => 'decimal',              'label' => cs_maybe_recall( 'label_decimal' )              ],
          [ 'value' => 'decimal-leading-zero', 'label' => cs_maybe_recall( 'label_decimal_leading_zero' ) ],
          [ 'value' => 'lower-alpha',          'label' => cs_maybe_recall( 'label_lower_alpha' )          ],
          [ 'value' => 'upper-alpha',          'label' => cs_maybe_recall( 'label_upper_alpha' )          ],
          [ 'value' => 'lower-greek',          'label' => cs_maybe_recall( 'label_lower_greek' )          ],
          [ 'value' => 'upper-greek',          'label' => cs_maybe_recall( 'label_upper_greek' )          ],
          [ 'value' => 'lower-roman',          'label' => cs_maybe_recall( 'label_lower_roman' )          ],
          [ 'value' => 'upper-roman',          'label' => cs_maybe_recall( 'label_upper_roman' )          ],
        ],
      ],


      // 13
      // --

      'border-style' => [
        'type'    => 'select',
        'initial' => 'solid',
        'options' => [
          [ 'value' => 'none',   'label' => cs_maybe_recall( 'label_none' )   ],
          [ 'value' => 'solid',  'label' => cs_maybe_recall( 'label_solid' )  ],
          [ 'value' => 'dotted', 'label' => cs_maybe_recall( 'label_dotted' ) ],
          [ 'value' => 'dashed', 'label' => cs_maybe_recall( 'label_dashed' ) ],
          [ 'value' => 'double', 'label' => cs_maybe_recall( 'label_double' ) ],
          [ 'value' => 'groove', 'label' => cs_maybe_recall( 'label_groove' ) ],
          [ 'value' => 'ridge',  'label' => cs_maybe_recall( 'label_ridge' )  ],
          [ 'value' => 'inset',  'label' => cs_maybe_recall( 'label_inset' )  ],
          [ 'value' => 'outset', 'label' => cs_maybe_recall( 'label_outset' ) ],
        ],
      ],


      // 14
      // --

      'overflow' => [
        'type'    => 'choose',
        'initial' => 'visible',
        'options' => [
          [ 'value' => 'visible', 'label' => cs_maybe_recall( 'label_visible' ) ],
          [ 'value' => 'hidden',  'label' => cs_maybe_recall( 'label_hidden' )  ],
        ],
      ],


      // 15
      // --

      'text-overflow' => [
        'type'    => 'choose',
        'initial' => 'normal',
        'options' => [
          [ 'value' => 'normal',   'label' => cs_maybe_recall( 'label_normal' )   ],
          [ 'value' => 'ellipsis', 'label' => cs_maybe_recall( 'label_ellipsis' ) ],
        ],
      ],


      // 16
      // --

      'social-share-networks' => [
        'type'    => 'select',
        'initial' => 'facebook',
        'options' => [
          [ 'value' => 'facebook',  'label' => cs_maybe_recall( 'label_facebook' )  ],
          [ 'value' => 'twitter',   'label' => cs_maybe_recall( 'label_twitter' )   ],
          [ 'value' => 'linkedin',  'label' => cs_maybe_recall( 'label_linkedin' )  ],
          [ 'value' => 'pinterest', 'label' => cs_maybe_recall( 'label_pinterest' ) ],
          [ 'value' => 'reddit',    'label' => cs_maybe_recall( 'label_reddit' )    ],
          [ 'value' => 'email',     'label' => cs_maybe_recall( 'label_email' )     ],
        ],
      ],


      // 17
      // --

      'message-managed-content' => [
        'type'    => "message",
        'icon'    => "ui:info-styled-square",
        'initial' => "This Element's content is managed from its parent. Click here to edit.",
        'action'  => "inspect-parent",
        'when'    => "not(eq($.turnOnMessage, ''))",
      ],


      // 18
      // --

      'fade' => [
        'type'     => 'choose',
        'label'    => cs_maybe_recall( 'label_fade' ),
        'initial'  => 'off',
        'offValue' => 'off',
        'options'  => [
          [ 'value' => 'up',    'icon' => 'ui:uarr' ],
          [ 'value' => 'down',  'icon' => 'ui:darr' ],
          [ 'value' => 'left',  'icon' => 'ui:larr' ],
          [ 'value' => 'right', 'icon' => 'ui:rarr' ],
        ],
      ],


      // 19
      // --

      'fade-stop' => [
        'type'    => 'dimension',
        'label'   => cs_maybe_recall( 'label_fade_stop' ),
        'initial' => '33%',
        'isVar'   => true,
        'when'    => "not(eq(fade, 'off'))"
      ],


      // 20
      // --

      'phone-make-model' => [
        'type'     => 'select',
        'label'    => cs_maybe_recall( 'label_type' ),
        'initial'  => 'apple-iphone-13-pro',
        'options'  => [
          [ 'value' => 'apple-iphone-13-pro', 'label' => 'iPhone 13 Pro'     ],
          [ 'value' => 'google-pixel-6',      'label' => 'Google Pixel 6'    ],
          [ 'value' => 'samsung-s22-ultra',   'label' => 'Samsung S22 Ultra' ],
        ],
      ],


      // 21 (proto)
      // ----------

      'text-format' => [
        'type'   => 'group',
        'params' => [
          'fontFamily'     => [ 'type' => 'font-family',     'label' => cs_maybe_recall( 'label_font' ),       'initial' => 'inherit' ],
          'fontWeight'     => [ 'type' => 'font-weight',     'label' => cs_maybe_recall( 'label_weight' ),     'initial' => 'inherit' ],
          'fontSize'       => [ 'type' => 'font-size',       'label' => cs_maybe_recall( 'label_size' ),       'initial' => '1em'     ],
          'letterSpacing'  => [ 'type' => 'letter-spacing',  'label' => cs_maybe_recall( 'label_spacing' ),    'initial' => '0em'     ],
          'lineHeight'     => [ 'type' => 'line-height',     'label' => cs_maybe_recall( 'label_height' ),     'initial' => '1.6'     ],
          'fontStyle'      => [ 'type' => 'font-style',      'label' => cs_maybe_recall( 'label_style' ),      'initial' => 'normal'  ],
          // 'textAlign'      => [ 'type' => 'text-align',      'label' => cs_maybe_recall( 'label_align' ),      'initial' => 'none'    ],
          // 'textDecoration' => [ 'type' => 'text-decoration', 'label' => cs_maybe_recall( 'label_decoration' ), 'initial' => 'none'    ],
          'textTransform'  => [ 'type' => 'text-transform',  'label' => cs_maybe_recall( 'label_transform' ),  'initial' => 'none'    ],
          'color'          => [ 'type' => 'color',           'label' => cs_maybe_recall( 'label_color' ),      'initial' => '#000000' ]
        ],
      ],

      // Row direction to be used in layout-row Direction control
      'row-direction' => [
        'type'    => 'choose',
        'label'   => cs_maybe_recall( 'label_direction' ),
        'initial' => 'row',
        'options' => [
          [ 'value' => 'row', 'label' => cs_maybe_recall( 'label_standard' ) ],
          [ 'value' => 'row-reverse',  'label' => cs_maybe_recall( 'label_reverse' )  ],
        ],
      ],

    ];

    // Filter any custom ones
    return apply_filters('cs_parameters_managed', $managed);
  }

  public static function transform( $input, $type, $isList ) {
    if (!isset(self::$managed)) {
      self::$managed = self::managedTypes();
    }
    if ( isset( self::$managed[ $type ] ) ) {
      unset($input['type']);
      $additional_params = isset( $input['params'] ) ? $input['params'] : [];
      unset($input['params']);
      $transformed = array_merge( self::$managed[ $type ], $input ); self::$managed[ $type ];

      if ( isset($transformed['params'] ) ) {
        foreach ($additional_params as $key => $param)  {
          $transformed['params'][$key] = $param;
        }
      }

      if ( $isList ) {
        $transformed['type'] .= '[]';
        $transformed['managed'] = true;
      }
      return $transformed;
    }
    return $input;
  }

}
