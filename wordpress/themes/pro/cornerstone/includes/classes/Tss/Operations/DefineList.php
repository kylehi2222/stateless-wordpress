<?php

namespace Themeco\Cornerstone\Tss\Operations;

class DefineList implements Operation {

  public static function run( $stack, $value ) {
    return $stack->evaluator()->makeTyped('valueList',array_map(function($item) use ($stack) {
      return $stack->evaluator()->resolve($item);
    }, $value));
  }
}