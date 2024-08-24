<?php

namespace Themeco\Cornerstone\Parsy\Lib;

use Themeco\Cornerstone\Parsy\Parsers\Chain;
use Themeco\Cornerstone\Parsy\Parsers\Noop;
use Themeco\Cornerstone\Parsy\Parsers\Abort;
use Themeco\Cornerstone\Parsy\Parsers\Lazy;
use Themeco\Cornerstone\Parsy\Parsers\SeparatedBy;
use Themeco\Cornerstone\Parsy\Parsers\Regex;
use Themeco\Cornerstone\Parsy\Parsers\Sequence;
use Themeco\Cornerstone\Parsy\Parsers\Lookahead;
use Themeco\Cornerstone\Parsy\Parsers\Any;

trait Combinators {

  public static function regex($pattern, $group = 0) {
    return new Regex($pattern, $group);
  }

  public static function sequence() {
    return new Sequence(func_get_args());
  }

  public static function any() {
    return new Any(func_get_args());
  }

  public static function noop() {
    return new Noop;
  }

  public static function chain($fn) {
    return new Chain(new Noop, $fn);
  }

  public static function many( $parser ) {
    return $parser->times(0,-1)->name('many');
  }

  public static function many1( $parser ) {
    return $parser->times(1,-1)->name('many1');
  }

  public static function lookahead( $parser ) {

    if ( is_string( $parser ) ) {
      return self::lookahead(self::str($parser));
    }

    return new Lookahead($parser);
  }

  public static function abort( $content = null) {
    return new Abort($content);
  }

  public static function sepBy( $separatorParser ) {
    return function($valueParser) use ($separatorParser) {
      return new SeparatedBy($separatorParser, $valueParser );
    };
  }

  public static function sepBy1( $separatorParser ) {
    return function($valueParser) use ($separatorParser) {
      return new SeparatedBy($separatorParser, $valueParser, 1);
    };
  }

  public static function lazy($fn, $name = '') {
    return new Lazy($fn, $name);
  }



}