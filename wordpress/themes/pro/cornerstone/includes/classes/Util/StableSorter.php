<?php

namespace Themeco\Cornerstone\Util;

class StableSorter {
  protected $userCmp;
  protected $i;
  
  protected function setCmp( $cmp ) {
    $this->userCmp = $cmp;
  }

  protected function cmp($a, $b) {
    $user = call_user_func( $this->userCmp, $a[1], $b[1]);
    return $user ?: ($a[0] - $b[0]);
  }

  protected function index_array( $el ) {
    return array( $this->i++, $el );
  }

  public function sort( $array ) {
    $this->i = 0;
    $indexed = array_map( array( $this, 'index_array' ), $array );
    usort($indexed, array( $this, 'cmp'));
    $indexed = array_column($indexed, 1);
    return $indexed;
  }
  
  public static function make( $cmp ) {
    $sorter = new self();
    $sorter->setCmp( $cmp );
    return $sorter;
  }
  
}
