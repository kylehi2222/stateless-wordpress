<?php

namespace Themeco\Cornerstone\Vm\Lib;

class LibLoader {

  public static function load($resolver) {
    $resolver->fromStaticClass(Math::class);
  }

}