<?php

namespace Themeco\Cornerstone\Parsy\Parsers;

class Abort extends Parser {

  protected $name = 'abort';
  protected $content;

  public function __construct($content) {
    $this->content = $content;
  }

  public function transform( $state ) {
    return $this->error( $state, $this->content);
  }

}
