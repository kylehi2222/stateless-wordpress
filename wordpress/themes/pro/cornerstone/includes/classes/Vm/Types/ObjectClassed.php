<?php

namespace Themeco\Cornerstone\Vm\Types;

class ObjectClassed extends BaseObject {

  public $class;

  public static function primitive() {
    return 'object-c';
  }

  public function setClass($class) {
    $this->class = $class;
    return $this;
  }
}
