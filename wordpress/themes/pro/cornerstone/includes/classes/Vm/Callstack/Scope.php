<?php

namespace Themeco\Cornerstone\Vm\Callstack;

class Scope {

  protected $data;

  public function __construct() {
    $this->data = new \stdClass;
  }

  public function set($key, $value) {
    $this->data->{$key} = $value;
  }

  public function unset($key) {
    unset($this->data->{$key});
  }

  public function has($key) {
    return isset($this->data->{$key});
  }

  public function get($key) {
    return $this->data->{$key};
  }
}