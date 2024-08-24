<?php

namespace Themeco\Cornerstone\Vm\Functions;

class InvokableClosure extends Invokable{

  public function invoke( $frame, Arguments $input ) {

    $args = $this->applyInput( $frame, $input );

    $orderedArgs = [];

    foreach ( $this->orderedArguments as $name ) {
      $orderedArgs[] = $args[$name]->raw();
    }

    $fn = $this->handler->bindTo($frame);
    return $fn(...$orderedArgs);
  }

}