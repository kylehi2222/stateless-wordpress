<?php

namespace Themeco\Cornerstone\Tss\Operations;

class Binary implements Operation {

  public static function run( $stack, $input ) {
    list( $operandA, $operator, $operandB ) = $input;

    return $stack->evaluator()
      ->resolve( $operandA )
      ->binaryOperation(
        $operator,
        $stack->evaluator()->resolve( $operandB ) );

  }
}