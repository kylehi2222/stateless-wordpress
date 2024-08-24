<?php

namespace Themeco\Cornerstone\Tss\Operations;
use Themeco\Cornerstone\Util\Factory;

class FunctionCall implements Operation {
  public static function run( $stack, $input) {
    return Factory::create(FunctionCaller::class)->setStack($stack)->run($input);
  }
}
