<?php

namespace Themeco\Cornerstone\Util;

class MinifyCss {

  public function run( $css ) {
    // 1. Remove comments.
    // 2. Remove duplicate linebreaks
    // 3. Remove whitespace.
    // 4. Remove starting whitespace.

    $css = preg_replace( '#/\*.*?\*/#s', '', $css );         // 1
    $css = preg_replace( '/\s+/', ' ', $css );               // 2
    $css = preg_replace( '/\s*([{}|:;,])\s+/', '$1', $css ); // 3
    $css = preg_replace( '/\s\s+(.*)/', '$1', $css );        // 4

    return $css;
  }

}
