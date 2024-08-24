<?php

namespace Cornerstone\API\Prefabs;

/**
 * Api Tester prefab
 */

add_action("cs_register_elements", function() {

  // @TODO add to 'advanced' group
  cs_register_prefab_element( 'advanced', 'api-tester', [
    'type'   => 'layout-div',
    'scope'  => [ 'all' ],
    'title'  => __( 'API Tester', 'cornerstone' ),
    'values' => [
      "_type" => "layout-div",
      "_bp_base" => "3_4",
      "_label" => "API Tester",
      "layout_div_padding" => "35px",
      "looper_consumer" => true,
      "looper_provider" => true,
      "looper_provider_api_debug" => true,
      "looper_provider_apiglobal_debug" => true,
      "looper_provider_api_endpoint" => 'http://change-me-in-the-customize-tab/',
      "looper_provider_type" => "api",
      "_modules" => [
        [
          "_type" => "layout-div",
          "_bp_base" => "3_4",
          "_label" => "Success",
          "layout_div_border_color" => "green",
          "layout_div_border_color_alt" => "green",
          "layout_div_border_width" => "2px",
          "layout_div_max_height" => "400px",
          "layout_div_overflow_y" => "scroll",
          "layout_div_padding" => "10px",
          "show_condition" => [
            [
              "group" => true,
              "condition" => "expression:string",
              "value" => "",
              "operand" => '{{dc:looper:field key="errors"}}',
              "operator" => "is"
            ]
          ],
          "_modules" => [
            [
              "_type" => "layout-div",
              "_bp_base" => "3_4",
              "_label" => "Debug Info",
              "looper_provider" => true,
              "looper_provider_array_loop_keys" => true,
              "looper_provider_dc" => '{{dc:looper:field key="info"}}',
              "looper_provider_type" => "dc",
              "layout_div_overflow_y" => "scroll",
              "layout_div_max_height" => "200px",
              "_modules" => [
                [
                  "_type" => "text",
                  "_bp_base" => "3_4",
                  "_label" => "Info Title",
                  "text_content" => "Info",
                  "text_font_size" => "1.5em",
                ],
                [
                  "_type" => "text",
                  "_bp_base" => "3_4",
                  "looper_consumer" => true,
                  "text_content" => "{{dc:looper:index}}: {{dc:looper:item}}",
                ]
              ]
            ],
            [
              "_type" => "gap",
            ],
            [
              "_type" => "text",
              "_bp_base" => "3_4",
              "_label" => "Response Title",
              "text_content" => "Response",
              "text_font_size" => "1.5em",
            ],
            [
              "_type" => "raw-content",
              "_bp_base" => "3_4",
              "raw_content" => '<pre>{{dc:looper:field key="response" type="json" pretty_print="1"}}</pre>',
            ]
          ]
        ],
        [
          "_type" => "layout-div",
          "_bp_base" => "3_4",
          "_label" => "Error",
          "layout_div_border_color" => "red",
          "layout_div_border_color_alt" => "red",
          "layout_div_border_width" => "2px",
          "layout_div_padding" => "10px",
          "show_condition" => [
            [
              "group" => true,
              "condition" => "expression:string",
              "value" => "",
              "operand" => '{{dc:looper:field key="errors"}}',
              "operator" => "is-not"
            ]
          ],
          "_modules" => [
            [
              "_type" => "layout-div",
              "_bp_base" => "3_4",
              "_label" => "Debug Info",
              "looper_provider" => true,
              "looper_provider_array_loop_keys" => true,
              "looper_provider_dc" => '{{dc:looper:field key="info"}}',
              "looper_provider_type" => "dc",
              "_modules" => [
                [
                  "_type" => "text",
                  "_bp_base" => "3_4",
                  "_label" => "Info Title",
                  "text_content" => "Info",
                  "text_font_size" => "1.5em",
                ],
                [
                  "_type" => "text",
                  "_bp_base" => "3_4",
                  "looper_consumer" => true,
                  "text_content" => "{{dc:looper:index}}: {{dc:looper:item}}",
                ]
              ]
            ],
            [
              "_type" => "gap",
              "_bp_base" => "3_4",
            ],
            [
              "_type" => "raw-content",
              "_bp_base" => "3_4",
              "raw_content" => '<code>{{dc:looper:field key="errors" type="json"}}</code>',
            ]
          ]
        ]
      ]
    ]
  ]);

});
