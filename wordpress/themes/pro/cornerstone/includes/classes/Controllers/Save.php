<?php

namespace Themeco\Cornerstone\Controllers;

use Themeco\Cornerstone\Services\Components;
use Themeco\Cornerstone\Services\Routes;
use Themeco\Cornerstone\Services\Permissions;
use Themeco\Cornerstone\Services\ThemeOptions;
use Themeco\Cornerstone\Services\DevToolkit;

class Save {
  private $components;
  private $routes;
  private $permissions;
  private $themeOptions;
  private $devToolkit;

  public function __construct(Routes $routes, Components $components, Permissions $permissions, ThemeOptions $themeOptions, DevToolkit $devToolkit) {
    $this->components = $components;
    $this->routes = $routes;
    $this->permissions = $permissions;
    $this->themeOptions = $themeOptions;
    $this->devToolkit = $devToolkit;

    //Copy paste from App::boot()
    add_action('cs_save_document', function($doc) {
      $updateTime = current_time( 'mysql' );
      update_option( 'cs_last_save', $updateTime);

      // Shouldnt happen, but ive seen docs do weird things
      if (empty($doc)) {
        return;
      }

      // Update post meta last save
      update_post_meta($doc->id(), '_cs_last_save', $updateTime);
    });
  }

  public function setup() {
    $this->routes->add_route('post', 'dev-toolkit-user-save', [$this->devToolkit, 'save']);
    $this->routes->add_route('post', 'save', [$this, 'save']);
    $this->routes->add_save_handler('colors', [$this, 'save_colors']);
    $this->routes->add_save_handler('fontItems', [$this, 'save_font_items']);
    $this->routes->add_save_handler('fontConfig', [$this, 'save_font_config']);
    $this->routes->add_save_handler('themeOptions', [$this, 'save_theme_options']);
  }

  public function save($params) {

    if (!isset($params['requests'])) {
      throw new \Exception('Nothing requested');
    }

    do_action("cs_before_save_request");

    $results = array();
    $context = isset($params['context']) ? $params['context'] : array();

    $handlers = $this->routes->get_save_handlers();
    $document_handlers = $this->routes->get_document_save_handlers();

    foreach ($params['requests'] as $key => $request_params) {
      try {
        $args = array_merge( $context, $request_params );
        if ($key === 'document' && is_callable($document_handlers[$request_params['type']]) ) {
          $results[$key] = call_user_func_array($document_handlers[$request_params['type']], array( $args ) );
        } elseif (is_callable($handlers[$key])) {
          $results[$key] = call_user_func_array($handlers[$key], array( $args ) );
        } else {
          throw new \Exception('No handler registered ' . json_encode($params));
        }
      } catch (\Exception $e) {
        $results[$key] = array( 'error' => $e->getMessage() );
      }
    }

    $results['side-effects'] = [
      'components' => $this->components->enumerate()
    ];

    return $results;

  }

  public function save_colors($params) {

    if ( ! $this->permissions->userCan('global.colors') ) {
      throw new \Exception( 'Unauthorized' );
    }

    if ( ! isset( $params['data']) ) {
      throw new \Exception( 'Attempting to update colors without specifying data.' );
    }

    update_option('cornerstone_color_items', wp_slash( cs_json_encode( $params['data'] ) ) );

    cornerstone_cleanup_generated_styles();

    return [ 'success' => true ];

  }

  public function save_font_option( $option, $params ) {

    if ( ! $this->permissions->userCan('global.fonts') ) {
      throw new \Exception( 'Unauthorized' );
    }

    if ( ! isset( $params['data']) ) {
      throw new \Exception( 'Attempting to update fonts without specifying data.' );
    }

    update_option($option, wp_slash( cs_json_encode( $params['data'] ) ) );

    cornerstone_cleanup_generated_styles();

    return [ 'success' => true ];

  }

  public function save_font_items( $params ) {
    return $this->save_font_option( 'cornerstone_font_items', $params );
  }

  public function save_font_config( $params ) {
    return $this->save_font_option( 'cornerstone_font_config', $params );
  }

  public function save_theme_options($params) {

    if ( ! ( current_user_can( 'manage_options' ) || $this->permissions->userCan('global.theme_options') ) ) {
      throw new \Exception( 'Unauthorized' );
    }

    if ( ! isset( $params['data']) || ! $params['data'] ) {
      throw new \Exception( 'Attempting to update Theme Options without specifying data.' );
    }

    do_action('cs_theme_options_before_save');

    foreach ($params['data'] as $key => $value) {
      $this->themeOptions->update_value( $key, $value );
    }

    $this->themeOptions->commit();

    do_action('cs_theme_options_after_save');

    cornerstone_cleanup_generated_styles();

    return [ 'success' => true ];

  }

}
