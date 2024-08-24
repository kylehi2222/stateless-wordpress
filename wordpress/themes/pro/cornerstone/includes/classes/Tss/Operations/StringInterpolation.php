<?php

namespace Themeco\Cornerstone\Tss\Operations;

class StringInterpolation implements Operation {

  public static function run( $stack, $input ) {
    list($format, $args) = $input;

    $values = array_map(function($arg) use ($stack) {
      return $stack->evaluator()->resolve($arg)->toString();
    }, $args);

    $formatted = vsprintf(str_replace("\%", '##PERCENT##', $format),$values);
    $formatted = str_replace("##PERCENT##", '%', $formatted);

    return $stack->evaluator()->makeTyped('primitive',$formatted);

  }
}
