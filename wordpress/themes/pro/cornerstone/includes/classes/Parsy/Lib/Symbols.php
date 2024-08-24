<?php

namespace Themeco\Cornerstone\Parsy\Lib;


trait Symbols {

  public static function openBrace() {
    return self::symbol('{');
  }

  public static function closeBrace() {
    return self::symbol('}');
  }

  public static function openParen() {
    return self::symbol('(');
  }

  public static function closeParen() {
    return self::symbol(')');
  }

  public static function openBracket() {
    return self::symbol('[');
  }

  public static function closeBracket() {
    return self::symbol(']');
  }

  public static function comma() {
    return self::symbol(',');
  }

  public static function dot() {
    return self::symbol('.');
  }

  public static function colon() {
    return self::symbol(':');
  }

  public static function semicolon() {
    return self::symbol(';');
  }

  public static function arrow() {
    return self::symbol('=>');
  }

}