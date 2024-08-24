<?php

namespace Themeco\Cornerstone\Parsy\Parsers;

class Lazy extends Parser {

  protected $name = 'lazy';
  protected $fn;

  public function __construct($fn, $name = '') {
    $this->fn = $fn;
    $this->name = "lazy:$name";
  }

  public function transform( $state ) {
    $fn = $this->fn;
    return $fn()->parse( $state );
  }

}