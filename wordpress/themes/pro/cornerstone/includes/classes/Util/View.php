<?php

namespace Themeco\Cornerstone\Util;

use Themeco\Cornerstone\Plugin;

class View {

  protected $view = false;
  protected $plugin;
  protected $path;

  public function __construct(Plugin $plugin) {
    $this->plugin = $plugin;
    $this->path = $this->plugin->path;
  }

  public function name( $name ) {
    $file = $this->plugin->path . '/includes/views/' . $name . '.php';

    if ( file_exists( $file ) ) {
      $this->view = $file;
    }

    return $this;
  }

  public function file() {
    return $this->view;
  }

  /**
   * Include a view file, optionally outputting its contents.
   */
  public function render( $echo = true, $data = array(), $extract = false ) {

    if ( empty( $this->view ) ) {
      return '';
    }

    ob_start();

    if ( $extract ) {
      extract( $data );
    }

    include( $this->view );

    $contents = ob_get_clean();

    if ( $echo ) {
      echo $contents;
    }

    return $contents;

  }

  public function view( $name ) {
    return (new self($this->plugin))->name($name)->render();
  }

}
