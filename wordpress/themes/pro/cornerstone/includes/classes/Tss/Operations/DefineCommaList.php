<?php

namespace Themeco\Cornerstone\Tss\Operations;


class DefineCommaList implements Operation {

  public static function run( $stack, $value ) {
    return $stack->evaluator()->makeTyped('commaList',array_map(function($item) use ($stack) {
      return $stack->evaluator()->resolve($item);
    }, $value));
  }
}