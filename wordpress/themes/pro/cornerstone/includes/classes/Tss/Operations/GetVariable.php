<?php

namespace Themeco\Cornerstone\Tss\Operations;
use Themeco\Cornerstone\Tss\Operations\Operation;

class GetVariable implements Operation {
  public static function run( $stack, $name ) {
    return $stack->evaluator()->ensureType($stack->lookup('variable', $name));
  }
}
