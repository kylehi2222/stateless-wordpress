<?php

namespace Themeco\Cornerstone\Elements;

use Themeco\Cornerstone\Services\RuleMatching;
use Themeco\Cornerstone\Util\Parameter;
use Themeco\Cornerstone\Util\StripAnchors;

class Renderer {

  protected $linkStack = [];
  protected $componentStack = [];
  protected $hidden = [];

  protected $stripAnchors;
  protected $ruleMatching;
  protected $elementService;
  protected $previewRenderer;

  public function __construct(StripAnchors $stripAnchors, RuleMatching $ruleMatching) {
    $this->stripAnchors = $stripAnchors;
    $this->ruleMatching = $ruleMatching;
  }

  public function setup($service) {
    $this->elementService = $service;
    $this->stripAnchors->setup();
    add_action('x_render_children', [ $this, 'echoRenderElements' ], 10, 2 );
    return $this;
  }

  public function echoRenderElements($elements, $parent = null) {
    echo $this->renderElements( $elements, $parent );
  }

  public function setPreviewRenderer( $r ) {
    $this->previewRenderer = $r;
  }

  public function renderElements($elements, $parent = null) {

    $result = '';

    if ( is_array( $elements ) && count( $elements ) > 0 ) {

      $teardownLink = $this->stripAnchors->maybeAddLink( $parent, $this->elementService );

      if (isset($parent['_builder_outlet']) && ! apply_filters( 'cs_render_looper_is_virtual', false )) {

        echo '%%{children(\'' . $parent['_builder_outlet'] . '\')}%%';

        $had_filter = has_filter('cs_is_element_preview', '__return_false' );

        if ($had_filter) {
          remove_filter('cs_is_element_preview', '__return_false', 1000 );
        }

        foreach ( $elements as $element ) {
          $this->previewRenderer->render_element( $element );
        }

        if ($had_filter) {
          add_filter( 'cs_is_element_preview', '__return_false', 1000 );
        }

      } else {
        add_filter('cs_is_element_preview', '__return_false', 1000 );

        foreach ( $elements as $element ) {
          $result .= $this->renderElement( $element );
        }
        remove_filter('cs_is_element_preview', '__return_false', 1000 );
      }

      $teardownLink();

    } else {
      if ($parent) {
        $parent_definition = $this->elementService->get_element($parent['_type']);
        if (isset($parent_definition->def['options']['fallback_content'] ) ) {
          $result .= $parent_definition->def['options']['fallback_content'];
        }
      }
    }

    return $result;

  }

  public function renderElement( $data ) {

    // If we have a component, we are going to render that instead of the given element
    if ( isset( $data['_virtual_root'] ) ) {
      $toRender = $data['_virtual_root'];

      if ( $data['_virtual_direct'] ) {
        $toRender['_modules'] = $data['_modules'];
        if ( isset( $data['_builder_outlet'])) {
          $toRender['_builder_outlet'] = $data['_builder_outlet'];
        }
        return $this->renderElement($toRender);
      }

      $map = [];
      foreach ($data['_modules'] as $child) {
        if ( isset( $data['_virtual_map'][$child['_id']] )) {
          $item = $data['_virtual_map'][$child['_id']];
          $map[$item['id']] = [$child, $item['unwrap']];
        }
      }
      $this->componentStack[] = $map;
      $rendered = $this->renderElement($toRender);
      array_pop($this->componentStack);

      return $rendered;

    }

    $stack = end($this->componentStack);

    if ($stack && isset( $stack[$data['_id']])) {

      list($egress) = $stack[$data['_id']];
      // var_dump($customize);
      if ( $egress['_type'] === 'slot' ) {
        $data['_modules'] = $egress['_modules'];
      }

    }

    $frame = cornerstone('Vm')->runtime()->newFrame();

    // merge _p_json initial values with _p_data to get _parameters
    // If this element is a component instance it will already have _p_json from the component available
    Parameter::defineParametersForRender($data['_parameters'], $frame, $data['style_id']);

    $result = $this->renderWithLoopers( $data );
    $frame->dispose();

    return $result;

  }

  public function render_one( $data ) {

    // Check if classes is setup
    if (!is_array($data['classes'])) {
      $data['classes'] = [];
    }

    // Add hide BP classes
    $data['classes'] = array_merge(
      $data['classes'],
      cs_hide_breakpoint_classes($data)
    );

    // Builder attributes
    if ( !empty( $data['_builder_atts'] ) ) {

      if ( apply_filters( 'cs_render_looper_is_virtual', false ) ) {
        unset($data['_builder_atts']['data-cs-dropzone']);
      }

      // Custom atts processing
      $customAtts = empty($data['custom_atts'])
        ? []
        : $data['custom_atts'];

      $customAtts = is_string( $customAtts )
        ? json_decode( $customAtts, true )
        : $customAtts;

      $source = is_array( $data['_builder_atts'] ) ? $data['_builder_atts'] : json_decode( $data['_builder_atts'], true );
      $source = $source ? $source : [];

      $data['custom_atts'] = json_encode(array_merge( $customAtts, $source));
    }

    // Begin utilizing element scoped Dynamic Content
    if ( ! isset( $data['_pre_dc'] ) ) {
      $data['_pre_dc'] = [];
    }

    foreach( $data['_dc_keys'] as $key ) {
      $data['_pre_dc'] = $data[$key];
      $data[$key] = cs_dynamic_content( $data[$key] );
    }

    // Update Unique ID from current looper state
    $indexes = array_map(function( $consumer ) {
      return $consumer->provider()->get_index();
    }, CS()->component('Looper_Manager')->get_consumers() );

    $index_id = implode('-', $indexes);
    if ($index_id !== '') $data['unique_id'] .= "-$index_id";

    // Grab definition class
    $definition = $this->elementService->get_element( $data['_type'] );

    // Pre render filters
    $data = apply_filters("cs_element_pre_render", $data, $definition);

    // Render fn and setup
    $renderFn = $definition->getRenderFn();
    ob_start();

    $data = cornerstone('Tss')->applyTssToElement( $data );

    // To match Pro5 behaviour
    if (!isset($data['class']) && !empty($data['classes'])) {
      $data['class'] = implode(' ', $data['classes']);
    }


    $result = $this->stripAnchors->clean( $renderFn( $data ) );
    $buffer = ob_get_clean();

    // End utilizing element scoped Dynamic Content

    $result .= apply_filters('cs_render_handle_extraneous', $buffer);


    return $result;
  }

  public function renderWithLoopers( $data ) {

    $in_preview = apply_filters( 'cs_is_element_preview', false );

    $looper = CS()->component('Looper_Manager');


    // Maybe begin a looper. This is where loopers providers are initialized
    $loop = $looper->maybe_start_element( $data );
    $is_looper_consumer = $loop === 'consumer';
    $is_looper_provider = $loop === 'provider';
    $buffer = '';

    // Render potentially repeating element via looper consumer
    if ( $is_looper_consumer ) {

      $did_iterate = $looper->iterate();
      $currently_is_initial = apply_filters( 'cs_render_looper_is_virtual', false );

      // Try to output the first element in a way that it can be interacted with the live preview
      if ( $did_iterate || $in_preview ) { // always render at least one in the preview

        $is_hidden = $this->ruleMatching->shouldHideElement( $data );
        $should_render_final = true;


        // If the first element is hidden, keep trying until we can output one for the preview
        while ( $is_hidden && $looper->iterate() ) {
          $is_hidden = $this->ruleMatching->shouldHideElement( $data );
          if ( ! $is_hidden ) {
            $should_render_final = false;
            $buffer .= $this->render_one( $data );
          }
        }

        if ( ! $is_hidden && $should_render_final) {
          $buffer .= $this->render_one( $data );
        }

      }

      // Remaining iterations will be virtualized in the live preview

      if ( ! $currently_is_initial ) {
        add_filter( 'cs_render_looper_is_virtual', '__return_true' );
      }

      if ($did_iterate) {

        do_action('cs_preview_the_content_begin');

        while( $looper->iterate() ) {

          if ( ! $this->ruleMatching->shouldHideElement( $data ) ) {
            $buffer .= $this->render_one( $data );
          }
        }

        do_action('cs_preview_the_content_end');

      }

      if ( ! $currently_is_initial ) {
        remove_filter( 'cs_render_looper_is_virtual', '__return_true' );
      }

      $looper->end_element();

      if ( !$buffer) {
        $this->hidden[] = $data['_id'];
      }

      return $buffer;

    }


    // Maybe hide the element based on the show_condition
    if ( $this->ruleMatching->shouldHideElement( $data ) ) {
      if ( $is_looper_provider ) {
        $looper->end_element();
      }

      $is_virtual = apply_filters( 'cs_render_looper_is_virtual', false );

      // Looper handles hiding it's own way
      // This is for normal hidden elements
      if (!$is_virtual) {
        $this->hidden[] = $data['_id'];
      }

      return '';
    }

    // Normal render when element is not a looper consumer
    $buffer = $this->render_one( $data );

    if ($is_looper_provider) {
      $looper->end_element();
    }

    return $buffer;

  }

  public function isHidden( $id ) {
    return in_array( $id, $this->hidden, true );
  }

}
