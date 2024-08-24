<?php

namespace Themeco\Cornerstone\Tss\Operations;

class Unary implements Operation {

  public static function run( $stack, $input ) {
    list( $operator, $value ) = $input;
    $typed = $stack->evaluator()->resolve( $value );

    return $typed->copy()->unaryOperation( $operator );

  }
}