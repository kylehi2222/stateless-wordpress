<?php

/**
 *  Rss Feed External API
 */

cs_register_prefab_element("advanced", "external-api-rss-feed", [
  "title" => __("RSS Feed", CS_LOCALIZE),
  "type" => "section",
  'scope'  => [ 'all' ],
  "values" => [
    "_type" => "section",
    "_bp_base" => "3_4",
    "_label" => "RSS Feed",
    "_m" => [
      "e" => 2
    ],
    "_p_json" => '{"url": "https://www.nasa.gov/rss/dyn/lg_image_of_the_day.rss", "image_key": "enclosure.attr.url" }',
    "looper_provider" => true,
    "looper_provider_api_cache_time" => "21600",
    "looper_provider_api_data_key" => "rss.channel.item",
    "looper_provider_api_endpoint" => "{{dc:p:url}}",
    "looper_provider_api_return_type" => "xml",
    "looper_provider_type" => "api",
    "_modules" => [
      [
        "_type" => "layout-row",
        "_bp_base" => "3_4",
        "_bp_data3_4" => [
          "layout_row_layout" => [
            "100%",
            "50% 50%",
            null,
            null,
            null
          ]
        ],
        "_m" => [
          "e" => 2
        ],
        "layout_row_global_container" => true,
        "layout_row_grow" => true,
        "layout_row_layout" => "33.33% 33.33% 33.33%",
        "_modules" => [
          [
            "_type" => "layout-column",
            "_bp_base" => "3_4",
            "_label" => "RSS Item",
            "_m" => [
              "e" => 2
            ],
            "bg_lower_image" => '{{dc:looper:field key="{{dc:p:image_key}}"}}',
            "bg_lower_type" => "image",
            "effects_alt" => true,
            "effects_provider" => true,
            "effects_transform_alt" => "translate(0px, -2px) scale3d(1.02, 1.02, 1.02)",
            "layout_column_bg_advanced" => true,
            "layout_column_blank" => true,
            "layout_column_box_shadow_color_alt" => "rgb(0, 0, 0)",
            "layout_column_box_shadow_dimensions" => "0px 0px 30px 0px",
            "layout_column_flex_justify" => "space-between",
            "layout_column_flexbox" => true,
            "layout_column_height" => "250px",
            "layout_column_href" => '{{dc:looper:field key="link.content"}}',
            "layout_column_tag" => "a",
            "looper_consumer" => true,
            "looper_consumer_repeat" => "9",
            "_modules" => [
              [
                "_type" => "layout-div",
                "_bp_base" => "3_4",
                "_label" => "Top Right",
                "_m" => [
                  "e" => 1
                ],
                "layout_div_flex_direction" => "row",
                "layout_div_flex_justify" => "flex-end",
                "layout_div_flexbox" => true,
                "layout_div_text_align" => "right",
                "layout_div_width" => "100%",
                "_modules" => [
                  [
                    "_type" => "text",
                    "_bp_base" => "3_4",
                    "_label" => "Pub Date",
                    "_m" => [
                      "e" => 1
                    ],
                    "text_bg_color" => "rgba(0, 0, 0, 0.53)",
                    "text_border_radius" => "0px 0px 0px 6px",
                    "text_content" => '{{dc:looper:field key="pubDate.content" type="date" format="m/d/Y"}}',
                    "text_padding" => "0.499em",
                    "text_text_align" => "right",
                    "text_text_color" => "rgb(255, 255, 255)",
                    "_modules" => [
                    ]
                  ]
                ]
              ],
              [
                "_type" => "text",
                "_bp_base" => "3_4",
                "_label" => "Title",
                "_m" => [
                  "e" => 1
                ],
                "effects_type_alt" => "animation",
                "text_bg_color" => "rgba(0, 0, 0, 0.53)",
                "text_content" => '{{dc:looper:field key="title.content"}}',
                "text_padding" => "0.499em",
                "text_text_color" => "rgb(255, 255, 255)",
                "text_text_color_alt" => "rgb(225, 167, 255)",
                "text_width" => "100%",
                "_modules" => [
                ]
              ]
            ]
          ]
        ]
      ]
    ]
  ],
]);
