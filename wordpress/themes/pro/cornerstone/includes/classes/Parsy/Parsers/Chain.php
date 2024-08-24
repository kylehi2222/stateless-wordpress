<?php

namespace Themeco\Cornerstone\Parsy\Parsers;

class Chain extends Parser {
  protected $chainFn;

  public function __construct($parser, $fn) {
    $this->parser = $parser;
    $this->chainFn = $fn;
  }

  public function transform($state) {
    $nextState = $this->parser->parse($state);
    if ($nextState->isError()) return $nextState;
    $fn = $this->chainFn;
    $nextParser = $fn($nextState->getResult(), $nextState, $state);
    if (!$nextParser) {
      return $this->error( $state, 'next parser not identified');
    }
    return $nextParser->parse($nextState);
  }

}
