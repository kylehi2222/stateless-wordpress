<?php

namespace Themeco\Cornerstone\Parsy\Parsers;

class FollowedBy extends Parser {

  protected $name = 'followedBy';
  protected $parser;

  public function __construct($parser) {
    $this->parser = $parser;
  }

  public function transform( $state ) {
    $valueState = $this->parser->parse($state);
    if ($valueState->isError()) $this->error( $state, 'Unable to match any input');
    return $this->update($state, null);
  }

}