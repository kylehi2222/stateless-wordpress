<?php

namespace Themeco\Cornerstone\Parsy\Parsers;

class Otherwise extends Parser {

  protected $name = 'otherwise';
  protected $parser;
  protected $fallback;

  public function __construct($parser, $fallback) {
    $this->parser = $parser;
    $this->fallback = $fallback;
  }

  public function transform( $state ) {
    if ($state->isError()) return $state;
    $nextState = $this->parser->parse($state);

    if (!$nextState->isError()) return $nextState;

    return $this->update($nextState, $this->fallback);
  }

}