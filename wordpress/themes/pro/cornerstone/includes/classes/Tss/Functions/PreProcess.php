<?php

namespace Themeco\Cornerstone\Tss\Functions;

class PreProcess extends BuiltInFunction {

  public function processValue( $item ) {

    $value = $item->toString();
    preg_match('/global-((?:ff|fw|color)):([|:.\w-]+)/', $value, $matches);

    if ( isset( $matches[1] ) ) {
      $result = $matches[1] === 'color' ? 'global-color:' . $matches[2] : $matches[2];
      return $this->stack->evaluator()->makeTyped(
        'primitive',
        '%%post tss-' . $matches[1] . '%%' . $result . '%%/post%%'
      );
    }

    return null;
  }

  public function run( $valueTyped) {

    if ( $valueTyped->isIterable() ) {
      return $valueTyped->map(function($item) {
        $result = $this->processValue(( $item ));
        return $result ? $result : $item;
      });
    }

    $result = $this->processValue(( $valueTyped ));
    return $result ? $result : $valueTyped;

  }

}