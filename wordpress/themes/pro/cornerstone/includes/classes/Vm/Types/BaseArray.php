<?php

namespace Themeco\Cornerstone\Vm\Types;

class BaseArray extends Type {
  public $arrayType;

  public static function primitive() {
    return 'array';
  }

  public function setType($arrayType) {
    $this->arrayType = $arrayType;
    return $this;
  }

}
