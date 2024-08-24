<?php

namespace Themeco\Cornerstone\Vm\Types;

abstract class Type {

  protected $name;

  public function setName( $name ) {
    $this->name = $name;
  }

  public function name() {
    return $this->name;
  }

  public function get( $nanite ) {
    return $nanite->raw();
  }

  abstract public static function primitive();

}