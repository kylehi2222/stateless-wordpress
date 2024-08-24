<?php

namespace Themeco\Cornerstone\Vm\Types;

class ScalarString extends BaseScalar {

  public static function primitive() {
    return 'string';
  }

  public function get( $nanite ) {
    return (string) $nanite->raw();
  }
}