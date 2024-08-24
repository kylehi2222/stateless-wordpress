<?php

namespace Themeco\Cornerstone\Parsy\Lib;

trait Macros {

  public static function sepByComma( $parser ) {
    return self::sepBy(self::comma())($parser);
  }

  public static function betweenBraces( $parser ) {
    return self::openBrace()->then($parser)->skip(self::closeBrace());
  }

  public static function betweenParens( $parser ) {
    return self::openParen()->then($parser)->skip(self::closeParen());
  }

  public static function betweenBrackets( $parser ) {
    return self::openBracket()->then($parser)->skip(self::closeBracket());
  }

  public static function betweenParensSepByComma( $parser ) {
    return self::openParen()->then(self::sepByComma($parser))->skip(self::closeParen());
  }

  public static function betweenBracesSepByComma( $parser ) {
    return self::openBrace()->then(self::sepByComma($parser))->skip(self::closeBrace());
  }

  public static function betweenBracketsSepByComma( $parser ) {
    return self::openBracket()->then(self::sepByComma($parser))->skip(self::closeBracket());
  }

  // Strict methods will cause a syntax error if the closing element is missing

  public static function betweenBracesStrict( $parser ) {
    return self::openBrace()->then($parser)->skip(self::closeBrace()->isSyntaxError());
  }

  public static function betweenParensStrict( $parser ) {
    return self::openParen()->then($parser)->skip(self::closeParen()->isSyntaxError());
  }

  public static function betweenBracketsStrict( $parser ) {
    return self::openBracket()->then($parser)->skip(self::closeBracket()->isSyntaxError());
  }

  public static function betweenParensSepByCommaStrict( $parser ) {
    return self::openParen()->then(self::sepByComma($parser))->skip(self::closeParen()->isSyntaxError());
  }

  public static function betweenBracesSepByCommaStrict( $parser ) {
    return self::openBrace()->then(self::sepByComma($parser))->skip(self::closeBrace()->isSyntaxError());
  }

  public static function betweenBracketsSepByCommaStrict( $parser ) {
    return self::openBracket()->then(self::sepByComma($parser))->skip(self::closeBracket()->isSyntaxError());
  }



}