<?php

use Themeco\Cornerstone\Elements\NavMenuFallback;


/**
 * Access Cornerstone without a global variable
 * @return object  main Cornerstone instance.
 */
function CS($component = '') {
  if ($component) {
		return Cornerstone_Plugin::instance()->component( $component );
	}
	return Cornerstone_Plugin::instance();
}




// Partial Data
// =============================================================================

function cs_without( $data, $keys ) {
  return array_diff_key( $data, array_flip( $keys ) );
}

function cs_extract( $data, $find ) {

  // Notes
  // -----
  // 01. $find - (a) Returns $data with a beginning that matches
  //     the $key and (b) that $data is cleaned to reflect the $value as
  //     the new beginning so it can be passed on to the partial template.

  $extracted = array();

  foreach ( $find as $begins_with => $update_to ) {

    foreach ( $data as $key => $value ) {
      if ( 0 === strpos( $key, $begins_with )  ) { // 01

        if ( ! empty( $update_to ) ) {
          $key = $update_to . substr( $key, strlen( $begins_with ) );
        }

        if ( $update_to === null ) {
          $key = trim(substr( $key, strlen( $begins_with ) ),'_');
        }

        $extracted[$key] = $value;

      }
    }
  }

  return $extracted;

}


// ARIA
// =============================================================================

function cs_make_aria_atts( $key_prefix, $aria, $id, $unique_id ) {

  $atts = array();
  $key_prefix  = ( ! empty( $key_prefix ) ) ? $key_prefix . '_' : '';

  if ( isset( $aria['controls'] ) ) {

    $the_id   = ( ! empty( $id ) ) ? $id : $unique_id;
    $the_type = '-' . $aria['controls'];

    $atts[$key_prefix . 'aria_controls'] = $the_id . $the_type;

  }

  if ( isset( $aria['expanded'] ) ) {
    $atts[$key_prefix . 'aria_expanded'] = $aria['expanded'];
  }

  if ( isset( $aria['selected'] ) ) {
    $atts[$key_prefix . 'aria_selected'] = $aria['selected'];
  }

  if ( isset( $aria['haspopup'] ) ) {
    $atts[$key_prefix . 'aria_haspopup'] = $aria['haspopup'];
  }

  if ( isset( $aria['label'] ) ) {
    $atts[$key_prefix . 'aria_label'] = $aria['label'];
  }

  if ( isset( $aria['labelledby'] ) ) {
    $atts[$key_prefix . 'aria_labelledby'] = $aria['labelledby'];
  }

  if ( isset( $aria['hidden'] ) ) {
    $atts[$key_prefix . 'aria_hidden'] = $aria['hidden'];
  }

  if ( isset( $aria['orientation'] ) ) {
    $atts[$key_prefix . 'aria_orientation'] = $aria['orientation'];
  }

  return $atts;

}


// Preview
// =============================================================================

function x_preview_props($keys, $data) {
  return cornerstone('Elements')->previewProps( $keys, $data );
}


// Generated Navigation
// =============================================================================

function cs_pre_wp_nav_menu( $menu, $args ) {

  if ( isset( $args->sample_menu ) ) {
    return cs_wp_nav_menu_fallback( array_merge( (array) $args, array( 'echo' => false ) ) );
  }

  return $menu;

}

add_filter( 'pre_wp_nav_menu', 'cs_pre_wp_nav_menu', 10, 2 );


function cs_wp_nav_menu_fallback( $args ) {

  $fallback = cornerstone()->resolve(NavMenuFallback::class);
  $fallback->config($args);

  return $fallback->output();

}


function cs_render_child_elements( $parent, $hook = 'x_render_children') {
  ob_start();
  do_action( $hook, $parent['_modules'], $parent );
  return ob_get_clean();
}

/**
 * A helper function for elements that do not need to render
 * but need to be passed through loopers
 *
 * @param array $data
 *
 * @return array
 */
function cs_render_function_as_array($data = []) {
  return json_encode($data) . "\n";
}



// Image Setup
// -----------
// This function takes a source image which could be a URL or an attachment ID with a potential size appended.
// It returns an array with src, width, and height keys that can be used to display the image.
// The $retina argument determines if the natural dimensions are divided in half
// 01. Process dynamic content which will also cast any ints to strings
// 02. If $src is empty, return empty values or generate a placeholder for the preview
// 03. If $src contains an integer we assume it is the
//     WordPress attachment ID.
// 04. $src could also be in the format "123:full" which allows us to extract the image size

// 05. Treat all other $src values as a valid URL. This is the only time the $width and $height are actually used

function cs_apply_alt_text( $atts, $alt = '', $fallback_alt = '') {

  if ($alt) {
    $atts['alt'] = cs_dynamic_content($alt);
  } else if ($fallback_alt) {
    $atts['alt'] = $fallback_alt;
  }

  return $atts;
}


function cs_apply_placeholder_src_atts( $alt, $fallback_alt, $retina ) {

  if ( apply_filters( 'cs_is_preview', false ) || did_action( 'cs_element_rendering' ) ) {

    $natural_width  = apply_filters( 'cs_default_image_width', 48 );
    $natural_height = apply_filters( 'cs_default_image_width', 48 );

    return cs_apply_alt_text([
      'src'    => cornerstone_make_placeholder_image_uri( 'rgba(0, 0, 0, 0.35)', $natural_height, $natural_width ),
      'width'  => ( $retina === true ) ? $natural_width / 2 : $natural_width,
      'height' => ( $retina === true ) ? $natural_height / 2 : $natural_height,
    ], $alt, $fallback_alt );

  }

  return cs_apply_alt_text([ 'src' => ''], $alt, $fallback_alt );

}

function cs_apply_lazy_loading( $atts, $enabled ) {
  // https://developer.wordpress.org/reference/functions/wp_get_attachment_image/
  if ( $enabled && function_exists('wp_lazy_loading_enabled') && wp_lazy_loading_enabled( 'img', 'cs_apply_image_atts' ) ) {
    $atts['loading'] = 'lazy';
  }
  return apply_filters("cs_apply_lazy_loading", $atts);
}


// Runs wp_get_attachment_url and works based on that
function cs_resolve_attachment_source($id, $local = false) {
  // Local needs
  $idToCheck = $local
    ? preg_replace("/:.*$/", "", $id)
    : $id;

  // Local file or use url
  $url = $local
    ? get_attached_file($idToCheck)
    : wp_get_attachment_url($id);

  // Either a full URL or not an attachment
  if (empty($url)) {
    return $id;
  }

  return $url;
}


function cs_resolve_image_source( $source, $size = null ) {
  $img_atts = cs_apply_image_atts( [ 'src' => $source, 'size' => null ]);
  return isset( $img_atts['src'] ) && $img_atts['src'] ? $img_atts['src'] : $source;
}

function cs_apply_image_atts( $args ) {

  /**
   * Add the code below to a child theme to enable srcset for any images
   * not configured to use retina (double pixel density)
   * This is not enabled by default because the implementation is subject
   * to change in a future major release where we are revisiting theme options.
   *
   * add_filter( 'cs_enable_srcset', '__return_true' );
   *
   */

  $args = array_merge([
    'src'          => '',
    'retina'       => false,
    'width'        => null,
    'height'       => null,
    'alt'          => '',
    'size'         => null,
    'fallback_alt' => apply_filters( 'cs_fallback_alt_text', __('Image', '__x___') ),
    'lazy'         => apply_filters( 'cs_lazy_load_images', true, $args),
    'attachment_srcset' => false, // force set by the filter as well later
  ], $args );

  // Force set srcset if the filter is enabled
  if (apply_filters('cs_enable_srcset', false)) {
    $args['attachment_srcset'] = true;
  }

  extract( $args );

  if ($retina) {
    $srcset = false;
  }

  $src = cs_dynamic_content( $src ); // 01

  if ( empty( $src ) ) { // 02
    return cs_apply_placeholder_src_atts( $alt, $fallback_alt, $retina );
  }

  $parts = explode(':', $src);
  $attachment_id = intval($parts[0]);

  // Check if src is an attachment URL
  if (!$attachment_id) {
    $postId = attachment_url_to_postid($src);
    if ($postId) {
      $attachment_id = $postId;
    }
  }

  if ($attachment_id) { // 03

    $size = isset( $parts[1] ) ? $parts[1] : 'full'; // 04
    if ( ! is_null( $args['size'] ) ) {
      $size = $args['size'];
    }

    $attachment_meta = wp_get_attachment_image_src( $attachment_id, $size );

    list( $img_src, $img_width, $img_height ) = $attachment_meta;

    if (empty($img_src)) {
      return cs_apply_placeholder_src_atts( $alt, $fallback_alt, $retina );
    }

    // Get alt tag from attachment ID
    $img_alt = cs_attachment_alt_from_id($attachment_id);

    if ($img_alt) {
      $fallback_alt = $img_alt;
    }

    $atts = [
      'src' => $img_src,
    ];

    // Attachment srcset setup
    // using wp_get_attachment_image_srcset
    if (!empty($args['attachment_srcset'])) {
      $image_meta = wp_get_attachment_metadata( $attachment_id );

      if ( is_array( $image_meta ) ) {
        $size_array = [ absint( $img_width ), absint( $img_height ) ];
        $srcset     = wp_calculate_image_srcset( $size_array, $img_src, $image_meta, $attachment_id );
        $sizes      = wp_calculate_image_sizes( $size_array, $img_src, $image_meta, $attachment_id );

        if ( $srcset && ( $sizes || ! empty( $attr['sizes'] ) ) ) {
          $atts['srcset'] = $srcset;

          if ( empty( $attr['sizes'] ) ) {
            $atts['sizes'] = $sizes;
          }
        }
      }
    } else {
      // Non srcset width and height
      $atts['width']  =  ( $retina === true )
        ? floor($img_width / 2)
        : $img_width;
      $atts['height'] =  ( $retina === true )
        ? floor($img_height / 2)
        : $img_height;
    }

    $atts = cs_apply_lazy_loading( cs_apply_alt_text( $atts, $alt, $fallback_alt ), $lazy );
    return apply_filters("cs_apply_image_atts", $atts);

  }

  $atts = [ 'src' => $src ];

  $natural_width  = $width ? round( (float)$width ) : $width;
  $natural_height = $height ? round( (float)$height ) : $height;

  if ( !empty( $natural_width ) ) {
    $atts['width'] = (is_float($natural_width)  && $retina === true)
      ? floor($natural_width / 2)
      : $natural_width;
  }

  if ( !empty( $natural_height ) ) {
    $atts['height'] = (is_float($natural_height) && $retina === true)
      ? floor($natural_height / 2)
      : $natural_height;
  }

  $atts = cs_apply_lazy_loading( cs_apply_alt_text( $atts, $alt, $fallback_alt ), $lazy );
  return apply_filters("cs_apply_image_atts", $atts);

}

// Get alt text from a post id
// nicely formatted for an alt attribute
function cs_attachment_alt_from_id($id) {
  return trim( strip_tags( get_post_meta( $id, '_wp_attachment_image_alt', true ) ) );
}

function cs_identity_bar_position( $bar ) {

  $region = $bar['_region'];
  if ( $region === 'top' ) {
    if ( $bar['bar_sticky'] === true && $bar['bar_sticky_hide_initially'] === true ) {
      return 'absolute';
    } else {
      return $bar['bar_position_top'];
    }
  } else if ( ! in_array($region, [ 'top', 'bottom', 'right', 'left' ])) {
    return 'relative';
  }

  return 'fixed';

}


/**
* Mostly for migrations as I dont want to use the classes
*/
function cs_hide_breakpoint_classes($data = []) {
  if (empty($data['hide_bp'])) {
    return [];
  }

  // Breakpoint instance
  $breakpoints = cornerstone('Breakpoints');

  // Loop hidden breakpoints from delimiter space
  $classes = [];
  $bps = explode(' ', trim(cs_dynamic_content($data['hide_bp'])));
  foreach ($bps as $bp) {
    // Older versioned used none
    if (empty($bp) || $bp === "none") {
      continue;
    }

    $classes[] = $breakpoints->hideClass($bp);
  }

  return $classes;
}

function cs_hide_breakpoint_classes_string($data = []) {
  return implode(" ", cs_hide_breakpoint_classes($data));
}

/**
 * For ranges output the values with prefix and suff
 */
function cs_breakpoint_output_loop($values = [], $prefix = "", $suffix = "") {
  return cornerstone("Breakpoints")->mediaOutputLoop($values, $prefix, $suffix);
}

/**
 * Helper for cs_breakpoint_output_loop
 * that echos after
 */
function cs_breakpoint_output_loop_output($values, $prefix = "", $suffix = "") {
  echo cs_breakpoint_output_loop($values, $prefix, $suffix);
}

function cs_breakpoint_config() {
  return cornerstone("Breakpoints")->breakpointConfigObject();
}

/**
 * Get element values
 * from _bp_base
 */
function cs_breakpoint_get_element_values($element, $key) {
  if (empty($element['_bp_base'])) {
    return [];
  }

  $base = $element['_bp_base'];

  return empty($element['_bp_data' . $base][$key])
    ? []
    : $element['_bp_data' . $base][$key];
}

/**
 * Set breakpoint val
 */
function cs_breakpoint_set_element_value(&$element, $key, $index, $value) {
  if (empty($element['_bp_base'])) {
    return false;
  }

  $base = $element['_bp_base'];
  $element['_bp_data' . $base][$key][$index] = $value;

  return true;
}

function cs_create_bar_space( $bar ) {
  $classes = [ $bar['style_id'], $bar['_tss']['bar'], 'x-bar-space', 'x-bar-space-' . $bar['_region'], $bar['_region'] === 'left' || $bar['_region'] === 'right' ? 'x-bar-space-v' : 'x-bar-space-h' ];

  $classes = array_merge(cs_hide_breakpoint_classes($bar), $classes);

  if ( isset( $bar['class'] ) ) { // custom class only - not generated classes
    $classes[] = $bar['class'];
  }

  // Prepare Atts
  // ------------

  $atts = [ 'class' => $classes ];

  if ( $bar['_region'] === 'top' ) {
    $atts['style'] = 'display: none;';
  }

  return cs_tag( 'div', $atts, null );
}


function cs_get_path( $data, $key ) {
  // Attempt to grab path of object
  // Used by loopers
  if (is_object($data)) {
    $data = cs_array_safe_conversion($data);
  }

  // Key valid
  // 0 passes
  if ( (!$key && !is_int($key)) || ! is_scalar( $key ) ) {
    return null;
  }

  // Ignore dot syntax check if there is a key
  // with a possible dot in its key
  if (isset($data[$key])) {
    return $data[$key];
  }

  $paths = array_reverse( explode('.', $key) );

  $current = $data;
  while( count($paths) > 0 ){
    $path = array_pop($paths);
    if (! isset($current[$path])) {
      return null;
    }
    $current = $current[$path];
  }

  return $current;

}

// Access array properties safely
function cs_get_array_value($data, $key, $default = null) {
  if (!is_array($data)) {
    trigger_error("Passed invalid array cs_get_array_value");
    return null;
  }

  if (!isset($data[$key])) {
    return $default;
  }

  return $data[$key];
}

// Safe conversion to (array)
function cs_array_safe_conversion($data) {
  try {
    $data = (array)$data;
  } catch (\Exception $e) {
    return null;
  }

  return $data;
}

// Generate HTML Attributes
// =============================================================================
// 01. Merge all incoming arguments together
// 02. Treat strings as JSON and decode before merging
// 03. Additional consideration when merging the class attribute
// 04. Combine the output into a string
// 05. Values that are explicitly set to false will be omitted
// 06. When attribute is null, treat as an attribute without a value [ 'data-thing' => null ] becomes "data-thing"
// 07. Create attribute name/value pair

function cs_ensure_class_array( $input ) {
  if ( is_string($input ) ) {
    return array_filter( explode(' ', $input ) );
  }

  return array_reduce($input,function($acc,$next) {
    $items = cs_ensure_class_array( $next );
    foreach( $items as $item ) {
      if ( is_array($item ) ) {
        $acc = array_merge( $acc, cs_ensure_class_array( $item ) ) ;
      } else {
        $acc[] = $item;
      }
    }
    return $acc;
  }, []);
}

function cs_atts_merge( $args ) {

  $merged = array();
  foreach($args as $set) { // 01
    if (is_string($set)) {
      $set = json_decode( $set, true ); // 02
    }
    if (!is_array($set)) {
      continue;
    }
    if (isset($merged['class']) && isset($set['class']) ) { // 03
      $set['class'] = array_unique(array_merge( cs_ensure_class_array( $merged['class']), cs_ensure_class_array( $set['class'])));
    }
    $merged = array_merge( $merged, $set );
  }

  return $merged;
}

function cs_atts_flatten($merged) {
  $results = [];

  foreach ( $merged as $attr => $value ) { // 04

    if ($value === false) { // 05
      continue;
    }

    if ( is_null( $value ) ) { // 06
      $results[] = esc_attr($attr);
    } else {
      $results[] = esc_attr($attr) . '="' . esc_attr( is_array( $value ) ? implode( ' ', array_filter( $value ) ) : $value ) . '"'; // 07
    }

  }

  return implode(' ', $results);
}

function cs_atts() {
  return cs_atts_flatten(cs_atts_merge(func_get_args()));
}


// Shim for Pro4 used to be x_attr_class
function cs_attr_class($classes = []) {
  $result = '';

  if ( ! empty( $classes ) ) {
    $result = implode( ' ', array_filter( $classes ) );
  }

  return $result;
}

// Generate HTML Tag
// =============================================================================
// 01. First argument is the tag like "div"
// 02. Last argument represents the tag contents. Here is how the types are handled:
//       true       - self closing
//       false/null - no children
//       string     - direct output
//       array      - combine
// 03. Intermediate arguments are passed to x_atts allowing multiple sets of attributes to be passed in
// 04. When children is explicitly true, return a self closing tag
// 05. When children is explicitly false or null, return an empty tag
// 06. Combine array children if needed. Default array_filter omits 0 and '0' which might be valid output
// 07. Final string output

// Example usage:
//  cs_tag( 'div', [ 'class' => ['hello', 'world'] ], 'content' ); yields <div class="hello world">content</div>
//  cs_tag( 'div', [ 'class' => 'one', ['first', 'second'] ); yields <div class="one">firstsecond</div>
//  cs_tag( 'div', [ 'class' => 'one' ], [ 'class' => 'two', 'style' => 'display:none;' ], null ); yeilds <div class="one two" style="display:none;"></div>
//  cs_tag( 'br', true); yields <br/>

function cs_tag() { // $tag, ...$atts, $children
  $args = func_get_args();
  $tag = array_shift( $args ); // 01
  $content = array_pop( $args ); // 02

  $atts = cs_atts_merge( $args );
  $atts_str = cs_atts_flatten( $atts );
  $tag_str = trim("$tag $atts_str");

  if ( $content === true ) { // 04
    return "<$tag_str/>";
  }

  if ( $content === false || $content === null ) { // 05
    return "<$tag_str></$tag>";
  }

  if ( is_array( $content ) ) { // 06
    $content = implode( '', array_filter( $content, function( $child ) { return $child !== null;  } ) );
  }

  $atts = apply_filters('cs_tag_atts', $atts, $content );
  $content = apply_filters('cs_tag_content', $content, $atts );

  return "<$tag_str>$content</$tag>"; // 07
}

function cs_open_tag( $tag, $atts = []) {
  $atts_str = cs_atts($atts);
  $tag_str = trim("$tag $atts_str");
  return "<$tag_str>";
}

function csi18n( $key ) {
	return cornerstone('I18n')->get( $key );
}

function e_csi18n( $key ) {
	echo csi18n( $key );
}

function cs_fa_all() {
  return cornerstone('FontAwesome')->getFontIds();
}

/**
 * Get an HTML entity for an icon
 * @param  string $key Icon to lookup
 * @return string      HTML entity
 */

function fa_get_attr( $key ) {
  return cornerstone('FontAwesome')->attr( $key );
}

function fa_entity( $key ) {
  return fa_get_attr( $key )['entity'];
}

function fa_icon_object($key) {
  return cornerstone('FontAwesome')->getFontIconObject( $key );
}

/**
 * Svg url output of given font awesome key
 */
function fa_get_svg_path( $key ) {
  return cornerstone('FontAwesome')->getSVGPath( $key );
}

function fa_get_svg_output( $key ) {
  return cornerstone('FontAwesome')->getSVGOutput( $key );
}

function cs_fa_get_icon_from_unicode( $unicode ) {
  return cornerstone('FontAwesome')->getIconFromUnicode( $unicode );
}

function cs_fa_icon_tag_from_unicode($icon, $className = '', $content = '', $type = 's') {
  if (
    function_exists("fa_get_svg_output")
    && x_get_option("x_font_awesome_icon_type") === "svg"
  ) {
    $icon = cs_fa_get_icon_from_unicode($icon);
    $icon = $type . '-' . $icon;
    $svgOutput = fa_get_svg_output($icon);

    return "<span class='x-framework-icon $className' aria-hidden=true>{$svgOutput}{$content}</span>";
  }

  return "<i class='x-framework-icon $className' data-x-icon-$type='&#x$icon;' aria-hidden=true>{$content}</i>";
}

// Better naming move above to this
function cs_icon_unicode_get($icon, $className = '', $content = '', $type = 's') {
  return cs_fa_icon_tag_from_unicode($icon, $className, $content, $type);
}

/**
 * Template function that returns a data attribute for an icon
 * @param  string $key Icon to lookup
 * @return string      Data attribute string that can be placed inside an element tag
 */
function fa_data_icon( $key ) {
  $icon = fa_get_attr( $key );
  return $icon['attr']. '="' . $icon['entity'] . '"';
}

/**
 * Get a posts excerpt without the_content filters being applied
 * This is useful if you need to retreive an excerpt from within
 * a shortcode.
 * @return string Post excerpt
 */

function cs_get_excerpt_for_social( $post = null) {

	// Swap wp_trim_excerpt for cs_trim_excerpt_for_social
	add_filter( 'get_the_excerpt', 'cs_trim_excerpt_for_social' );
	remove_filter( 'get_the_excerpt', 'wp_trim_excerpt' );

	$excerpt = get_the_excerpt( $post );

	// Restore original WordPress behavior
	add_filter( 'get_the_excerpt', 'wp_trim_excerpt' );
	remove_filter( 'get_the_excerpt', 'cs_trim_excerpt_for_social' );

	return $excerpt;
}

/**
 * Themeco customized version of the wp_trim_excerpt function in WordPress formatting.php
 * Generates an excerpt from the content, if needed.
 *
 * @param string $text Optional. The excerpt. If set to empty, an excerpt is generated.
 * @return string The excerpt.
 */

function cs_trim_excerpt_for_social( $text = '' ) {

	$raw_excerpt = $text;

	if ( '' === $text ) {

		$text = get_the_content( '' );
		$text = strip_shortcodes( $text );

		$text = str_replace( ']]>', ']]&gt;', $text );

		$excerpt_length = apply_filters( 'excerpt_length', 55 );

		$excerpt_more = apply_filters( 'excerpt_more', ' [&hellip;]' );
		$text = wp_trim_words( $text, $excerpt_length, $excerpt_more );
	}

	return apply_filters( 'wp_trim_excerpt', $text, $raw_excerpt );

}


function cs_expand_content( $content = '' ) {
  return apply_filters( 'cs_expand_content', $content );
}

function cs_expand_content_no_wp( $content = '' ) {
  return apply_filters( 'cs_expand_content_no_wp', $content );
}

// Get Unitless Milliseconds
// =============================================================================
// 01. If unit is "seconds", multiply by 1000 to get millisecond value.
// 02. Fallback if we fail.

function cs_get_unitless_ms( $duration = '500ms' ) {

  $unit_matches = array();

  if ( preg_match( '/(s|ms)/', $duration, $unit_matches ) ) {

    $duration_unit = $unit_matches[0];
    $duration_num  = floatval( preg_replace( '/' . $duration_unit . '/', '', $duration ) );

    if ( $duration_unit === 's' ) {
      $duration_num = $duration_num * 1000; // 01
    }

    $the_duration = $duration_num;

  } else {

    $the_duration = 500; // 02

  }

  return $the_duration;

}



// Data Attribute Generator
// =============================================================================

function cs_prepare_json_att( $atts, $filter = false ) {
  return htmlspecialchars( wp_json_encode( $filter ? array_filter( $atts, 'strlen' ) : $atts ), ENT_QUOTES, 'UTF-8' );
}

function cs_element_js_atts( $element, $params = array(), $inline = false ) {

  if ($inline) {
    // We should move all old elements to the "inline" version since cs_atts already handles escaping, and the JS would be less redundant
    $atts['data-x-element-' . $element ] = empty( $params ) ? '' : cs_prepare_json_att( $params );
  } else {
    $atts = array( 'data-x-element' => esc_attr( $element ) );

    if ( ! empty( $params ) ) {
      $atts['data-x-params'] = cs_prepare_json_att( $params );
    }
  }



  return $atts;

}

// Notes
// -----
// 01. Must only start with `x-effect-exit` class because if we add in the
//     "out" animation on load, if using something like "hinge," the default
//     position that is used might be WAYYYYYY down from the actual position
//     of the button. Starting with only the `x-effect-exit` class gives us the
//     correct starting position.

function cs_apply_effect( $atts, $_cd, $options = [] ) {
  $json_data                   = [];
  $has_effect_provider         = isset($_cd['effects_provider'] )    && $_cd['effects_provider']    === true;
  $has_effect_alt              = isset($_cd['effects_alt'] )         && $_cd['effects_alt']         === true;
  $has_effect_scroll           = isset($_cd['effects_scroll'] )      && $_cd['effects_scroll']      === true;
  $has_effect_transform_alt    = isset($_cd['effects_type_alt'] )    && $_cd['effects_type_alt']    === 'transform';
  $has_effect_animation_alt    = isset($_cd['effects_type_alt'] )    && $_cd['effects_type_alt']    === 'animation';
  $has_effect_animation_scroll = isset($_cd['effects_type_scroll'] ) && $_cd['effects_type_scroll'] === 'animation';

  if ( $has_effect_scroll ) {
    // Class sometimes not set
    if (!isset($atts['class'])) {
      $atts['class'] = [];
    }

    if ( is_array( $atts['class'] ) ) {
      $atts['class'][] = 'x-effect-exit';
    } else {
      $atts['class'] .= ' x-effect-exit';
    }
  }

  if ( $has_effect_provider && isset( $_cd['effects_provider_targets'] ) ) {
    $atts['data-x-effect-provider'] = $_cd['effects_provider_targets'];
  }

  if ( $has_effect_alt || $has_effect_scroll ) {
    if ( $has_effect_alt ) {
      if ( $has_effect_transform_alt ) {
        $json_data['durationBase'] = $_cd['effects_duration'];
      }

      if ( $has_effect_animation_alt ) {
        $json_data['animationAlt'] = $_cd['effects_animation_alt'];
      }
    }

    if ( $has_effect_scroll ) {

      $json_data['scroll'] = true;
      $json_data['offsetTop']      = $_cd['effects_offset_top'];
      $json_data['offsetBottom']   = $_cd['effects_offset_bottom'];
      $json_data['behaviorScroll'] = $_cd['effects_behavior_scroll'];

      if ( $has_effect_animation_scroll ) {
        $json_data['animationEnter'] = $_cd['effects_animation_enter'];
        $json_data['animationExit']  = $_cd['effects_animation_exit'];
      }

      $force_scroll_effects = apply_filters( 'cs_preview_force_scroll_effects', '' );

      if ( $force_scroll_effects ) {
        $json_data['forceScrollEffect'] = $force_scroll_effects;
      }

    }

    return array_merge( $atts, [ 'data-x-effect' => cs_prepare_json_att( array_merge( $json_data, $options ) ) ] );

  }

  return $atts;
}

// Notes
// -----
// 01. Applies the link attributes to an element.

function cs_apply_link( $atts, $_cd, $k_pre, $fallbackTag = 'span' ) {


  if ( ! isset( $_cd["{$k_pre}_tag"])) {
    return ['div', $atts];
  }


  $tag = isset( $atts['href'] ) ? 'a' : $_cd["{$k_pre}_tag"];

  if ( $tag === "a" && apply_filters( 'cs_in_link', false ) ) {
    $tag = $fallbackTag;
  }

  if ( $tag === 'a' ) {

    if (isset( $atts['href'] )) {
      $href = $atts['href'];
    } else {
      $href = isset( $_cd["{$k_pre}_href"]) && ! empty( $_cd["{$k_pre}_href"] ) ?  $_cd["{$k_pre}_href"] : '';
    }

    if ( ! $href ) {
      return ['div', $atts];
    }

    $atts['href'] = $href;

    if ( isset( $_cd["{$k_pre}_nofollow"] ) && !empty($_cd["{$k_pre}_nofollow"]) ) {
      $atts['rel'] = 'nofollow';
    }

    if ( isset( $_cd["{$k_pre}_blank"] ) && !empty($_cd["{$k_pre}_blank"]) ) {
      $atts['target'] = '_blank';
      $atts = cs_atts_with_targeted_link_rel( $atts );
    }
  }

  return [$tag, $atts];

}



// Data Attribute Generator - Legacy Shortcodes
// =============================================================================

function cs_generate_data_attributes( $element, $params = array(), $inline = false ) {
  return cs_atts( cs_element_js_atts( $element, $params, $inline ) );
}

function cs_generate_data_attributes_extra( $type, $trigger, $placement, $title = '', $content = '' ) {

  wp_enqueue_script( 'jquery' ); // jQuery Blocking

  if ( ! in_array( $type, array( 'tooltip', 'popover' ), true ) ) {
		return '';
	}

	$js_params = array(
		'type'      => ( 'tooltip' === $type ) ? 'tooltip' : 'popover',
		'trigger'   => $trigger,
		'placement' => $placement,
		'title'     => wp_specialchars_decode( $title ), // to avoid double encoding.
		'content'   => wp_specialchars_decode( $content ),
	);

	return cs_generate_data_attributes( 'extra', $js_params );

}

// JS Asset helper
function cs_js_asset_get($assetName = "") {
  return cornerstone("EnqueueScripts")->getJsAsset($assetName);
}

function cs_js_internal_asset_register($name, $assetName) {

  $asset = cs_js_asset_get($assetName);
  wp_register_script( $name, $asset['url'], ['code-editor'], $asset['version'] );

}


// Background Video Output
// =============================================================================

function cs_bg_video( $video, $poster, $loop = true, $options = [] ) {

  $output = do_shortcode( '[x_video_player class="bg transparent" src="'
    . $video
    . '" poster="' . $poster
    . '" hide_controls="true" autoplay="true"'.( $loop ? ' loop="true"' : '' )
    .' muted="true" playsinline="true" no_container="true"'
    . ' options=\'' . json_encode($options) . '\''
    . ']'
  );

	return $output;

}








// Animation Base Class
// =============================================================================

function cs_animation_base_class( $animation_string ) {

	if ( false !== strpos( $animation_string, 'In' ) ) {
		$base_class = ' animated-hide';
	} else {
		$base_class = '';
	}

	return $base_class;

}

/**
 * Sanitize a value for use in a shortcode attribute
 * @param  string $value Value to clean
 * @return string        Value ready for use in shortcode markup
 */
function cs_clean_shortcode_att( $value ) {

  if ( ! is_scalar( $value ) ) {
    return '';
  }

	$value = wp_kses( $value, wp_kses_allowed_html( 'post' ) );
	$value = esc_html( $value );
	$value = str_replace( ']', '&rsqb;', str_replace( '[', '&lsqb;', $value ) );

	return $value;
}

/**
 * Remove <p> and <br> tags added by wpautop around shortcodes.
 * This is used for anything within a Cornerstone section to keep
 * the markup clean and predictable.
 * @param  string $content Content to be cleaned
 * @return string          Cleaned content
 */

function cs_shortcode_att( $attribute, $content, $echo = false ) {

	$att = '';

	if ( $content ) {
		$att = esc_attr( $attribute ) . '="' . cs_clean_shortcode_att( $content ) . '" ';
	}

	if ( is_null( $content ) ) {
		$att = esc_attr( $attribute ) . ' ';
	}

	if ( $echo ) {
		echo $att;
	}

	return $att;

}

function cs_shortcode_atts( $atts, $echo = false ) {
	$result = '';
	foreach ( $atts as $att => $content) {
		$result .= cs_shortcode_att( $att, $content, false );
	}
	if ( $echo ) {
		echo $result;
	}
	return $result;
}

// Build Shortcode
// =============================================================================

function cs_build_shortcode( $name, $attributes, $extra = '', $content = '', $require_content = false ) {

	$output = "[{$name} " . cs_shortcode_atts( $attributes );

	if ( '' !== $extra ) {
		$output .= " {$extra}";
	}

	if ( '' === $content && ! $require_content ) {
		$output .= ']';
	} else {
    $content = apply_filters( 'cs_element_update_build_shortcode_content', $content, null );
		$output .= "]{$content}[/{$name}]";
	}

	return $output;

}


function cs_render_shortcode( $name, $attributes, $extra = '', $content = '', $require_content = false ) {
  return cs_expand_content( cs_build_shortcode( $name, $attributes, $extra, $content, $require_content ) );
}


function cs_alias_shortcode( $new_tag, $existing_tag, $filter_atts = true ) {

  global $cs_shortcode_aliases;

  if ( ! $cs_shortcode_aliases ) {
    $cs_shortcode_aliases = array();
  }

	if ( is_array( $new_tag ) ) {
		foreach ($new_tag as $tag) {
			cs_alias_shortcode( $tag, $existing_tag, $filter_atts );
		}
		return;
	}

	if ( ! shortcode_exists( $existing_tag ) ) {
		return;
	}

	global $shortcode_tags;
	add_shortcode( $new_tag, $shortcode_tags[ $existing_tag ] );

  if ( ! in_array($new_tag, $cs_shortcode_aliases) ) {
    $cs_shortcode_aliases[] = $new_tag;
  }

  if ( ! in_array($existing_tag, $cs_shortcode_aliases) ) {
    $cs_shortcode_aliases[] = $existing_tag;
  }


	if ( ! $filter_atts || ! has_filter( $tag = "shortcode_atts_$existing_tag" ) ) {
		return;
	}

	global $wp_filter;

	foreach ( $wp_filter[ $tag ] as $priority => $filter ) {
		foreach ($filter as $tag => $value) {
			add_filter( "shortcode_atts_$new_tag", $value['function'], $priority, $value['accepted_args'] );
		}
	}

}

function cs_array_filter_use_keys( $array, $callback ) {
	return array_intersect_key( $array, array_flip( array_filter( array_keys( $array ), $callback ) ) );
}

/**
 * Runs wp_parse_args, then applies key whitelisting based on keys from defaults
 * @param  array $args     User arguments
 * @param  array $defaults Default keys ans values
 * @return array           Arguments with defaults and key whitelisting applied.
 */
function cs_define_defaults( $args, $defaults ) {
	return array_intersect_key( wp_parse_args( $args, $defaults ), array_flip( array_keys( $defaults ) ) );
}



function cs_noemptyp( $content ) {

	$array = array(
		'<p>['    => '[',
		']</p>'   => ']',
		']<br />' => ']',
	);

	$content = strtr( $content, $array );

	return $content;

}


/**
 * Allows HTML to be passed through shortcode attributes by decoding entities.
 * We apply the cs_decode_shortcode_attribute filter to allow other
 * functions to process and expand directives if needed.
 * @param  string $content Original content from shortcode attribute.
 * @return string          HTML ready to use in shortcode output
 */
function cs_decode_shortcode_attribute( $content ) {
  if ( ! is_string( $content ) ) {
    return $content;
  }
	return apply_filters( 'cs_decode_shortcode_attribute', wp_specialchars_decode( $content, ENT_QUOTES ) );
}


function cs_update_serialized_post_meta( $post_id, $meta_key, $meta_value, $prev_value = '', $allow_revision_updates = false, $filter = '' ) {


	if ( is_array( $meta_value ) && apply_filters( 'cornerstone_store_as_json', true ) ) {
		$meta_value = wp_slash( cs_json_encode( $meta_value ) );
	}

  if ( $filter ) {
    $meta_value = apply_filters( $filter, $meta_value );
  }

  if ( $allow_revision_updates ) {
    return update_metadata('post', $post_id, $meta_key, $meta_value, $prev_value );
  }

	return update_post_meta( $post_id, $meta_key, $meta_value, $prev_value );

}


function cs_json_encode( $value ) {

  if ( apply_filters( 'cornerstone_json_unescaped_slashes', true ) ) {
    return wp_json_encode( $value, JSON_UNESCAPED_SLASHES );
  }

  return wp_json_encode( $value );

}

function cs_get_serialized_post_meta( $post_id, $key = '', $single = false, $filter = '' ) {
  $meta_value = get_post_meta( $post_id, $key, $single );
  if ( $filter ) {
    $meta_value = apply_filters( $filter, $meta_value );
  }
  return apply_filters('cs_get_serialized_post_meta', cs_maybe_json_decode( $meta_value ), $post_id, $key );
}

function cs_maybe_json_decode( $value ) {
	if ( is_string( $value ) ) {
		$decoded = json_decode( $value, true );
    if ( is_null( $decoded ) ) { // older versions of Cornerstone stored JSON with escaped slashes
      $decoded = json_decode( wp_unslash( $value ), true );
    }
    return $decoded;
	}
	return $value;
}


/**
 * Add 'noopener noreferrer' to a string if it doesn't exist yet
 */
function cs_targeted_link_rel( $rel = '', $is_target_blank = true ) {

	if ( $is_target_blank && apply_filters( 'tco_targeted_link_rel', ! is_ssl() ) ) {

		$more = apply_filters( 'tco_targeted_link_rel', array( 'noopener', 'noreferrer' ) );

		foreach ($more as $str ) {
			if ( false === strpos($rel, $str ) ) {
				$rel .= " $str";
			}
		}

	}

	return ltrim($rel);

}


function cs_atts_for_social_sharing( $atts, $type, $title ) {
	return cornerstone('Social')->setup_atts( $atts, $type, $title );
}

/**
 * Maybe add rel att
 */
function cs_atts_with_targeted_link_rel( $atts = array(), $is_target_blank = true ) {

	$rel = cs_targeted_link_rel( isset($atts['rel']) ? $atts['rel'] : '', $is_target_blank );

	if ( $rel ) {
		$atts['rel'] = $rel;
	}

	return $atts;
}

function cs_output_target_blank($echo = true) {
	$output = 'target="_blank" rel="' . cs_targeted_link_rel() .'"';
	if ($echo) {
		echo $output;
	}
	return $output;
}

function cs_get_countdown_labels( $plural = true, $compact = false ) {
	if ($compact) {
		return array(
			'd' => __( 'D', 'cornerstone' ),
			'h' => __( 'H', 'cornerstone' ),
			'm' => __( 'M', 'cornerstone' ),
			's' => __( 'S', 'cornerstone' )
		);
	}

	return array(
    'd' => _n( 'Day', 'Days', $plural ? 2 : 1, 'cornerstone' ),
    'h' => _n( 'Hour', 'Hours', $plural ? 2 : 1, 'cornerstone' ),
    'm' => _n( 'Minute', 'Minutes', $plural ? 2 : 1, 'cornerstone' ),
    's' => _n( 'Second', 'Seconds', $plural ? 2 : 1, 'cornerstone' )
	);
}


function cs_make_particle( $data, $class, $k_pre, $always_active ) {

  if ( ! isset( $data[$k_pre] ) || ! $data[$k_pre] ) {
    return '';
  }

  // x_preview_props doesn't work here anymore because by the time we get to this level, key prefixes have been removed
  // in the preview, everything is "undefined" because it doesn't map to the original element key

  // $args = cs_extract( x_preview_props([
  //   $k_pre . '_location',
  //   $k_pre . '_placement',
  //   $k_pre . '_scale',
  //   $k_pre . '_style',
  // ], $data), [ $k_pre => 'particle' ] );

  $args = cs_extract( $data, [ $k_pre => 'particle' ] );

  $particle_class = $always_active ? 'is-' . $class . ' x-always-active' : 'is-' . $class;
  $particle_scale = ( $args['particle_scale'] != 'none'  ) ? $args['particle_scale'] . ' ' : '';

  $atts = [
    'class'           => [ 'x-particle', $particle_class ],
    'data-x-particle' => $particle_scale . $args['particle_placement'] . '-' . $args['particle_location'],
    'aria-hidden'     => 'true',
  ];

  return cs_tag( 'span', $atts, cs_tag('span', [ 'style' => $args['particle_style'] ], '' ) );

}


function cs_make_particles( $data, $k_pre, $primary_always_active = false, $secondary_always_active = false ) {

  $primary = cs_make_particle( $data, 'primary', $k_pre . '_primary_particle', $primary_always_active );
  $secondary = cs_make_particle( $data, 'secondary', $k_pre . '_secondary_particle', $secondary_always_active );
  return $primary . $secondary;

}

function cs_make_bg( $data, $k_pre = '' ) {
  $pre = $k_pre ? $k_pre . '_' : '';
  return cs_get_partial_view( 'bg',
    cs_extract(
      x_preview_props([
        $pre . 'bg_lower_color',
        $pre . 'bg_lower_image_repeat',
        $pre . 'bg_lower_image_size',
        $pre . 'bg_lower_image_position',
        $pre . 'bg_lower_img_alt',
        $pre . 'bg_lower_img_object_fit',
        $pre . 'bg_lower_img_object_position',
        $pre . 'bg_lower_custom_aria_hidden',
        $pre . 'bg_upper_color',
        $pre . 'bg_upper_image_repeat',
        $pre . 'bg_upper_image_size',
        $pre . 'bg_upper_image_position',
        $pre . 'bg_upper_img_alt',
        $pre . 'bg_upper_img_object_fit',
        $pre . 'bg_upper_img_object_position',
        $pre . 'bg_upper_custom_aria_hidden',
        $pre . 'bg_border_radius',
      ], $data ),
      $k_pre ? [ "{$k_pre}_bg" => 'bg'] : [ 'bg' => '' ]
    )
  );
}


function cs_anchor_text_content( $_cd, $type = 'main' ) {

  $p_atts = array( 'class' => 'x-anchor-text-primary'   );
  $s_atts = array( 'class' => 'x-anchor-text-secondary' );

  if ( $_cd['anchor_text_interaction'] != 'none' ) {
    $the_interaction              = str_replace( 'anchor-', '', $_cd['anchor_text_interaction'] );
    $p_atts['data-x-single-anim'] = $the_interaction;
    $s_atts['data-x-single-anim'] = $the_interaction;
  }

  $p_text     = ( $type == 'main' ) ? $_cd['anchor_text_primary_content']   : $_cd['anchor_interactive_content_text_primary_content'];
  $s_text     = ( $type == 'main' ) ? $_cd['anchor_text_secondary_content'] : $_cd['anchor_interactive_content_text_secondary_content'];
	$tag = ( $type == 'main' ) ? 'span' : 'div';
	$p_markup   = ( ! empty( $p_text ) ) ? '<' . $tag . ' ' . cs_atts( $p_atts ) . '>' . $p_text . '</' . $tag . '>' : '';
  $s_markup   = ( ! empty( $s_text ) ) ? '<' . $tag . ' ' . cs_atts( $s_atts ) . '>' . $s_text . '</' . $tag . '>' : '';
  $the_order  = ( $_cd['anchor_text_reverse'] == true ) ? $s_markup . $p_markup : $p_markup . $s_markup;
  $the_markup = ( ! empty( $p_markup ) || ! empty( $s_markup ) ) ? '<div class="x-anchor-text">' . $the_order . '</div>' : '';

  return $the_markup;

}

function cs_bg_layer( $_cd, $layer, $hide_lower, $hide_upper, $hide_all ) {

  $k_pre    = "bg_{$layer}_";
  $the_type = $_cd["{$k_pre}type"];


  // No Output
  // ---------

  if ( empty( $the_type ) || is_null( $the_type ) || $the_type === 'none' ) {
    return null;
  }


  // Setup
  // -----

  $hide_this_layer  = $layer === 'lower' ? $hide_lower : $hide_upper;
  $bg_layer_content = '';
  $bg_layer_atts    = array(
    'class' => 'x-bg-layer-' . $layer . '-' . $the_type,
  );

  if ( $hide_this_layer && ! $hide_all ) {
    $bg_layer_atts['aria-hidden'] = 'true';
  }


  // Parallax
  // --------

  $the_p_bool = $_cd["{$k_pre}parallax"];

  if ( $the_p_bool && $the_type !== 'none' && $the_type !== 'color' ) {
    $the_p_size = $_cd["{$k_pre}parallax_size"];
    $the_p_dir  = $_cd["{$k_pre}parallax_direction"];
    $the_p_rev  = $_cd["{$k_pre}parallax_reverse"];

    $bg_layer_data = array( 'parallaxSize' => $the_p_size, 'parallaxDir' => $the_p_dir, 'parallaxRev' => $the_p_rev );
    $bg_layer_atts = array_merge( $bg_layer_atts, cs_element_js_atts( 'bg-layer', $bg_layer_data, true ) );
  }


  // Content
  // -------

  switch ( $the_type ) {

    // Color
    // -----

    case 'color' :
      $bg_layer_atts['style'] = ' background-color: ' . cornerstone_post_process_color( $_cd["{$k_pre}color"] ) . ';';
      break;


    // Image
    // -----

    case 'image' :
      $the_image_url      = $_cd["{$k_pre}image"];
      $the_image_repeat   = $_cd["{$k_pre}image_repeat"];
      $the_image_position = $_cd["{$k_pre}image_position"];
      $the_image_size     = $_cd["{$k_pre}image_size"];
      if ($the_image_url) {
        $image_atts         = cs_apply_image_atts( [ 'src' => $the_image_url ] );
        $bg_layer_atts['style'] = ' background-image: url(' . $image_atts['src'] . '); background-repeat: ' . $the_image_repeat . '; background-position: ' . $the_image_position . '; background-size: ' . $the_image_size . ';';
      }

      break;


    // <img/>
    // ------

    case 'img' :
      $the_img_src             = $_cd["{$k_pre}img_src"];
      $the_img_alt             = $_cd["{$k_pre}img_alt"];
      $the_img_object_fit      = $_cd["{$k_pre}img_object_fit"];
      $the_img_object_position = $_cd["{$k_pre}img_object_position"];

      $bg_layer_img_atts = cs_apply_image_atts([
        'src' => $the_img_src,
        'attachment_srcset' => !empty($_cd["{$k_pre}img_attachment_srcset"]),
        'alt' => $the_img_alt,
      ]);

      unset($bg_layer_img_atts['width']);
      unset($bg_layer_img_atts['height']);

      $bg_layer_img_atts['style'] = 'object-fit: ' . $the_img_object_fit . '; object-position: ' . $the_img_object_position . ';';

      $bg_layer_content = '<img ' . cs_atts( $bg_layer_img_atts ) . '/>';
      break;


    // Video
    // -----

    case 'video' :
      $video_src = cs_expand_content( $_cd["{$k_pre}video"] );
      $video_poster = cs_expand_content( $_cd["{$k_pre}video_poster"] );
      $bg_layer_content = ( function_exists( 'cs_bg_video' ) )
        ? cs_bg_video( $video_src, $video_poster, $_cd["{$k_pre}video_loop"], [
          'pause_out_of_view' => !empty($_cd["{$k_pre}video_pause_out_of_view"])
        ])
        : '';
      break;


    // Custom
    // ------

    case 'custom' :
      $bg_layer_content = $_cd["{$k_pre}custom_content"];
      break;

  }

  return cs_tag( 'div', $bg_layer_atts, $bg_layer_content);

}


function cs_get_disallowed_ids() {
  $skip = array();

	$page_for_posts = (int) get_option( 'page_for_posts' );

	if ($page_for_posts > 0) {
		$skip[] = $page_for_posts;
	}

  if ( function_exists('wc_get_page_id') ) {
		$shop_page_id = (int) wc_get_page_id( 'shop' );
		if ( $shop_page_id > 0) {
			$skip[] = $shop_page_id;
		}
  }

  $skip = apply_filters('cs_document_list_ignore', $skip);

  return $skip;
}

function cs_make_options_from_object($obj) {
	$options = [];

	foreach ($obj as $value => $label) {
		$options[] = ['value' => $value, 'label' => $label];
	}

	return $options;
}

function cs_get_page_template_options( $post_type = 'page', $post = null) {
	$page_templates = wp_get_theme()->get_page_templates( $post, $post_type );
	ksort( $page_templates );

	return array_merge(array( array(
		'value' => 'default', 'label' => apply_filters( 'default_page_template_title',  __( 'Default Template' ), 'cornerstone' )
	)), cs_make_options_from_object( $page_templates ) );
}

function cs_get_post_status_options() {
	return cs_make_options_from_object(get_post_statuses());
}

function cs_get_post_format_options() {
	return cs_make_options_from_object(get_post_format_strings());
}

function cs_get_wp_roles_options() {

	$wp_roles = wp_roles();
	$roles = array();

	foreach ($wp_roles->roles as $key => $value) {
		$roles[] = array( 'value' => $key, 'label' => $value['name'] );
	}

	return $roles;

}

// Action Defer Helper
// =============================================================================

function cs_action_defer( $action, $function, $args = array(), $priority = 10, $array_args = false  ) {
  CS_Action_Defer::defer( $action, $function, $args, $priority, $array_args );
}



// Action Defer Class
// =============================================================================

class CS_Action_Defer {

  static $instance;

  public $memory = array();


  // Route
  // -----

  public function add_action( $action, $function, $args = array(), $priority = 10, $array_args = false ) {

    if ( ! isset( $this->memory[$action] ) ) {
      $this->memory[$action] = array();
    }

    $key = $this->generate_key( array( $action, $priority ) );

    while ( isset( $this->memory[$action][$key] ) ) {
      $key = $this->generate_key( array( $action, $priority++ ) );
    }

    $this->memory[$action][$key] = array( $function, $array_args ? $args : array( $args ) );

    add_action( $action, array( $this, $key ), $priority );

  }


  // Generate Key
  // ------------

  public function generate_key( $array ) {
    return $this->sanitize( implode( '_', $array ) );
  }


  // Call
  // ----

  public function __call( $name, $args ) {

    $action = current_filter();

    if ( ! isset( $this->memory[$action] ) || ! isset( $this->memory[$action][$name] ) ) {
			return;
    }

    $recalled = $this->memory[$action][$name];

    if ( is_callable( $recalled[0] ) ) {
      call_user_func_array( $recalled[0], is_array( $recalled[1] ) ? $recalled[1] : array() );
    }

  }


  // Sanitize
  // --------

  public function sanitize( $key ) {
    return preg_replace( '/[^a-z0-9_]/', '', strtolower( str_replace( '-', '_', $key ) ) );
  }


  // Set
  // ---

  public static function defer( $action, $function, $args = array(), $priority = 10, $array_args = false  ) {

    if ( ! isset( self::$instance ) ) {
      self::init();
    }

    return self::$instance->add_action( $action, $function, $args, $priority, $array_args );

  }

  // Init
  // ----

  public static function init() {
    if ( ! isset( self::$instance ) ) {
      self::$instance = new self();
    }
  }

}



// Element Views
// =============================================================================

function cs_render_view( $_template_file, $_view_data = array()) {

  global $posts, $post, $wp_did_header, $wp_query, $wp_rewrite, $wpdb, $wp_version, $wp, $comment, $user_ID;

  if ( is_array( $wp_query->query_vars ) ) {
    extract( $wp_query->query_vars, EXTR_SKIP );
  }

  if ( isset( $s ) ) {
    $s = esc_attr( $s );
  }

  $_extractable_data = ( is_callable( $_view_data ) ) ? call_user_func( $_view_data ) : $_view_data;

  if ( is_array( $_extractable_data ) ) {
    extract( $_extractable_data );
  }

  include( $_template_file );

}

function cs_get_view( $path, $view_data = array(), $echo = true ) {

  $file = cornerstone()->path . '/includes/views/' . $path . '.php';

  if ( ! file_exists( $file ) ) {
    return;
  }

  ob_start();
  cs_render_view( $file, $view_data );
  $output = ob_get_clean();

  if ( $echo ) {
    echo $output;
  }

  return $output;

}

function cs_get_partial_view( $name, $data = array() ) {

  $user_partial = apply_filters( 'cs_get_partial_view', null, $name, $data );
  $user_partial = apply_filters( "cs_get_partial_view_$name", $user_partial, $data );

  if (!empty($user_partial)) {
    return $user_partial;
  }

  return cs_get_view( "partials/$name", $data, false );
}

function cs_defer_partial( $name, $data = array(), $priority = 100 ) {
  cs_defer_view('cs_deferred', "partials/$name", $data, $priority);
}

function cs_defer_view( $action, $path, $data = NULL, $priority = 10 ) {
  cs_defer_html($action, cs_get_view( $path, $data, false ), $priority );
}

function cs_defer_html( $action, $content, $priority = 10 ) {
  $content = apply_filters( 'cs_defer_html', $content, $action );
  add_action( $action, function() use ($content) {
    echo $content;
  }, $priority );
}




function cs_get_filtered_post_status_choices( $post ) {

  $choices = array();

  $choices[] = array( 'value' => 'publish', 'label' => __( 'Publish', 'cornerstone' ) );

  switch ($post->post_status) {
    case 'private':
      $choices[] = array( 'value' => 'private', 'label' => __( 'Privately Published', 'cornerstone' ) );
      break;
    case 'future':
      $choices[] = array( 'value' => 'future', 'label' => __( 'Scheduled', 'cornerstone' ) );
      break;
    case 'pending':
      $choices[] = array( 'value' => 'pending', 'label' => __( 'Pending Review', 'cornerstone' ) );
      break;
    default:
      $choices[] = array( 'value' => 'draft', 'label' => __( 'Draft', 'cornerstone' ) );
      break;
  }

  return $choices;

}

function cs_get_filtered_post_parent_choices( $post ) {

  $limit = apply_filters( 'cs_locator_limit', 100 );
  $limit = apply_filters( 'cs_parent_page_choices_limit', $limit );

  $posts  = get_posts( array(
    'post_status' => 'any',
    'post_type' => $post->post_type,
    'exclude' => [ $post->ID ],
    'posts_per_page' => $limit,
  ) );

  $options = array(
    array( 'label' => __( '(no parent)', 'cornerstone' ), 'value' => '0')
  );

  foreach ( $posts as $post) {
    if ($post->post_title) {
      $options[] = array( 'label' => $post->post_title, 'value' => "$post->ID" );
    }
  }

  return $options;

}

function cs_whitelist_script_tag( $tag, $handle, $src ) {
  return cs_whitelist_script_src( $handle, $tag );
}

function cs_whitelist_script_src( $handle, $src ) {
  $safe_handles = apply_filters( 'cs_script_src_whitelist', [ 'x-google-map' ] );
  if ( in_array( $handle, $safe_handles, true ) ) {
    $src = preg_replace('/(&#038;|&amp;)/', '&', $src );
  }
  return $src;
}


/**
 * Create an image URI of a blank SVG image to be used as a placeholder
 * @return string
 */
function cs_placeholder_image( $height = '300', $width = '250', $color = '#eeeeee' ) {
  return 'data:image/svg+xml;base64,' . base64_encode( "<svg xmlns='http://www.w3.org/2000/svg' width='{$width}px' height='{$height}px' viewBox='0 0 {$width} {$height}' version='1.1'><rect fill='{$color}' x='0' y='0' width='{$width}' height='{$height}'></rect></svg>" );
}


/**
 * Detect if a post has saved Cornerstone data
 * @return bool true is Cornerstone meta exists
 */
function cs_uses_cornerstone( $post = false ) {

  if ( ! $post ) {
    return false;
  }

  $data = cs_get_serialized_post_meta( $post->ID, '_cornerstone_data', true );
  $override = get_post_meta( $post->ID, '_cornerstone_override', true );

  return $data && ! $override;

}



function cs_sanitize($value) {

  $tags = wp_kses_allowed_html( 'post' );

  $tags['iframe'] = array (
    'align'       => true,
    'frameborder' => true,
    'height'      => true,
    'width'       => true,
    'sandbox'     => true,
    'seamless'    => true,
    'scrolling'   => true,
    'srcdoc'      => true,
    'src'         => true,
    'class'       => true,
    'id'          => true,
    'style'       => true,
    'border'      => true,
    'list'        => true //YouTube embeds
  );

  if ( is_array( $value ) ) {
    $clean = [];
    foreach( $value as $key => $value) {
      $clean[ is_string($key) ? sanitize_text_field( $key) : $key ] = cs_sanitize($value);
    }
    return $clean;
  }

  if ( is_string( $value ) && ! current_user_can( 'unfiltered_html' ) ) {
    return wp_kses( $value, $tags );
  }

  return $value;

}

/**
* Element hiding
* using RuleMatching service
*/

function cs_should_hide_element($element) {
  return cornerstone('RuleMatching')->shouldHideElement( $element );
}

function cs_render_child_elements_as_array($data = []) {
  $output = [];
  $children = cs_render_child_elements($data);
  $children = explode("\n", $children);

  foreach ($children as $json) {
    // Invalid
    if (empty($json)) {
      continue;
    }

    $output[] = json_decode($json, true);
  }

  return $output;
}

// Remove internal cs keys
function cs_element_remove_private_keys(&$data = []) {
  foreach ($data as $key => $item) {
    if (strpos($key, '_') !== 0) {
      continue;
    }

    unset($data[$key]);
  }
}

/**
 * Remove omega properties for easier processing
 */
function cs_element_remove_omega(&$data = []) {
  cs_array_remove_by_prefix($data, "looper_");
  unset($data['class']);
  unset($data['id']);
  unset($data['classes']);
  unset($data['css']);
  unset($data['custom_atts']);
  unset($data['style_id']);
  unset($data['style']);
  unset($data['unique_id']);
  unset($data['mod_id']);
  unset($data['hide_bp']);
  unset($data['show_condition']);
}

function cs_array_remove_by_prefix(&$data = [], $prefix = '') {
  foreach($data as $key => $value) {
    if (strpos($key, $prefix) !== 0) {
      continue;
    }

    unset($data[$key]);
  }
}

function cs_split_to_object(&$data = [], $prefix = '') {
  $out = [];
  $prefix .= '_';

  foreach($data as $key => $value) {
    if (strpos($key, $prefix) !== 0) {
      continue;
    }

    $cleanKey = preg_replace("/^$prefix/", '', $key);
    $out[$cleanKey] = $value;

    unset($data[$key]);
  }

  return $out;
}

function cs_delete_empty(&$data = []) {
  foreach ($data as $key => $value) {
    if (!empty($value)) {
      continue;
    }

    unset($data[$key]);
  }
}

/**
 * Comma delimited number from string / input
 * to 'normal' decimal point
 *
 * @param string $str
 *
 * @return string
 */
function cs_comma_delimited_to_point($str) {
  if (!is_string($str)) {
    return $str;
  }

  return str_replace(',', '.', $str);
}

/**
 * Deletes a directory, using the WordPress Filesystem API
 *
 * @param string $path
 * @return void
 * @author Rasso Hilber <mail@rassohilber.com>
 */
function cs_delete_directory($path) {
  if ($path === "/") {
    trigger_error("Attempting to delete root /");
    return;
  }

  // make it work from the frontend, as well
  require_once ABSPATH . 'wp-admin/includes/file.php';

  // this variable will hold the selected filesystem class
  global $wp_filesystem;

  // this function selects the appropriate filesystem class
  WP_Filesystem();

  // finally, you can call the 'delete' function on the selected class,
  // which is now stored in the global '$wp_filesystem'
  $wp_filesystem->delete($path, true);
}

/**
 * Gets CS slug
 */
function cs_post_get_slug($post) {
  $slug = basename(get_permalink($post));

  $slug = apply_filters("cs_post_slug", $slug);

  return $slug;
}

/**
 * Can write using
 * @return bool
 */
function cs_can_write_to_filesystem() {

  // Attempt to get credentials without output
  ob_start();
  $creds = request_filesystem_credentials( '', '', false, false, null );
  ob_end_clean();

  // Return true/false if file system is available
  return (bool) WP_Filesystem( $creds );

}

/**
 * Convert array to { value, label }
 * object using singular value for both
 */
function cs_array_as_choices($arr) {
  $out = [];

  foreach ($arr as $val) {
    $out[] = [
      'value' => $val,
      'label' => $val,
    ];
  }

  return $out;
}

function cs_print_inline_script_tag($content) {
  echo "<script>{$content}</script>";
}


function cs_to_array($arr) {
  if (\is_array($arr)) {
    return $arr;
  }
  $arr = [$arr];
  return $arr;
}

/**
 * Some setups like HTTP_HOST, some like SERVER_NAME, it's complicated
 *
 * @api
 * @link http://stackoverflow.com/questions/2297403/http-host-vs-server-name
 *
 * @return string the HTTP_HOST or SERVER_NAME
 */
function cs_get_host()
{
  if (!empty($_SERVER['HTTP_HOST'])) {
    return $_SERVER['HTTP_HOST'];
  }
  if (!empty($_SERVER['SERVER_NAME'])) {
    return $_SERVER['SERVER_NAME'];
  }
  return '';
}

/**
 * Groups an object of arrays in a keyed array by the key passed
 *
 * @param array $array
 * @param string $key
 * @param string $defaultValue
 *
 * @return array
 */
function cs_array_group_by($array, $key, $defaultVal = 'default') {
  $return = [];

  foreach($array as $val) {
    $valKey = !isset($val[$key])
      ? $defaultVal
      : $val[$key];

    if (!isset($return[$valKey])) {
      $return[$valKey] = [];
    }

    $return[$valKey][] = $val;
  }

  return $return;
}
