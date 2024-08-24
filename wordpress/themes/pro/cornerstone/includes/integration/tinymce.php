<?php

namespace Themeco\Cornerstone\TinyMCE;

const TEXTCOLOR_MAP_KEY = "tinymce_use_textcolor_map";

// Add preference values for TinyMCE
add_filter("cs_app_preference_defaults", function($defaults) {
  $defaults[TEXTCOLOR_MAP_KEY] = apply_filters("cs_tinymce_use_textcolor_map", false);
  $defaults['tinymce_preserve_p_tags'] = true;

  return $defaults;
});

// Add Rich Text editor group to preferences
add_filter("cs_preference_controls", function($controls) {

  $controls[] = [
    'type' => 'group',
    'label' => __("Rich Text", "cornerstone"),
    'controls' => [
      [
        'key' => TEXTCOLOR_MAP_KEY,
        'label' => __("Use Global Colors", "cornerstone"),
        'description' => __("Uses your Theme global colors in the text color picker. This plugin can only use Hex colors and will convert your colors to that if it can. Usage of this does not change if you change your colors", "cornerstone"),
        'type' => 'toggle',
      ],

      [
        'key' => 'tinymce_preserve_p_tags',
        'label' => __('Preserve P Tags', CS_LOCALIZE),
        'description' => __('Preserve and add P tags for new lines. Turning this off is useful if you use HTML mode and dont want the text editor to add P tags', 'cornerstone'),
        'type' => 'toggle',
      ],
    ],
  ];

  return $controls;
});


/**
 * Creates a map where [ "Hex", "Color Name", ] and keeps going
 * https://www.tiny.cloud/docs-4x/plugins/textcolor/#textcolor_map
 *
 * @return array
 */
function cs_tinymce_text_color_map() {
  $GlobalColors = cornerstone("GlobalColors");
  $colors = $GlobalColors->getAllColorItems();

  $built = [];

  foreach ($colors as $color) {
    $hex = $GlobalColors->rgbToHex($color['value']);
    $hex = str_replace("#", "", $hex);
    $built[] = $hex;
    $built[] = $color['title'];
  }

  return $built;
}


// TinyMCE additional from preferences
add_filter("cs_wp_editor_args", function($editorArgs) {
  // No preference for it
  if (!cs_preference_user(TEXTCOLOR_MAP_KEY, false)) {
    return $editorArgs;
  }

  // Add to tinymce args
  $editorArgs['tinymce']['textcolor_map'] = json_encode(
    cs_tinymce_text_color_map()
  );

  return $editorArgs;
}, 0, 1);
