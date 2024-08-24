<?php

namespace Themeco\Cornerstone\Tss\Functions;

class GlobalColor extends BuiltInFunction {

  public function run( $valueTyped) {

    return $valueTyped->map(function($item) {
      $value = $item->toString();
      if (strpos($value,'global-color:') !== false) {
        return $this->stack->evaluator()->makeTyped(
          'primitive',
          '%%post tss-color%%' . $value . '%%/post%%'
        );
      }
      return $value;
    });

  }

}