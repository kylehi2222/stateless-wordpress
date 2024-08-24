<?php

namespace Themeco\Cornerstone\Parsy\Lib;


trait Whitespace {

  public static function whitespace() {
    return self::regex('/\A\s*/m')->name('whitespace');
  }

  public static function whitespace1() {
    return self::regex('/\A\s+/m')->name('whitespace1');
  }

  public static function ws($parser) {
    return self::whitespace()->then($parser);
  }

  public static function symbol($str) {
    return self::regex('/\A\s*(' . preg_quote($str) . ')/m',1)->name("symbol:$str");
  }

  public static function wsWord() {
    return self::ws(self::word());
  }

}