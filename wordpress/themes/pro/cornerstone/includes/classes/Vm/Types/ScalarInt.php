<?php

namespace Themeco\Cornerstone\Vm\Types;

class ScalarInt extends BaseScalar {

  public static function primitive() {
    return 'int';
  }

  public function get( $nanite ) {
    return (int) $nanite->raw();
  }

}