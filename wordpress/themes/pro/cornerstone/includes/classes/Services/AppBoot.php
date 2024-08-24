<?php

namespace Themeco\Cornerstone\Services;

use Themeco\Cornerstone\Plugin;
use Themeco\Cornerstone\Util\ManagedParameters;

class AppBoot implements Service {
  protected $settings;
  protected $app;
  protected $permalinks;

  public function __construct(Settings $settings, App $app, Permalinks $permalinks) {
    $this->settings = $settings;
    $this->app = $app;
    $this->permalinks = $permalinks;
  }

  public function setup() {
    add_action( 'parse_request', [ $this, 'detect_load' ] );
  }

  public function detect_load( $wp ) {

    if ( defined( 'IFRAME_REQUEST' ) || ( isset( $_REQUEST['wp_customize'] ) && 'on' == $_REQUEST['wp_customize'] ) ) {
      return;
    }

    $route = null;

    // Check if we're loading the ugly way
    $ugly = ( isset( $_GET['cs-launch'] ) && '1' === $_GET['cs-launch'] );

    // Or if we're loading the nice way
    $nice = false;
    if ( $wp->request ) {

      // If we have a request, see if it matches our app slug
      $parts = explode( '/', $wp->request );

      if ( is_array( $parts ) && $parts[0] === $this->settings->appSlug() ) {

        if ( 1 === count( $parts ) && '/' !== substr( $_SERVER['REQUEST_URI'], -1, 1 ) ) {
          wp_safe_redirect( $wp->request . '/' );
        }

        $nice = true;
      }

    }


    // Bail if we're not loading
    if ( !$ugly && !$nice ) {
      return;
    }


    $can_redirect = ( $ugly && !$nice && $this->permalinks->hasValidStructure() );


    // Allow an initial route to be passed if not using permalinks
    if ( isset( $_GET['cs_route'] ) ) {

      $route = esc_attr( $_GET['cs_route'] );

      if ( $route ) {

        // If we loaded ugly but we can use nice URLs, let's redirect.
        if ( $can_redirect ) {

          $redirect = add_query_arg( array(
            'cs_route' => $route
          ), trailingslashit( $this->settings->appUrl()  ) );

          wp_safe_redirect( $redirect );
          exit;
        }

      }

    } elseif ( $can_redirect ) {
      wp_safe_redirect( trailingslashit( $this->settings->appUrl()  ) );
      exit;
    }

    // Can't redirect, but needs auth
    if ( ! is_user_logged_in() ) {
      auth_redirect();
      exit;
    }

    $this->app->setArgs([ 'permalinks' => [ $nice, $route ] ])->boot();

  }

}
