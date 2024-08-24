<?php

namespace Themeco\Cornerstone\Services;

use Themeco\Cornerstone\Elements\Definition;
use Themeco\Cornerstone\Elements\Renderer;
use Themeco\Cornerstone\Elements\ControlMacros;
use Themeco\Cornerstone\Elements\Migrations;
use Themeco\Cornerstone\Plugin;

class Elements implements Service {

  protected $elements = [];
  protected $includes = [];

  protected $plugin;
  protected $renderer;
  protected $env;
  protected $controlMacros;
  protected $migrations;


  public function __construct(
    Plugin $plugin,
    Renderer $renderer,
    Env $env,
    ControlMacros $controlMacros,
    Migrations $migrations
  ) {
    $this->plugin = $plugin;
    $this->renderer = $renderer;
    $this->env = $env;
    $this->controlMacros = $controlMacros;
    $this->migrations = $migrations;
  }

  public function renderer() {
    return $this->renderer;
  }

  public function controlMacros() {
    return $this->controlMacros;
  }

  public function migrations() {
    return $this->migrations;
  }

  public function setup() {
    $this->renderer->setup($this);
    add_action('init', [ $this, 'init' ]);
  }

  public function init() {

    if ( ! did_action('init') ) {
      trigger_error( 'Cornerstone Element_Manager should not be requested before init action', E_USER_WARNING );
    }

    $this->register_native_elements();
    $this->upgrade_classic_elements();

    do_action( 'cs_register_elements' );
    do_action( 'cs_registered_elements' );

    $this->attachRenderChildrenAliases();

    add_filter( 'cs_save_element_output_section', function( $shortcode, $data, $content ) {
      $data_string = json_encode( $data );

      if ( false !== strpos( $data_string, '<!--nextpage-->' ) ) {
        $shortcode .= '[/cs_content]<!--nextpage-->[cs_content]';
      }

      return $shortcode;
    }, 10, 3 );

  }

  public function attachRenderChildrenAliases() {
    foreach ($this->elements  as $name => $definition) {
      $hook = $definition->get_children_hook();
      if ($hook) {
        add_action( $hook, [ $this, 'aliasRenderChildren' ], 10, 2 );
      }
    }
  }

  public function aliasRenderChildren( $elements, $parent = null ) {
    do_action( 'x_render_children', $elements, $parent );
  }

  public function register_include( $name, $options = [] ) {

    if ( ! $name || ! is_array( $options ) ) {
      return;
    }

    $include = cs_define_defaults( $options, [
      'values'       => [],
      'migrations'   => [],
      'value_prefix' => '',
    ]);

    // remove the value_prefix at registration so we can use keys like "effects_duration" but the actual include only stores "duration" so it can be potentially be re-prefixed at the element level
    if ( $include['value_prefix'] ) {
      $start = strlen( $include['value_prefix'] ) + 1;
      $unprefixed = [];
      foreach( $include['values'] as $key => $value ) {
        $key = substr($key, $start);
        $unprefixed[$key] = $value;
      }
      $include['values'] = $unprefixed;
    }

    $include['id'] = $name;
    $this->includes[ $name ] = $include;

  }

  public function get_include( $name ) {
    return isset( $this->includes[ $name ] ) ? $this->includes[ $name ] : null;
  }

  public function register_element( $name, $element ) {

    if ( ! $element ) {
      return;
    }

    if ( isset( $this->elements[ $name ] ) ) {
      $this->elements[ $name ]->update( $element );
    }

    $this->elements[ $name ] = $this->plugin->resolve(Definition::class)->setup($name, $element);

  }

  public function unregister_element( $name ) {
    unset( $this->elements[ $name ] );
  }

  public function get_element( $name ) {
    return isset( $this->elements[ $name ] ) ? $this->elements[ $name ] : $this->elements['undefined'];
  }

  public function get_element_names() {
    return array_keys( $this->elements );
  }

  public function get_all_elements() {
    return $this->elements;
  }

  public function get_element_definitions() {
    $elements = array();

    foreach ($this->elements as $element) {
      $elements[] = $element->serialize();
    }

    return $elements;
  }

  public function get_includes() {
    return $this->includes;
  }

  public function get_element_inspector_data() {
    $elements = array();

    foreach ($this->elements as $id => $element) {
      $elements[$id] = $element->get_inspector();
    }

    return $elements;
  }

  public function get_public_definitions() {

    $elements = array();

    foreach ($this->elements as $element) {
      if ( $element->in_library() ) {
        $elements[] = $element;
      }
    }

    usort( $elements, array( $this, 'sort_definitions' ) );

    return $elements;
  }

  public function sort_definitions( $a, $b ) {
    return strcasecmp( $a->get_title(), $b->get_title() );
  }

  public function register_native_elements() {

    $this->register_element('undefined', [
      'title' => csi18n('common.elements.undefined-title'),
      'options' => [ 'library' => false ]
    ]);

    $this->register_element('root', [
      'valid_children' => [ 'region' ],
      'render'  => [ $this, 'directRender' ],
      'options' => [ 'library' => false ]
    ]);

    $this->register_element('region', array(
      'title'   => csi18n('common.elements.region-title'),
      'render'  => [ $this, 'directRender' ],
      'options' => [
        'valid_children' => [
          '*'       => '*', // Layouts, Footers
          'content' => '*',
          'right'   => 'bar',
          'bottom'  => 'bar',
          'left'    => 'bar',
        ],
        'library' => false,
        'add_new_element' => [
          '*'      => [ '_type' => 'section' ],
          'right'  => [ '_type' => 'bar' ],
          'bottom' => [ '_type' => 'bar' ],
          'left'   => [ '_type' => 'bar' ]
        ]
      ]
    ) );

    $definitions = cornerstone()->config( 'element-definitions' );

    // To support the old page templates these pretty much always need
    // to be here
    if (
      $this->plugin->service('ThemeManagement')->isClassic()
      || $this->plugin->service('ThemeManagement')->isClassicElementsEnabled()
      || true
    ) {
      $definitions[] = 'classic-row-v2';
      $definitions[] = 'classic-column-v2';
    }

    $this->load_files( $definitions, $this->plugin->path . '/includes/elements/definitions' );

    // These files only exist in pro builds
    if ( $this->env->isSiteBuilder() ) {
      $path = $this->plugin->path . '/includes/elements/definitions-pro';
      require_once( $path . '/bar.php' );
      require_once( $path . '/container.php' );
      require_once( $path . '/layout-grid.php' );
      require_once( $path . '/layout-cell.php' );
    }

  }

  public function load_files( $files, $path ) {
    foreach ($files as $file) {
      if (is_array( $file ) ) {
        $flags = apply_filters('tco_feature_flags', [] );
        if ( isset( $flags[$file[0] ] ) && $flags[$file[0] ] ) {
          $filename = $file[1];
        } else {
          continue;
        }
      } else {
        $filename = $file;
      }
      $filename = "$path/$filename.php";
      if ( file_exists($filename) ) {
        require_once( $filename );
      }
    }
  }

  public function upgrade_classic_elements() {

    $classic_elements = CS()->component( 'Classic_Element_Manager' )->getModels();

    foreach ($classic_elements as $element) {
      $classic = $this->upgrade_classic_element( $element );
      if ( $classic ) {
        $this->register_element( 'classic:' . $element['name'], $classic );
      }
    }

  }

  public function upgrade_classic_element( $element ) {

    if ( $element['flags']['context'] === 'generator' ) {
      return false;
    }

    $values = array();

    $options = array(
      'is_classic'   => true,
      'query_styles' => false,
      'classic'      => $element['flags'],
      'library'      => $element['flags']['library'] ? [ 'all' ] : false
    );

    if ( $element['flags']['context'] === '_layout' ) {
      $options['is_draggable'] = false;
      $options['empty_placeholder'] = false;
    }

    if ( ( isset( $options['classic']['empty_placeholder']) && ! $options['classic']['empty_placeholder'] ) ) {
      $options['empty_placeholder'] = false;
    }

    if ( isset($options['classic']['render_children'] ) ) {
      $options['render_children'] = true;
    }

    if ( ( isset( $options['classic']['dropzone']) ) ) {
      $options['is_draggable'] = false;
      $options['dropzone'] = $options['classic']['dropzone'];
    }

    if ( isset( $options['classic']['label_key'] ) ) {
      $options['label_key'] = $options['classic']['label_key'];
    }

    if ( isset( $options['classic']['alt_breadcrumb'] ) ) {
      $options['alt_breadcrumb'] = $options['classic']['alt_breadcrumb'];
    }

    if ( isset( $options['classic']['can_preview'] ) ) {
      $options['can_preview'] = $options['classic']['can_preview'];
    }

    $protected_keys = ( isset( $options['classic']['protected_keys'] ) && is_array( $options['classic']['protected_keys'] ) ) ? $options['classic']['protected_keys'] : array();

    if ( isset($options['classic']['child'] ) && $options['classic']['child']) {
      $options['library'] = false;
    }

    $controls = $this->upgrade_classic_element_controls( $element['controls'] );

    foreach ($controls as $control) {
      if ($control['type'] === 'classic:sortable') {
        $options['add_new_element'] = "classic:" . $control['options']['element'];
        break;
      }
    }
    foreach ($element['defaults'] as $key => $value) {
      if ( 'elements' === $key ) {
        $options['default_children'] = $this->migrations()->migrate($value);
        continue;
      }
      $designation = 'markup';
      $values[$key] = cs_value( $value, $designation, in_array( $key, $protected_keys, true ) );
    }

    $valid_children = array();

    if ( 'section' === $element['name'] ) {
      $valid_children[] = 'classic:row';
      $options['valid_parent'] = 'region';
      $options['unnestable'] = true;
      unset($options['library']);
    } elseif ( 'row' === $element['name'] ) {
      $valid_children[] = 'classic:column';
    } elseif ( 'column' === $element['name'] ) {
      $valid_children[] = '*';
    }
    else {
      foreach($element['controls'] as $control) {
        if ($control['type'] !== 'sortable') continue;
        if (isset($control['options']) && isset($control['options']['element'])) {
          $valid_children[] = 'classic:' . $control['options']['element'];
        }
      }
    }

    if ( count( $valid_children ) > 0 ) {
      $options['valid_children'] = $valid_children;
    }

    $title = $element['ui']['title'];
    $builder = function() use ( $controls, $title ) {
      return [
        'controls'       => [
          [
            'type' => 'group-module',
            'label' =>  $title,
            'options' => [ 'name' => 'classic-element' ],
            'controls' => $controls
          ]
        ]
      ];
    };

    return array(
      'title'          => sprintf( csi18n('common.classic'), $title ),
      'values'         => $values,
      'style'          => '__return_empty_string',
      'render'         => array( $this, 'upgrade_classic_element_render' ),
      'icon'           => $element['icon'],
      'options'        => $options,
      'group'          => 'classic',
      'active'         => $element['active'],
      'builder'        => $builder
    );
  }

  public function upgrade_classic_element_controls( $controls ) {
    $upgraded = array();

    foreach ($controls as $key => $control ) {
      $upgrade_control = $this->upgrade_classic_element_control( $control );
      if ( $upgrade_control ) {
        $upgraded[] = $upgrade_control;
      }
    }

    return $upgraded;
  }

  public function upgrade_classic_element_control( $control ) {

    if ( in_array( $control['context'], array( '_layout' ), true ) ) {
      return false;
    }

    $conditions = array();

    if ( isset( $control['condition'] ) && is_array( $control['condition'] ) ){
      foreach ($control['condition'] as $key => $value) {
        $conditions[] = $this->upgrade_classic_element_control_condition( $key, $value );
      }
    }

    return array(
      'type'        => 'classic:' . $control['type'],
      'key'         => $control['key'],
      'label'       => ( isset( $control['ui']) && isset( $control['ui']['title'] ) ) ? $control['ui']['title'] : '',
      'options'     => ( isset( $control['options'] ) ) ? $control['options'] : array(),
      'conditions'  => $conditions
    );
  }

  public function upgrade_classic_element_control_condition( $key, $value ) {

    $not = ':not' === substr($key, -strlen(':not'));

    if ( is_array( $value ) ) {
      $op = ( $not ) ? 'NOT IN' : 'IN';
    } else {
      $op = ( $not ) ? '!=' : '==';
    }

    return array(
      'key' => str_replace(':not', '', $key ),
      'value' => $value,
      'op' => $op
    );
  }

  public function upgrade_classic_element_render( $element ) {

    $render_data = $element;
    $render_data['_type'] = str_replace('classic:', '', $render_data['_type']);

    $content = '';
    if ( ! empty( $element['_modules'] ) ) {
      ob_start();
      do_action( 'x_render_children', $element['_modules'], $element );
      $content = ob_get_clean();
    }

    return CS()->component('Classic_Renderer')->render_classic_element( $render_data, $content );

  }




  // BEGIN RENDERER
  // --------------

  public function directRender( $data ) {
    return $this->renderElements( $data['_modules'] );
  }

  public function renderElements($elements, $parent = null) {
    return $this->renderer->renderElements( $elements, $parent );
  }

  // This functions allows preview values to update instantly via React props
  // while waiting for the server's next response

  public function formatPreviewProp( $key, $value ) {
    if (strpos( $value, '{{dc:') !== false ) {
      return $value;
    }
    // should check for DC and shortcodes and return the value in those cases
    return "%%{data.$key}%%";
  }

  public function usePreviewProp() {
    return apply_filters( 'cs_is_preview', false ) && ! apply_filters( 'cs_render_looper_is_virtual', false );
  }

  public function previewProp( $key, $value ) {
    return $this->usePreviewProp() ? $this->formatPreviewProp( $key, $value ) : $value;
  }

  public function previewProps( $keys, $data ) {
    // if ( $this->usePreviewProp() ) {
    //   foreach ($keys as $key) {
    //     $data[$key] = $this->formatPreviewProp($key, $data[$key]);
    //   }
    // }
    return $data;
  }
}
