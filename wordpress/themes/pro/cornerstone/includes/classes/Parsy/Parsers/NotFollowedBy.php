<?php

namespace Themeco\Cornerstone\Parsy\Parsers;

class NotFollowedBy extends Parser {

  protected $name = 'notFollowedBy';
  protected $parser;

  public function __construct($parser) {
    $this->parser = $parser;
  }

  public function transform( $state ) {
    $valueState = $this->parser->parse($state);
    if ($valueState->isError()) return $this->update($state, null);
    return $this->error( $state, 'notFollowedBy matched');
  }

}