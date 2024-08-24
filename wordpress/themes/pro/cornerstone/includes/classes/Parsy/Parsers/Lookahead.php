<?php

namespace Themeco\Cornerstone\Parsy\Parsers;

class Lookahead extends Parser {

  protected $name = 'lookahead';
  protected $parser;

  public function __construct($parser) {
    $this->parser = $parser;
  }

  public function transform( $state ) {
    $valueState = $this->parser->parse($state);
    if ($valueState->isError()) $this->error( $state, 'Unable to match any input');
    $valueState->setIndex($state->getIndex());
    return $valueState;
  }

}