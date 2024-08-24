<?php

namespace Themeco\Cornerstone\Tss\Functions;

class GlobalFontFamily extends BuiltInFunction {

  public function run( $valueTyped ) {

    $value = $valueTyped->toString();

    if (strpos($value,'var(') !== false) {
      return $valueTyped;
    }

    if ($value === 'inherit') {
      return $this->stack->evaluator()->makeTyped( 'primitive', 'inherit');
    }

    return $this->stack->evaluator()->makeTyped(
      'primitive',
      "%%post tss-ff%%$value%%/post%%"
    );

  }

}