<?php

class Cornerstone_Dynamic_Content_Looper extends Cornerstone_Plugin_Component {

  public function setup() {
    add_filter('cs_dynamic_content_looper', array( $this, 'supply_field' ), 10, 3 );
    add_action('cs_dynamic_content_setup', array( $this, 'register' ) );
  }

  public function register() {
    $depthControl = [
      'type' => 'text',
      'key' => 'depth',
      'label' => __('Depth', CS_LOCALIZE),
      'description' => __('The current looper in the stack to use to use, 0 would be the current looper and numbers above would get parents in the stack. -1 will get the first parent', CS_LOCALIZE),
      'options' => [
        'placeholder' => '0',
      ],
    ];

    cornerstone_dynamic_content_register_group(array(
      'name'  => 'looper',
      'label' => csi18n('app.dc.group-title-looper')
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'item',
      'group' => 'looper',
      'type'  => 'mixed',
      'label' => csi18n( 'app.dc.looper.item' ),
      'controls' => [ $depthControl ],
    ));

    // {{dc:looper:index}}
    cornerstone_dynamic_content_register_field(array(
      'name'  => 'index',
      'group' => 'looper',
      'label' => __("Index (Name)", "cornerstone"),
      'controls' => [ $depthControl ],
    ));

    // {{dc:looper:index_number}}
    cornerstone_dynamic_content_register_field(array(
      'name'  => 'index_number',
      'group' => 'looper',
      'label' => __("Index (Number)", "cornerstone"),
      'controls' => [ $depthControl ],
    ));

    // {{dc:looper:index_zero}}
    cornerstone_dynamic_content_register_field(array(
      'name'  => 'index_zero',
      'group' => 'looper',
      'label' => __("Index (Zero)", "cornerstone"),
      'controls' => [ $depthControl ],
    ));

    // {{dc:looper:count}}
    cornerstone_dynamic_content_register_field(array(
      'name'  => 'count',
      'group' => 'looper',
      'label' => csi18n( 'app.dc.looper.count' ),
      'controls' => [ $depthControl ],
    ));

    // {{dc:looper:total_pages}}
    cornerstone_dynamic_content_register_field(array(
      'name'  => 'total_pages',
      'group' => 'looper',
      'label' => csi18n( 'app.dc.query.total-pages' ),
      'controls' => [ $depthControl ],
    ));

    // {{dc:looper:found_posts}}
    cornerstone_dynamic_content_register_field(array(
      'name'  => 'found_posts',
      'group' => 'looper',
      'label' => __('Found Posts', 'Cornerstone'),
      'controls' => [ $depthControl ],
    ));

    // cornerstone_dynamic_content_register_field(array(
    //   'name'  => 'debug_provider',
    //   'group' => 'looper',
    //   'label' => csi18n( 'app.dc.looper.debug-provider' ),
    // ));

    // cornerstone_dynamic_content_register_field(array(
    //   'name'  => 'debug_consumer',
    //   'group' => 'looper',
    //   'label' => csi18n( 'app.dc.looper.debug-consumer' ),
    // ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'field',
      'group' => 'looper',
      'type'  => 'mixed',
      'label' => csi18n( 'app.dc.looper.field' ),
      'controls' => array(
        array(
          'key' => 'key',
          'type' => 'text',
          'label' => csi18n('app.dc.key'),
          'options' => array( 'placeholder' => csi18n('app.dc.key') )
        ),
        array(
          'key' => 'fallback',
          'type' => 'text',
          'label' => csi18n('app.dc.fallback'),
          'options' => array( 'placeholder' => csi18n('app.dc.fallback') )
        ),
        $depthControl,
      ),
      'deep' => true
    ));

  }

  public function supply_field( $result, $field, $args) {

    $looper_manager = CS()->component('Looper_Manager');
    $depth = (int)cs_get_array_value($args, 'depth', 0);

    $provider = $looper_manager->get_provider_at_depth($depth);

    // Safety
    if (empty($provider)) {
      return 0;
    }

    switch ($field) {
      case 'debug_provider':
        $result = $looper_manager->debug_provider($provider);
        break;
      case 'debug_consumer':
        $result = $looper_manager->debug_consumer();
        break;
      case 'index_zero':
        $result = $provider->get_index();
        break;
      case 'index_number':
        $result = $provider->get_index() + 1;
        break;
      case 'index':
        $result = $provider->get_index_name();
        break;
      case 'count':
        $result = $provider->get_size();
        break;
      case 'total_pages':
        $result = $provider->total_pages();
        break;
      case 'found_posts':
        $result = $provider->found_posts();
        break;
      case 'item':
        $result = $provider->get_current_data();
        break;
      case 'field':
        // Key to grab path from
        $key = cs_get_array_value($args, 'key', '0');
        if (empty($key)) {
          $key = 0;
        }

        // Loopkup and send
        $lookup = apply_filters( 'cs_looper_field', cs_get_path($provider->get_current_data(), $key), $args );
        if (!is_null($lookup)) {
          $result = is_string( $lookup ) ? cs_dynamic_content( $lookup ) : $lookup;
        }

        break;
    }

    return $result;
  }

}
