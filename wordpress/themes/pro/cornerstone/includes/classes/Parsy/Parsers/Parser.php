<?php

namespace Themeco\Cornerstone\Parsy\Parsers;

use Themeco\Cornerstone\Parsy\Util\Result;
use Themeco\Cornerstone\Parsy\Util\Error;
use Themeco\Cornerstone\Parsy\Util\State;
use Themeco\Cornerstone\Parsy\Util\Token;

abstract class Parser {
  // Used by a couple of other parsers
  protected $mapFn;
  protected $parser;

  protected static $tracing = [];
  protected $name = 'generic';
  protected $properties = [];

  public function clone() {
    return clone $this;
  }

  abstract public function transform( $state );

  public function parse($state) {
    return $this->transform( $state );
  }

  public function run($targetString) {

    $finalState = $this->parse(State::create($targetString) );

    if ($finalState->isError()) {
      throw new \Exception($finalState->getErrorMessage() );
    }

    return $finalState->getResult();

  }

  public function name( $name ) {
    return $this->clone()->setName($name);
  }

  // The name should never be changed directly because it mutates the parser. Use the name function which will create a clone
  protected function setName( $name ) {
    $this->name = $name;
    return $this;
  }

  public function getName() {
    return $this->name;
  }

  public function update($state, $result, $advance = null) {
    return new Result($state, $result, $advance);
  }

  public function error($state, $message) {
    return new Error($state, $this->name, $message);
  }

  public function chain($fn) {
    return new Chain($this, $fn);
  }

  public function flag($flag) {
    return new Flag($this, $flag);
  }

  public function map($fn) {
    return new Map($this, $fn);
  }

  public function errorMap($fn) {
    return new ErrorMap($this, $fn);
  }

  public function isSyntaxError() {
    return new Failure($this);
  }

  public function validate($fn) {
    return new Failure(new Chain($this,function($result,$state,$prevState) use ($fn) {
      $result = $fn($result,$state,$prevState);
      return $result === true ? new Noop() : new Abort($result);
    }));
  }

  public function result( $result ) {
    return new SetResult($this, $result);
  }

  public function next($parser) {
    return (new Sequence([$this, $parser]));
  }

  public function or($parser) {
    return (new Any([$this, $parser]));
  }

  public function skip($parser) {
    return $this->next($parser)->map(function($results) {
      return $results[0];
    });
  }

  public function then($parser) {
    return $this->next($parser)->map(function($results) {
      return $results[1];
    });
  }

  public function otherwise($fallback) {
    return new Otherwise($this, $fallback);
  }

  public function maybeSkip( $parser ) {
    return $this->skip($parser->times(0,1));
  }

  public function maybeNext( $parser ) {
    return $this->next($parser->times(0,1));
  }

  public function times($min = 0, $max = -1) {
    return new Times($this, $min, $max);
  }

  public function repeat() {
    return $this->times();
  }

  public function thru($wrapper) {
    return $wrapper($this);
  }

  public function concat( $parser ) {
    return $this->next($parser)->join();
  }

  public function merge( $parser ) {
    return $this->next($parser)->flatten();
  }

  public function join($sep = '') {
    return $this->map(function( $items) use ($sep) {
      return implode( $sep, $items );
    })->name( "{$this->name}:join" );
  }

  public function followedBy( $parser ) {
    return $this->skip( new FollowedBy($parser) );
  }

  public function notFollowedBy( $parser ) {
    return $this->skip( new NotFollowedBy($parser) );
  }

  public function flatten() {
    return $this->map(function( $items) {
      return array_reduce( $items, function($acc, $next) {
        return array_merge( $acc, is_array( $next ) ? $next : [ $next ] ) ;
      }, []);
    })->name( "{$this->name}:flat" );
  }

  public function reverse() {
    return $this->map(function($items) {
      return array_reverse($items);
    })->name( "{$this->name}:flat" );
  }

  public function trim() {
    return $this->map(function($result) {
      return is_string( $result ) ? trim( $result) : $result;
    })->name( "{$this->name}:trim" );
  }

  public function split($sep) {
    return $this->map(function( $items) use ($sep) {
      return explode( $sep, $items );
    })->name( "{$this->name}:split" );
  }

  public function asToken($name) {
    return $this->map(function( $result ) use ($name ){
      return new Token( $name );
    })->trace($name);
  }

  public function token($name) {
    return $this->map(function( $result ) use ($name ){
      $token = new Token( $name );
      $token->setContent( $result );
      return $token;
    })->trace($name);
  }

  public function tap($prefix = '') {
    return $this->map(function($result) use ($prefix) {
      echo $prefix . json_encode($result, JSON_PRETTY_PRINT);
      return $result;
    });
  }

  public function errorTap($prefix = '') {
    return $this->errorMap(function($error) use ($prefix) {
      echo $prefix . json_encode($error, JSON_PRETTY_PRINT);
      return $error;
    });
  }

  public function nth($index) {
    return $this->map(function( $items) use ($index) {
      return $items[$index < 0 ? count($items) + $index : $index];
    })->name( "{$this->name}:nth" );
  }

  public function first() {
    return $this->nth(0);
  }

  public function last() {
    return $this->nth(-1);
  }

  public function trace($name) {
    return $this->errorMap(function($error) use ($name){
      if (!isset(self::$tracing[$name])) self::$tracing[$name] = 0;
      self::$tracing[$name]++;
      return $error;
    });
  }

}
