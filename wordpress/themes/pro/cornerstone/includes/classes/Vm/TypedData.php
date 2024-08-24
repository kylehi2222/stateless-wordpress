<?php

namespace Themeco\Cornerstone\Vm;
use Themeco\Cornerstone\Vm\Types\Type;

class TypedData {
  private $type;
  private $value;

  public function __construct( Type $type, $value ) {
    $this->type = $type;
    $this->value = $value;
  }

  public function raw() {
    return $this->value;
  }

  public function type() {
    return $this->value;
  }

  public function __call( $name, $arguments ) {
    return $this->type->$name($this, $arguments);
  }

  public function clone() {
    return new self($this->type, $this->value);
  }
}