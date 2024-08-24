<?php

namespace Themeco\Cornerstone\Vm\Types;

class ScalarBool extends BaseScalar {

  public static function primitive() {
    return 'bool';
  }

  public function get( $nanite ) {
    return (bool) $nanite->raw();
  }

}