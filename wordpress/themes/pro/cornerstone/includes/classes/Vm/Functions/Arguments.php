<?php

namespace Themeco\Cornerstone\Vm\Functions;

class Arguments {

  protected $ordered = [];
  protected $named = [];

  public function add($value, $name = null) {
    if ($name) {
      $this->named[$name] = $value;
    } else {
      $this->ordered[] = $value;
    }
  }

  public function toNamed($names) {
    $result = [];

    foreach ( $names as $index => $name) {
      if (!isset($this->ordered[$index])) {
        break;
      }
      $result[$name] = $this->ordered[$index];
    }

    foreach ($this->named as $key => $value) {
      $result[$key] = $value;
    }

    return $result;
  }

}