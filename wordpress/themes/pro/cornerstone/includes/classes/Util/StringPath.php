<?php

namespace Themeco\Cornerstone\Util;

class StringPath {
  public $data;

  public function data($data) {
    $this->data = $data;
    return $this;
  }

  public function get($path) {


    if ( ! is_array( $this->data ) || ! is_string( $path ) ) {
      return null;
    }

    $paths = array_reverse( explode('.', $path) );
    $current = $this->data;

    try {
      while( count($paths) > 0 ) {
        $current = $this->resolvePath( $current, array_pop($paths) );
      }
    } catch (\Exception $e) {
      return null;
    }

    return $current;
  }

  public function resolvePath($data, $path) {

    if ( is_array($data)) {

      if ($path === '') {
        return $data;
      }

      if (isset($data[$path])) {
        return $data[$path];
      }

      if ($path === '$end') { // keyword to access last item in array
        return $data[count($data) -1];
      }

      $numeric_path = (int) $path;

      if ($numeric_path == $path) { // convert numeric indexes
        if (isset($data[$numeric_path])) {
          return $data[$numeric_path];
        }
      }

    }

    throw new \Exception;
  }

}
