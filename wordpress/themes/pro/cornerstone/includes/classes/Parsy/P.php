<?php

namespace Themeco\Cornerstone\Parsy;

use Themeco\Cornerstone\Parsy\Lib\Lib;
use Themeco\Cornerstone\Parsy\Language;
use Themeco\Cornerstone\Parsy\Util\Token;

class P {

  protected static $cache = [];

  public static function __callStatic($name, $arguments) {
    if (empty($arguments)) {
      if (!isset(self::$cache[$name])) {
        self::$cache[$name] = forward_static_call_array([ Lib::class, $name], $arguments);
      }
      return self::$cache[$name];
    }
    return forward_static_call_array([ Lib::class, $name], $arguments);
  }

  public static function createLanguage() {
    return forward_static_call_array([ Language::class, 'create' ], func_get_args() );
  }

  public static function token( $name, $content ) {
    $token = new Token( $name );
    $token->setContent( $content );
    return $token;
  }

}
