<?php

namespace Themeco\Cornerstone\Parsy\Lib;

use Themeco\Cornerstone\Parsy\Parsers\EndOfInput;

trait Terminators {

  public static function eol() {
    return self::regex('/\A\s*$/m');
  }

  // terminates on semicolon or end of line
  public static function laxTerminate() { // terminates on semicolon or end of line
    return self::any( self::semiColon(), self::eol() );
  }

  // Will terminate on end of line, semicolons, of if the next character is a closing brace
  public static function laxBraceTerminate() {
    return self::any(self::semiColon(), self::eol(), self::lookahead(self::closeBrace()));
  }

  public static function end() {
    return new EndOfInput;
  }

}