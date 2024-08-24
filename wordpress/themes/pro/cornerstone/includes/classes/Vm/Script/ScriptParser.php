<?php


namespace Themeco\Cornerstone\Vm\Script;


use Themeco\Cornerstone\Parsy\OrderOperations;
use Themeco\Cornerstone\Parsy\LanguageParser;
use Themeco\Cornerstone\Parsy\P;
use Themeco\Cornerstone\Parsy\ParserState;
use Themeco\Cornerstone\Parsy\Util\ParseException;



class ScriptParser implements LanguageParser {
  public $grammar;
  public $debug;

  public function __construct(NanoScript $grammar) {
    $this->grammar = $grammar;
  }

  public function setup() {
    $this->grammar->setup($this->debug);
  }

  public function debug($enabled) {
    $this->debug = $enabled;
    return $this;
  }

  public function run( $input, $parser = 'script' ) {
    try {
      return $this->grammar->{$parser}->run( $input, $this->debug );
    } catch (ParseException $e) {
      if ($this->debug) {
        $this->debugOutput($e);
      }
      throw new \Exception( $e->getMessage() );
    }
  }

  // @TODO if we want to do this
  public function debugOutput($e) {

  }

  public function compile( $input ) {
    return $this->run( $this->cleanInput( $input ) );
  }

  public function hash( $input ) {
    return crc32($input);
  }

  public function cleanInput( $input ) {
    $input = preg_replace('/\/\*(?:.|\n)+?\*\//', '', $input); // multi line comments
    $input = preg_replace('/\/\/.*\\n/', '', $input); // single line comments
    return preg_replace('/\/\/.*$/', '', $input); // comments leading to end of input
  }

}
