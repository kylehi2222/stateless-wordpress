<?php

namespace Themeco\Cornerstone\Tss\Operations;

interface Operation {
  public static function run($stack, $input);
}