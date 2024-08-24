<?php

namespace Themeco\Cornerstone\Vm\Bytecode;
use Themeco\Cornerstone\Parsy\LanguageParser;

class JitCache {
  private $cache = [];
  private $parser;

  public function setup(LanguageParser $parser) {
    $this->parser = $parser;
    return $this;
  }

  public function compile($input) {
    $hash = $this->parser->hash( $input );
    return isset( $this->cache[ $hash ] )
      ? $this->cache[ $hash ]
      : $this->cache( $hash, $this->parser->compile($input) );
  }


  public function cache($hash, IBytecode $bytecode) {
    $this->cache[ $hash ] = $bytecode;
    return $bytecode;
  }

}