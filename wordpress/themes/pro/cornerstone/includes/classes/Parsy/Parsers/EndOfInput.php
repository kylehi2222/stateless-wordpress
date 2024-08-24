<?php

namespace Themeco\Cornerstone\Parsy\Parsers;

class EndOfInput extends Parser {

  protected $name = 'end';

  public function transform( $state ) {
    if ($state->isError() || $state->isComplete()) return $state;
    return $this->error($state, "Expected end of input but got: " . substr($state->next(), 0, 100) );
  }

}