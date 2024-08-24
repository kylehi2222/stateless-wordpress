<?php

namespace Themeco\Cornerstone\Parsy\Parsers;

class Sequence extends Parser {

  protected $name = 'sequence';
  protected $parsers;

  public function __construct($parsers) {
    $this->parsers = $parsers;
  }

  public function transform( $state ) {
    if ($state->isError()) return $state;

    $results = [];
    $nextState = $state;

    foreach ($this->parsers as $index => $parser) {
      $nextState = $parser->parse($nextState);
      if ($nextState->isError()) return $this->error($state, 'did not match sequence');
      $results[] = $nextState->getResult();
    }

    return $this->update($nextState, $results);
  }
}