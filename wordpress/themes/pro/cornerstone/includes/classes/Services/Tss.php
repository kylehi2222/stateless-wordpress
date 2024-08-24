<?php

namespace Themeco\Cornerstone\Services;

use Themeco\Cornerstone\Util\Parameter;
use Themeco\Cornerstone\Util\ErrorHandler;
use Themeco\Cornerstone\Util\PostMetaCache;
use Themeco\Cornerstone\Tss\Environment;
use Themeco\Cornerstone\Tss\ContainerConfig;
use Themeco\Cornerstone\Plugin;
use Themeco\Cornerstone\Tss\Reducers\DeclarationReducer;
use Themeco\Cornerstone\Tss\Util\IdEncoder;

class Tss implements Service {

  protected $plugin;
  protected $env;
  protected $documentStyleCache;
  protected $containerStore = [];
  protected $registered = [];

  protected $vm;
  protected $breakpoints;
  protected $stylingService;
  protected $elementsService;
  protected $errorHandler;
  protected $themeOptionsConfig;
  protected $themeOptions;
  protected $themeManagement;

  public function __construct(Plugin $plugin, Vm $vm, Elements $elements, Styling $styling, Breakpoints $breakpoints, Environment $env, PostMetaCache $documentStyleCache, ErrorHandler $errorHandler, ContainerConfig $themeOptionsConfig, ThemeOptions $themeOptions, ThemeManagement $themeManagement) {
    $this->plugin = $plugin;
    $this->env = $env;
    $this->vm = $vm;
    $this->breakpoints = $breakpoints;
    $this->stylingService = $styling;
    $this->elementsService = $elements;
    $this->documentStyleCache = $documentStyleCache;
    $this->errorHandler = $errorHandler;
    $this->themeOptionsConfig = $themeOptionsConfig;
    $this->themeOptions = $themeOptions;
    $this->themeManagement = $themeManagement;
  }

  public function setup() {
    add_action( 'init', [$this, 'init'] );
    add_action('cs_purge_tmp', function() {
      $this->cleanGeneratedStyles();
    }, 20);
  }

  public function init() {

    $this->documentStyleCache->setup('_cs_generated_tss', function(){
      return apply_filters('cs_disable_style_cache', false );
    });

    list($base, $ranges) = $this->breakpoints->breakpointConfig();
    $this->env()->configureBreakpoints($base, $ranges);
    $this->env()->configure( 'getCssParameterByPath', function( $path ) {
      return $this->vm->getCssParameterByPath( $path );
    });
    $this->env()->configure( 'getCssGlobalParameterByPath', function( $path ) {
      return $this->vm->getCssParameterByPath( $path );
    });
    $this->import('elements-base');

    do_action("cs_before_standalone_enqueue_styles");

    // Output theme options .tss
    if (
      $this->themeManagement->isStandalone()
    ) {
      $this->import('global-base');
      $this->themeOptionsConfig->setup([
        'modules' => [ 'theme-options']
      ]);

      $this->registerType("theme-options", $this->themeOptionsConfig);

      add_action('template_redirect', function() {
        // Filter to block
        if (!apply_filters('cornerstone_enqueue_styles', true)) {
          return;
        }

        $this->stylingService->addStyles( 'theme-options-generated', $this->generateGlobalCss(), 0 );
      });

    }

    // Generate global variables
    add_action("cs_after_styling", [$this, 'generateGlobalParametersVariables']);
  }

  public function generateGlobalParametersVariables() {
    $globalParameter = $this->themeOptions->get_value('cs_global_parameter_data');

    if (empty($globalParameter)) {
      return;
    }

    // No Breakpoint vars
    if (empty($globalParameter['_bp_base'])) {
      return;
    }

    $base = $globalParameter['_bp_base'];
    $baseKey = '_bp_data' . $base;
    //$baseIndex = ((int)str_replace("1_", "", $base) - 1);
    $bpData = $globalParameter[$baseKey];

    list($base, $ranges) = $this->breakpoints->breakpointConfig();

    unset($globalParameter['_bp_base']);
    unset($globalParameter[$baseKey]);

    // Create declarations
    $declarations = $this->addGlobalDeclarations($globalParameter, $bpData);

    $css = DeclarationReducer::reduce($declarations, $base, $ranges, ":root");
    $this->stylingService->addStyles( 'global-parameter-variables', $css, 0 );

    return $css;
  }

  /**
   * Create declarations for TSS
   *
   * @param array $globalParameters
   * @param array $bpData
   * @param string $prefix
   *
   * @return array
   */
  public function addGlobalDeclarations($globalParameters, $bpData, $prefix = '') {
    $declarations = [];

    list($base, $ranges) = $this->breakpoints->breakpointConfig();
    $numRanges = count($ranges);

    $encoder = IdEncoder::getEncoder("param", 'global');

    foreach ($globalParameters as $key => $param) {
      // Prefix key so it can reference BP data
      // which is using dot syntax
      $key = $prefix . $key;

      if (is_array($param)) {
        $declarations = array_merge(
          $declarations,
          $this->addGlobalDeclarations($param, $bpData, $key . '.')
        );
        continue;
      }

      $path = $encoder->idForPath($key);
      $varName = "--" . $path;


      // Base breakpoint
      $declaration = [
        cornerstone_post_process_value($param),
        $varName,
        "",
        [null, null],
      ];
      $declarations[] = $declaration;

      foreach ($ranges as $index => $range) {
        if ($index === $base || empty($bpData[$key][$index])) {
          continue;
        }

        $value = cornerstone_post_process_value($bpData[$key][$index]);

        // Min Max values for breakpoint min max
        $minMax = DeclarationReducer::getMinMaxBreakpoint(
          $bpData[$key],
          $index,
          $base,
          $ranges,
          $numRanges
        );

        // Breakpoint declaration
        $declarations[] = [
          $value,
          $varName,
          "",
          $minMax
        ];
      }
    }

    return $declarations;
  }

  public function generateGlobalCss() {
    $runtime = $this->env()->runtime('theme-options');
    $this->env()->configure( 'selectorPrefix', '');
    $runtime->process(
      "theme-options",
      "theme-options",
      $this->themeOptions->getValues()[0]
    );

    $result = $runtime->finalize([
      'selectorFormat' => ':root'
    ]);

    $css = $result['tss'];

    // Add custom stack CSS
    if (cs_stack_is_custom()) {
      $css = cs_dynamic_content(cs_stack_custom_css()) . "\n" . $css;
    }

    return $css;
  }

  public function env() {
    return $this->env;
  }

  public function read($name) {
    ob_start();
    include $this->plugin->path . "/assets/tss/$name.php";
    return ob_get_clean();
  }

  public function import($dep) {
    $this->errorHandler->start();
    $this->env()->import($dep, $this->read($dep));
    $this->errorHandler->stop();
    $this->errorHandler->flush();
  }

  public function registerElementType( $type ) {
    $this->registerType( "el:$type", $this->elementsService->get_element( $type )->get_tss_config());
  }

  public function registerDocument( $document ) {

    $id = $document->id();
    if ( ! apply_filters( 'cs_register_document_styles', true, $document ) ) {
      return;
    }

    if ( isset( $this->registered[$id] ) ) {
      return;
    }

    $this->registered[$id] = true;

    $decorated = $document->getElementData()->decoratedWithTypes();

    $documentStyles = $this->documentStyleCache->resolve( $id, function() use ($id, $decorated){

      list( $elements, $types ) = $decorated;

      $element_css = [];
      // For each element type being used, register it's configuration into a container
      foreach ($types as $type) {
        $this->registerElementType( $type );
      }

      list($finalized, $element_css) = $this->processElements( $id, $elements );

      // Finalize the output so it can be stored in the cache
      return array_merge( $finalized, [
        'element_css' => implode(' ', $element_css)
      ]);

    });

    // Make the output available to other systems
    $this->containerStore['c:' . $document->id()] = $documentStyles['containers'];

    $priority = $document->getStylePriority()[0];
    $this->stylingService->addStyles( $document->id() . '-generated', $documentStyles['tss'], $priority );
    $this->stylingService->addStyles( $document->id() . '-element-css', $documentStyles['element_css'], $priority + 1 );

  }

  public function processElements( $runtimeId, $elements ) {

    // return [['containers' => [], 'tss' => ''], []];
    // Create a runtime scoped to the document (or root preview element)
    $runtime = $this->env()->runtime($runtimeId);

    $componentStack = [];
    $element_css = [];

    // Process all the elements using their registered container
    $process = function($data) use (&$process, &$element_css, &$componentStack, $runtime) {

      // If we have a component, we are going to render that instead of the given element
      if ( isset( $data['_virtual_root'] ) ) {
        $toRender = $data['_virtual_root'];

        if ( $data['_virtual_direct'] ) {
          $toRender['_modules'] = $data['_modules'];
          return $process($toRender);
        }

        $map = [];
        foreach ($data['_modules'] as $child) {
          if ( isset( $data['_virtual_map'][$child['_id']] )) {
            $item = $data['_virtual_map'][$child['_id']];
            $map[$item['id']] = [$child, $item['unwrap']];
          }
        }
        $componentStack[] = $map;
        $process($toRender);
        array_pop($componentStack);
        return;

      }

      $stack = end($componentStack);

      if ($stack && isset( $stack[$data['_id']])) {

        list($egress) = $stack[$data['_id']];

        if ( $egress['_type'] === 'slot' ) {
          $data['_modules'] = $egress['_modules'];
        }

      }

      $frame = $this->vm->runtime()->newFrame();
      Parameter::defineParametersForRender($data['_parameters'], $frame, $data['style_id']);

      $this->env()->configure( 'selectorPrefix', $this->getSelectorPrefix() );
      $runtime->process(
        "el:" . $data['_id'],
        "el:" . $data['_type'],
        $this->elementsService->get_element( $data['_type'] )->preprocess_tss( $data ),
        function( $data, $scope ) use (&$element_css) {
          if ( isset( $data['css'] )) {
            $element_css[] = str_replace('$el', '.' . $data['style_id'], call_user_func($scope->lookup('parser', 'elementCssParser'), $data['css'] ) );
          }
        }
      );

      if (!empty($data['_modules']) && ! isset($data['_builder_outlet']) && ! isset($data['_preview_children']) ) {
        array_map($process, $data['_modules']);
      }

      $frame->dispose();

    };

    array_map($process, $elements);



    return [$runtime->finalize(), $element_css];

  }

  public function processPreviewElement( $element ) {

    $element_css = [];

    list($result, $element_css) = $this->processElements( $element['_id'], [$element] );
    $this->containerStore['c:' . $element['_id'] ] = $result['containers'];

    return $result['tss'] . implode(' ', $element_css);
  }

  /**
   * Processor component shortcode see Services/Components
   */
  public function processComponentShortcode($elementDataWithTypes) {
    list($element, $types) = $elementDataWithTypes;
    foreach ($types as $type) {
      $this->registerElementType($type);
    }

    $root = $element[0]['_virtual_root'];
    $css = $this->processComponent($root, $root['style_id']);

    // It inserts m as a prefix
    // and -NUMBER here, this hack fixes it to use the style_id
    $css = preg_replace("/m{$root['style_id']}\-\d*/", $root['style_id'], $css);

    return $css;
  }

  /**
   * Process element of component
   */
  public function processComponent( $element, $runtime = '' ) {

    list($result, $element_css) = $this->processElements( $runtime, [$element] );

    if (!empty($element['_virtual_root'])) {
      $element_css[] = $this->processComponent($element['_virtual_root'], $element['_virtual_root']['style_id']);
    }

    foreach( $element['_modules'] as $child) {
      $element_css[] = $this->processComponent( $child, $child['style_id'] );
    }

    if (is_array($element_css)) {
      $element_css = implode('  ', $element_css);
    }

    $css = $result['tss'] . $element_css;
    $css = preg_replace("/m{$element['style_id']}\-\d*/", $element['style_id'], $css);

    return $css;
  }

  public function registerType( $type, $config ) {

    $deps = $config->deps();
    foreach( $deps as $dep) {
      if (is_string($dep)) {
        $this->import($dep);
      }
    }

    $this->env()->registerType( $type, $config);
  }

  public function getContainer($containerId, $itemId) {

    if ( ! isset( $this->containerStore['c:' .$containerId] ) ) {
      return [];
    }

    $docData = $this->containerStore['c:' .$containerId];

    return isset( $docData["el:" . $itemId] ) ?  $docData["el:" . $itemId] : [];
  }

  public function getSelectorPrefix() {
    $prefixes = apply_filters('cs_tss_selector_prefixes', $this->themeManagement->compatibilityMode() ? ['#cs-content', '#cs-footer', '#cs-header', '.x-layout'] : []);
    if (count($prefixes) > 0) {
      return sprintf(":where(%s) ", implode(', ', $prefixes ) );
    }
    return '';
  }

  public function previewConfig() {
    return [
      'prefixes' => $this->getSelectorPrefix(),
    ];
  }

  public function applyTssToElement( $element ) {
    return $this->applyElementContainer( $element, $this->getContainer( $element['_tss_container'], $element['_id'] ) );
  }

  public function applyElementContainer( $element, $container ) {

    $element['_tss'] = [];
    $element['_tss_style'] = '';
    $element['classes'][] = $element['style_id'];
    $modules = $this->elementsService->get_element( $element['_type'] )->get_tss_config()->modules();

    if (! empty($container) && ! empty( $modules ) ) {

      foreach( $container as $key => $value) {
        if ($key === 'dynamic-content') {
          $element['_tss_style'] = $this->applyDynamicContentStyle( $value );
        } else {
          $element['_tss'][$key] = implode(' ', $value);
        }

      }

      foreach( $modules as $module) {
        if ( ! isset( $element['_tss'][$module['name']] ) ) {
          $element['_tss'][$module['name']] = '';
        }
        if ( isset( $container[$module['name']] ) && !$module['nested'] ) {
          $element['classes'][] = implode(' ', $container[$module['name']]);
        }
      }

    }


    if ( ! isset($element['style']) ) {
      $element['style'] = '';
    }

    $element['style'] = $element['_tss_style'] . $element['style'];

    if ( isset($element['class']) && $element['class'] && ! in_array( $element['class'], $element['classes'] ) ) {
      $element['classes'][] = $element['class'];
    }

    return $element;

  }

  public function applyDynamicContentStyle( $items ) {
    $styles = [];

    foreach ($items as $id => $dc) {
      $styles[] = '--tco-' . $id . ':' . cs_dynamic_content( $dc );
    }
    $styles_str = implode(';', $styles);
    return $styles_str ? $styles_str . ';' : '';
  }

  public function cleanGeneratedStyles() {
    global $wpdb;
    $wpdb->delete( $wpdb->postmeta, [ 'meta_key' => '_cs_generated_styles' ] );
    $wpdb->delete( $wpdb->postmeta, [ 'meta_key' => '_cs_generated_tss' ] );
  }

}
