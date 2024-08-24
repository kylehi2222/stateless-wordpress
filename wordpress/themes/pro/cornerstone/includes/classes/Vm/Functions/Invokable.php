<?php

namespace Themeco\Cornerstone\Vm\Functions;

use Themeco\Cornerstone\Vm\Callstack\Frame;

class Invokable {

  protected $orderedArguments = [];
  protected $namedArguments = [];
  protected $handler;

  public function addArgument($name, $type, $default = null) {
    $this->orderedArguments[] = $name;
    $this->namedArguments[$name] = [ $type, $default ];
  }

  public function setHandler( $fn ) {
    $this->handler = $fn;
    return $this;
  }

  public function invoke( $frame, Arguments $input ) {

    $args = $this->applyInput( $frame, $input );

    if (PHP_VERSION_ID >= 70000) {
      return $this->handler->call($frame, $args);
    }

    $call = $this->handler->bindTo($frame);
    return $call($args);
  }

  public function applyInput( Frame $frame, Arguments $input ) {
    var_dump($input);
    return [];
  }

}