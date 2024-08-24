<?php

namespace Themeco\Cornerstone\Vm\Lib;

class Math {

  public static function add() {
    return [[
      'a' => 'number',
      'b' => 'number'
    ], function($args) {
      return $args['a'] + $args['b'];
    }];
  }

}