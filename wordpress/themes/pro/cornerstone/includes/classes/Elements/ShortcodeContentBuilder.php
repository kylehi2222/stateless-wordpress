<?php

namespace Themeco\Cornerstone\Elements;

class ShortcodeContentBuilder {

  public $depths = [];

  public function normalize_regions( $elements ) {

    $elements = isset($elements['data']) ? $elements['data'] : [];
    if (! is_array( $elements ) ) {
      $elements = [];
    }

    return [
      [
        '_type'    => 'region',
        '_region'  => 'content',
        '_modules' => $elements
      ]
    ];
  }

  public function build_output( $elements, $settings ) {

    // Generate shortcodes
    $buffer = '';
    $sanitized = array();

    $regions = cornerstone()->resolve(IdPopulater::class)->populate( $this->normalize_regions(['data' => $elements]) )->result();
    $elements = $regions[0]['_modules'];

    foreach ( $elements as $element ) {
      $output = $this->build_element_output( $element );
      if ( is_wp_error( $output ) ) {
        return $output;
      }
      $buffer .= $output['content'];
      $sanitized[] = $output['data'];
    }

    return array(
      'content' => $buffer,
      'data' => $sanitized
    );
  }



  public function build_element_output( $element, $parent = null ) {

    if ( ! isset( $element['_type'] ) ) {
      return new \WP_Error( 'cs-content', 'Element _type not set: ' . maybe_serialize( $element ) );
    }

    if ( 0 === strpos( $element['_type'], 'classic:' ) ) {
      return $this->build_classic_element_output( $element, $parent );
    }

    //
    // Build V2 element
    //

    $definition = cornerstone('Elements')->get_element( $element['_type'] );

    $buffer = '';
    $atts = array();

    if ( isset( $element['_modules'] ) ) {
      $sanitized = array();
      $this->inc_depth( $element['_type'] );

      if ( $definition->render_children() ) {
        $children = array();
        foreach ( $element['_modules'] as $child ) {
          $children[] = $child['_id'];
          $sanitized[] = $child;
        }
        $atts['_modules'] = implode(',', $children);
      } else {

        foreach ( $element['_modules'] as $child ) {
          $output = $this->build_element_output( $child, $element );
          if ( is_wp_error( $output ) ) {
            return $output;
          }
          $buffer .= $output['content'];
          $sanitized[] = $output['data'];
        }
      }

      $this->dec_depth( $element['_type'] );

      $element['_modules'] = $sanitized;
    }

    $content = '';
    if ( ! isset( $element['_active'] ) || $element['_active'] ) {
      $content = $definition->save( $element, $buffer, $atts, $this->get_depth( $element['_type'] ) );
    }

    unset($element['_id']);
    unset($element['_region']);

    return array(
      'content' => $content,
      'data' => $element
    );

  }

  public function inc_depth( $type ) {
    if ( !isset( $this->depths[$type] ) ) {
      $this->depths[$type] = 1;
    }
    $this->depths[$type]++;
  }

  public function dec_depth( $type ) {
    if ( !isset( $this->depths[$type] ) ) {
      $this->depths[$type] = 1;
    }
    $this->depths[$type]--;
  }

  public function get_depth( $type ) {
    return isset( $this->depths[$type] ) ? $this->depths[$type] : 1;
  }

  public function build_classic_element_output( $element, $parent = null ) {

    $element['_type'] = str_replace('classic:', '', $element['_type'] );
    $definition = CS()->component( 'Classic_Element_Manager' )->get( $element['_type'] );

    // No classic elements
    // Does this to bypass bad saves
    if (empty($definition)) {
      if (WP_DEBUG) {
        trigger_error("Classic Elements is not installed, but you are trying to reference them " . $element['_type'], E_USER_WARNING);
      }

      return [
        'content' => '',
        'data' => [],
      ];
    }

    $element = $definition->sanitize( $element );

    if ( 'mk1' === $definition->version() ) {
      return CS()->component( 'Legacy_Renderer' )->save_element( $element );
    }

    $buffer = '';

    if ( isset( $element['_modules'] ) ) {
      $sanitized = array();
      foreach ( $element['_modules'] as $child ) {
        $output = $this->build_element_output( $child, $definition->compose( $element ) );
        if ( is_wp_error( $output ) ) {
          return $output;
        }
        $buffer .= $output['content'];
        $sanitized[] = $output['data'];
      }
      $element['_modules'] = $sanitized;
    }

    $content = '';

    if ( ! isset( $element['_active']) || $element['_active'] ) {
      if ( isset($element['_modules'] ) ) {
        $element['elements'] = $element['_modules'];
      }
      $content = $definition->build_shortcode( $element, $buffer, $parent );

      // <!--nextpage--> support for classic sections
      if ( 'section' === $element['_type'] ) {
        // Move all <!--nextpage--> directives to outside their section.
        $content = preg_replace( '#(?:<!--nextpage-->.*?)(\[\/cs_section\])#', '$0<!--nextpage-->', $content );

        //Strip all <!--nextpage--> directives still within sections
        $content = preg_replace( '#(?<!\[\/cs_section\])<!--nextpage-->#', '', $content );

        $content = str_replace( '<!--more-->', '', $content );
      }

      unset($element['elements']);
    }

    $element['_type'] = 'classic:' . $element['_type'];
    unset($element['_id']);
    unset($element['_region']);

    return array(
      'content' => $content,
      'data' => $element
    );

  }

}
