<?php

namespace Themeco\Cornerstone\Tss\Operations;

class DefineSplit implements Operation {

  public static function run( $stack, $value ) {
    return $stack->evaluator()->makeTyped('split',array_map(function($item) use ($stack) {
      return $stack->evaluator()->resolve($item);
    }, $value));
  }
}