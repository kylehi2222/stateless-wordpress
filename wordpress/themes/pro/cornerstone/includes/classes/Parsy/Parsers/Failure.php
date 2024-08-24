<?php

namespace Themeco\Cornerstone\Parsy\Parsers;

use Themeco\Cornerstone\Parsy\Util\ParseException;

class Failure extends Parser {

  protected $name = 'failure';
  protected $parser;

  public function __construct($parser) {
    $this->parser = $parser;
  }

  public function transform( $state ) {
    $nextState = $this->parser->parse($state);
    if ($nextState->isError()) {
      throw new ParseException($nextState->getErrorMessage(), [$state,$nextState]);
    }
    return $nextState;
  }
}