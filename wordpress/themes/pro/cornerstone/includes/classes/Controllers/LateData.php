<?php

namespace Themeco\Cornerstone\Controllers;

use Themeco\Cornerstone\Services\App;
use Themeco\Cornerstone\Services\Routes;

class LateData {
  protected $routes;
  protected $app;

  public function __construct(Routes $routes, App $app) {
    $this->routes = $routes;
    $this->app = $app;
  }

  public function setup() {
    $this->routes->add_route('post', 'late-data', [$this, 'get_late_data']);
    $this->routes->add_route('post', 'late-expansion-data', [$this, 'get_late_expansion_data']);
  }

  public function get_late_data() {
    return $this->app->get_late_data();
  }

  public function get_late_expansion_data() {
    return $this->app->get_late_expansion_data();
  }
}
