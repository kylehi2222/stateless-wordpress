<?php

namespace Themeco\Cornerstone\Parsy\Util;

class Encoding {

  public static function decodeEscapedCharacters( $input ) {

    $escapes = [ 'b' => "\b", 'f' =>  "\f", 'n' =>  "\n", 'r' =>  "\r", 't' => "\t" ];

    return preg_replace_callback('/(?:\\\u[0-9a-fA-F]{4})+|\\\[^u]/', function ( $matches ) use ($escapes) {
      $match = $matches[0];
      $type = substr($matches[0],1,1);
      if ( strpos( $type, 'u') === 0 ) {
        return json_decode('"'.$matches[0].'"');
      }
      if (isset($escapes[$type])) {
        return $escapes[$type];
      }
      return $type;
    }, $input);

  }

}