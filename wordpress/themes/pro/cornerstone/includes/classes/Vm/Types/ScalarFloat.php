<?php

namespace Themeco\Cornerstone\Vm\Types;

class ScalarFloat extends BaseScalar {

  public static function primitive() {
    return 'float';
  }

  public function get( $nanite ) {
    return (float) $nanite->raw();
  }

}