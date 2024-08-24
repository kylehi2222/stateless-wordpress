<?php

namespace Themeco\Cornerstone\Tss\Functions;

class GlobalFontWeight extends BuiltInFunction {

  public function run( $ffTyped, $fwTyped ) {

    $fw = $fwTyped->toString();

    if (strpos($fw,'var(') !== false) {
      return $fwTyped;
    }

    if (trim($fw) === 'inherit') {
      return $this->stack->evaluator()->makeTyped( 'primitive', $fw);
    }

    $value = $ffTyped->toString() . '|' . $fw;

    return $this->stack->evaluator()->makeTyped(
      'primitive',
      "%%post tss-fw%%$value%%/post%%"
    );

  }

}