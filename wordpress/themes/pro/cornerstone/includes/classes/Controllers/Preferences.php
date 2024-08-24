<?php

namespace Themeco\Cornerstone\Controllers;

use Themeco\Cornerstone\Services\Routes;
use Themeco\Cornerstone\Services\Preferences as PreferencesService;


class Preferences {
  private $routes;
  private $preferences;

  public function __construct(Routes $routes, PreferencesService $preferences) {
    $this->routes = $routes;
    $this->preferences = $preferences;
  }

  public function setup() {
    $this->routes->add_route('post', 'update-preferences', [$this, 'save']);
  }


  public function save($params) {

    if ( ! isset( $params['user_id']) || ! $params['user_id'] ) {
      throw new \Exception( 'Attempting to save preferences without specifying user_id.' );
    }

    if ( ! $params['user_id'] === get_current_user_id() && ! current_user_can('manage_options') ) {
      throw new \Exception( 'Unauthorized attempt to save preferences of another user' );
    }

    if ( ! isset( $params['data']) || ! $params['data'] ) {
      throw new \Exception( 'Attempting to update preferences without specifying data.' );
    }

    do_action("cs_before_preferences_save_request");

    $this->preferences->update_user_preferences( $params['user_id'], $params['data'] );

    return [ 'success' => true ];

  }

}
