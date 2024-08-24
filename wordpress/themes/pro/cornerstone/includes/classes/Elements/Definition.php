<?php

namespace Themeco\Cornerstone\Elements;

use Themeco\Cornerstone\Tss\ContainerConfig;

class Definition {

  protected $type;
  public $def = [];
  protected $style = null;

  protected $cached_designations;
  protected $cached_designations_by_key = [];
  protected $aggregate_values = [];
  protected $tssConfig;

  public function __construct(ContainerConfig $tssConfig) {
    $this->tssConfig = $tssConfig;
  }

  public function setup( $type, $definition ) {
    $this->type = $type;
    $this->update( $definition );
    return $this;
  }

  public function update( $update ) {

    $defaults = array(
      'title'               => '',
      'values'              => array(),
      'migrations'          => array(),
      'includes'            => array(),
      'style'               => null, // callback to define style template
      'tss'                 => null, // callback to define tss config template
      'builder'             => null, // callback to populate builder data (not called on front end)
      'children'            => null, // can be a hook used to manage children (e.g. x_section)
      'options'             => array(),
      'icon'                => null,
      'active'              => true,
      'group'               => null,
      'control_nav'         => null,
      'controls'            => null,
      'render'              => null,
      'preprocess_css_data' => null,
      '_upgrade_data' => array()
    );

    if ( isset( $update['components'] ) && ! isset( $update['includes'] ) ) { // includes was formerly called components
      $update['includes'] = $update['components'];
    }

    $this->def = array_merge( $defaults, $this->def, array_intersect_key( $update, $defaults ) );

    $this->def['includes'] = $this->normalize_includes( $this->def['includes'] );

    if (isset( $this->def['tss'] )) {
      $this->tssConfig->setup(is_callable( $this->def['tss'] ) ? call_user_func( $this->def['tss'] ) : $this->def['tss'], true);
    }

    // No builder function support
    // But has control_nav and controls
    if (
      empty($this->def['builder'])
      && !empty($this->def['control_nav'])
      && !empty($this->def['controls'])
    ) {
      $this->def['builder'] = function() {
        return cs_compose_controls([
          'control_nav' => $this->def['control_nav'],
          'controls' => $this->def['controls'],
        ]);
      };
    }

    // Add custom module for TSS compiliation
    if (is_callable($this->def['style']) && !$this->is_classic()) {
      $this->tssConfig->addCustomModule([
        'module' => 'usercustom',
        'name' => 'usercustom',
        'args' => [],
        'nested' => false,
        'remap' => null,
        'conditions' => [],
      ]);
    }
  }

  public function normalize_includes( $input ) {
    $normalized = [];

    foreach ($input as $include) {
      $item = is_string( $include ) ? [ 'type' => $include ] : $include;
      if ( isset( $item['type'] ) ) {
        $item = cs_define_defaults($item,[
          'type'  => $item['type'],
          'key_prefix' => str_replace('-', '_', $item['type']),
          'values' => [],
          'migrations' => []
        ]);
        $key = $item['type'] . ':' . $item['key_prefix'];
        if ( isset( $normalized[$key]) ) {
          trigger_error( 'An element can not register the same include multiple times unless a different key_prefix is used (Element: ' . $this->type . ')', E_USER_WARNING );
        }
        $normalized[$key] = $item;
      }

    }

    return array_values( $normalized );
  }

  public function get_group() {
    return $this->def['group'];
  }

  public function get_migrations() {
    return $this->def['migrations'];
  }

  public function get_aggregated_values() {
    if ( empty( $this->aggregate_values ) ) {

      $values = [];
      $manager = cornerstone('Elements');

      foreach( $this->def['includes'] as $include ) {

        $include_definition = $manager->get_include( $include['type'] );

        foreach ($include_definition['values'] as $include_key => $include_value) {
          $values[$include['key_prefix'] . '_' . $include_key] = $include_value;
        }

        foreach ( $include['values'] as $key => $override_value) {
          if ( isset( $values[$key]) ) {
            $values[$key][0] = $override_value;
          }
        }

      }

      $this->aggregate_values = array_merge( $this->def['values'], $values ); // merge aggregated includes with the base
    }
    return $this->aggregate_values;

  }

  public function get_defaults() {
    $defaults = array();

    $values = $this->get_aggregated_values();
    foreach ($values as $key => $value) {
      list( $default ) = $value;
      $defaults[$key] = $default;
    }

    return $defaults;
  }

  public function apply_defaults( $data ) {

    $values = $this->get_aggregated_values();

    foreach ($values as $key => $value) {
      list($default, $designation) = $value;
      if ( ! isset( $data[$key] ) ) {
        $data[$key] = $default;
      }
    }

    return $data;

  }

  public function get_designations() {

    if ( ! isset( $this->cached_designations ) ) {
      $designations = array();

      $values = $this->get_aggregated_values();
      foreach ($values as $key => $value) {
        list( $default, $designation ) = $value;
        $designations[$key] = $designation;
      }

      $this->cached_designations = $designations;

    }

    return $this->cached_designations;

  }

  public function get_designated_keys() {

    $args = func_get_args();

    $cacheKey = implode(',',$args);

    if ( ! isset( $this->cached_designations_by_key[ $cacheKey ] ) ) {
      $designations = $this->get_designations();

      $keys = array();

      foreach ($args as $group) {

        $top_level = false === strpos( $group, ':' );
        $wild      = 0 === strpos( $group, '*' );

        foreach ($designations as $key => $value) {

          $check = $value;
          $parts = explode(':', $value);
          $primary = array_shift($parts);

          if ( $top_level ) {
            $check = $primary;
          }

          if ( $wild ) {
            $check = str_replace($primary,'*', $check);
          }

          if ( $check === $group ) {
            $keys[] = $key;
          }

        }
      }

      $this->cached_designations_by_key[ $cacheKey ] = array_unique( $keys );
    }


    return $this->cached_designations_by_key[ $cacheKey ];
  }

  public function get_tss_config() {
    return $this->tssConfig;
  }

  public function get_style_template() {

    if ( is_null( $this->style ) ) {

      if ( ! isset( $this->def['style'] ) ) {
        return '';
      }

      $this->style = trim( is_callable( $this->def['style'] ) ? call_user_func( $this->def['style'], $this->type ) : $this->def['style'] );

    }

    return $this->style;
  }

  public function preprocess_tss( $data ) {
    if ( is_callable( $this->def['preprocess_css_data'] ) ) {
      $data = call_user_func( $this->def['preprocess_css_data'], $data );
    }

    // For usage in Tss as custom user element
    if (is_callable($this->def['style']) && !$this->is_classic()) {
      $data['tss_template'] = $this->get_style_template();
    }

    return $data;
  }

  // Redundant. Could be removed if all style template processing was done client side in the builder.
  public function preprocess_style( $data ) {

    $data = $this->apply_defaults($data);

    $unique_id = $data['_id'];

    if ( isset( $data['_p'] ) ) {
      $unique_id = $data['_p'] . '-' . $unique_id;
    }

    $data['_el'] = 'e' . $unique_id;

    if ( is_callable( $this->def['preprocess_css_data'] ) ) {
      $data = call_user_func( $this->def['preprocess_css_data'], $data );
    }

    $style_keys = $this->get_designations();

    $post_process_keys = array();
    foreach ($style_keys as $data_key => $style_key) {

      $pos = strpos($style_key, ':' );

      if ( false === $pos ) {
        continue;
      }

      $post_process_keys[$data_key] = substr($style_key, $pos + 1);

    }

    if ( ! empty( $post_process_keys ) ) {
      foreach ($data as $key => $value) {
        if ( isset($post_process_keys[$key]) && $value && is_scalar($value) ) {
          $data[$key] = '%%post ' . $post_process_keys[$key] . '%%' . $value .'%%/post%%';
        }
      }
    }

    return $data;

  }

  public function get_title() {
    return $this->def['title'];
  }

  public function shadow_parent() {
    return ( isset( $this->def['options']['shadow_parent'] ) && $this->def['options']['shadow_parent'] );
  }

  public function is_dropzone() {
    return ( isset( $this->def['options']['dropzone'] ) &&  isset( $this->def['options']['dropzone']['enabled'] ) && $this->def['options']['dropzone']['enabled'] );
  }

  public function is_classic() {
    return 0 === strpos($this->type, 'classic:');
  }

  public function in_library() {
    $is_classic_child = ( isset( $this->def['options']['classic'] ) && isset( $this->def['options']['classic']['child'] ) && $this->def['options']['classic']['child'] );
    return ( !isset( $this->def['options']['library'] ) || false !== $this->def['options']['library'] ) && ! $is_classic_child;
  }

  public function render_children() {
    return ( isset( $this->def['options']['render_children'] ) && $this->def['options']['render_children'] );
  }

  public function get_type() {
    return $this->type;
  }

  public function get_children_hook() {
    return $this->def['children'];
  }

  public function serialize() {

    $data = array(
      'id'         => $this->type,
      'title'      => $this->def['title'],
      'options'    => $this->def['options'],
      'active'     => $this->def['active'],
      'group'      => $this->def['group'],
      'version'    => count( $this->def['migrations'] ),

      // PHP side we use get_aggregated_values but client they are broken out
      // this helps send less duplicate default values over HTTP
      'values'   => $this->def['values'],
      'includes' => $this->def['includes'],
    );

    if ( is_string( $this->def['icon'] ) ) {
      $data['icon'] = $this->def['icon'];
    }

    return $data;
  }

  public function get_inspector() {

    $data = is_callable( $this->def['builder'] ) ? call_user_func( $this->def['builder'], $this->type ) : [];

    $inspector = [
      'controls'     => isset( $data['controls'] ) ? $data['controls']  : [],
      'control_nav'  => isset( $data['control_nav'] ) ? $data['control_nav'] : [],
    ];

    return $inspector;
  }

  public function save( $data, $content, $atts = array(), $depth = 0 ) {

    $type = str_replace('-', '_', $data['_type'] );
    $tag = "cs_element_$type";

    // WordPress does not support nesting shortcodes of the same type
    // We append a number to indicate an element's depth and handle each shortcode separately
    if ( $depth > 1 && $this->supports_children() ) {
      $tag .= "_$depth";
    }

    $atts = array_merge( $atts, array( '_id' => $data['_id'] ) );
    $atts = cs_shortcode_atts( $atts );
    $shortcode = "[$tag $atts]";

    if ( ! $content && isset( $this->def['options']['fallback_content'] ) ) {
      $content = $this->def['options']['fallback_content'];
    }

    if ( $content || $this->supports_children() ) {
      $shortcode .= $content . "[/$tag]";
    }

    $shortcode = apply_filters("cs_save_element_output_$type", $shortcode, $data, $content );
    $shortcode .= $this->generate_seo_data( $data );

    return apply_filters('cs_save_element_output', $shortcode, $data, $content );
  }

  public function generate_seo_data( $data ) {

    $buffer = '';
    $keys = $this->get_designated_keys('*:seo' );

    foreach( $keys as $key ) {
      if ( isset( $data[$key] ) && $data[$key] && is_string($data[$key]) ) {
        $buffer .= $data[$key] . '\\n\\n';
      }
    }

    return $this->format_seo_shortcode( $buffer );
  }

  protected function format_seo_shortcode( $content ) {

    $images = array();

    if ( strpos($content, 'img' ) !== false ) {
      preg_match_all('/<img .*>/U', $content, $images);
    }

    //Optional change, but should clean all the spaces
    $cleaned = trim(strip_tags($content));
    $result = count( $images ) > 0 ? $cleaned . implode('', $images[0]) : $cleaned;
    return $result ? '[cs_content_seo]' . $result . '[/cs_content_seo]' : $result;

  }

  public function supports_children() {
    return isset( $this->def['options']['valid_children'] );
  }

  public function will_render_link( $atts ) {
    if ( ! isset( $this->def['options']['link_prefix'] ) ) {
      return false;
    }

    return $atts[$this->def['options']['link_prefix'] . '_tag'] === 'a' && ! empty( $atts[ $this->def['options']['link_prefix'] . '_href'] );
  }

  public function has_offscreen_dropzone() {
    return isset( $this->def['options']['dropzone'] ) && isset( $this->def['options']['dropzone']['offscreen'] ) && $this->def['options']['dropzone']['offscreen'];
  }

  public function getRenderFn() {
    return isset( $this->def['render'] ) && is_callable( $this->def['render'] ) ? $this->def['render'] : function( $data ) { return ''; };
  }

  /**
   * To support older plugins this used to be tied to the class
   * now just a proxy for cs_should_hide_element
   *
   * @return bool
   */
  public function should_hide($element) {
    return cs_should_hide_element($element);
  }
}
