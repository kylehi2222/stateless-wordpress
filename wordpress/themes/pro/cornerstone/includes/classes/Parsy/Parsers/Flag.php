<?php

namespace Themeco\Cornerstone\Parsy\Parsers;

class Flag extends Parser {
  public $flag;

  public function __construct($parser, $flag) {
    $this->parser = $parser;
    $this->flag = $flag;
  }

  public function transform($state) {
    $flagged = clone $state;
    $flagged->setFlag($this->flag);
    $nextState = $this->parser->parse($flagged);
    $nextState->unsetFlag($this->flag);
    return $nextState;
  }

}
