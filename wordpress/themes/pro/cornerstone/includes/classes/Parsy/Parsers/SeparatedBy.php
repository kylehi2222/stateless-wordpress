<?php

namespace Themeco\Cornerstone\Parsy\Parsers;

class SeparatedBy extends Parser {

  protected $name = 'separatedBy';
  protected $separatorParser;
  protected $valueParser;
  protected $min;
  protected $max;

  public function __construct($separatorParser, $valueParser, $min = 0, $max = -1) {
    $this->separatorParser = $separatorParser;
    $this->valueParser = $valueParser;
    $this->min = $min;
    $this->max = $max;
  }

  public function transform( $state ) {
    if ($state->isError()) return $state;

    $nextState = $state;
    $results = [];

    $iterations = 0;
    $has_max = $this->max !== -1;

    while (!$has_max || ($iterations++ < $this->max)) {

      $valueState = $this->valueParser->parse($nextState);
      if ($valueState->isError()) break;
      $results[] = $valueState->getResult();
      $nextState = $valueState;

      $separatorState = $this->separatorParser->parse($nextState);
      if ($separatorState->isError()) break;
      $nextState = $separatorState;

    }

    if (count($results) < $this->min) {
      return $this->error( $state, 'Unable to match any input');
    }

    return $this->update($nextState, $results);
  }

}
