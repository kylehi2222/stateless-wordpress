<?php

namespace Themeco\Cornerstone\Parsy\Lib;

use Themeco\Cornerstone\Parsy\Util\Encoding;
use Themeco\Cornerstone\Parsy\Parsers\Str;
use Themeco\Cornerstone\Parsy\Parsers\AnyChar;

trait Values {

  public static function str( $str ) {
    return new Str($str);
  }

  public static function anyChar() {
    return new AnyChar;
  }

  public static function letter() {
    return self::regex('/\A[A-Za-z]/');
  }

  public static function digit() {
    return self::regex('/\A[0-9]/');
  }

  public static function letters() {
    return self::regex('/\A[A-Za-z]+/');
  }

  public static function word() { // alphanumeric + underscores + hyphen
    return self::regex('/\A\w+/');
  }

  public static function char() { // single alphanumeric + underscores + hyphen
    return self::regex('/\A[\w-]/');
  }

  public static function digits() {
    return self::regex('/\A[0-9]+/');
  }

  public static function digitsWithHex() {
    return self::regex('/\A[0-9a-fA-F]+/');
  }

  public static function ascii() {
    return self::regex('/\A[\x00-\x7F]/');
  }

  public static function non_ascii() {
    return self::regex('/\A[^\x00-\x7F]/');
  }

  public static function number() {
    return self::regex('/\A-?(0|[1-9][0-9]*)([.][0-9]+)?([eE][+-]?[0-9]+)?/')->map(function($value) {
      return floatval($value);
    });
  }

  public static function doubleQuotedEscapedString() {
    return self::regex('/\A"((?:\\.|.)*?)"/', 1)->map(function($value) {
      return Encoding::decodeEscapedCharacters($value);
    });
  }

  public static function singleQuotedEscapedString() {
    return self::regex("/\A'(.*?)'/", 1);
  }

  public static function quotedString() {
    return self::any(
      self::singleQuotedEscapedString(),
      self::doubleQuotedEscapedString()
    );
  }


  // These scalar methods are quick ways to get common types

  public static function scalarTrue() {
    return self::symbol('true')->result(true)->name('true');
  }

  public static function scalarFalse() {
    return self::symbol('false')->result(false)->name('false');
  }

  public static function scalarNull() {
    return self::symbol('null')->result(null)->name('null');
  }

  public static function scalarString() {
    return self::ws(self::quotedString())->name('quotedString');
  }

  public static function scalarNumber() {
    return self::ws(self::number())->name('number');
  }

}