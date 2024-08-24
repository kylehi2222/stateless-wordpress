<?php

namespace Themeco\Cornerstone\Services;

class DevToolkit implements Service {

  public function save( $params ) { // See Controllers/Save.php
    if ( ! current_user_can( 'manage_options' ) ) {
      throw new \Exception( 'Unauthorized' );
    }

    if ( ! isset( $params['name']) || ! $params['name'] ) {
      throw new \Exception( 'Attempting to save dev toolkit data without specifying a name.' );
    }

    if ( ! isset( $params['data']) || ! $params['data'] ) {
      throw new \Exception( 'Attempting to save dev toolkit data without specifying data.' );
    }


    do_action( 'cs_dev_toolkit_user_save', $params['name'], $params['data'] );

    return [ 'success' => true ];

  }

  public function getAppData() {
    return apply_filters('cs_dev_toolkit', [] );
  }

  public function registerPostTypes() {
    register_post_type( 'cs_template', array(
      'public'              => false,
      'exclude_from_search' => false,
      'capability_type'     => 'page',
      'supports'            => false
    ) );

    // Classic Cornerstone templates
    register_post_type( 'cs_user_templates', array(
			'public'          => false,
			'capability_type' => 'page',
			'supports'        => false
    ));
  }

}