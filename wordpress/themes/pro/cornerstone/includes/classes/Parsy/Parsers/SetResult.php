<?php

namespace Themeco\Cornerstone\Parsy\Parsers;

class SetResult extends Parser {

  protected $name = 'result';
  protected $parser;
  protected $result;

  public function __construct($parser, $result) {
    $this->parser = $parser;
    $this->result = $result;
  }

  public function transform( $state ) {
    $nextState = $this->parser->parse($state);
    if ($nextState->isError()) return $nextState;
    return $this->update($nextState, $this->result);
  }

}