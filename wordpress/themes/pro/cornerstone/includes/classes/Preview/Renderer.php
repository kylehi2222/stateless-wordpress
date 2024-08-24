<?php

namespace Themeco\Cornerstone\Preview;

class Renderer {

  protected $elements = [];
  protected $portals = [];
  protected $extraneous = [];
  protected $tss = [];
  protected $inline_styling_handles = [];
  protected $is_element_preview = false;
  protected $the_content_stack = [];
  protected $target = [];
  public $resolver;
  public $dynamicContent;
  public $elementService;
  public $tssService;

  public function render( $data, $is_initial = false ) {
    if ( ! isset( $data['rootElement'] ) ) {
      throw new \Exception('render elements not specified');
    }

    if ( ! isset( $data['config'] ) || ! isset( $data['config']['docType'] ) || ! isset( $data['config']['documentId'] ) ) {
      throw new \Exception('invalid config');
    }

    if (is_singular() && have_posts()) {
      the_post();
    }

    $enqueue_extractor = CS()->component( 'Enqueue_Extractor' );
    if (!$is_initial) {
      $enqueue_extractor->start();
    }

    $this->resolver = cornerstone('Resolver');
    $this->dynamicContent = cornerstone('DynamicContent');
    $this->elementService = cornerstone('Elements');
    $this->tssService = cornerstone('Tss');
    $this->elementService->renderer()->setPreviewRenderer($this);

    add_filter( 'cs_element_update_build_shortcode_content', array( $this, 'expand_dc_in_preview' ) );

    add_action( 'cs_styling_add_styles', array( $this, 'track_inline_styling_handles' ) );

    add_filter( 'cs_defer_html', [ $this, 'capture_deferred'], 99999, 2);
    add_filter( 'cs_render_handle_extraneous', [ $this, 'capture_extraneous' ] );
    add_filter( 'cs_is_preview_render', '__return_true' );
    add_filter( 'cs_is_element_preview', '__return_true', 0 );

    do_action( 'cs_element_rendering' );

    $flags = array_merge(
      [
        'elementConditions'  => 'allow',
        'forceScrollEffects' => 'none'
      ],
      isset( $data['flags'] ) ? $data['flags'] : []
    );

    if ( $flags['elementConditions'] === 'ignore' ) {
      add_filter( 'cs_preview_disable_element_conditions', '__return_true' );
    }

    if ( $flags['forceScrollEffects'] !== 'none' ) {
      add_filter( 'cs_preview_force_scroll_effects', function($force = '') use ($flags) {
        return $flags['forceScrollEffects'];
      } );
    }


    $elementData = $this->resolver->makeElementData(
      $data['config']['documentId'],
      [ $data['rootElement'] ]
    )->options([
      'preview' => true
    ]);


    // echo '<pre>';
    // var_dump($elementData);
    // echo '</pre>';

    list($decorated, $types) = $elementData->decoratedWithTypes();

    // echo '<pre>';
    // var_dump($decorated);
    // echo '</pre>';

    foreach ($types as $type) {
      $this->tssService->registerElementType( $type );
    }

    $this->render_element( $decorated[0] );

    if (!$is_initial) {
      $enqueue_extractor->extract();
    } else {
      // Add in any styles that were dynamically added from element rendering
      $styles = $enqueue_extractor->extract_styles();
      $this->ensureStylesLoaded($styles);

      // Add in any scripts that were dynamically added from element rendering
      $scripts = $enqueue_extractor->extract_scripts();
      $this->ensureScriptsLoaded($scripts);
    }

    $result = array_merge(
      $this->finalize_elements( $flags ),
      [
        'documentId' => $data['config']['documentId']
      ],
      $is_initial ? [] : [
        'scripts'  => $enqueue_extractor->get_scripts(),
        'styles'   => $enqueue_extractor->get_styles()
      ], [
        'components' => $elementData->getReferencedComponents()
      ]
    );

    add_filter( 'cs_is_preview_render', '__return_false' );

    if (defined('CS_APP_DEV_TOOLS') && CS_APP_DEV_TOOLS && isset( $_REQUEST['cs-render-test'])) {

          // echo '<pre>';
    // var_dump($data);
    // echo '</pre>';

      // // list ($type, $hash, $original, $na1, $na2, $na3, $style) = $result["elements"]["cs2"];
      // // echo cs_tag('style', $style);
      // // echo $result['markup'][$hash];

      // // list ($type, $hash, $original, $na1, $na2, $na3, $style) = $result["elements"]["cs6"];
      // // echo cs_tag('style', $style);
      // // echo $result['markup'][$hash];
      // $debug = [];
      // foreach ($result["elements"] as $elId => $elData) {
      //   $m =  esc_html($result['markup'][$elData[1]]);
      //   $elData[$elId][] = $m;
      //   $debug[$elId] = $elData[0];
      // }
      // echo '<pre>';
      // var_dump($debug);
      // echo '</pre>';
    }

    return $result;
  }

  public function finalize_elements( $flags ) {

    $elements = [];
    $markup = [];

    foreach ($this->elements as $id => $element) {

      $hidden = false;

      list( $type, $original_type, $content, $inline_css, $context_ids ) = $element;

      $tss_shim = isset($this->tss[$id]) ? $this->tss[$id] : '';

      if ($this->elementService->renderer()->isHidden( $id ) ) {
        $content = '';
        $hidden = true;
      }

      if ( isset($this->portals[$id]) ) {
        $content .= $this->portals[$id];
      }
      $extraneous = isset($this->extraneous[$id]) ? $this->extraneous[$id] : '';

      $hash = md5($type . $content . $extraneous . json_encode( $flags ));

      $elements[$id] = [$type, $hash, $original_type, $inline_css, $hidden, $context_ids, $tss_shim, $extraneous]; // app-render.js
      $markup[$hash] = $content;

    }

    return [
      'elements' => $elements,
      'markup'   => $markup
    ];
  }

  public function render_element( $data, $parent = null ) {

    if ( in_array( $data['_type'], ['region', 'root'] ) ) {
      foreach( $data['_modules'] as $element ) {
        $this->render_element( $element, $data );
      }
      return;
    }

    $response = '';
    $this->inline_styling_handles = [];
    array_push($this->target, $data['_id']);

    $this->tss[$data['_id']] = $this->tssService->processPreviewElement( $data );

    $renderer = $this->elementService->renderer();

    $response = cs_expand_content(
      $renderer->renderElement( $data, $parent )
    );

    if ($renderer->isHidden( $data['_id'] ) ) {
      foreach( $data['_modules'] as $element ) {
        $this->render_element( $element, $data );
      }
    }

    $this->elements[$data['_id']] = [
      $data['_type'],
      isset( $data['_original_type'] ) ? $data['_original_type'] : "",
      $response,
      $this->get_inline_css(),
      $this->get_context_ids()
    ];

    array_pop($this->target);

  }

  public function get_inline_css() {

    $inline_css = '';
    $styling = cornerstone('Styling');

    add_filter('cs_css_post_processing', '__return_false');

    foreach ($this->inline_styling_handles as $handle) {
      $inline_css .= $styling->processStyle( $handle ) . ' ';
    }

    remove_filter('cs_css_post_processing', '__return_false');

    return $inline_css;

  }

  public function get_context_ids() {

    $post = $this->dynamicContent->get_contextual_post();
    $term = $this->dynamicContent->get_contextual_term();
    $user = $this->dynamicContent->get_contextual_user();

    return [
      is_a( $post, 'WP_Post') ? $post->ID : null,
      is_a( $term, 'WP_Term') ? $term->term_id : null,
      is_a( $user, 'WP_User') ? $user->ID : null
    ];
  }

  public function is_element_preview() {
    return $this->is_element_preview;
  }

  public function preview_container_output( $children, $parent ) {

    echo '%%{children(\'' . $parent['_id'] . '\')}%%';
    $teardownLink = $this->elementService->renderer()->inLink( $parent );

    foreach( $children as $element ) {
      $this->render_element( $element, $parent );
    }

    $teardownLink();
  }


  public function track_inline_styling_handles( $handle ) {
    $this->inline_styling_handles[] = $handle;
  }

  public function capture_portal( $content, $action ) {

    $id = end($this->target);
    if (!isset($this->portals[$id])) {
      $this->portals[$id] = '';
    }

    $this->portals[$id] .= "<div tco-html-portal=\"$action\">$content</div>";

  }

  public function capture_deferred($content, $action) {
    $this->capture_portal( $content, $action);
    return $content;
  }

  // Capture anything output during a render that isn't a top level div
  public function capture_extraneous( $extraneous ) {

    if ( $extraneous ) {
      $id = end($this->target);
      if (!isset($this->extraneous[$id])) {
        $this->extraneous[$id] = '';
      }

      $this->extraneous[$id] .= $extraneous;
    }


    return ''; // don't include in the actual render
  }

  public function expand_dc_in_preview( $content ) {
    return apply_filters( 'cs_is_preview', false ) ? cs_dynamic_content( $content ) : $content;
  }

  /**
   * Add in stylesheets that were added in dynamically through element rendering
   * The preview render happens after a needed event
   */
  private function ensureStylesLoaded($styles) {
    $wp_styles = wp_styles();

    foreach ($styles as $style) {
      // Already added
      if (in_array($style, $wp_styles->done)) {
        continue;
      }

      $registry = $wp_styles->registered[$style];

      if (empty($registry)) {
        continue;
      }

      echo "<link rel='stylesheet' id='{$style}' href='{$registry->src}'>";
    }
  }

  /**
   * Add in scripts that were added in dynamically through element rendering
   * The preview render happens after a needed event
   */
  private function ensureScriptsLoaded($scripts) {
    $wp_scripts = wp_scripts();

    foreach ($scripts as $script) {
      // Already added
      if (in_array($script, $wp_scripts->done)) {
        continue;
      }

      $registry = $wp_scripts->registered[$script];

      if (empty($registry)) {
        continue;
      }

      echo "<script type='text/javascript' id='{$script}' src='{$registry->src}'></script>";
    }
  }
}
