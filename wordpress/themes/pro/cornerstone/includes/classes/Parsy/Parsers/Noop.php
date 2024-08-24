<?php

namespace Themeco\Cornerstone\Parsy\Parsers;

class Noop extends Parser {

  protected $name = 'noop';

  public function transform( $state ) {
    return $state;
  }

}