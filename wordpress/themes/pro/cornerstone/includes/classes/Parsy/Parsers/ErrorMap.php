<?php

namespace Themeco\Cornerstone\Parsy\Parsers;

class ErrorMap extends Parser {

  public function __construct($parser, $fn) {
    $this->parser = $parser;
    $this->mapFn = $fn;
  }

  public function transform($state) {
    $nextState = $this->parser->parse($state);
    if (!$nextState->isError()) return $nextState;
    $fn = $this->mapFn;
    return $this->error($state, $fn($nextState->getResult(), $nextState->getIndex()));
  }

}
