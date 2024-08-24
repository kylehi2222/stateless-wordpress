<?php

namespace Themeco\Cornerstone\Parsy\Parsers;

class AnyChar extends Parser {

  protected $name = 'anyChar';

  public function transform( $state ) {
    if ($state->isError()) return $state;
    return $this->update($state, $state->take(1), 1);
  }

}